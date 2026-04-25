<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') header("Location: admin/index.php");
            elseif ($user['role'] == 'pimpinan') header("Location: pimpinan/index.php");
            else header("Location: pelamar/index.php");
            exit;
        } else $error = "Password salah.";
    } else $error = "Email tidak ditemukan.";
}
?>
<!doctype html>
<html lang="id">

<head>
    <title>Login SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital"></i><span>SIRA - RS Ar-Rasyid</span></div>
        <div class="nav-links"><a href="index.php" class="nav-link">Beranda</a></div>
        <div class="nav-buttons"><a href="daftar.php" class="btn btn-register">Daftar</a></div>
    </nav>
    <div class="auth-container">
        <h2>Login SIRA</h2><?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div><button type="submit" class="btn-primary">Login</button>
            <div class="text-center">Belum punya akun? <a href="daftar.php">Daftar sekarang</a></div>
        </form>
    </div>
</body>

</html>