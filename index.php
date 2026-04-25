<?php
require_once 'config/koneksi.php';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRA - Sistem Informasi Rumah Sakit Ar-Rasyid</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-hospital"></i>
            <span>SIRA - RS Ar-Rasyid</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="#layanan" class="nav-link">Layanan</a>
            <a href="lowongan.php" class="nav-link">Lowongan</a>
            <a href="#kontak" class="nav-link">Kontak</a>
        </div>
        <div class="nav-buttons">
            <a href="login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="daftar.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Daftar</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang di <span>Sistem Informasi RS Ar-Rasyid</span></h1>
            <p>Sistem rekrutmen dan informasi terpadu. Bergabunglah dengan tim medis profesional kami yang berdedikasi.</p>
            <div class="hero-buttons">
                <a href="lowongan.php" class="btn btn-hero btn-explore"><i class="fas fa-briefcase"></i> Lihat Lowongan</a>
                <a href="daftar.php" class="btn btn-hero btn-learn"><i class="fas fa-user-graduate"></i> Daftar Pelamar</a>
            </div>
        </div>
    </section>

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
            <a href="daftar.php" class="btn btn-rekrutmen"><i class="fas fa-paper-plane"></i> Daftar Sekarang</a>
        </div>
    </section>

    <footer class="footer" id="kontak">
        <div class="footer-content">
            <div class="footer-section">
                <h4>SIRA - RS Ar-Rasyid</h4>
                <p>Sistem Informasi Rekrutmen & Pelayanan</p>
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
</body>

</html>