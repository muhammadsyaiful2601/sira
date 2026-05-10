<?php
require_once '../config/koneksi.php';
if ($_SESSION['role'] !== 'admin') die("Akses ditolak");
$id_lamaran = $_GET['id'];
$lamaran = mysqli_fetch_assoc(mysqli_query($conn, "SELECT l.*, u.nama, u.email, u.no_hp, u.alamat, low.judul 
    FROM lamaran l JOIN users u ON l.pelamar_id=u.id JOIN lowongan low ON l.lowongan_id=low.id WHERE l.id=$id_lamaran"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aksi = $_POST['aksi'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    if ($aksi == 'verifikasi') {
        $status_baru = 'verifikasi';
        $pesan = "Dokumen dinyatakan lengkap. Tunggu jadwal interview.";
    } elseif ($aksi == 'jadwal') {
        $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal_interview']);
        mysqli_query($conn, "UPDATE lamaran SET jadwal_interview='$jadwal', status_lamaran='interview', catatan_admin='$catatan' WHERE id=$id_lamaran");
        $status_baru = 'interview';
        $pesan = "Jadwal interview ditentukan: $jadwal";
    }
    if ($aksi != 'jadwal') {
        mysqli_query($conn, "UPDATE lamaran SET status_lamaran='$status_baru', catatan_admin='$catatan' WHERE id=$id_lamaran");
    }
    header("Location: verifikasi_lamaran.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Detail Lamaran</title>
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body>
    <div style="max-width:800px; margin:2rem auto; background:white; padding:2rem; border-radius:20px;">
        <h3>Detail Pelamar & Lamaran</h3>
        <p><strong>Nama:</strong> <?= $lamaran['nama'] ?></p>
        <p><strong>Email:</strong> <?= $lamaran['email'] ?></p>
        <p><strong>No HP:</strong> <?= $lamaran['no_hp'] ?></p>
        <p><strong>Alamat:</strong> <?= nl2br($lamaran['alamat']) ?></p>
        <p><strong>Lowongan:</strong> <?= $lamaran['judul'] ?></p>
        <p><strong>Status Saat Ini:</strong> <?= $lamaran['status_lamaran'] ?></p>
        <p><strong>CV:</strong> <a href="../upload/cv/<?= $lamaran['cv_file'] ?>" target="_blank">Lihat CV</a></p>
        <hr>
        <form method="POST">
            <label>Catatan Admin:</label><br>
            <textarea name="catatan" rows="3" style="width:100%"><?= $lamaran['catatan_admin'] ?></textarea><br><br>
            <?php if ($lamaran['status_lamaran'] == 'pending'): ?>
                <button type="submit" name="aksi" value="verifikasi" class="btn-sm btn-primary">✅ Verifikasi (Dokumen Lengkap)</button>
            <?php elseif ($lamaran['status_lamaran'] == 'verifikasi'): ?>
                <label>Jadwal Interview:</label>
                <input type="datetime-local" name="jadwal_interview" required>
                <button type="submit" name="aksi" value="jadwal" class="btn-sm btn-primary">📅 Tetapkan Jadwal Interview</button>
            <?php else: ?>
                <p><i>Lamaran sudah dalam status <?= $lamaran['status_lamaran'] ?>, tidak dapat diubah di sini.</i></p>
            <?php endif; ?>
            <br><a href="verifikasi_lamaran.php">Kembali</a>
        </form>
    </div>
</body>

</html>