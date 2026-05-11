<?php
require_once '../config/koneksi.php';
if ($_SESSION['role'] !== 'admin') die("Akses ditolak");

// Ambil semua lamaran dengan info pelamar & lowongan
$query = "SELECT l.*, u.nama, u.email, u.no_hp, u.alamat, low.judul 
          FROM lamaran l 
          JOIN users u ON l.pelamar_id = u.id 
          JOIN lowongan low ON l.lowongan_id = low.id 
          ORDER BY FIELD(l.status_lamaran, 'pending', 'verifikasi', 'interview', 'diterima', 'ditolak'), l.tanggal_lamaran DESC";
$lamaran = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Verifikasi Lamaran</title>
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .btn-sm {
            padding: 5px 10px;
            border-radius: 20px;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }

        .btn-primary {
            background: #0a6b9e;
            color: white;
        }

        .btn-warning {
            background: #ffc107;
            color: black;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 50px;
            font-size: 12px;
        }

        .pending {
            background: #ffc107;
        }

        .verifikasi {
            background: #17a2b8;
            color: white;
        }

        .interview {
            background: #28a745;
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">Verifikasi Lamaran</div><a href="index.php" style="color:white;">Kembali</a>
    </nav>
    <div style="padding:2rem;">
        <h3>Daftar Lamaran Masuk</h3>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Lowongan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($lamaran)): ?>
                    <tr>
                        <td><?= $row['nama'] ?><br><small><?= $row['email'] ?></small></td>
                        <td><?= $row['judul'] ?></td>
                        <td><span class="status-badge <?= $row['status_lamaran'] ?>"><?= strtoupper($row['status_lamaran']) ?></span></td>
                        <td>
                            <a href="detail_lamaran.php?id=<?= $row['id'] ?>" class="btn-sm btn-primary">Detail & Proses</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>