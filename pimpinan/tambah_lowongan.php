<?php
session_start();
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kualifikasi = mysqli_real_escape_string($conn, $_POST['kualifikasi']);
    $query = "INSERT INTO lowongan (judul, deskripsi, kualifikasi, status, tanggal_posting) VALUES ('$judul', '$deskripsi', '$kualifikasi', 'buka', CURDATE())";
    if (mysqli_query($conn, $query)) header("Location: index.php");
    else $error = "Gagal menambah lowongan.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Lowongan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../asset/css/style.css">
</head>

<body style="padding:2rem; font-family:Poppins;">
    <h2>Tambah Lowongan Baru</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <label>Judul:</label><br><input type="text" name="judul" required style="width:100%; margin-bottom:1rem;"><br>
        <label>Deskripsi:</label><br><textarea name="deskripsi" rows="5" required style="width:100%; margin-bottom:1rem;"></textarea><br>
        <label>Kualifikasi:</label><br><textarea name="kualifikasi" rows="5" required style="width:100%; margin-bottom:1rem;"></textarea><br>
        <button type="submit" style="background:#0a6b9e; color:white; border:none; padding:0.5rem 1rem; border-radius:30px;">Simpan & Buka Lowongan</button>
        <a href="index.php">Batal</a>
    </form>
</body>

</html>