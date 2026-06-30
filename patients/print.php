<?php
/*
  - patients/print.php
  - Printable, generic consent form for a specific appointment. 
  - The admin opens this, prints it, and hands it to the patient to sign 
  -  admin goes back to appointments/view.php and clicks Mark as Signed
 */
require_once __DIR__ . '/../includes/auth-check.php';
require_role('admin');
require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: ../appointments/index.php');
    exit;
}

$stmt = $conn->prepare(
    "SELECT a.appointment_date, a.appointment_time, s.name AS service_name,
            u.first_name, u.last_name, u.date_of_birth
     FROM appointments a
     JOIN services s ON s.service_id = a.service_id
     JOIN users u ON u.user_id = a.patient_id
     WHERE a.appointment_id = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appt) {
    header('Location: ../appointments/index.php');
    exit;
}
?>
<html>
  <head>
    <title>Consent Form — SmileCare Dental Clinic</title>
    <style>
      body { font-family: 'Segoe UI', sans-serif; color: #1e2d34; max-width: 700px; margin: 40px auto; line-height: 1.6; }
      h1 { font-size: 1.4rem; margin-bottom: 4px; }
      .meta { color: #4a5e68; margin-bottom: 24px; }
      .field { margin-bottom: 14px; }
      .field strong { display: inline-block; width: 160px; }
      .signatureBlock { margin-top: 60px; display: flex; justify-content: space-between; }
      .signatureLine { border-top: 1px solid #1e2d34; width: 280px; text-align: center; padding-top: 6px; }
      @media print {
        button { display: none; }
      }
    </style>
  </head>
  <body>
    <h1>SmileCare Dental Clinic — Patient Consent Form</h1>
    <p class="meta">Appointment on <?php echo date('F j, Y', strtotime($appt['appointment_date'])); ?> at <?php echo date('g:i A', strtotime($appt['appointment_time'])); ?></p>

    <div class="field"><strong>Patient Name:</strong> <?php echo htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']); ?></div>
    <div class="field"><strong>Date of Birth:</strong> <?php echo $appt['date_of_birth'] ? date('F j, Y', strtotime($appt['date_of_birth'])) : '—'; ?></div>
    <div class="field"><strong>Procedure:</strong> <?php echo htmlspecialchars($appt['service_name']); ?></div>

    <p style="margin-top:24px;">
      I, the undersigned, confirm that the dental procedure listed above, along with its
      general nature, purpose, expected benefits, and possible risks, has been explained
      to me by clinic staff. I voluntarily consent to undergo this procedure at
      SmileCare Dental Clinic. I understand that I may ask questions at any time before
      treatment begins, and that I may withdraw consent prior to the start of the
      procedure.
    </p>

    <div class="signatureBlock">
      <div class="signatureLine">Patient / Guardian Signature</div>
      <div class="signatureLine">Date</div>
    </div>

    <button onclick="window.print()" style="margin-top:32px; padding:10px 20px; background:#00b8c4; color:white; border:none; border-radius:8px; cursor:pointer;">
       Print This Form
    </button>
  </body>
</html>
