<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_lamaran = intval($_GET['id'] ?? 0);
if (!$id_lamaran) {
    header("Location: verifikasi_lamaran.php");
    exit;
}

// Ambil data lamaran
$query = "SELECT l.*, u.nama, u.email, u.no_hp, u.alamat, low.judul, low.deskripsi, low.kualifikasi 
          FROM lamaran l 
          JOIN users u ON l.pelamar_id = u.id 
          JOIN lowongan low ON l.lowongan_id = low.id 
          WHERE l.id = $id_lamaran";
$result = mysqli_query($conn, $query);
$lamaran = mysqli_fetch_assoc($result);
if (!$lamaran) {
    die("Data lamaran tidak ditemukan.");
}

// Proses form
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $catatan = mysqli_real_escape_string($conn, trim($_POST['catatan_admin'] ?? ''));

    if ($action === 'terima') {
        $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal_interview']);
        if (empty($jadwal)) {
            $error = "Jadwal interview harus diisi.";
        } else {
            $update = "UPDATE lamaran SET 
                        status_lamaran = 'interview',
                        jadwal_interview = '$jadwal',
                        catatan_admin = CONCAT(IFNULL(catatan_admin,''), ' | Verifikasi: $catatan')
                       WHERE id = $id_lamaran";
            if (mysqli_query($conn, $update)) {
                $message = "Lamaran diverifikasi dan jadwal interview telah ditentukan. Status berubah menjadi INTERVIEW.";
                // Refresh data
                $lamaran = mysqli_fetch_assoc(mysqli_query($conn, $query));
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($conn);
            }
        }
    } elseif ($action === 'tolak') {
        $update = "UPDATE lamaran SET 
                    status_lamaran = 'ditolak',
                    catatan_admin = CONCAT(IFNULL(catatan_admin,''), ' | Ditolak: $catatan')
                   WHERE id = $id_lamaran";
        if (mysqli_query($conn, $update)) {
            $message = "Lamaran telah ditolak. Pelamar akan diberi notifikasi.";
            $lamaran = mysqli_fetch_assoc(mysqli_query($conn, $query));
        } else {
            $error = "Gagal menolak: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lamaran - Admin SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/detail.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
</head>

<body>
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

    <div class="main-content">
        <div class="page-header">
            <a href="verifikasi_lamaran.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
            <h2><i class="fas fa-file-alt"></i> Detail Lamaran</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="detail-grid">
            <!-- Kartu Data Pelamar -->
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <h3>Data Pelamar</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Nama Lengkap</span>
                        <span class="info-value"><?= htmlspecialchars($lamaran['nama']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?= htmlspecialchars($lamaran['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. HP</span>
                        <span class="info-value"><?= htmlspecialchars($lamaran['no_hp']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?= nl2br(htmlspecialchars($lamaran['alamat'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Kartu Lowongan -->
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-briefcase"></i>
                    <h3>Lowongan yang Dilamar</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Judul Lowongan</span>
                        <span class="info-value"><?= htmlspecialchars($lamaran['judul']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Deskripsi</span>
                        <span class="info-value"><?= nl2br(htmlspecialchars($lamaran['deskripsi'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kualifikasi</span>
                        <span class="info-value"><?= nl2br(htmlspecialchars($lamaran['kualifikasi'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Kartu Berkas -->
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-paperclip"></i>
                    <h3>Berkas Lamaran</h3>
                </div>
                <div class="card-body">
                    <?php if ($lamaran['cv_file'] && file_exists("../upload/cv/" . $lamaran['cv_file'])): ?>
                        <a href="../upload/cv/<?= urlencode($lamaran['cv_file']) ?>" target="_blank" class="btn-file">
                            <i class="fas fa-file-pdf"></i> Lihat CV Pelamar
                        </a>
                    <?php else: ?>
                        <p class="text-muted"><i class="fas fa-exclamation-circle"></i> Berkas CV tidak tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Kartu Status -->
            <div class="detail-card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i>
                    <h3>Status Lamaran</h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <span class="info-label">Status Saat Ini</span>
                        <span class="status-badge <?= $lamaran['status_lamaran'] ?>"><?= strtoupper($lamaran['status_lamaran']) ?></span>
                    </div>
                    <?php if ($lamaran['jadwal_interview']): ?>
                        <div class="info-row">
                            <span class="info-label">Jadwal Interview</span>
                            <span class="info-value"><i class="fas fa-calendar-alt"></i> <?= date('d F Y H:i', strtotime($lamaran['jadwal_interview'])) ?> WIB</span>
                        </div>
                    <?php endif; ?>
                    <?php if ($lamaran['catatan_admin']): ?>
                        <div class="info-row">
                            <span class="info-label">Catatan Admin</span>
                            <span class="info-value note-text"><?= nl2br(htmlspecialchars($lamaran['catatan_admin'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form Aksi (hanya jika status pending atau verifikasi) -->
        <?php if (in_array($lamaran['status_lamaran'], ['pending', 'verifikasi'])): ?>
            <div class="action-card">
                <div class="card-header">
                    <i class="fas fa-check-double"></i>
                    <h3>Proses Lamaran</h3>
                </div>
                <div class="card-body">
                    <form method="POST" class="action-form" id="formProsesLamaran">
                        <div class="form-group">
                            <label for="catatan_admin">Catatan Admin <span class="optional">(opsional)</span></label>
                            <textarea name="catatan_admin" id="catatan_admin" rows="3" class="form-control" placeholder="Misal: Dokumen lengkap, silakan ikuti jadwal interview..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="jadwal_interview">Jadwal Interview <span class="required">*</span></label>
                            <input type="datetime-local" name="jadwal_interview" id="jadwal_interview" class="form-control">
                            <small><i class="fas fa-clock"></i> Waktu dalam zona WIB (sesuaikan dengan server).</small>
                        </div>

                        <div class="button-group">
                            <button type="submit" name="action" value="terima" class="btn btn-primary" id="btnTerima">
                                <i class="fas fa-calendar-check"></i> Verifikasi & Jadwalkan Interview
                            </button>
                            <button type="submit" name="action" value="tolak" class="btn btn-danger" id="btnTolak">
                                <i class="fas fa-times-circle"></i> Tolak Lamaran
                            </button>
                        </div>
                    </form>
                    <div class="info-note">
                        <i class="fas fa-info-circle"></i> Dengan menekan "Verifikasi & Jadwalkan Interview", status lamaran akan berubah menjadi INTERVIEW dan langsung masuk ke penilaian pimpinan. Pastikan data pelamar sudah benar dan lengkap.
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Lamaran sudah diproses. Tidak dapat mengubah status lagi.
                <div style="margin-top: 10px;">
                    <a href="verifikasi_lamaran.php" class="btn btn-secondary">Kembali ke Daftar Lamaran</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
    </footer>

    <script src="js/detail.js"></script>
</body>

</html>