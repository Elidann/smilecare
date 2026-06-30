<?php
/*
  - appointments/index.php
  - Admin= full list of all appointments, with filters by status.
 */
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

//default all 
$filter = $_GET['status'] ?? 'all';
$where = '';

// only valid status values 
// anti sql injection 
if (in_array($filter, ['pending', 'confirmed', 'completed', 'cancelled'], true)) {
    $where = "WHERE a.status = '" . $conn->real_escape_string($filter) . "'";
}

$appointments = $conn->query(
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.consent_status,
            s.name AS service_name, d.full_name AS dentist_name,
            u.first_name, u.last_name
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     JOIN users u ON u.user_id = a.patient_id
     LEFT JOIN dentists d ON d.dentist_id = a.dentist_id
     $where
     ORDER BY a.appointment_date DESC, a.appointment_time DESC"
);
// Used by the navigation bar to highlight the active page.
$current = 'appointments';
?>
<html>
  <head>
    <!-- Browser tab title -->
    <title>Appointments — SmileCare Admin</title>
    <!-- Admin dashboard stylesheet -->
    <link rel="stylesheet" href="../assets/css/adminDashboard.css" />
  </head>

  <body>
    <!--  Load the admin navigation bar.-->
    <?php require_once __DIR__ . '/../includes/navbar-admin.php'; ?>

<!-- layout left -->
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

      <main class="dashMain">
        <div class="dashHeader">
          <h2>All Appointments</h2>
          <p>Manage appointment status and consent form sign-off.</p>
        </div>

        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3>Filter</h3>
            <div class="sectionCardActions">
              <a class="btnSm <?php echo $filter === 'all' ? 'btnSmTeal' : 'btnSmOutline'; ?>" href="?status=all">All</a>
              <a class="btnSm <?php echo $filter === 'pending' ? 'btnSmTeal' : 'btnSmOutline'; ?>" href="?status=pending">Pending</a>
              <a class="btnSm <?php echo $filter === 'confirmed' ? 'btnSmTeal' : 'btnSmOutline'; ?>" href="?status=confirmed">Confirmed</a>
              <a class="btnSm <?php echo $filter === 'completed' ? 'btnSmTeal' : 'btnSmOutline'; ?>" href="?status=completed">Completed</a>
              <a class="btnSm <?php echo $filter === 'cancelled' ? 'btnSmTeal' : 'btnSmOutline'; ?>" href="?status=cancelled">Cancelled</a>
            </div>
          </div>

          <div class="tableWrap">
            <table class="dataTable">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Patient</th>
                  <th>Service</th>
                  <th>Dentist</th>
                  <th>Status</th>
                  <th>Consent</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($appointments->num_rows === 0): ?>
                  <tr><td colspan="8">No appointments found.</td></tr>
                <?php endif; ?>
                <?php while ($row = $appointments->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo date('M j, Y', strtotime($row['appointment_date'])); ?></td>
                    <td><?php echo date('g:i A', strtotime($row['appointment_time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['dentist_name'] ?? '—'); ?></td>
                    <td>
                      <span class="badge <?php
                        echo $row['status'] === 'confirmed' ? 'badgeTeal' :
                             ($row['status'] === 'completed' ? 'badgeGreen' :
                             ($row['status'] === 'cancelled' ? 'badgeGray' : 'badgeAmber'));
                      ?>"><?php echo ucfirst($row['status']); ?></span>
                    </td>
                    <td>
                      <span class="badge <?php echo $row['consent_status'] === 'signed' ? 'badgeGreen' : 'badgeAmber'; ?>">
                        <?php echo $row['consent_status'] === 'signed' ? 'Signed' : 'Pending'; ?>
                      </span>
                    </td>
                    <td><a class="btnSm btnSmOutline" href="view.php?id=<?php echo $row['appointment_id']; ?>">Manage</a></td>
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
