<?php
/*
  - actions/login-action.php
  - Handles POST from login.php. Verifies credentials and starts a session.
  - Works for both patients and admin since they share the same users table.
 */
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../login.php?error=1');
    exit;
}

$stmt = $conn->prepare('SELECT user_id, first_name, last_name, password_hash, role FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password_hash'])) {
    header('Location: ../login.php?error=1');
    exit;
}

// Credentials are good = start the session.
$_SESSION['user_id']    = $user['user_id'];
$_SESSION['role']       = $user['role'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name']  = $user['last_name'];

if ($user['role'] === 'admin') {
    header('Location: ../adminDashboard.php');
} else {
    header('Location: ../userDashboard.php');
}
exit;
