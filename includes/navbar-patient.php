<?php
/*
 - includes/navbar-patient.php
  -Navbar for logged-in patients. Expects $_SESSION['first_name'] /
  - $_SESSION['last_name'] to already be set by the login action.
 */
$initials = strtoupper(substr($_SESSION['first_name'] ?? 'U', 0, 1) . substr($_SESSION['last_name'] ?? '', 0, 1));
?>
<nav class="navbar">
  <div class="navbarLogo">
<!--  to note <img src="/smilecare/assets/images/logoSample.png" alt="SmileCare Logo" /> -->
    <img src="/smilecare/assets/images/logoSample.png" alt="SmileCare Logo" />
  </div>
  <h1 class="navbarBrand">SmileCare</h1>

  <div class="navLinks">
    <a class="navLink active" href="/smilecare/userDashboard.php">Dashboard</a>
  </div>

  <div class="navbarBtn">
    <div class="navAvatar"><?php echo htmlspecialchars($initials); ?></div>
  </div>
</nav>
