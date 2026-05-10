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

// PROSES HAPUS LOWONGAN (dengan pengecekan apakah sudah ada pelamar diterima)
if (isset($_GET['hapus_lowongan'])) {
    $id_lowongan = intval($_GET['hapus_lowongan']);
    // Cek apakah ada lamaran dengan status 'diterima' untuk lowongan ini
    $cek = mysqli_query($conn, "SELECT id FROM lamaran WHERE lowongan_id=$id_lowongan AND status_lamaran='diterima' LIMIT 1");
    if (mysqli_num_rows($cek) > 0) {
        $error_hapus = "Lowongan tidak dapat dihapus karena sudah ada pelamar yang diterima.";
    } else {
        // Hapus lowongan (karena foreign key cascade, lamaran terkait ikut terhapus)
        mysqli_query($conn, "DELETE FROM lowongan WHERE id=$id_lowongan");
        $success_hapus = "Lowongan berhasil dihapus.";
    }
    header("Location: index.php?" . ($error_hapus ? "error_hapus=" . urlencode($error_hapus) : "success_hapus=" . urlencode($success_hapus)));
    exit;
}

// TAMPILKAN PESAN ERROR/SUKSES DARI HAPUS
$info_hapus = '';
if (isset($_GET['error_hapus'])) {
    $info_hapus = '<div class="alert alert-error">' . htmlspecialchars($_GET['error_hapus']) . '</div>';
} elseif (isset($_GET['success_hapus'])) {
    $info_hapus = '<div class="alert alert-success">' . htmlspecialchars($_GET['success_hapus']) . '</div>';
}

// AMBIL LAMARAN STATUS INTERVIEW
$lamaran_interview = mysqli_query($conn, "SELECT l.*, u.nama, u.email, u.no_hp, low.judul 
    FROM lamaran l 
    JOIN users u ON l.pelamar_id = u.id 
    JOIN lowongan low ON l.lowongan_id = low.id 
    WHERE l.status_lamaran = 'interview' 
    ORDER BY l.jadwal_interview ASC");

// AMBIL SEMUA LOWONGAN
$lowongan_list = mysqli_query($conn, "SELECT * FROM lowongan ORDER BY tanggal_posting DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pimpinan | RS Ar-Rasyid</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital-user"></i> Pimpinan RS Ar-Rasyid</div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container">
        <!-- ==================== KEPUTUSAN INTERVIEW ==================== -->
        <div class="card-section">
            <h2><i class="fas fa-gavel"></i> Keputusan Final Lamaran (Interview)</h2>
            <?php if (mysqli_num_rows($lamaran_interview) == 0): ?>
                <div class="alert alert-info">✅ Tidak ada lamaran yang perlu diputuskan saat ini.</div>
            <?php else: ?>
                <div class="interview-grid">
                    <?php while ($lam = mysqli_fetch_assoc($lamaran_interview)): ?>
                        <div class="card-interview">
                            <div class="interview-header">
                                <h3><?= htmlspecialchars($lam['nama']) ?> - <?= htmlspecialchars($lam['judul']) ?></h3>
                                <span class="badge badge-interview">Interview</span>
                            </div>
                            <p><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($lam['no_hp']) ?></p>
                            <p><i class="fas fa-calendar-alt"></i> Jadwal: <?= date('d F Y H:i', strtotime($lam['jadwal_interview'])) ?></p>
                            <form method="POST" class="form-keputusan">
                                <input type="hidden" name="id_lamaran" value="<?= $lam['id'] ?>">
                                <input type="text" name="catatan_pimpinan" placeholder="Catatan (opsional)" class="input-catatan">
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

        <!-- ==================== TAMBAH ADMIN (TOGGLE) ==================== -->
        <div class="card-section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2><i class="fas fa-user-shield"></i> Kelola Admin</h2>
                <button id="toggleAdminFormBtn" class="btn-primary"><i class="fas fa-plus-circle"></i> Tambah Admin</button>
            </div>
            <!-- Form Tambah Admin (disembunyikan awal) -->
            <div id="adminFormContainer" style="display: none; margin-top: 20px;">
                <?php if (isset($error_admin)): ?>
                    <div class="alert alert-error"><?= $error_admin ?></div>
                <?php elseif (isset($success_admin)): ?>
                    <div class="alert alert-success"><?= $success_admin ?></div>
                <?php endif; ?>
                <form method="POST" class="form-admin">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" required placeholder="Masukkan nama admin">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="admin@rumahsakit.com">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter">
                    </div>
                    <button type="submit" name="tambah_admin" class="btn-primary"><i class="fas fa-save"></i> Simpan Admin</button>
                </form>
            </div>
        </div>

        <!-- ==================== DAFTAR LOWONGAN + HAPUS ==================== -->
        <div class="card-section">
            <h2><i class="fas fa-briefcase"></i> Lowongan Tersedia</h2>
            <?= $info_hapus ?>
            <div class="lowongan-actions">
                <a href="tambah_lowongan.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Lowongan Baru</a>
            </div>
            <?php if (mysqli_num_rows($lowongan_list) == 0): ?>
                <div class="alert alert-info">Belum ada lowongan. Silakan tambah lowongan.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table-lowongan">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Deskripsi Singkat</th>
                                <th>Kualifikasi</th>
                                <th>Status</th>
                                <th>Tanggal Posting</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($low = mysqli_fetch_assoc($lowongan_list)): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($low['judul']) ?></strong></td>
                                    <td><?= htmlspecialchars(substr($low['deskripsi'], 0, 80)) ?>...</td>
                                    <td><?= htmlspecialchars(substr($low['kualifikasi'], 0, 60)) ?>...</td>
                                    <td><span class="badge <?= $low['status'] == 'buka' ? 'badge-open' : 'badge-closed' ?>"><?= $low['status'] == 'buka' ? 'Buka' : 'Tutup' ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($low['tanggal_posting'])) ?></td>
                                    <td>
                                        <a href="?hapus_lowongan=<?= $low['id'] ?>" class="btn-hapus" onclick="return confirmHapus(event, this.href)"><i class="fas fa-trash-alt"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/dashboard.js"></script>
</body>

</html>