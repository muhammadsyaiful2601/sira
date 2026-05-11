<?php
require_once '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data admin saat ini
$query = "SELECT id, nama, email, no_hp, alamat FROM users WHERE id = ? AND role = 'admin'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

if (!$admin) {
    die("Data admin tidak ditemukan.");
}

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));

    if (empty($nama) || empty($email)) {
        $error = "Nama dan Email wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        $check = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt_check = mysqli_prepare($conn, $check);
        mysqli_stmt_bind_param($stmt_check, "si", $email, $user_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Email sudah digunakan oleh akun lain.";
        } else {
            $update = "UPDATE users SET nama = ?, email = ?, no_hp = ?, alamat = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $update);
            mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $email, $no_hp, $alamat, $user_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['nama'] = $nama;
                $message = "Profil berhasil diperbarui.";
                $admin['nama'] = $nama;
                $admin['email'] = $email;
                $admin['no_hp'] = $no_hp;
                $admin['alamat'] = $alamat;
            } else {
                $error = "Gagal memperbarui profil: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_update);
        }
        mysqli_stmt_close($stmt_check);
    }
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field password harus diisi.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi tidak cocok.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter.";
    } else {
        $pass_query = "SELECT password FROM users WHERE id = ?";
        $stmt_pass = mysqli_prepare($conn, $pass_query);
        mysqli_stmt_bind_param($stmt_pass, "i", $user_id);
        mysqli_stmt_execute($stmt_pass);
        $result_pass = mysqli_stmt_get_result($stmt_pass);
        $user_data = mysqli_fetch_assoc($result_pass);
        mysqli_stmt_close($stmt_pass);

        if (password_verify($current_password, $user_data['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_update = mysqli_prepare($conn, $update_pass);
            mysqli_stmt_bind_param($stmt_update, "si", $hashed_password, $user_id);
            if (mysqli_stmt_execute($stmt_update)) {
                $message = "Password berhasil diubah.";
            } else {
                $error = "Gagal mengubah password: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $error = "Password saat ini salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/profil_admin.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
</head>

<body>
    <div class="app-wrapper">
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-user-shield"></i>
                <span>Admin SIRA</span>
            </div>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Dashboard</a>
                <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
                <a href="profile_admin.php" class="nav-link active">Profil Saya</a>
            </div>
            <div class="nav-buttons">
                <a href="../logout.php" class="btn btn-login">Logout</a>
            </div>
        </nav>

        <main class="main-content">
            <div class="page-header">
                <h2><i class="fas fa-user-circle"></i> Profil Administrator</h2>
                <p>Kelola informasi akun dan keamanan Anda</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="profile-grid">
                <!-- Kartu Edit Profil -->
                <div class="profile-card">
                    <div class="card-header">
                        <i class="fas fa-id-card"></i>
                        <h3>Informasi Akun</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="action-form" id="formUpdateProfile">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($admin['nama']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="no_hp">Nomor Telepon</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= htmlspecialchars($admin['no_hp'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" id="alamat" rows="3" class="form-control"><?= htmlspecialchars($admin['alamat'] ?? '') ?></textarea>
                            </div>
                            <div class="button-group">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kartu Ganti Password -->
                <div class="profile-card">
                    <div class="card-header">
                        <i class="fas fa-lock"></i>
                        <h3>Ganti Password</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="action-form" id="formChangePassword">
                            <div class="form-group">
                                <label for="current_password">Password Saat Ini <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <button type="button" class="toggle-password" data-target="current_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="new_password">Password Baru <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" name="new_password" id="new_password" class="form-control" required>
                                    <button type="button" class="toggle-password" data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small>Minimal 6 karakter</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Konfirmasi Password Baru <span class="required">*</span></label>
                                <div class="password-wrapper">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                    <button type="button" class="toggle-password" data-target="confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="button-group">
                                <button type="submit" name="change_password" class="btn btn-primary" style="background: #28a745;">
                                    <i class="fas fa-key"></i> Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="footer">
            <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
        </footer>
    </div>

    <script src="js/profil_admin.js"></script>
</body>

</html>