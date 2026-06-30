<?php
/*
  - logout.php
  - Destroys the session and sends the user back to the landing page.
 */
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit;
