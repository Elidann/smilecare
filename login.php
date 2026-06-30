<?php
/*
  login.php
  Public login form. Submits to actions/login-action.php.
  Shows an error message if redirected back with ?error=1
 */
session_start();
if (isset($_SESSION['user_id'])) {
    // already logged in, skip straight to the right dashboard
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'adminDashboard.php' : 'userDashboard.php'));
    exit;
}
$error = isset($_GET['error']);
?>
<html>
  <head>
    <title>Login — SmileCare Dental Clinic</title>
    <link rel="stylesheet" href="assets/css/login.css" />
  </head>

  <body class="authBody">
    <div class="authPage">
      <div class="authLeft">
        <div class="authLeftLogo">
          <img src="assets/images/logoSample.png" alt="SmileCare Logo" />
          <h2>SmileCare</h2>
        </div>

        <h3>Welcome Back!</h3>
        <p>Log in to manage your dental appointments and stay on top of your oral health.</p>

        <div class="authFeatures">
          <div class="authFeature"><span>View upcoming appointments</span></div>
          <div class="authFeature"><span>Get appointment reminders</span></div>
          <div class="authFeature"><span>Access your dental records</span></div>
        </div>

        <a class="backToHome" href="index.php">← Back to Home</a>
      </div>

      <div class="authRight">
        <div class="authForm">
          <h3>Log In</h3>
          <p class="authSubtitle">Enter your credentials to access your account</p>

          <?php if ($error): ?>
            <p style="color:#ff5a5a; margin-bottom:12px;">Incorrect email or password.</p>
          <?php endif; ?>

          <form action="actions/login-action.php" method="POST">
            <div class="formGroup">
              <label class="formLabel">Email Address</label>
              <div class="inputIconWrap">
                <input class="formInput" type="email" name="email" placeholder="example@gmail.com" required />
              </div>
            </div>

            <div class="formGroup">
              <label class="formLabel">Password</label>
              <div class="inputIconWrap">
                <input class="formInput" type="password" name="password" placeholder="Enter your password" required />
              </div>
            </div>

            <button class="btnFull" type="submit">Sign In →</button>
          </form>

          <div class="authSwitch">
            Don't have an account?
            <a href="register.php">Register here</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
