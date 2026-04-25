<?php
require_once 'config/koneksi.php';
$lowongan = mysqli_query($conn, "SELECT * FROM lowongan WHERE status='buka' ORDER BY tanggal_posting DESC");
?>
<!doctype html>
<html lang="id">

<head>
    <title>Lowongan - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital"></i><span>SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links"><a href="index.php">Beranda</a><a href="lowongan.php" class="nav-link">Lowongan</a></div>
        <div class="nav-buttons"><a href="login.php" class="btn btn-login">Login</a><a href="daftar.php" class="btn btn-register">Daftar</a></div>
    </nav>
    <div style="max-width: 1000px; margin: 2rem auto; padding: 0 1rem;">
        <h2 style="color:#1e3a5f;">Lowongan Tersedia</h2><?php if (mysqli_num_rows($lowongan) == 0): ?><p>Belum ada lowongan aktif.</p><?php else: ?><?php while ($row = mysqli_fetch_assoc($lowongan)): ?><div style="background:white; border-radius:20px; padding:1.5rem; margin-bottom:1rem; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                <h3><?= $row['judul'] ?></h3>
                <p><?= nl2br($row['deskripsi']) ?></p>
                <p><strong>Kualifikasi:</strong> <?= nl2br($row['kualifikasi']) ?></p><?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'pelamar'): ?><a href="#" class="btn btn-hero btn-explore" style="padding:0.5rem 1rem;">Lamar Sekarang</a><?php else: ?><a href="login.php" class="btn btn-login">Login untuk melamar</a><?php endif; ?>
            </div><?php endwhile; ?><?php endif; ?>
    </div>
</body>

</html>