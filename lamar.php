<?php
require_once 'config/koneksi.php';
session_start();

$lowongan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lowongan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lowongan WHERE id=$lowongan_id AND status='buka'"));

if (!$lowongan) {
    die("<div style='padding:2rem; text-align:center;'>Lowongan tidak valid atau sudah ditutup. <a href='lowongan.php'>Kembali</a></div>");
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $no_hp = mysqli_real_escape_string($conn, trim($_POST['no_hp']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $password = $_POST['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Cek email sudah terdaftar
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Email sudah terdaftar. Silakan <a href='login.php'>login</a> atau gunakan email lain.";
    } else {
        // Upload CV
        $cv_file = null;
        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == 0) {
            $target_dir = "upload/cv/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
            $cv_file = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['cv']['tmp_name'], $target_dir . $cv_file);
        }

        // Simpan user dengan data lengkap
        $query_user = "INSERT INTO users (nama, email, no_hp, alamat, password, role, status) 
                       VALUES ('$nama', '$email', '$no_hp', '$alamat', '$hashed', 'pelamar', 'aktif')";
        if (mysqli_query($conn, $query_user)) {
            $user_id = mysqli_insert_id($conn);
            // Simpan lamaran
            $query_lamar = "INSERT INTO lamaran (pelamar_id, lowongan_id, cv_file, status_lamaran) 
                            VALUES ($user_id, $lowongan_id, '$cv_file', 'pending')";
            mysqli_query($conn, $query_lamar);
            // Auto login
            $_SESSION['user_id'] = $user_id;
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = 'pelamar';
            header("Location: pelamar/index.php");
            exit;
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lamar Pekerjaan - SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 0.7rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-submit {
            background: #0a6b9e;
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #0a5a85;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
            background: #ffe6e6;
            padding: 0.5rem;
            border-radius: 10px;
        }

        .info-lowongan {
            background: #eef2f8;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }

        .row-2cols {
            display: flex;
            gap: 1rem;
        }

        .row-2cols .form-group {
            flex: 1;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo"><i class="fas fa-paper-plane"></i><span>Lamar Pekerjaan</span></div>
    </nav>
    <div class="form-container">
        <h3>Formulir Lamaran Pekerjaan</h3>
        <div class="info-lowongan">
            <strong>Posisi yang dilamar:</strong> <?= htmlspecialchars($lowongan['judul']) ?>
        </div>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap <span style="color:red;">*</span></label>
                <input type="text" name="nama" required>
            </div>
            <div class="row-2cols">
                <div class="form-group">
                    <label>Email <span style="color:red;">*</span></label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>No. WhatsApp <span style="color:red;">*</span></label>
                    <input type="tel" name="no_hp" placeholder="08xxxxxxxxxx" required>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap <span style="color:red;">*</span></label>
                <textarea name="alamat" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label>Password (untuk login nanti) <span style="color:red;">*</span></label>
                <input type="password" name="password" required>
                <small>Minimal 8 karakter</small>
            </div>
            <div class="form-group">
                <label>Upload CV (PDF/DOC/DOCX) <span style="color:red;">*</span></label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-check-circle"></i> Kirim Lamaran & Daftar</button>
        </form>
        <p style="margin-top:1rem; text-align:center;">Sudah punya akun? <a href="login.php">Login disini</a></p>
    </div>
    <footer class="footer">
        <p>&copy; 2026 SIRA - Rumah Sakit Ar-Rasyid</p>
    </footer>
</body>

</html>