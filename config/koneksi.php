<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'sira_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
