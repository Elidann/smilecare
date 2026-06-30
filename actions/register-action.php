<?php
/*
  - actions/register-action.php
  - Handles POST from register.php. Creates a new patient account.
    - re-check the terms checkbox and passwords here
  
 */
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

// terms and agreement must be accepter 
if (!isset($_POST['agree_terms']) || $_POST['agree_terms'] !== '1') {
    header('Location: ../register.php?error=terms');
    exit;
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$dob        = $_POST['date_of_birth'] ?? '';
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['confirm_password'] ?? '';

//  validation 
if ($first_name === '' || $last_name === '' || $email === '' || $phone === '' || $dob === '' || $password === '') {
    header('Location: ../register.php?error=fields');
    exit;
}

if ($password !== $confirm) {
    header('Location: ../register.php?error=password');
    exit;
}

if (strlen($password) < 8) {
    header('Location: ../register.php?error=password');
    exit;
}

// check for dpli email
$stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    header('Location: ../register.php?error=email');
    exit;
}
$stmt->close();

// create account 
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    'INSERT INTO users (first_name, last_name, email, phone, date_of_birth, password_hash, role, terms_accepted_at)
     VALUES (?, ?, ?, ?, ?, ?, "patient", NOW())'
);
$stmt->bind_param('ssssss', $first_name, $last_name, $email, $phone, $dob, $password_hash);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $stmt->close();

    // rekta login 
    $_SESSION['user_id']    = $user_id;
    $_SESSION['role']       = 'patient';
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name']  = $last_name;

    header('Location: ../userDashboard.php');
    exit;
} else {
    $stmt->close();
    header('Location: ../register.php?error=fields');
    exit;
}
