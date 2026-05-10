<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelamar') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil profil pelamar
$profil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama, email, no_hp, alamat FROM users WHERE id = $user_id"));

// Ambil lamaran terbaru pelamar (beserta jadwal interview, status)
$lamaran = mysqli_query($conn, "SELECT l.*, low.judul FROM lamaran l 
                                JOIN lowongan low ON l.lowongan_id = low.id 
                                WHERE l.pelamar_id = $user_id 
                                ORDER BY l.tanggal_lamaran DESC LIMIT 1");
$lamaran_data = mysqli_fetch_assoc($lamaran);

// Tentukan pesan berdasarkan status
$status_message = '';
$jadwal_info = '';
if ($lamaran_data) {
    $status = $lamaran_data['status_lamaran'];
    switch ($status) {
        case 'pending':
            $status_message = '🕒 Lamaran Anda sedang diverifikasi oleh admin. Harap tunggu.';
            break;
        case 'verifikasi':
            $status_message = '✅ Dokumen Anda dinyatakan lengkap. Admin akan segera menentukan jadwal interview.';
            break;
        case 'interview':
            $jadwal = date('d F Y H:i', strtotime($lamaran_data['jadwal_interview']));
            $status_message = "📅 Selamat! Anda dijadwalkan interview pada <strong>$jadwal</strong>. Siapkan diri Anda.";
            $jadwal_info = $jadwal;
            break;
        case 'diterima':
            $status_message = '🎉 SELAMAT! Lamaran Anda DITERIMA. Silakan cek email untuk informasi lebih lanjut.';
            break;
        case 'ditolak':
            $status_message = '😞 Maaf, lamaran Anda DITOLAK. Tetap semangat coba lowongan lainnya.';
            break;
    }
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
    <style>
        .status-card {
            background: #f8f9fc;
            border-radius: 20px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 6px solid #0a6b9e;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-user"></i><span>Dashboard Pelamar</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="profil.php" class="nav-link">Profil</a>
        </div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
    <div style="padding: 2rem 5%;">
        <div class="info-card">
            <h3><i class="fas fa-user-circle"></i> Selamat datang, <?= htmlspecialchars($profil['nama']) ?></h3>
            <p><i class="fas fa-envelope"></i> <?= $profil['email'] ?> | <i class="fab fa-whatsapp"></i> <?= $profil['no_hp'] ?></p>
        </div>

        <?php if ($lamaran_data): ?>
            <div class="status-card">
                <h4><i class="fas fa-info-circle"></i> Status Lamaran Terakhir</h4>
                <p><?= $status_message ?></p>
                <?php if ($status == 'interview' && $jadwal_info): ?>
                    <p><i class="fas fa-calendar-alt"></i> <strong>Jadwal Interview:</strong> <?= $jadwal_info ?> WIB</p>
                <?php endif; ?>
                <?php if ($lamaran_data['catatan_admin'] && $status == 'ditolak'): ?>
                    <p><i class="fas fa-comment"></i> Catatan: <?= nl2br(htmlspecialchars($lamaran_data['catatan_admin'])) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Anda belum melamar pekerjaan. <a href="../lowongan.php">Lihat lowongan</a></p>
        <?php endif; ?>
    </div>
    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>