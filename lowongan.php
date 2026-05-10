<?php
require_once 'config/koneksi.php';
$query = "SELECT * FROM lowongan WHERE status='buka' ORDER BY tanggal_posting DESC";
$lowongan = mysqli_query($conn, $query);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lowongan Kerja - SIRA RS Ar-Rasyid</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
    <!-- CSS khusus halaman lowongan -->
    <link rel="stylesheet" href="asset/css/lowongan.css">
    <link rel="icon" href="asset/image/icon/1.png" type="image/png">
</head>

<body class="lowongan-page">
    <nav class="navbar">
        <div class="logo"><i class="fas fa-briefcase"></i><span>SIRA - Lowongan</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="lowongan.php" class="nav-link active">Lowongan</a>
            <a href="login.php" class="nav-link">Login</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="job-list">
            <h2><i class="fas fa-bullhorn"></i> Lowongan Tersedia</h2>

            <?php if (mysqli_num_rows($lowongan) > 0): ?>
                <?php while ($job = mysqli_fetch_assoc($lowongan)): ?>
                    <div class="job-card">
                        <h3><?= htmlspecialchars($job['judul']) ?></h3>
                        <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($job['deskripsi'])) ?></p>
                        <p><strong>Kualifikasi:</strong> <?= nl2br(htmlspecialchars($job['kualifikasi'])) ?></p>
                        <a href="lamar.php?id=<?= $job['id'] ?>" class="btn-lamar">
                            <i class="fas fa-paper-plane"></i> Lamar Sekarang
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-message">
                    <i class="fas fa-folder-open"></i>
                    Maaf, lowongan saat ini tidak tersedia.
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>