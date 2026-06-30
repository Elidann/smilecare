<?php
/*
 -  userDashboard.php
  - Patient dashboard: summary cards, upcoming appointments, book new
  - appointment, and visit history. All data is pulled live from the DB for the logged-in patient only
 */
require_once __DIR__ . '/includes/auth-check.php';
require_role('patient');
require_once __DIR__ . '/config/db.php';

$patient_id = $_SESSION['user_id'];

//  Summary counts 
$upcoming_count = $conn->query(
    "SELECT COUNT(*) AS c FROM appointments
     WHERE patient_id = $patient_id AND status IN ('pending','confirmed') AND appointment_date >= CURDATE()"
)->fetch_assoc()['c'];

$completed_count = $conn->query(
    "SELECT COUNT(*) AS c FROM appointments WHERE patient_id = $patient_id AND status = 'completed'"
)->fetch_assoc()['c'];

$next_appt = $conn->query(
    "SELECT appointment_date FROM appointments
     WHERE patient_id = $patient_id AND status IN ('pending','confirmed') AND appointment_date >= CURDATE()
     ORDER BY appointment_date ASC, appointment_time ASC LIMIT 1"
)->fetch_assoc();
$next_label = $next_appt ? date('M j', strtotime($next_appt['appointment_date'])) : '—';

//  Upcoming appointments list 
$upcoming = $conn->query(
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
            s.name AS service_name, d.full_name AS dentist_name
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     WHERE a.patient_id = $patient_id AND a.status IN ('pending','confirmed') AND a.appointment_date >= CURDATE()
     ORDER BY a.appointment_date ASC, a.appointment_time ASC"
);

// Visit history (completed / cancelled) 
$history = $conn->query(
    "SELECT a.appointment_date, a.status, s.name AS service_name, d.full_name AS dentist_name
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     WHERE a.patient_id = $patient_id AND a.status IN ('completed','cancelled')
     ORDER BY a.appointment_date DESC
     LIMIT 10"
);

$services = $conn->query("SELECT service_id, name FROM services ORDER BY name");
$dentists = $conn->query("SELECT dentist_id, full_name FROM dentists ORDER BY full_name");

$bookError = $_GET['error'] ?? '';
?>
<html>
  <head>
    <title>Dashboard — SmileCare Dental Clinic</title>
    <link rel="stylesheet" href="assets/css/userDashboard.css" />
  </head>

  <body>
    <?php require_once __DIR__ . '/includes/navbar-patient.php'; ?>

    <div class="dashLayout">
      <aside class="sidebar">
        <div class="sidebarUser">
          <div class="sidebarAvatar"><?php echo htmlspecialchars(strtoupper($_SESSION['first_name'][0] . $_SESSION['last_name'][0])); ?></div>
          <div class="sidebarUserName"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
        </div>

        <nav class="sidebarNav">
          <div class="sidebarSectionLabel">Main</div>
          <a class="sidebarLink active" href="userDashboard.php"> Overview</a>
          <a class="sidebarLink" href="logout.php"> Logout</a>
        </nav>
      </aside>

      <main class="dashMain">
        <div class="dashHeader">
          <h2>Hi, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! </h2>
          <p>Here's a summary of your dental care account.</p>
        </div>

        <div class="summaryCards">
          <div class="summaryCard">
            
            <div>
              <div class="summaryLabel">Upcoming Appointments</div>
              <div class="summaryValue"><?php echo $upcoming_count; ?></div>
            </div>
          </div>
          <div class="summaryCard">
            
            <div>
              <div class="summaryLabel">Completed Visits</div>
              <div class="summaryValue"><?php echo $completed_count; ?></div>
            </div>
          </div>
          <div class="summaryCard">
            
            <div>
              <div class="summaryLabel">Next Appointment</div>
              <div class="summaryValue summaryValueSm"><?php echo htmlspecialchars($next_label); ?></div>
            </div>
          </div>
        </div>

        <!-- UPCOMING APPOINTMENTS -->
        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3>Upcoming Appointments</h3>
          </div>
          <div class="sectionCardBody">
            <?php if ($upcoming->num_rows === 0): ?>
              <p>You have no upcoming appointments. Book one below.</p>
            <?php endif; ?>
            <?php while ($row = $upcoming->fetch_assoc()): ?>
              <div class="apptItem">
                <div class="apptDateBox">
                  <div class="apptDay"><?php echo date('d', strtotime($row['appointment_date'])); ?></div>
                  <div class="apptMonth"><?php echo date('M', strtotime($row['appointment_date'])); ?></div>
                </div>
                <div class="apptDetails">
                  <div class="apptTitle"><?php echo htmlspecialchars($row['service_name']); ?></div>
                  <div class="apptMeta">
                    <?php echo date('g:i A', strtotime($row['appointment_time'])); ?>
                    <?php if ($row['dentist_name']): ?> · <?php echo htmlspecialchars($row['dentist_name']); ?><?php endif; ?>
                  </div>
                </div>
                <span class="badge <?php echo $row['status'] === 'confirmed' ? 'badgeTeal' : 'badgeAmber'; ?>">
                  <?php echo ucfirst($row['status']); ?>
                </span>
                <div class="apptActions">
                  <form action="actions/cancel-action.php" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                    <input type="hidden" name="appointment_id" value="<?php echo $row['appointment_id']; ?>" />
                    <button class="btnSm btnSmOutline btnSmRed" type="submit">Cancel</button>
                  </form>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>

        <!-- BOOK APPOINTMENT -->
        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3> Book a New Appointment</h3>
          </div>
          <div class="sectionCardBody">
            <?php if ($bookError === 'fields'): ?>
              <p style="color:#ff5a5a;">Please fill in all required fields.</p>
            <?php elseif ($bookError === 'past'): ?>
              <p style="color:#ff5a5a;">Please choose a date that hasn't already passed.</p>
            <?php endif; ?>

            <form action="actions/book-action.php" method="POST">
              <div class="bookForm">
                <div class="formGroup">
                  <label class="formLabel">Service</label>
                  <select class="formSelect" name="service_id" required>
                    <option value="">Select a service…</option>
                    <?php while ($s = $services->fetch_assoc()): ?>
                      <option value="<?php echo $s['service_id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="formGroup">
                  <label class="formLabel">Preferred Dentist</label>
                  <select class="formSelect" name="dentist_id">
                    <option value="">Any available dentist</option>
                    <?php while ($d = $dentists->fetch_assoc()): ?>
                      <option value="<?php echo $d['dentist_id']; ?>"><?php echo htmlspecialchars($d['full_name']); ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="formGroup">
                  <label class="formLabel">Preferred Date</label>
                  <input class="formInput" type="date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required />
                </div>

                <div class="formGroup">
                  <label class="formLabel">Preferred Time</label>
                  <select class="formSelect" name="appointment_time" required>
                    <option value="">Select a time slot…</option>
                    <option value="08:00">8:00 AM</option>
                    <option value="09:00">9:00 AM</option>
                    <option value="10:00">10:00 AM</option>
                    <option value="11:00">11:00 AM</option>
                    <option value="13:00">1:00 PM</option>
                    <option value="14:00">2:00 PM</option>
                    <option value="15:00">3:00 PM</option>
                  </select>
                </div>
              </div>

              <div class="formGroup formGroupMt">
                <label class="formLabel">Notes / Concerns (optional)</label>
                <textarea class="formInput formTextarea" name="notes" rows="2" placeholder="Describe your concern or symptoms…"></textarea>
              </div>

              <button class="btnSolid btnSolidLg" type="submit">Confirm Booking →</button>
            </form>
          </div>
        </div>

        <!-- VISIT HISTORY -->
        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3>Recent Visit History</h3>
          </div>
          <div class="sectionCardBody">
            <?php if ($history->num_rows === 0): ?>
              <p>No past visits yet.</p>
            <?php endif; ?>
            <?php while ($row = $history->fetch_assoc()): ?>
              <div class="apptItem">
                <div class="apptDateBox">
                  <div class="apptDay"><?php echo date('d', strtotime($row['appointment_date'])); ?></div>
                  <div class="apptMonth"><?php echo date('M', strtotime($row['appointment_date'])); ?></div>
                </div>
                <div class="apptDetails">
                  <div class="apptTitle"><?php echo htmlspecialchars($row['service_name']); ?></div>
                  <div class="apptMeta">
                    <?php echo $row['dentist_name'] ? htmlspecialchars($row['dentist_name']) . ' · ' : ''; ?>
                    <?php echo ucfirst($row['status']); ?>
                  </div>
                </div>
                <span class="badge <?php echo $row['status'] === 'completed' ? 'badgeGreen' : 'badgeGray'; ?>">
                  <?php echo ucfirst($row['status']); ?>
                </span>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
