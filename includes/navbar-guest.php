<?php
/*
  - includes/navbar-guest.php
  - Navbar shown on public pages (index, login, register) to visitors  who are not logged in.
 */
?>
<nav class="navbar">
  <div class="navbarLeft">
    <img src="/smilecare/assets/images/logoSample.png" alt="SmileCare Logo" />
    <h1 class="navbarBrand">SmileCare</h1>
  </div>

<!--<a class="navLink" href="/smilecare/index.php#services">Services</a> -->
  <div class="navLinks">
    <a class="navLink active" href="/smilecare/index.php">Home</a>
    <a class="navLink" href="/smilecare/index.php#services">Services</a>
    <a class="navLink" href="/smilecare/index.php#doctors">Doctors</a>
    <a class="navLink" href="/smilecare/index.php#about">About</a>
  </div>
<!--  imp xmple <button class="btnOutline" onclick="window.location.href='/smilecare/login.php'">Login</button> --> 
  <div class="navbarBtn">
    <button class="btnOutline" onclick="window.location.href='/smilecare/login.php'">Login</button>
    <button class="btnSolid" onclick="window.location.href='/smilecare/register.php'">Register</button>
  </div>
</nav>
