<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelamar') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Pelamar - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-user"></i><span>Pelamar SIRA</span></div>
        <div class="nav-links"><a href="../lowongan.php">Lowongan</a><a href="#">Lamaran Saya</a></div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login">Logout</a></div>
    </nav>
    <div style="padding: 2rem 5%;">
        <h2>Dashboard <?= $_SESSION['nama'] ?></h2>
        <p>Silakan cari dan lamar pekerjaan di RS Ar-Rasyid.</p><a href="../lowongan.php" class="btn btn-hero btn-explore">Lihat Lowongan Tersedia</a>
    </div>
</body>

</html>