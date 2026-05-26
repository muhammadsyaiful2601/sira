<?php
require_once '../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// PROSES TAMBAH LOWONGAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_lowongan'])) {
    $judul       = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $kualifikasi = mysqli_real_escape_string($conn, trim($_POST['kualifikasi']));
    $status      = $_POST['status'] == 'buka' ? 'buka' : 'tutup';

    if (empty($judul) || empty($deskripsi) || empty($kualifikasi)) {
        $error_lowongan = "Semua field harus diisi!";
    } else {
        $query = "INSERT INTO lowongan (judul, deskripsi, kualifikasi, status, tanggal_posting) 
                  VALUES ('$judul', '$deskripsi', '$kualifikasi', '$status', NOW())";
        if (mysqli_query($conn, $query)) {
            $success_lowongan = "Lowongan berhasil ditambahkan.";
        } else {
            $error_lowongan = "Gagal menambahkan lowongan: " . mysqli_error($conn);
        }
    }
}

// PROSES HAPUS LOWONGAN
if (isset($_GET['hapus_lowongan'])) {
    $id_lowongan = intval($_GET['hapus_lowongan']);
    mysqli_query($conn, "DELETE FROM lowongan WHERE id=$id_lowongan");
    $success_hapus = "Lowongan berhasil dihapus.";
    header("Location: index.php?success_hapus=" . urlencode($success_hapus));
    exit;
}

// Statistik utama
$total_lamaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='pending'"))['total'];
$interview = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lamaran WHERE status_lamaran='interview'"))['total'];

// Ambil 5 lamaran terbaru
$query_recent = "SELECT l.id, l.status_lamaran, l.tanggal_lamaran, u.nama as pelamar, low.judul as judul_lowongan 
                 FROM lamaran l 
                 JOIN users u ON l.pelamar_id = u.id 
                 JOIN lowongan low ON l.lowongan_id = low.id 
                 ORDER BY l.tanggal_lamaran DESC LIMIT 5";
$recent_lamaran = mysqli_query($conn, $query_recent);

// Daftar lowongan untuk ditampilkan
$lowongan_list = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY tanggal_posting DESC");

$info_hapus = '';
if (isset($_GET['success_hapus'])) {
    $info_hapus = '<div class="alert alert-success">' . htmlspecialchars($_GET['success_hapus']) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Dashboard - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
    <style>
        /* CSS tambahan untuk form lowongan modern */
        .lowongan-form-wrapper {
            background: #f8fafc;
            border-radius: 20px;
            padding: 1.8rem;
            margin-bottom: 2rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .form-lowongan-modern .form-grid-2cols {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.2rem;
        }

        .form-lowongan-modern .form-group {
            margin-bottom: 1.2rem;
        }

        .form-lowongan-modern label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.6rem;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .form-lowongan-modern label i {
            width: 1.5rem;
            color: #3b82f6;
        }

        .form-lowongan-modern input,
        .form-lowongan-modern select,
        .form-lowongan-modern textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            transition: all 0.2s;
            background: white;
        }

        .form-lowongan-modern input:focus,
        .form-lowongan-modern select:focus,
        .form-lowongan-modern textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-lowongan-modern .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5rem;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-hapus {
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-hapus:hover {
            background: #fecaca;
        }

        @media (max-width: 640px) {
            .form-lowongan-modern .form-grid-2cols {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-building-user"></i>
            <span>SIRA Admin</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link active">Dashboard</a>
            <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
            <a href="profile_admin.php" class="nav-link">Profil Saya</a>
        </div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="welcome-header">
            <div>
                <h1>Selamat datang, <span class="admin-name"><?= htmlspecialchars($_SESSION['nama']) ?></span></h1>
                <p class="greeting-sub">Kelola rekrutmen & verifikasi pelamar dengan mudah</p>
            </div>
            <div class="header-date">
                <i class="fas fa-calendar-alt"></i>
                <span><?= date('d F Y') ?></span>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="dashboard-stats">
            <div class="stat-card stat-card-total">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <span class="stat-number"><?= number_format($total_lamaran, 0, ',', '.') ?></span>
                    <span class="stat-label">Total Lamaran Masuk</span>
                </div>
            </div>
            <div class="stat-card stat-card-pending">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <span class="stat-number"><?= number_format($pending, 0, ',', '.') ?></span>
                    <span class="stat-label">Pending Verifikasi</span>
                </div>
            </div>
            <div class="stat-card stat-card-interview">
                <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                <div class="stat-info">
                    <span class="stat-number"><?= number_format($interview, 0, ',', '.') ?></span>
                    <span class="stat-label">Menunggu Keputusan Pimpinan</span>
                </div>
            </div>
        </div>

        <!-- Tabel Lamaran Terbaru -->
        <div class="action-section">
            <div class="section-header">
                <h2><i class="fas fa-receipt"></i> Lamaran Terbaru</h2>
            </div>
            <div class="recent-table-wrapper">
                <?php if (mysqli_num_rows($recent_lamaran) > 0): ?>
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Pelamar</th>
                                <th>Posisi</th>
                                <th>Tgl. Lamar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($recent_lamaran)):
                                $status_class = '';
                                $status_text = '';
                                switch ($row['status_lamaran']) {
                                    case 'pending':
                                        $status_class = 'badge-pending';
                                        $status_text = 'Pending';
                                        break;
                                    case 'interview':
                                        $status_class = 'badge-interview';
                                        $status_text = 'Interview';
                                        break;
                                    default:
                                        $status_class = 'badge-default';
                                        $status_text = ucfirst($row['status_lamaran']);
                                }
                            ?>
                                <tr>
                                    <td data-label="Pelamar"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($row['pelamar']) ?></td>
                                    <td data-label="Posisi"><?= htmlspecialchars($row['judul_lowongan']) ?></td>
                                    <td data-label="Tgl. Lamar"><?= date('d/m/Y', strtotime($row['tanggal_lamaran'])) ?></td>
                                    <td data-label="Status"><span class="badge <?= $status_class ?>"><?= $status_text ?></span></td>
                                    <td data-label="Aksi"><a href="verifikasi_lamaran.php?detail=<?= $row['id'] ?>" class="btn-sm">Proses</a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state"><i class="fas fa-inbox"></i>
                        <p>Belum ada lamaran masuk</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="quick-links">
                <a href="verifikasi_lamaran.php" class="quick-link-card"><i class="fas fa-user-check"></i><span>Verifikasi Pelamar</span><i class="fas fa-arrow-right"></i></a>
                <a href="jadwal_interview.php" class="quick-link-card"><i class="fas fa-calendar-week"></i><span>Jadwal Interview</span></a>
            </div>
        </div>

        <!-- ========== BAGIAN KELOLA LOWONGAN (TAMBAH + HAPUS) ========== -->
        <div class="card-section" style="margin-top: 2rem;">
            <div class="section-header">
                <h2><i class="fas fa-briefcase"></i> Kelola Lowongan</h2>
                <button id="toggleLowonganFormBtn" class="btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Tambah Lowongan
                </button>
            </div>

            <?php if (isset($error_lowongan)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error_lowongan) ?></div>
            <?php elseif (isset($success_lowongan)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_lowongan) ?></div>
            <?php endif; ?>
            <?= $info_hapus ?>

            <div id="lowonganFormContainer" class="lowongan-form-wrapper" style="display: none;">
                <form method="POST" class="form-lowongan-modern">
                    <div class="form-grid-2cols">
                        <div class="form-group">
                            <label for="judul"><i class="fas fa-tag"></i> Judul Lowongan</label>
                            <input type="text" id="judul" name="judul" placeholder="Contoh: Administrasi Rumah Sakit" required>
                        </div>
                        <div class="form-group">
                            <label for="status"><i class="fas fa-toggle-on"></i> Status</label>
                            <select id="status" name="status">
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi Pekerjaan</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tugas, tanggung jawab, jam kerja, dll." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="kualifikasi"><i class="fas fa-graduation-cap"></i> Kualifikasi</label>
                        <textarea id="kualifikasi" name="kualifikasi" rows="3" placeholder="Pendidikan minimal, pengalaman, skill yang dibutuhkan" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="tambah_lowongan" class="btn-primary">
                            <i class="fas fa-save"></i> Simpan Lowongan
                        </button>
                        <button type="button" id="cancelLowonganBtn" class="btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </form>
            </div>

            <?php if (mysqli_num_rows($lowongan_list) == 0): ?>
                <div class="empty-state">Belum ada lowongan. Klik "Tambah Lowongan".</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="recent-table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Kualifikasi</th>
                                <th>Status</th>
                                <th>Tgl Posting</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($low = mysqli_fetch_assoc($lowongan_list)): ?>
                                <tr>
                                    <td data-label="Judul"><strong><?= htmlspecialchars($low['judul']) ?></strong></td>
                                    <td data-label="Deskripsi"><?= htmlspecialchars(substr($low['deskripsi'], 0, 60)) ?>...</td>
                                    <td data-label="Kualifikasi"><?= htmlspecialchars(substr($low['kualifikasi'], 0, 50)) ?>...</td>
                                    <td data-label="Status"><span class="badge <?= $low['status'] == 'buka' ? 'badge-open' : 'badge-closed' ?>"><?= ucfirst($low['status']) ?></span></td>
                                    <td data-label="Tgl Posting"><?= date('d/m/Y', strtotime($low['tanggal_posting'])) ?></td>
                                    <td data-label="Aksi">
                                        <a href="?hapus_lowongan=<?= $low['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus lowongan ini? Semua lamaran terkait akan terhapus.')">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
    </footer>

    <script>
        const toggleBtn = document.getElementById('toggleLowonganFormBtn');
        const formContainer = document.getElementById('lowonganFormContainer');
        const cancelBtn = document.getElementById('cancelLowonganBtn');

        function showForm() {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-times-circle"></i> Tutup Form';
            formContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function hideForm() {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Lowongan';
            // Reset form
            const form = document.querySelector('.form-lowongan-modern');
            if (form) form.reset();
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                    showForm();
                } else {
                    hideForm();
                }
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', hideForm);
        }
    </script>
</body>

</html>