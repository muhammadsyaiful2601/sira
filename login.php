<?php
require_once 'config/koneksi.php';
if (session_status() == PHP_SESSION_NONE) session_start();

// Jika sudah login, langsung redirect sesuai role (tanpa destroy session)
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        header("Location: admin/index.php");
        exit;
    } elseif ($role == 'pimpinan') {
        header("Location: pimpinan/index.php");
        exit;
    } elseif ($role == 'pelamar') {
        header("Location: pelamar/index.php");
        exit;
    }
    // Hanya jika role tidak dikenal, baru destroy
    session_destroy();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi.";
    } else {
        // Ambil data user berdasarkan email (tanpa filter status dulu, agar bisa kasih pesan akun nonaktif)
        $stmt = mysqli_prepare($conn, "SELECT id, nama, password, role, status FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            $error = "Email tidak ditemukan.";
        } elseif ($user['status'] !== 'aktif') {
            $error = "Akun Anda tidak aktif. Hubungi admin.";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Password salah.";
        } else {
            // Login sukses
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin/index.php");
                    break;
                case 'pimpinan':
                    header("Location: pimpinan/index.php");
                    break;
                case 'pelamar':
                    header("Location: pelamar/index.php");
                    break;
                default:
                    $error = "Role tidak dikenali.";
                    session_destroy();
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIRA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/login.css">
    <link rel="icon" href="asset/image/icon/1.png" type="image/png">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-area">
                <img src="asset/image/icon/1.png" alt="Logo Rumah Sakit" class="hospital-logo"
                    onerror="this.onerror=null; this.src='https://placehold.co/90x90?text=RS'">
                <h1 class="app-title">Login SIRA</h1>
                <p class="tagline">Sistem Informasi Rekrutmen Ar-Rasyid</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" id="server-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <div class="alert alert-error" id="client-error" style="display: none;">
                <i class="fas fa-exclamation-circle"></i> <span id="client-error-msg"></span>
            </div>

            <form method="POST" id="loginForm">
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" id="email" placeholder=" " required>
                    <label class="floating-label">Email</label>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" placeholder=" " required>
                    <label class="floating-label">Kata Sandi</label>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan sandi">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember"> Ingat saya
                    </label>
                    <a href="lupa_password.php" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span>Masuk</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="back-link">
                <a href="index.php"><i class="fas fa-home"></i> Kembali ke Beranda</a>
            </div>

            <div class="hospital-footer">
                <i class="fas fa-hospital-user"></i> Pelayanan 24 Jam &nbsp;|&nbsp; <i class="fas fa-shield-alt"></i> Data Terenkripsi
            </div>
        </div>
    </div>

    <script src="asset/js/login.js"></script>
</body>

</html>