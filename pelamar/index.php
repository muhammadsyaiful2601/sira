<?php
// Pastikan session dimulai dan koneksi database
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelamar') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil profil pelamar
$query_profil = "SELECT nama, email FROM users WHERE id = $user_id";
$result_profil = mysqli_query($conn, $query_profil);
if (!$result_profil) {
    die("Error query profil: " . mysqli_error($conn));
}
$profil = mysqli_fetch_assoc($result_profil);

// Ambil lowongan aktif
$query_lowongan = "SELECT * FROM lowongan WHERE status='buka' ORDER BY tanggal_posting DESC";
$lowongan_aktif = mysqli_query($conn, $query_lowongan);
if (!$lowongan_aktif) {
    die("Error query lowongan: " . mysqli_error($conn));
}

// Ambil riwayat lamaran pelamar
$query_riwayat = "SELECT l.*, low.judul FROM lamaran l 
                  JOIN lowongan low ON l.lowongan_id = low.id 
                  WHERE l.pelamar_id = $user_id 
                  ORDER BY l.tanggal_lamaran DESC";
$riwayat = mysqli_query($conn, $query_riwayat);
if (!$riwayat) {
    die("Error query riwayat: " . mysqli_error($conn));
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
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .avatar {
            background: #eef2f8;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #0a6b9e;
        }

        .job-card {
            background: white;
            border-radius: 20px;
            padding: 1.2rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        .job-card:hover {
            transform: translateY(-3px);
        }

        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-diterima {
            background: #d4edda;
            color: #155724;
        }

        .status-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-lamar {
            background: #0a6b9e;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 20px;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eef2f8;
        }

        th {
            background: #f4f7fc;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-user"></i>
            <span>Pelamar SIRA - RS Ar-Rasyid</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="lamaran-saya.php" class="nav-link">Lamaran Saya</a>
            <a href="profil.php" class="nav-link">Profil</a>
        </div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div style="padding: 2rem 5%;">
        <div class="profile-card">
            <div class="avatar"><i class="fas fa-user-circle"></i></div>
            <div>
                <h3><?= htmlspecialchars($profil['nama']) ?></h3>
                <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($profil['email']) ?></p>
            </div>
            <div style="margin-left: auto;">
                <a href="profil.php" class="btn btn-login"><i class="fas fa-edit"></i> Edit Profil</a>
            </div>
        </div>

        <div class="section-title" style="text-align: left;">
            <h3>🚀 Lowongan Tersedia</h3>
        </div>
        <?php if (mysqli_num_rows($lowongan_aktif) > 0): ?>
            <?php while ($job = mysqli_fetch_assoc($lowongan_aktif)): ?>
                <div class="job-card">
                    <h4><?= htmlspecialchars($job['judul']) ?></h4>
                    <p><?= nl2br(htmlspecialchars(substr($job['deskripsi'], 0, 150))) ?>...</p>
                    <p><strong>Kualifikasi:</strong> <?= nl2br(htmlspecialchars(substr($job['kualifikasi'], 0, 100))) ?>...</p>
                    <a href="../lamar.php?id=<?= $job['id'] ?>" class="btn-lamar"><i class="fas fa-paper-plane"></i> Lamar Sekarang</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada lowongan terbuka saat ini.</p>
        <?php endif; ?>

        <div class="section-title" style="text-align: left; margin-top: 2rem;">
            <h3>📋 Riwayat Lamaran</h3>
        </div>
        <?php if (mysqli_num_rows($riwayat) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Posisi</th>
                            <th>Tanggal Melamar</th>
                            <th>Status</th>
                            <th>CV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($riwayat)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= $row['tanggal_lamaran'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= $row['status_lamaran'] ?>">
                                        <?= ucfirst($row['status_lamaran']) ?>
                                    </span>
                                </td>
                                <td><?= $row['cv_file'] ? '<a href="../upload/cv/' . $row['cv_file'] . '" target="_blank">Lihat CV</a>' : '-' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Anda belum melamar pekerjaan apapun.</p>
        <?php endif; ?>
    </div>

    <footer class="footer" style="margin-top: 3rem;">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
    <script src="../asset/js/script.js"></script>
</body>

</html>