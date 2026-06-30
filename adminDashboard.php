<?php
/*
- adminDashboard.php
  - Admin overview: KPI cards + today's schedule, including consent status
  
 */
require_once __DIR__ . '/includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/config/db.php';

$appts_today = $conn->query(
    "SELECT COUNT(*) AS c FROM appointments WHERE appointment_date = CURDATE() AND status != 'cancelled'"
)->fetch_assoc()['c'];

$total_patients = $conn->query(
    "SELECT COUNT(*) AS c FROM users WHERE role = 'patient'"
)->fetch_assoc()['c'];

$pending_count = $conn->query(
    "SELECT COUNT(*) AS c FROM appointments WHERE status = 'pending'"
)->fetch_assoc()['c'];

$cancelled_today = $conn->query(
    "SELECT COUNT(*) AS c FROM appointments WHERE appointment_date = CURDATE() AND status = 'cancelled'"
)->fetch_assoc()['c'];

$todaySchedule = $conn->query(
    "SELECT a.appointment_id, a.appointment_time, a.status, a.consent_status,
            s.name AS service_name, d.full_name AS dentist_name,
            u.first_name, u.last_name
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     JOIN users u ON u.user_id = a.patient_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     WHERE a.appointment_date = CURDATE() AND a.status != 'cancelled'
     ORDER BY a.appointment_time ASC"
);

$current = 'dashboard';
?>
<html>
  <head>
    <title>Admin Dashboard — SmileCare Dental Clinic</title>
    <link rel="stylesheet" href="assets/css/adminDashboard.css" />
  </head>

  <body>
    <?php require_once __DIR__ . '/includes/navbar-admin.php'; ?>

    <div class="dashLayout">
      <aside class="sidebar sidebarAdmin">
        <div class="sidebarUser">
          <div class="sidebarAvatar sidebarAvatarDark">AD</div>
          <div class="sidebarUserName sidebarUserNameLight">Admin</div>
        </div>

        <nav class="sidebarNav">
          <div class="sidebarSectionLabel sidebarSectionLabelLight">Management</div>
          <a class="sidebarLink sidebarLinkLight active" href="adminDashboard.php"> Dashboard</a>
          <a class="sidebarLink sidebarLinkLight" href="appointments/index.php"> Appointments</a>
          <a class="sidebarLink sidebarLinkLight" href="patients/index.php"> Patients</a>

          <div class="sidebarSectionLabel sidebarSectionLabelLight">System</div>
          <a class="sidebarLink sidebarLinkLight" href="logout.php"> Logout</a>
        </nav>
      </aside>

      <main class="dashMain">
        <div class="dashHeader dashHeaderRow">
          <div>
            <h2>Admin Dashboard</h2>
            <p>Overview of clinic operations · <?php echo date('F Y'); ?></p>
          </div>
        </div>

          <!-- admin cards start  -->
        <div class="adminCards">
          <div class="adminCard">
            <div class="adminCardTop"></div>
            <div class="adminCardValue"><?php echo $appts_today; ?></div>
            <div class="adminCardLabel">Appointments Today</div>
          </div>

          <div class="adminCard">
            <div class="adminCardTop"></div>
            <div class="adminCardValue"><?php echo $total_patients; ?></div>
            <div class="adminCardLabel">Total Patients</div>
          </div>

          <div class="adminCard">
            <div class="adminCardTop"></div>
            <div class="adminCardValue"><?php echo $pending_count; ?></div>
            <div class="adminCardLabel">Pending Confirmations</div>
          </div>

          <div class="adminCard">
            <div class="adminCardTop"></div>
            <div class="adminCardValue"><?php echo $cancelled_today; ?></div>
            <div class="adminCardLabel">Cancellations Today</div>
          </div>
        </div>

        <!-- TODAY'S SCHEDULE -->
        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3>Today's Schedule</h3>
          </div>

          <div class="tableWrap">
            <table class="dataTable">
              <thead>
                <tr>
                  <th>Patient</th>
                  <th>Time</th>
                  <th>Service</th>
                  <th>Dentist</th>
                  <th>Status</th>
                  <th>Consent</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($todaySchedule->num_rows === 0): ?>
                  <tr><td colspan="7">No appointments scheduled for today.</td></tr>
                <?php endif; ?>
                <?php while ($row = $todaySchedule->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo date('g:i A', strtotime($row['appointment_time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['dentist_name'] ?? '—'); ?></td>
                    <td>
                      <span class="badge <?php
                        echo $row['status'] === 'confirmed' ? 'badgeTeal' :
                             ($row['status'] === 'completed' ? 'badgeGreen' : 'badgeAmber');
                      ?>"><?php echo ucfirst($row['status']); ?></span>
                    </td>
                    <td>
                      <span class="badge <?php echo $row['consent_status'] === 'signed' ? 'badgeGreen' : 'badgeAmber'; ?>">
                        <?php echo $row['consent_status'] === 'signed' ? 'Signed' : 'Pending'; ?>
                      </span>
                    </td>
                    <td><a class="btnSm btnSmOutline" href="appointments/view.php?id=<?php echo $row['appointment_id']; ?>">Manage</a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
