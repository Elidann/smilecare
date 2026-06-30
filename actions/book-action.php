<?php
/*
    - actions/book-action.php
    - Handles POST from userDashboard.php booking form. handles post from userDashboard.php booking form
    - New appointments always start as 'pending' and admin confirm ltr 
    - consent_status starts as 'pending' too, since the consent form is always signed in person before treatment.
 */

// load auth function   
require_once __DIR__ . '/../includes/auth-check.php';
require_role('patient'); // ensure user patient 
// db connection 
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../userDashboard.php');
    exit;
}

$patient_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'] ?? '';
$dentist_id = $_POST['dentist_id'] ?? '';
$date       = $_POST['appointment_date'] ?? '';
$time       = $_POST['appointment_time'] ?? '';
$notes      = trim($_POST['notes'] ?? '');

if ($service_id === '' || $date === '' || $time === '') {
    header('Location: ../userDashboard.php?error=fields');
    exit;
}

if (strtotime($date) < strtotime(date('Y-m-d'))) {
    header('Location: ../userDashboard.php?error=past');
    exit;
}

$dentist_id_param = $dentist_id === '' ? null : (int)$dentist_id;

$stmt = $conn->prepare(
    'INSERT INTO appointments (patient_id, dentist_id, service_id, appointment_date, appointment_time, notes, status, consent_status)
     VALUES (?, ?, ?, ?, ?, ?, "pending", "pending")'
);
$stmt->bind_param('iiisss', $patient_id, $dentist_id_param, $service_id, $date, $time, $notes);
$stmt->execute();
$stmt->close();

header('Location: ../userDashboard.php');
exit;
