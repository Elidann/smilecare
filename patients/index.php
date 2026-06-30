<?php
/*
 - patients/index.php
  Admin-  list of all registered patients with quick search.
 */
require_once __DIR__ . '/../includes/auth-check.php'; 
// admin only
require_role('admin');
require_once __DIR__ . '/../config/db.php';

// Get the search keyword entered by the administrator
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $conn->real_escape_string($search) . '%';
    $patients = $conn->query(
        "SELECT user_id, first_name, last_name, email, phone,
                (SELECT COUNT(*) FROM appointments WHERE patient_id = users.user_id) AS visit_count
         FROM users
         WHERE role = 'patient' AND (first_name LIKE '$like' OR last_name LIKE '$like' OR email LIKE '$like')
         ORDER BY last_name, first_name"
    );
} else {
    // Retrieve patients whose first name, last name
    // or email matches the search keyword
    $patients = $conn->query(
        "SELECT user_id, first_name, last_name, email, phone,
                (SELECT COUNT(*) FROM appointments WHERE patient_id = users.user_id) AS visit_count
         FROM users
         WHERE role = 'patient'
         ORDER BY last_name, first_name"
    );
}

$current = 'patients';
?>
<html>
  <head>
    <title>Patients — SmileCare Admin</title>
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
          <h2>Patients</h2>
          <p>All registered patient accounts.</p>
        </div>

        <div class="sectionCard">
          <div class="sectionCardHeader">
            <h3>All Patients</h3>
            <form class="searchBar" method="GET">
              
              <input type="text" name="q" placeholder="Search name or email…" value="<?php echo htmlspecialchars($search); ?>" />
            </form>
          </div>

          <table class="dataTable">
            <thead>
              <tr>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Visits</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($patients->num_rows === 0): ?>
                <tr><td colspan="5">No patients found.</td></tr>
              <?php endif; ?>
              <?php while ($row = $patients->fetch_assoc()): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['email']); ?></td>
                  <td><?php echo htmlspecialchars($row['phone']); ?></td>
                  <td><?php echo $row['visit_count']; ?></td>
                  <td><a class="btnSm btnSmOutline" href="view.php?id=<?php echo $row['user_id']; ?>">View</a></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </body>
</html>
