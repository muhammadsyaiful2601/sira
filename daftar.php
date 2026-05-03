<?php
require_once 'config/koneksi.php';

// Cek apakah sudah ada user dengan role pimpinan di database
$queryPimpinan = "SELECT id FROM users WHERE role = 'pimpinan' LIMIT 1";
$resultPimpinan = mysqli_query($conn, $queryPimpinan);
$pimpinanExists = mysqli_num_rows($resultPimpinan) > 0;

// Jika sudah ada pimpinan, arahkan ke lowongan.php (pendaftaran pelamar)
if ($pimpinanExists) {
    header("Location: lowongan.php");
    exit;
}

// Jika belum ada pimpinan, tampilkan form pendaftaran untuk admin/pimpinan awal
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $role = $_POST['role']; // admin atau pimpinan
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Validasi sederhana
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi.";
    } elseif (!in_array($role, ['admin', 'pimpinan'])) {
        $error = "Role tidak valid.";
    } else {
        // Cek email sudah terdaftar
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Email sudah digunakan.";
        } else {
            $query = "INSERT INTO users (nama, email, password, role, status) 
                      VALUES ('$nama', '$email', '$hashed', '$role', 'aktif')";
            if (mysqli_query($conn, $query)) {
                $success = "Pendaftaran berhasil! Silakan <a href='login.php'>login</a>.";
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar - SIRA RS Ar-Rasyid</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 3rem auto;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input,
        select {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        .btn-register {
            background: #0a6b9e;
            color: white;
            border: none;
            padding: 0.7rem;
            width: 100%;
            border-radius: 30px;
            font-size: 1rem;
            cursor: pointer;
        }

        .error {
            color: red;
            background: #ffe6e6;
            padding: 0.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .success {
            color: green;
            background: #e6ffe6;
            padding: 0.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .info {
            background: #eef2f8;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital"></i><span>SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Beranda</a>
            <a href="lowongan.php" class="nav-link">Lowongan</a>
            <a href="login.php" class="nav-link">Login</a>
        </div>
    </nav>

    <div class="register-container">
        <h2>Pendaftaran Awal</h2>
        <div class="info">
            <i class="fas fa-info-circle"></i> Sistem belum memiliki akun pimpinan. Silakan daftarkan admin atau pimpinan pertama.
        </div>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Daftar sebagai</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="pimpinan">Pimpinan</option>
                </select>
            </div>
            <button type="submit" class="btn-register"><i class="fas fa-user-plus"></i> Daftar</button>
        </form>
        <p style="margin-top: 1rem; text-align: center;">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>

    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>