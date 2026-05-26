<?php
require_once '../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

// PROSES TAMBAH ADMIN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_admin'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = 'admin';

    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error_admin = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (nama, email, password, role, status) VALUES ('$nama', '$email', '$password', '$role', 'aktif')";
        if (mysqli_query($conn, $query)) {
            $success_admin = "Admin baru berhasil ditambahkan.";
        } else {
            $error_admin = "Gagal menambahkan admin: " . mysqli_error($conn);
        }
    }
}

// PROSES KEPUTUSAN LAMARAN (Terima/Tolak)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['keputusan'])) {
    $id_lamaran = intval($_POST['id_lamaran']);
    $keputusan  = $_POST['keputusan'];
    $catatan    = mysqli_real_escape_string($conn, trim($_POST['catatan_pimpinan']));
    $status_baru = ($keputusan == 'terima') ? 'diterima' : 'ditolak';

    $update = "UPDATE lamaran SET status_lamaran='$status_baru', catatan_pimpinan = CONCAT(IFNULL(catatan_pimpinan,''), ' | Keputusan: $catatan') WHERE id=$id_lamaran";
    mysqli_query($conn, $update);
    header("Location: index.php");
    exit;
}

// PROSES UPDATE PENGATURAN JUMLAH LOWONGAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_limit_lowongan'])) {
    $limit = intval($_POST['limit_lowongan']);
    if ($limit < 0) $limit = 0;
    $query = "INSERT INTO pengaturan (setting_key, setting_value) VALUES ('lowongan_limit_depan', '$limit')
              ON DUPLICATE KEY UPDATE setting_value = '$limit'";
    mysqli_query($conn, $query);
    $success_limit = "Pengaturan jumlah lowongan berhasil diupdate.";
}

$result_limit = mysqli_query($conn, "SELECT setting_value FROM pengaturan WHERE setting_key='lowongan_limit_depan'");
$current_limit = 0;
if ($row = mysqli_fetch_assoc($result_limit)) {
    $current_limit = intval($row['setting_value']);
}

// Query lamaran interview
$lamaran_interview = mysqli_query($conn, "SELECT l.*, u.nama, u.email, u.no_hp, low.judul 
    FROM lamaran l 
    JOIN users u ON l.pelamar_id = u.id 
    JOIN lowongan low ON l.lowongan_id = low.id 
    WHERE l.status_lamaran = 'interview' 
    ORDER BY l.jadwal_interview ASC");

// Query daftar lowongan (hanya untuk dilihat, tanpa aksi tambah/hapus)
$lowongan_list = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY tanggal_posting DESC");

// Riwayat keputusan untuk cetak surat
$riwayat = mysqli_query($conn, "SELECT l.id, l.status_lamaran, l.catatan_pimpinan, u.nama, low.judul 
                                FROM lamaran l 
                                JOIN users u ON l.pelamar_id = u.id 
                                JOIN lowongan low ON l.lowongan_id = low.id 
                                WHERE l.status_lamaran IN ('diterima','ditolak') 
                                ORDER BY l.tanggal_lamaran DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard Pimpinan | RS Ar-Rasyid</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital-user"></i> Pimpinan RS Ar-Rasyid</div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-grid">
            <!-- KOLOM KIRI: Keputusan Interview -->
            <div class="grid-col">
                <div class="card-section card-interview-full">
                    <div class="section-header">
                        <h2><i class="fas fa-gavel"></i> Keputusan Final Lamaran</h2>
                        <span class="badge-section">Interview</span>
                    </div>
                    <?php if (mysqli_num_rows($lamaran_interview) == 0): ?>
                        <div class="alert alert-info"><i class="fas fa-check-circle"></i> Tidak ada lamaran yang perlu diputuskan.</div>
                    <?php else: ?>
                        <div class="interview-grid">
                            <?php while ($lam = mysqli_fetch_assoc($lamaran_interview)): ?>
                                <div class="card-interview">
                                    <div class="interview-header">
                                        <h3><?= htmlspecialchars($lam['nama']) ?></h3>
                                        <span class="badge badge-interview">Interview</span>
                                    </div>
                                    <div class="interview-body">
                                        <p><i class="fas fa-briefcase"></i> <strong><?= htmlspecialchars($lam['judul']) ?></strong></p>
                                        <p><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($lam['no_hp']) ?></p>
                                        <p><i class="fas fa-calendar-alt"></i> <?= date('d F Y H:i', strtotime($lam['jadwal_interview'])) ?></p>
                                    </div>
                                    <form method="POST" class="form-keputusan">
                                        <input type="hidden" name="id_lamaran" value="<?= $lam['id'] ?>">
                                        <div class="form-group-catatan">
                                            <i class="fas fa-pen"></i>
                                            <input type="text" name="catatan_pimpinan" placeholder="Catatan (opsional)" class="input-catatan">
                                        </div>
                                        <div class="action-buttons">
                                            <button type="submit" name="keputusan" value="terima" class="btn-terima"><i class="fas fa-check-circle"></i> Terima</button>
                                            <button type="submit" name="keputusan" value="tolak" class="btn-tolak"><i class="fas fa-times-circle"></i> Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-shield"></i> Kelola Admin</h2>
                    <button id="toggleAdminFormBtn" class="btn-primary btn-sm"><i class="fas fa-plus-circle"></i> Tambah Admin</button>
                </div>
                <div id="adminFormContainer" style="display: none; margin-top: 20px;">
                    <?php if (isset($error_admin)): ?>
                        <div class="alert alert-error"><?= $error_admin ?></div>
                    <?php elseif (isset($success_admin)): ?>
                        <div class="alert alert-success"><?= $success_admin ?></div>
                    <?php endif; ?>
                    <div class="admin-form-wrapper">
                        <form method="POST" class="form-admin">
                            <div class="form-row">
                                <div class="form-group">
                                    <label><i class="fas fa-user"></i> Nama Lengkap</label>
                                    <input type="text" name="nama" required placeholder="Nama admin">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-envelope"></i> Email</label>
                                    <input type="email" name="email" required placeholder="admin@rsarasyid.com">
                                </div>
                                <div class="form-group">
                                    <label><i class="fas fa-lock"></i> Password</label>
                                    <input type="password" name="password" required placeholder="Minimal 6 karakter">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" name="tambah_admin" class="btn-primary"><i class="fas fa-save"></i> Simpan Admin</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Lowongan (Full Width) - Hanya untuk dilihat, tanpa aksi tambah/hapus -->
    <div class="card-section full-width">
        <div class="section-header">
            <h2><i class="fas fa-briefcase"></i> Daftar Lowongan</h2>
            <span class="badge-section">Hanya Lihat</span>
        </div>
        <?php if (mysqli_num_rows($lowongan_list) == 0): ?>
            <div class="alert alert-info">Belum ada lowongan.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-lowongan">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Kualifikasi</th>
                            <th>Status</th>
                            <th>Tgl Posting</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($low = mysqli_fetch_assoc($lowongan_list)): ?>
                            <tr>
                                <td data-label="Judul"><strong><?= htmlspecialchars($low['judul']) ?></strong></td>
                                <td data-label="Deskripsi"><?= htmlspecialchars(substr($low['deskripsi'], 0, 70)) ?>...</td>
                                <td data-label="Kualifikasi"><?= htmlspecialchars(substr($low['kualifikasi'], 0, 60)) ?>...</td>
                                <td data-label="Status"><span class="badge <?= $low['status'] == 'buka' ? 'badge-open' : 'badge-closed' ?>"><?= $low['status'] == 'buka' ? 'Buka' : 'Tutup' ?></span></td>
                                <td data-label="Tgl Posting"><?= date('d/m/Y', strtotime($low['tanggal_posting'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIWAYAT KEPUTUSAN (CETAK SURAT) -->
    <div class="card-section full-width" style="margin-top: 1rem;">
        <div class="section-header">
            <h2><i class="fas fa-print"></i> Riwayat Keputusan</h2>
            <span class="badge-section">Cetak Surat</span>
        </div>
        <?php if (mysqli_num_rows($riwayat) == 0): ?>
            <div class="alert alert-info">Belum ada lamaran yang diputus (diterima/ditolak).</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-lowongan">
                    <thead>
                        <tr>
                            <th>Pelamar</th>
                            <th>Lowongan</th>
                            <th>Keputusan</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['nama']) ?></td>
                                <td><?= htmlspecialchars($r['judul']) ?></td>
                                <td><span class="badge <?= $r['status_lamaran'] == 'diterima' ? 'badge-open' : 'badge-closed' ?>"><?= ucfirst($r['status_lamaran']) ?></span></td>
                                <td><?= htmlspecialchars(substr($r['catatan_pimpinan'], 0, 50)) ?>...</td>
                                <td><a href="cetak_surat.php?id=<?= $r['id'] ?>" class="btn-primary btn-sm" target="_blank"><i class="fas fa-print"></i> Cetak Surat</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggleAdminFormBtn');
        const formContainer = document.getElementById('adminFormContainer');
        if (toggleBtn && formContainer) {
            toggleBtn.addEventListener('click', function() {
                if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                    formContainer.style.display = 'block';
                    toggleBtn.innerHTML = '<i class="fas fa-times-circle"></i> Tutup Form';
                } else {
                    formContainer.style.display = 'none';
                    toggleBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Admin';
                }
            });
        }
    </script>
</body>

</html>