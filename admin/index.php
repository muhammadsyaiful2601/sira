<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
// Statistik
$total_lamaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='pending'"))['total'];
$interview = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='interview'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-user-shield"></i>
            <span>Admin SIRA</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
            <a href="kelola_lowongan.php" class="nav-link">Kelola Lowongan</a>
        </div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn btn-login">Logout</a>
        </div>
    </nav>

    <div class="main-content">
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-file-alt"></i>
                <div class="number"><?= $total_lamaran ?></div>
                <div class="label">Total Lamaran</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <div class="number"><?= $pending ?></div>
                <div class="label">Pending Verifikasi</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <div class="number"><?= $interview ?></div>
                <div class="label">Menunggu Keputusan Pimpinan</div>
            </div>
        </div>
        <div>
            <a href="verifikasi_lamaran.php" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Proses Verifikasi & Jadwal Interview
            </a>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
    </footer>

    <script src="js/admin.js"></script>
</body>

</html>