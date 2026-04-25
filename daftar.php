<?php
require_once 'config/koneksi.php';

$error = '';
$success = '';

// Cek jumlah admin dan pimpinan yang sudah terdaftar
$query_admin = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$admin_exists = mysqli_fetch_assoc($query_admin)['total'] > 0;

$query_pimpinan = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pimpinan'");
$pimpinan_exists = mysqli_fetch_assoc($query_pimpinan)['total'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi role admin/pimpinan hanya sekali
    if ($role == 'admin' && $admin_exists) {
        $error = "Akun Admin sudah terdaftar. Tidak dapat mendaftar admin lagi.";
    } elseif ($role == 'pimpinan' && $pimpinan_exists) {
        $error = "Akun Pimpinan sudah terdaftar. Hanya satu pimpinan diperbolehkan.";
    } else {
        // Cek email duplikat
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah terdaftar. Gunakan email lain.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed', '$role')");
            if ($insert) {
                $success = "Pendaftaran berhasil! Silakan login.";
                // Refresh status admin/pimpinan setelah insert
                if ($role == 'admin') $admin_exists = true;
                if ($role == 'pimpinan') $pimpinan_exists = true;
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital"></i><span>SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links"><a href="index.php" class="nav-link">Beranda</a></div>
        <div class="nav-buttons"><a href="login.php" class="btn btn-login">Login</a></div>
    </nav>
    <div class="auth-container">
        <h2>Daftar Akun Baru</h2>
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <div class="form-group"><label>Role</label>
                <select name="role" required>
                    <option value="pelamar">Pelamar (umum)</option>
                    <?php if (!$admin_exists): ?>
                        <option value="admin">Admin (hanya sekali)</option>
                    <?php endif; ?>
                    <?php if (!$pimpinan_exists): ?>
                        <option value="pimpinan">Pimpinan (hanya sekali)</option>
                    <?php endif; ?>
                </select>
                <small style="color:#6c757d; display:block;">* Role admin & pimpinan hanya bisa didaftarkan satu kali untuk keperluan sistem.</small>
            </div>
            <button type="submit" class="btn-primary">Daftar</button>
            <div class="text-center">Sudah punya akun? <a href="login.php">Login disini</a></div>
        </form>
    </div>
</body>

</html>