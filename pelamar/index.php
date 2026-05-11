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

// Tentukan pesan berdasarkan status & badge
$status_message = '';
$jadwal_info = '';
$status_badge = '';
if ($lamaran_data) {
    $status = $lamaran_data['status_lamaran'];
    switch ($status) {
        case 'pending':
            $status_message = 'Lamaran Anda sedang diverifikasi oleh admin. Harap tunggu.';
            $status_badge = 'pending';
            break;
        case 'verifikasi':
            $status_message = 'Dokumen Anda dinyatakan lengkap. Admin akan segera menentukan jadwal interview.';
            $status_badge = 'verifikasi';
            break;
        case 'interview':
            $jadwal = date('d F Y H:i', strtotime($lamaran_data['jadwal_interview']));
            $status_message = "Selamat! Anda dijadwalkan interview pada <strong>$jadwal</strong>. Siapkan diri Anda.";
            $jadwal_info = $jadwal;
            $status_badge = 'interview';
            break;
        case 'diterima':
            $status_message = 'SELAMAT! Lamaran Anda DITERIMA. Silakan cek email untuk informasi lebih lanjut.';
            $status_badge = 'diterima';
            break;
        case 'ditolak':
            $status_message = 'Maaf, lamaran Anda DITOLAK. Tetap semangat coba lowongan lainnya.';
            $status_badge = 'ditolak';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelamar - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/pelamar.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
    <script src="js/pelamar.js" defer></script>
</head>

<body>
    <div class="app-container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-briefcase"></i>
                <span>Dashboard Pelamar</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link active">Beranda</a>
                <a href="profil.php" class="nav-link">Profil Saya</a>
                <a href="../lowongan.php" class="nav-link"><i class="fas fa-search"></i> Cari Lowongan</a>
            </div>
            <div class="nav-buttons">
                <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <div class="welcome-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="welcome-text">
                        <h1>Selamat datang, <span class="highlight"><?= htmlspecialchars($profil['nama']) ?></span></h1>
                        <p>Kelola lamaran kerja Anda dan pantau status terbaru di sini.</p>
                    </div>
                    <div class="welcome-contact">
                        <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($profil['email']) ?></span>
                        <span><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($profil['no_hp']) ?></span>
                    </div>
                </div>

                <!-- Status Lamaran -->
                <?php if ($lamaran_data): ?>
                    <div class="status-card" data-status="<?= $status_badge ?>">
                        <div class="status-header">
                            <h3><i class="fas fa-paper-plane"></i> Status Lamaran Terakhir</h3>
                            <span class="status-badge status-<?= $status_badge ?>">
                                <?= ucfirst($status_badge) ?>
                            </span>
                        </div>
                        <div class="status-body">
                            <div class="job-info">
                                <i class="fas fa-briefcase"></i>
                                <strong><?= htmlspecialchars($lamaran_data['judul']) ?></strong>
                                <span class="apply-date">Dilamar: <?= date('d F Y', strtotime($lamaran_data['tanggal_lamaran'])) ?></span>
                            </div>
                            <div class="status-message">
                                <i class="fas fa-info-circle"></i>
                                <p><?= $status_message ?></p>
                            </div>
                            <?php if ($status == 'interview' && $jadwal_info): ?>
                                <div class="interview-schedule">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div class="schedule-detail">
                                        <strong>Jadwal Interview:</strong>
                                        <span id="interviewDate"><?= $jadwal_info ?> WIB</span>
                                        <button class="btn-copy" id="copyScheduleBtn" data-schedule="<?= $jadwal_info ?>">
                                            <i class="fas fa-copy"></i> Salin Jadwal
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($lamaran_data['catatan_admin'] && $status == 'ditolak'): ?>
                                <div class="admin-note">
                                    <i class="fas fa-comment-dots"></i>
                                    <div>
                                        <strong>Catatan Admin:</strong>
                                        <p><?= nl2br(htmlspecialchars($lamaran_data['catatan_admin'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="status-action">
                            <a href="../lowongan.php" class="btn btn-primary">
                                <i class="fas fa-search"></i> Lihat Lowongan Lain
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-card">
                        <i class="fas fa-file-alt"></i>
                        <h3>Belum Ada Lamaran</h3>
                        <p>Anda belum melamar pekerjaan. Mulailah dengan mencari lowongan yang sesuai.</p>
                        <a href="../lowongan.php" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lihat Lowongan
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Informasi Pendukung -->
                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-clock"></i>
                        <h4>Proses Lamaran</h4>
                        <p>Lamaran akan diproses dalam 3-5 hari kerja setelah dokumen lengkap.</p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-envelope-open-text"></i>
                        <h4>Notifikasi</h4>
                        <p>Pantau terus email Anda untuk pemberitahuan jadwal interview dan hasil seleksi.</p>
                    </div>
                    <div class="info-card">
                        <i class="fas fa-id-card"></i>
                        <h4>Kelengkapan Berkas</h4>
                        <p>Pastikan data diri dan dokumen pendukung sudah lengkap dan valid.</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <p>&copy; 2026 <span id="currentYear"></span> SIRA - Rumah Sakit Ar-Rasyid</p>
                <p class="footer-credit">Sistem Informasi Rekrutmen & Administrasi</p>
            </div>
        </footer>
    </div>
</body>

</html>