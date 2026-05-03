<?php
require_once 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    // Redirect sesuai role
    if ($_SESSION['role'] == 'admin') header("Location: admin/index.php");
    elseif ($_SESSION['role'] == 'pimpinan') header("Location: pimpinan/index.php");
    elseif ($_SESSION['role'] == 'pelamar') header("Location: pelamar/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE email='$email' AND status='aktif'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'admin') header("Location: admin/index.php");
            elseif ($user['role'] == 'pimpinan') header("Location: pimpinan/index.php");
            else header("Location: pelamar/index.php");
            exit;
        } else $error = "Password salah.";
    } else $error = "Email tidak ditemukan atau akun nonaktif.";
}
?>
<!doctype html>
<html lang="id">

<head>
    <title>Login SIRA</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body style="font-family:Poppins; display:flex; justify-content:center; align-items:center; height:100vh; background:#eef2f8;">
    <div style="background:white; padding:2rem; border-radius:20px; width:350px;">
        <h2>Login</h2>
        <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required style="width:100%; margin-bottom:1rem; padding:0.5rem;"><br>
            <input type="password" name="password" placeholder="Password" required style="width:100%; margin-bottom:1rem; padding:0.5rem;"><br>
            <button type="submit" style="width:100%; padding:0.7rem; background:#0a6b9e; color:white; border:none; border-radius:30px;">Login</button>
        </form>
        <p style="margin-top:1rem;"><a href="index.php">Kembali ke Beranda</a></p>
    </div>
</body>

</html>