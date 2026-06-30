<?php
/*
  includes/auth-check.php
  always needed for every login 
 
  Usage- 
    require_once __DIR__ . '/../includes/auth-check.php';
    require_role('patient');   // or require_role('admin')
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /smilecare/login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        // Logged in, but wrong role = send them to their own dashboard
        if ($_SESSION['role'] === 'admin') {
            header('Location: /smilecare/adminDashboard.php');
        } else {
            header('Location: /smilecare/userDashboard.php');
        }
        exit;
    }
}
