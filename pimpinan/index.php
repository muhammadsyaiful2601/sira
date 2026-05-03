<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

$total_pelamar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='pelamar'"))['total'];
$total_lowongan_buka = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lowongan WHERE status='buka'"))['total'];
$total_lamaran_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran"))['total'];
$lamaran_diterima = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='diterima'"))['total'];
$bulan_ini = date('m');
$tahun_ini = date('Y');
$lamaran_bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE MONTH(tanggal_lamaran)=$bulan_ini AND YEAR(tanggal_lamaran)=$tahun_ini"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pimpinan Dashboard - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
            border-bottom: 4px solid #0a6b9e;
        }

        .stat-card i {
            font-size: 2rem;
            color: #0a6b9e;
        }

        .number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        .report-box {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 1rem;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit {
            background: #eef2f7;
            color: #0a6b9e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #c0392b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eef2f8;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-chart-line"></i><span>Pimpinan SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links"><a href="index.php" class="nav-link">Dashboard</a><a href="laporan.php" class="nav-link">Laporan</a></div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
    <div style="padding:2rem 5%;">
        <h2>Selamat datang, Pimpinan <?= htmlspecialchars($_SESSION['nama']) ?></h2>
        <p>Pantau kinerja rekrutmen dan layanan RS Ar-Rasyid.</p>
        <div class="dashboard-stats">
            <div class="stat-card"><i class="fas fa-user-graduate"></i>
                <div class="number"><?= $total_pelamar ?></div>
                <div>Total Pelamar</div>
            </div>
            <div class="stat-card"><i class="fas fa-briefcase"></i>
                <div class="number"><?= $total_lowongan_buka ?></div>
                <div>Lowongan Aktif</div>
            </div>
            <div class="stat-card"><i class="fas fa-file-signature"></i>
                <div class="number"><?= $total_lamaran_masuk ?></div>
                <div>Lamaran Masuk</div>
            </div>
            <div class="stat-card"><i class="fas fa-check-circle"></i>
                <div class="number"><?= $lamaran_diterima ?></div>
                <div>Lamaran Diterima</div>
            </div>
        </div>
        <div class="report-box">
            <h3>📊 Rekap Bulan Ini</h3>
            <p>Lamaran masuk bulan <?= date('F Y') ?> : <strong><?= $lamaran_bulan_ini ?></strong> lamaran</p>
            <p>Rata-rata per hari: <strong><?= round($lamaran_bulan_ini / date('d'), 1) ?></strong></p>
            <hr>
            <p><i class="fas fa-chart-simple"></i> Sistem SIRA siap membantu monitoring.</p>
        </div>

        <!-- Fitur Kelola Lowongan untuk Pimpinan -->
        <div class="report-box" style="margin-top:2rem;">
            <h3><i class="fas fa-briefcase"></i> Kelola Lowongan</h3>
            <a href="tambah_lowongan.php" class="btn btn-hero btn-explore" style="display:inline-block; margin-bottom:1rem; padding:0.5rem 1rem;">+ Buka Lowongan Baru</a>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $lowongan_list = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY tanggal_posting DESC");
                        while ($l = mysqli_fetch_assoc($lowongan_list)): ?>
                            <tr>
                                <td><?= htmlspecialchars($l['judul']) ?></td>
                                <td><?= $l['status'] == 'buka' ? '<span style="color:green;">Buka</span>' : '<span style="color:red;">Tutup</span>' ?></td>
                                <td><?php if ($l['status'] == 'tutup'): ?><a href="ubah_status.php?id=<?= $l['id'] ?>&status=buka" class="btn-sm btn-edit">Buka</a><?php else: ?><a href="ubah_status.php?id=<?= $l['id'] ?>&status=tutup" class="btn-sm btn-delete">Tutup</a><?php endif; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>