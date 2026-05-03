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
    <style>
        .job-list {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .job-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .job-card h3 {
            color: #1e3a5f;
            margin-top: 0;
        }

        .btn-lamar {
            background: #0a6b9e;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            border: none;
            cursor: pointer;
        }

        .btn-lamar:hover {
            background: #0a5a85;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-briefcase"></i><span>SIRA - Lowongan</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="lowongan.php" class="nav-link active">Lowongan</a>
            <a href="login.php" class="nav-link">Login</a>
        </div>
    </nav>

    <div class="job-list">
        <h2><i class="fas fa-bullhorn"></i> Lowongan Tersedia</h2>
        <?php if (mysqli_num_rows($lowongan) > 0): ?>
            <?php while ($job = mysqli_fetch_assoc($lowongan)): ?>
                <div class="job-card">
                    <h3><?= htmlspecialchars($job['judul']) ?></h3>
                    <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($job['deskripsi'])) ?></p>
                    <p><strong>Kualifikasi:</strong> <?= nl2br(htmlspecialchars($job['kualifikasi'])) ?></p>
                    <a href="lamar.php?id=<?= $job['id'] ?>" class="btn-lamar"><i class="fas fa-paper-plane"></i> Lamar Sekarang</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada lowongan terbuka saat ini. Silakan cek kembali nanti.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>