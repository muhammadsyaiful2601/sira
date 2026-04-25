<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pimpinan') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Pimpinan - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-chart-line"></i><span>Pimpinan SIRA</span></div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login">Logout</a></div>
    </nav>
    <div style="padding: 2rem 5%;">
        <h2>Selamat datang, Pimpinan <?= $_SESSION['nama'] ?></h2>
        <p>Pantau rekrutmen, laporan, dan kinerja RS Ar-Rasyid.</p>
        <div class="stats" style="justify-content:flex-start;">
            <div class="stat-item">
                <div class="stat-number"><?= mysqli_num_rows(mysqli_query($conn, "SELECT id FROM lowongan")) ?></div>
                <div class="stat-label">Lowongan Aktif</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= mysqli_num_rows(mysqli_query($conn, "SELECT id FROM lamaran")) ?></div>
                <div class="stat-label">Total Lamaran</div>
            </div>
        </div>
    </div>
</body>

</html>