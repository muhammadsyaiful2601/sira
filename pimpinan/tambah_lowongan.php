<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul       = mysqli_real_escape_string($conn, trim($_POST['judul']));
    $deskripsi   = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $kualifikasi = mysqli_real_escape_string($conn, trim($_POST['kualifikasi']));

    if (empty($judul) || empty($deskripsi) || empty($kualifikasi)) {
        $error = "Semua bidang harus diisi.";
    } else {
        $query = "INSERT INTO lowongan (judul, deskripsi, kualifikasi, status, tanggal_posting) 
                  VALUES ('$judul', '$deskripsi', '$kualifikasi', 'buka', CURDATE())";
        if (mysqli_query($conn, $query)) {
            $success = "Lowongan berhasil ditambahkan!";
            // Redirect setelah 2 detik atau bisa langsung, tapi beri notifikasi
            header("refresh:2; url=index.php");
        } else {
            $error = "Gagal menambah lowongan: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lowongan | Pimpinan RS Ar-Rasyid</title>
    <!-- CSS Kustom -->
    <link rel="stylesheet" href="css/tambah.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="asset/image/icon/1.png" type="image/png">
</head>

<body>
    <div class="container">
        <div class="card-form">
            <div class="card-header">
                <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
                <h1><i class="fas fa-plus-circle"></i> Tambah Lowongan Baru</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?> Mengalihkan...</div>
            <?php endif; ?>

            <form method="POST" id="formTambahLowongan" class="lowongan-form">
                <div class="form-group">
                    <label for="judul"><i class="fas fa-briefcase"></i> Judul Lowongan <span class="required">*</span></label>
                    <input type="text" name="judul" id="judul" required placeholder="Contoh: Dokter Spesialis Anak" value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>">
                    <small class="form-text">Judul yang jelas akan menarik pelamar berkualitas.</small>
                </div>

                <div class="form-group">
                    <label for="deskripsi"><i class="fas fa-align-left"></i> Deskripsi Pekerjaan <span class="required">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="6" required placeholder="Tugas dan tanggung jawab, lingkungan kerja, dll."><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label for="kualifikasi"><i class="fas fa-graduation-cap"></i> Kualifikasi <span class="required">*</span></label>
                    <textarea name="kualifikasi" id="kualifikasi" rows="5" required placeholder="Pendidikan minimal, pengalaman, sertifikasi, dll."><?= isset($_POST['kualifikasi']) ? htmlspecialchars($_POST['kualifikasi']) : '' ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan & Buka Lowongan</button>
                    <a href="index.php" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="js/tambah.js"></script>
</body>

</html>