<?php
/*
  register.php
 -  Public registration form. Submits to actions/register-action.php.
 -  Terms & Agreement must be checked before an account can be created -
 */
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'adminDashboard.php' : 'userDashboard.php'));
    exit;
}
$error = $_GET['error'] ?? '';
?>
<html>
  <head>
    <title>Register — SmileCare Dental Clinic</title>
    <link rel="stylesheet" href="assets/css/register.css" />
  </head>

  <body class="authBody">
    <div class="authPage">
      <div class="authLeft">
        <div class="authLeftLogo">
          <img src="assets/images/logoSample.png" alt="SmileCare Logo" />
          <h2>SmileCare</h2>
        </div>

        <h3>Join SmileCare</h3>
        <p>Create your free account and start booking dental appointments in minutes.</p>

        <div class="authFeatures">
          <div class="authFeature"><span>Book appointments instantly</span></div>
          <div class="authFeature"><span>Access your dental history</span></div>
          <div class="authFeature"><span>Secure & private records</span></div>
        </div>

        <a class="backToHome" href="index.php">← Back to Home</a>
      </div>

      <div class="authRight">
        <div class="authForm">
          <h3>Create Account</h3>
          <p class="authSubtitle">Fill in your details to get started</p>

          <?php if ($error === 'terms'): ?>
            <p style="color:#ff5a5a; margin-bottom:12px;">You must agree to the Terms of Service to register.</p>
          <?php elseif ($error === 'email'): ?>
            <p style="color:#ff5a5a; margin-bottom:12px;">That email is already registered. Try logging in instead.</p>
          <?php elseif ($error === 'password'): ?>
            <p style="color:#ff5a5a; margin-bottom:12px;">Passwords do not match.</p>
          <?php endif; ?>

          <form action="actions/register-action.php" method="POST">
            <div class="formRow">
              <div class="formGroup">
                <label class="formLabel">First Name</label>
                <input class="formInput" type="text" name="first_name" placeholder="Juan" required />
              </div>
              <div class="formGroup">
                <label class="formLabel">Last Name</label>
                <input class="formInput" type="text" name="last_name" placeholder="Dela Cruz" required />
              </div>
            </div>

            <div class="formGroup">
              <label class="formLabel">Email Address</label>
              <div class="inputIconWrap">
                <input class="formInput" type="email" name="email" placeholder="you@email.com" required />
              </div>
            </div>

            <div class="formGroup">
              <label class="formLabel">Phone Number</label>
              <div class="inputIconWrap">
                <input class="formInput" type="tel" name="phone" placeholder="09XX-XXX-XXXX" required />
              </div>
            </div>

            <div class="formGroup">
              <label class="formLabel">Date of Birth</label>
              <input class="formInput" type="date" name="date_of_birth" required />
            </div>

            <div class="formRow">
              <div class="formGroup">
                <label class="formLabel">Password</label>
                <input class="formInput" type="password" name="password" placeholder="Min. 8 characters" minlength="8" required />
              </div>
              <div class="formGroup">
                <label class="formLabel">Confirm Password</label>
                <input class="formInput" type="password" name="confirm_password" placeholder="Repeat password" minlength="8" required />
              </div>
            </div>

            <!-- Terms & Agreement - required before an account can be created -->
            <label class="checkboxLabel checkboxTerms">
              <input type="checkbox" name="agree_terms" value="1" required />
              I agree to the
              <a href="terms.php" target="_blank">Terms of Service</a>
              and
              <a href="terms.php" target="_blank">Privacy Policy</a>
            </label>

            <button class="btnFull" type="submit">Create My Account →</button>
          </form>

          <div class="authSwitch">
            Already have an account?
            <a href="login.php">Sign in</a>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
