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
    <title>Admin Dashboard - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #0a6b9e;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-user-shield"></i><span>Admin SIRA</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
            <a href="kelola_lowongan.php" class="nav-link">Kelola Lowongan</a>
        </div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login">Logout</a></div>
    </nav>
    <div style="padding:2rem 5%;">
        <h2>Halo, <?= $_SESSION['nama'] ?></h2>
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="number"><?= $total_lamaran ?></div>
                <div>Total Lamaran</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $pending ?></div>
                <div>Pending Verifikasi</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $interview ?></div>
                <div>Menunggu Keputusan Pimpinan</div>
            </div>
        </div>
        <div><a href="verifikasi_lamaran.php" class="btn btn-login">➡️ Proses Verifikasi & Jadwal Interview</a></div>
    </div>
    <footer class="footer">
        <p>&copy; 2026 SIRA</p>
    </footer>
</body>

</html>