<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelamar') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data profil saat ini
$query = "SELECT nama, email, no_hp, alamat FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$profil = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $alamat = trim($_POST['alamat']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    // Validasi dasar
    if (empty($nama) || empty($email)) {
        $error = "Nama dan email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Cek apakah email sudah digunakan oleh user lain
        $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id");
        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah digunakan oleh akun lain.";
        } else {
            // Update data utama
            $update = "UPDATE users SET nama = '$nama', email = '$email', no_hp = '$no_hp', alamat = '$alamat' WHERE id = $user_id";
            if (mysqli_query($conn, $update)) {
                // Update session name
                $_SESSION['nama'] = $nama;
                $message = "Profil berhasil diperbarui.";

                // Jika ada password baru
                if (!empty($password_baru)) {
                    if (strlen($password_baru) < 6) {
                        $error = "Password minimal 6 karakter.";
                    } elseif ($password_baru !== $konfirmasi_password) {
                        $error = "Konfirmasi password tidak cocok.";
                    } else {
                        $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
                        $update_pass = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
                        if (mysqli_query($conn, $update_pass)) {
                            $message = "Profil dan password berhasil diperbarui.";
                        } else {
                            $error = "Gagal mengupdate password: " . mysqli_error($conn);
                        }
                    }
                }
            } else {
                $error = "Gagal memperbarui profil: " . mysqli_error($conn);
            }
        }
    }

    // Refresh data setelah update
    $result = mysqli_query($conn, "SELECT nama, email, no_hp, alamat FROM users WHERE id = $user_id");
    $profil = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/pelamar.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
    <script src="js/profile.js" defer></script>
</head>

<body>
    <div class="app-container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-briefcase"></i>
                <span>Dashboard Pelamar</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="profil.php" class="nav-link active">Profil Saya</a>
                <a href="../lowongan.php" class="nav-link"><i class="fas fa-search"></i> Cari Lowongan</a>
            </div>
            <div class="nav-buttons">
                <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <a href="index.php">Beranda</a> / <span>Profil Saya</span>
                </div>

                <!-- Profile Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2>Informasi Profil</h2>
                        <p>Kelola data diri Anda di sini</p>
                    </div>

                    <!-- Notifikasi -->
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="profile-form" id="profileForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama"><i class="fas fa-user"></i> Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($profil['nama']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($profil['email']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="no_hp"><i class="fab fa-whatsapp"></i> Nomor WhatsApp</label>
                                <input type="tel" id="no_hp" name="no_hp" value="<?= htmlspecialchars($profil['no_hp']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                                <textarea id="alamat" name="alamat" rows="2"><?= htmlspecialchars($profil['alamat']) ?></textarea>
                            </div>
                        </div>

                        <hr class="divider">

                        <div class="form-group password-section">
                            <label><i class="fas fa-lock"></i> Ganti Password (opsional)</label>
                            <div class="password-toggle">
                                <input type="password" id="password_baru" name="password_baru" placeholder="Password baru" autocomplete="off">
                                <button type="button" class="toggle-password" data-target="password_baru">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-toggle">
                                <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi password baru">
                                <button type="button" class="toggle-password" data-target="konfirmasi_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter.</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="footer-content">
                <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
                <p class="footer-credit">Sistem Informasi Rekrutmen & Administrasi</p>
            </div>
        </footer>
    </div>
</body>

</html>