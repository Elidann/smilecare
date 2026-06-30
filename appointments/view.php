<?php
/*
NOTES 
 - appointments/view.php
 - Admin =  manage a single appointment - update its status, and mark the consent form as signed once the patient has signed it on paper at  the clinic. 
 - print trigger for the consent form  

 Features - 
   - Displays complete appointment information.
   - Shows patient details.
  - Shows selected service and dentist.
  - Allows the admin to change the appointment status.
  - Allows the admin to print the patient's consent form.
  - Allows the admin to mark the consent form as signed after
  - the patient signs it physically at the clinic.
 */
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT a.*, s.name AS service_name, d.full_name AS dentist_name,
            u.first_name, u.last_name, u.email, u.phone
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     JOIN users u ON u.user_id = a.patient_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     WHERE a.appointment_id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appt) {
    header('Location: index.php');
    exit;
}

$current = 'appointments';
?>
<html>
  <head>
    <!-- title-->
    <title>Manage Appointment — SmileCare Admin</title>
    <!-- Dashboard stylesheet -->
    <link rel="stylesheet" href="../assets/css/adminDashboard.css" />
  </head>

  <body>
    <?php require_once __DIR__ . '/../includes/navbar-admin.php'; ?>

    
  <!-- dashbleft -->
    <div class="dashLayout">
      <aside class="sidebar sidebarAdmin">
        <nav class="sidebarNav">
          <div class="sidebarSectionLabel sidebarSectionLabelLight">Management</div>
          <a class="sidebarLink sidebarLinkLight" href="../adminDashboard.php"> Dashboard</a>
          <a class="sidebarLink sidebarLinkLight active" href="index.php"> Appointments</a>
          <a class="sidebarLink sidebarLinkLight" href="../patients/index.php"> Patients</a>
          <div class="sidebarSectionLabel sidebarSectionLabelLight">System</div>
          <a class="sidebarLink sidebarLinkLight" href="../logout.php"> Logout</a>
        </nav>
      </aside>
      <!-- MAIN content --> 
      <main class="dashMain">
        <div class="dashHeader">
          <h2>Appointment Details</h2>
          <p><a href="index.php">← Back to all appointments</a></p>
        </div>


        <!-- container card--> 
        <div class="sectionCard">
          <div class="sectionCardHeader"><h3>Patient & Appointment Info</h3></div>
          <div class="sectionCardBody">
            <p><strong>Patient:</strong> <?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($appt['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($appt['phone']); ?></p>
            <p><strong>Service:</strong> <?php echo htmlspecialchars($appt['service_name']); ?></p>
            <p><strong>Dentist:</strong> <?php echo htmlspecialchars($appt['dentist_name'] ?? 'Any available'); ?></p>
            <p><strong>Date/Time:</strong> <?php echo date('M j, Y', strtotime($appt['appointment_date'])); ?> at <?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></p>
            <?php if (!empty($appt['notes'])): ?>
              <p><strong>Notes:</strong> <?php echo htmlspecialchars($appt['notes']); ?></p>
            <?php endif; ?>
            <p><strong>Status:</strong>
              <span class="badge <?php
                echo $appt['status'] === 'confirmed' ? 'badgeTeal' :
                     ($appt['status'] === 'completed' ? 'badgeGreen' :
                     ($appt['status'] === 'cancelled' ? 'badgeGray' : 'badgeAmber'));
              ?>"><?php echo ucfirst($appt['status']); ?></span>
            </p>
          </div>
        </div>

        <!-- UPDATE STATUS -->
        <div class="sectionCard">
          <div class="sectionCardHeader"><h3>Update Status</h3></div>
          <div class="sectionCardBody">
            <form action="../actions/update-status-action.php" method="POST" style="display:flex; gap:12px; align-items:center;">
              <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>" />
              <select class="formSelect" name="status">
                <option value="pending" <?php echo $appt['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $appt['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="completed" <?php echo $appt['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $appt['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
              </select>
              <button class="btnSm btnSmTeal" type="submit">Save Status</button>
            </form>
          </div>
        </div>

        <!-- CONSENT FORM -->
        <div class="sectionCard">
          <div class="sectionCardHeader"><h3>Consent Form</h3></div>
          <div class="sectionCardBody">
            <p>
              Current consent status:
              <span class="badge <?php echo $appt['consent_status'] === 'signed' ? 'badgeGreen' : 'badgeAmber'; ?>">
                <?php echo $appt['consent_status'] === 'signed' ? 'Signed' : 'Pending'; ?>
              </span>
            </p>
            <?php if ($appt['consent_status'] === 'signed' && $appt['consent_signed_at']): ?>
              <p style="color:#4a5e68; font-size: 0.9rem;">
                Signed on <?php echo date('M j, Y g:i A', strtotime($appt['consent_signed_at'])); ?>
              </p>
            <?php endif; ?>

            <!-- for print consent form --> 
            <div style="display:flex; gap:12px; margin-top:12px;">
              <a class="btnSm btnSmOutline" href="../patients/print.php?id=<?php echo $appt['appointment_id']; ?>" target="_blank">
                 Print Consent Form
              </a>

              <?php if ($appt['consent_status'] !== 'signed'): ?>
                <form action="../actions/mark-consent-action.php" method="POST" onsubmit="return confirm('Confirm the patient has physically signed the consent form?');">
                  <input type="hidden" name="appointment_id" value="<?php echo $appt['appointment_id']; ?>" />
                  <button class="btnSm btnSmTeal" type="submit"> Mark as Signed</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
