<?php
/*
  - actions/update-status-action.php
  - Admin updates the status of an appointment (pending/confirmed/completed/cancelled).
 */
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../appointments/index.php');
    exit;
}

$appointment_id = $_POST['appointment_id'] ?? '';
$status = $_POST['status'] ?? '';

$allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
if ($appointment_id === '' || !in_array($status, $allowed, true)) {
    header('Location: ../appointments/index.php');
    exit;
}

$stmt = $conn->prepare('UPDATE appointments SET status = ? WHERE appointment_id = ?');
$stmt->bind_param('si', $status, $appointment_id);
$stmt->execute();
$stmt->close();

header('Location: ../appointments/view.php?id=' . urlencode($appointment_id));
exit;
