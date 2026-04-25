<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-user-shield"></i><span>Admin SIRA</span></div>
        <div class="nav-links"><a href="index.php">Dashboard</a><a href="#">Kelola Lowongan</a><a href="#">Data Pelamar</a></div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
    <div style="padding: 2rem 5%;">
        <h2>Halo, <?= $_SESSION['nama'] ?> (Admin)</h2>
        <p>Selamat datang di panel admin SIRA. Kelola lowongan, lamaran, dan data pengguna.</p>
        <p style="margin-top:1rem;"><i class="fas fa-check-circle" style="color:#0a6b9e;"></i> Sistem siap dikembangkan lebih lanjut sesuai kebutuhan.</p>
    </div>
</body>

</html>