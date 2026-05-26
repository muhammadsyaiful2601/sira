<?php
require_once '../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Ambil data user saat ini
$query = "SELECT id, nama, email, no_hp, alamat, role, created_at FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));

    // Validasi email unik (kecuali email milik sendiri)
    $cek_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id != $user_id");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah digunakan oleh user lain.";
    } else {
        // Update data utama
        $update = "UPDATE users SET nama='$nama', email='$email', no_hp='$no_hp', alamat='$alamat' WHERE id=$user_id";
        if (mysqli_query($conn, $update)) {
            $message = "Profil berhasil diperbarui.";
            // Refresh data user
            $query = "SELECT id, nama, email, no_hp, alamat, role, created_at FROM users WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil password hash dari database
    $pass_query = mysqli_query($conn, "SELECT password FROM users WHERE id=$user_id");
    $pass_data = mysqli_fetch_assoc($pass_query);

    if (password_verify($current_password, $pass_data['password'])) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 6) {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_pass = mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE id=$user_id");
                if ($update_pass) {
                    $message = "Password berhasil diubah.";
                } else {
                    $error = "Gagal mengubah password.";
                }
            } else {
                $error = "Password baru minimal 6 karakter.";
            }
        } else {
            $error = "Konfirmasi password baru tidak cocok.";
        }
    } else {
        $error = "Password saat ini salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pimpinan | RS Ar-Rasyid</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="../asset/image/icon/1.png" type="image/png">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .profile-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 20px 25px;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .profile-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-save {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-save:hover {
            background: #2980b9;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info-badge {
            display: inline-block;
            background: #e9ecef;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: 10px;
        }

        hr {
            margin: 25px 0;
            border: 0;
            border-top: 1px solid #eee;
        }

        .password-section {
            margin-top: 10px;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .btn-back:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-hospital-user"></i> Pimpinan RS Ar-Rasyid</div>
        <div class="nav-buttons">
            <a href="index.php" class="profile-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="container profile-container">
        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>

        <div class="profile-card">
            <div class="profile-header">
                <h2><i class="fas fa-user-circle"></i> Profil Pimpinan</h2>
            </div>
            <div class="profile-body">
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Nomor HP</label>
                        <input type="text" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
                        <textarea name="alamat"><?= htmlspecialchars($user['alamat']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Role</label>
                        <input type="text" value="<?= ucfirst($user['role']) ?>" disabled style="background:#f5f5f5;">
                        <span class="info-badge">Tidak dapat diubah</span>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Terdaftar Sejak</label>
                        <input type="text" value="<?= date('d F Y', strtotime($user['created_at'])) ?>" disabled style="background:#f5f5f5;">
                    </div>
                    <button type="submit" name="update_profile" class="btn-save"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </form>

                <hr>

                <h3><i class="fas fa-lock"></i> Ganti Password</h3>
                <form method="POST" class="password-section">
                    <div class="form-group">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="new_password" required>
                        <small>Minimal 6 karakter</small>
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-save"><i class="fas fa-key"></i> Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>