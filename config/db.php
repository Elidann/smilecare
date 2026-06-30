<?php
/*
 - config/db.php
 - Laragon defaults: host=localhost, user=root, password=''
 */

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'smilecare';

// conn 
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    //  cancel everything if db not onnected
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
