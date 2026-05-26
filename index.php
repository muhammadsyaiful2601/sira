<?php
session_start();
require_once 'config/koneksi.php';

// Validasi ketersediaan role user
$adminExists = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1")) > 0;
$pimpinanExists = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role='pimpinan' LIMIT 1")) > 0;
$hideRegister = ($adminExists && $pimpinanExists);

// Flash message registrasi
$successMessage = '';
if (isset($_SESSION['registration_success'])) {
    $successMessage = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRA - Sistem Informasi Rumah Sakit Ar-Rasyid</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="asset/image/icon/1.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .alert-success i {
            font-size: 1.5rem;
            color: #28a745;
        }

        .alert-success .close-notif {
            margin-left: auto;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #155724;
            opacity: 0.7;
        }

        .alert-success .close-notif:hover {
            opacity: 1;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hospital"></i>
            <span>SIRA</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="#layanan" class="nav-link">Layanan</a>
            <a href="#kontak" class="nav-link">Kontak</a>
            <a href="lowongan.php" class="nav-link">Lowongan</a>
        </div>
        <div class="nav-buttons">
            <a href="login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php if (!$hideRegister): ?>
                <a href="daftar.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Daftar</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di <span>Sistem Informasi Rekrutmen Ar-Rasyid</span></h1>
            <p>Sistem rekrutmen dan informasi terpadu. Bergabunglah dengan tim medis profesional kami yang berdedikasi.</p>
            <div class="hero-buttons">
                <a href="lowongan.php" class="btn btn-hero btn-explore"><i class="fas fa-briefcase"></i> Lihat Lowongan</a>
            </div>
        </div>
    </section>

    <?php if ($successMessage): ?>
        <div class="container" style="max-width: 1200px; margin: 1rem auto 0 auto; padding: 0 20px;">
            <div class="alert-success" id="successAlert">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($successMessage) ?></span>
                <button class="close-notif" onclick="this.parentElement.style.display='none'">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat-item">
            <div class="stat-number">200+</div>
            <div class="stat-label">Tempat Tidur</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">50+</div>
            <div class="stat-label">Dokter Spesialis</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Layanan Darurat</div>
        </div>
    </section>

    <section class="features" id="layanan">
        <div class="section-title">
            <h2>Layanan Unggulan</h2>
            <p>Fasilitas kesehatan terbaik untuk masyarakat</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-ambulance"></i></div>
                <h3>IGD 24 Jam</h3>
                <p>Penanganan cepat dan tepat dengan tim gawat darurat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-user-md"></i></div>
                <h3>Poliklinik Spesialis</h3>
                <p>Lebih dari 15 spesialisasi dokter.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-flask"></i></div>
                <h3>Laboratorium Modern</h3>
                <p>Hasil cepat dan akurat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-pills"></i></div>
                <h3>Apotek 24 Jam</h3>
                <p>Obat lengkap dan resep online.</p>
            </div>
        </div>
    </section>

    <section class="rekrutmen-cta">
        <div class="rekrutmen-content">
            <h2>Kembangkan Karir di RS Ar-Rasyid</h2>
            <p>Lowongan untuk tenaga medis, keperawatan, dan administrasi. Daftarkan dirimu sekarang!</p>
            <a href="lowongan.php" class="btn btn-rekrutmen"><i class="fas fa-paper-plane"></i> Lihat Lowongan</a>
        </div>
    </section>

    <footer class="footer" id="kontak">
        <div class="footer-content">
            <div class="footer-section">
                <h4>SIRA</h4>
                <p>Sistem Informasi Rekrutmen Ar-Rasyid</p>
            </div>
            <div class="footer-section">
                <h4>Kontak</h4>
                <p><i class="fas fa-phone"></i> (021) 1234-5678</p>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Kesehatan No.45, Jakarta</p>
            </div>
            <div class="footer-section">
                <h4>Jam Besuk</h4>
                <p>Pagi: 10.00 - 12.00</p>
                <p>Sore: 16.00 - 18.00</p>
            </div>
        </div>
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid. All rights reserved.</p>
    </footer>

    <script src="asset/js/script.js"></script>
    <script>
        setTimeout(function() {
            var alert = document.getElementById('successAlert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 5000);
    </script>
</body>

</html>