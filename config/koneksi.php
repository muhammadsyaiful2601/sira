<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sira_db';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
