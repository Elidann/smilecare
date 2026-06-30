<?php
/**
 * patients/view.php
 * Admin: detail view of a single patient and their full appointment history.
 */
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'patient'");
$stmt->bind_param('i', $id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$patient) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.consent_status,
            s.name AS service_name, d.full_name AS dentist_name
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     WHERE a.patient_id = ?
     ORDER BY a.appointment_date DESC"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$appointments = $stmt->get_result();
$stmt->close();

$current = 'patients';
?>
<html>
  <head>
    <title><?php echo htmlspecialchars($patient['first_name']); ?> — SmileCare Admin</title>
    <link rel="stylesheet" href="../assets/css/adminDashboard.css" />
  </head>

  <body>
    <?php require_once __DIR__ . '/../includes/navbar-admin.php'; ?>

    <div class="dashLayout">
      <aside class="sidebar sidebarAdmin">
        <nav class="sidebarNav">
          <div class="sidebarSectionLabel sidebarSectionLabelLight">Management</div>
          <a class="sidebarLink sidebarLinkLight" href="../adminDashboard.php"> Dashboard</a>
          <a class="sidebarLink sidebarLinkLight" href="../appointments/index.php"> Appointments</a>
          <a class="sidebarLink sidebarLinkLight active" href="index.php"> Patients</a>
          <div class="sidebarSectionLabel sidebarSectionLabelLight">System</div>
          <a class="sidebarLink sidebarLinkLight" href="../logout.php"> Logout</a>
        </nav>
      </aside>

      <main class="dashMain">
        <div class="dashHeader">
          <h2><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h2>
          <p><a href="index.php">← Back to all patients</a></p>
        </div>

        <div class="sectionCard">
          <div class="sectionCardHeader"><h3>Patient Info</h3></div>
          <div class="sectionCardBody">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo $patient['date_of_birth'] ? date('F j, Y', strtotime($patient['date_of_birth'])) : '—'; ?></p>
            <p><strong>Registered:</strong> <?php echo date('F j, Y', strtotime($patient['created_at'])); ?></p>
          </div>
        </div>

        <div class="sectionCard">
          <div class="sectionCardHeader"><h3>Appointment History</h3></div>
          <table class="dataTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Service</th>
                <th>Dentist</th>
                <th>Status</th>
                <th>Consent</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($appointments->num_rows === 0): ?>
                <tr><td colspan="7">No appointments yet.</td></tr>
              <?php endif; ?>
              <?php while ($row = $appointments->fetch_assoc()): ?>
                <tr>
                  <td><?php echo date('M j, Y', strtotime($row['appointment_date'])); ?></td>
                  <td><?php echo date('g:i A', strtotime($row['appointment_time'])); ?></td>
                  <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['dentist_name'] ?? '—'); ?></td>
                  <td><span class="badge badgeGray"><?php echo ucfirst($row['status']); ?></span></td>
                  <td>
                    <span class="badge <?php echo $row['consent_status'] === 'signed' ? 'badgeGreen' : 'badgeAmber'; ?>">
                      <?php echo $row['consent_status'] === 'signed' ? 'Signed' : 'Pending'; ?>
                    </span>
                  </td>
                  <td><a class="btnSm btnSmOutline" href="../appointments/view.php?id=<?php echo $row['appointment_id']; ?>">Manage</a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </body>
</html>
