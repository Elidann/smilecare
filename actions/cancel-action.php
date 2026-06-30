<?php
/*
    - actions/cancel-action.php
    - Patient cancels one of their own appointments.
    - Ownership is checked (patient_id = session user) so a patient can never cancel someone else's appointment by guessing an ID.
     
 */

    // AUTH purpose 
require_once __DIR__ . '/../includes/auth-check.php';
require_role('patient'); // check role  
//db
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../userDashboard.php');
    exit;
}

$appointment_id = $_POST['appointment_id'] ?? '';
$patient_id = $_SESSION['user_id'];

if ($appointment_id === '') {
    header('Location: ../userDashboard.php');
    exit;
}

$stmt = $conn->prepare(
    "UPDATE appointments SET status = 'cancelled'
     WHERE appointment_id = ? AND patient_id = ?"
);
$stmt->bind_param('ii', $appointment_id, $patient_id);
$stmt->execute();
$stmt->close();

header('Location: ../userDashboard.php');
exit;
