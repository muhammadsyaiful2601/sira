<?php
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_lowongan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lowongan"))['total'];
$total_lamaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran"))['total'];
$lamaran_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='pending'"))['total'];
$lowongan_terbaru = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY tanggal_posting DESC LIMIT 5");
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
            border-left: 4px solid #0a6b9e;
        }

        .stat-card i {
            font-size: 2rem;
            color: #0a6b9e;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            padding: 1rem;
            overflow-x: auto;
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

        th {
            background: #f4f7fc;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 20px;
            text-decoration: none;
        }

        .btn-edit {
            background: #eef2f7;
            color: #0a6b9e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #c0392b;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-user-shield"></i><span>Admin SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="lowongan.php" class="nav-link">Kelola Lowongan</a>
            <a href="lamaran.php" class="nav-link">Lamaran Masuk</a>
            <a href="pengguna.php" class="nav-link">Pengguna</a>
        </div>
        <div class="nav-buttons"><a href="../logout.php" class="btn btn-login"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </nav>
    <div style="padding: 2rem 5%;">
        <h2>Halo, <?= htmlspecialchars($_SESSION['nama']) ?> (Admin)</h2>
        <div class="dashboard-stats">
            <div class="stat-card"><i class="fas fa-users"></i>
                <div class="number"><?= $total_users ?></div>
                <div class="label">Total Pengguna</div>
            </div>
            <div class="stat-card"><i class="fas fa-briefcase"></i>
                <div class="number"><?= $total_lowongan ?></div>
                <div class="label">Total Lowongan</div>
            </div>
            <div class="stat-card"><i class="fas fa-file-alt"></i>
                <div class="number"><?= $total_lamaran ?></div>
                <div class="label">Total Lamaran</div>
            </div>
            <div class="stat-card"><i class="fas fa-clock"></i>
                <div class="number"><?= $lamaran_pending ?></div>
                <div class="label">Lamaran Pending</div>
            </div>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Lowongan Terbaru</h3><a href="lowongan.php" class="btn btn-hero btn-explore" style="padding:0.3rem 1rem;">+ Tambah Lowongan</a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Posting</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody><?php while ($row = mysqli_fetch_assoc($lowongan_terbaru)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td><?= substr(htmlspecialchars($row['deskripsi']), 0, 50) ?>...</td>
                            <td><?= $row['tanggal_posting'] ?></td>
                            <td><?= $row['status'] == 'buka' ? '<span style="color:#2e7d64;">Buka</span>' : '<span style="color:#c0392b;">Tutup</span>' ?></td>
                            <td><a href="edit-lowongan.php?id=<?= $row['id'] ?>" class="btn-sm btn-edit">Edit</a> <a href="hapus-lowongan.php?id=<?= $row['id'] ?>" class="btn-sm btn-delete" onclick="return confirm('Yakin hapus?')">Hapus</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>