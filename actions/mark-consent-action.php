<?php
/*
    - actions/mark-consent-action.php
  - Admin clicks this after the patient has physically signed the printed
  - consent form at the clinic. Records the consent as signed with a timestamp.
 */

// requiremnts
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../appointments/index.php');
    exit;
}

$appointment_id = $_POST['appointment_id'] ?? '';
if ($appointment_id === '') {
    header('Location: ../appointments/index.php');
    exit;
}

$stmt = $conn->prepare(
    "UPDATE appointments SET consent_status = 'signed', consent_signed_at = NOW() WHERE appointment_id = ?"
);
$stmt->bind_param('i', $appointment_id);
$stmt->execute();
$stmt->close();

header('Location: ../appointments/view.php?id=' . urlencode($appointment_id));
exit;
