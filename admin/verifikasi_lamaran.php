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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Lamaran - Admin SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/verifikasi.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
</head>

<body>
    <div class="app-wrapper">
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
                <span>Admin SIRA</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
                <a href="profile_admin.php" class="nav-link">Profil Saya</a>
            </div>
            <div class="nav-buttons">
                <a href="../logout.php" class="btn btn-login">Logout</a>
            </div>
        </nav>

        <main class="main-content">
            <div class="page-header">
                <h3><i class="fas fa-file-alt"></i> Daftar Lamaran Masuk</h3>
                <div class="filter-controls">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama atau lowongan...">
                    </div>
                    <div class="filter-box">
                        <label><i class="fas fa-filter"></i> Status:</label>
                        <select id="statusFilter">
                            <option value="all">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="verifikasi">Verifikasi</option>
                            <option value="interview">Interview</option>
                            <option value="diterima">Diterima</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="data-table" id="lamaranTable">
                    <thead>
                        <tr>
                            <th>Nama Pelamar</th>
                            <th>Lowongan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($lamaran)): ?>
                            <tr data-status="<?= htmlspecialchars($row['status_lamaran']) ?>">
                                <td class="nama-pelamar">
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                    <small><?= htmlspecialchars($row['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><span class="status-badge <?= $row['status_lamaran'] ?>"><?= strtoupper($row['status_lamaran']) ?></span></td>
                                <td class="aksi">
                                    <a href="detail_lamaran.php?id=<?= $row['id'] ?>" class="btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Detail & Proses
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
        </footer>
    </div>
    <script src="js/verifikasi.js"></script>
</body>

</html>