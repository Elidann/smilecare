<?php
/*
 * includes/navbar-admin.php
  Navbar for logged-in admin, $current is the active link key, passed in  by the page before including this file, suchas $current = 'dashboard';
 */
$current = $current ?? '';
?>
<nav class="navbar navbarAdmin">
  <div class="navbarLogo">
    <img src="/smilecare/assets/images/logoSample.png" alt="SmileCare Logo" />
  </div>

  <h1 class="navbarBrand navbarBrandLight">
    SmileCare <span class="adminBadge">ADMIN</span>
  </h1>

<!-- imp xmple ?php echo $current === 'dashboard' ? 'active' : ''; ? (need <>) --> 
  <div class="navLinks">
    <a class="navLink navLinkLight <?php echo $current === 'dashboard' ? 'active' : ''; ?>" href="/smilecare/adminDashboard.php">Dashboard</a>
    <a class="navLink navLinkLight <?php echo $current === 'appointments' ? 'active' : ''; ?>" href="/smilecare/appointments/index.php">Appointments</a>
    <a class="navLink navLinkLight <?php echo $current === 'patients' ? 'active' : ''; ?>" href="/smilecare/patients/index.php">Patients</a>
  </div>

  <div class="navbarBtn">
    <div class="navAvatar navAvatarDark">AD</div>
  </div>
</nav>
