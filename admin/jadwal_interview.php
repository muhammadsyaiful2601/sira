<?php
require_once '../config/koneksi.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$success_msg = '';
$error_msg = '';

// Proses update jadwal interview
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_jadwal') {
    $lamaran_id = intval($_POST['lamaran_id']);
    $jadwal_interview = $_POST['jadwal_interview'] ?? '';

    if (!empty($jadwal_interview)) {
        // Format datetime untuk MySQL
        $datetime_mysql = date('Y-m-d H:i:s', strtotime($jadwal_interview));
        $stmt = mysqli_prepare($conn, "UPDATE lamaran SET jadwal_interview = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $datetime_mysql, $lamaran_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Jadwal interview berhasil diperbarui.";
        } else {
            $error_msg = "Gagal memperbarui jadwal.";
        }
        mysqli_stmt_close($stmt);
    } else {
        // Hapus jadwal jika dikosongkan
        $stmt = mysqli_prepare($conn, "UPDATE lamaran SET jadwal_interview = NULL WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $lamaran_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_msg = "Jadwal interview dihapus.";
    }
}

// Hapus jadwal (opsional tombol hapus)
if (isset($_GET['hapus_jadwal']) && is_numeric($_GET['hapus_jadwal'])) {
    $id = intval($_GET['hapus_jadwal']);
    $stmt = mysqli_prepare($conn, "UPDATE lamaran SET jadwal_interview = NULL WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Jadwal interview berhasil dihapus.";
    } else {
        $error_msg = "Gagal menghapus jadwal.";
    }
    mysqli_stmt_close($stmt);
}

// Ambil data lamaran dengan status 'interview' (proses interview)
$query = "SELECT l.id, l.status_lamaran, l.jadwal_interview, l.tanggal_lamaran, 
                 u.nama AS pelamar, u.email, u.no_hp, 
                 low.judul AS posisi
          FROM lamaran l
          JOIN users u ON l.pelamar_id = u.id
          JOIN lowongan low ON l.lowongan_id = low.id
          WHERE l.status_lamaran = 'interview'
          ORDER BY l.jadwal_interview ASC, l.tanggal_lamaran DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Interview - Admin SIRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../asset/css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Tambahan khusus untuk halaman jadwal */
        .interview-schedule {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow-x: auto;
            margin-top: 1.5rem;
        }

        .interview-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .interview-table th {
            background: #f8fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #1e293b;
            border-bottom: 2px solid var(--border);
        }

        .interview-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .badge-scheduled {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-unscheduled {
            background: #ffedd5;
            color: #b45309;
        }

        .form-jadwal {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .form-jadwal input[type="datetime-local"] {
            padding: 0.4rem 0.6rem;
            border: 1px solid var(--border);
            border-radius: 2rem;
            font-family: inherit;
            font-size: 0.8rem;
        }

        .btn-icon {
            background: none;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 0.3rem 0.6rem;
            border-radius: 2rem;
            transition: 0.2s;
        }

        .btn-save {
            background: var(--primary);
            color: white;
        }

        .btn-save:hover {
            background: var(--primary-dark);
        }

        .btn-delete {
            background: #fee2e2;
            color: var(--danger);
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .empty-schedule {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .alert {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {

            .interview-table,
            .interview-table tbody,
            .interview-table tr,
            .interview-table td {
                display: block;
            }

            .interview-table thead {
                display: none;
            }

            .interview-table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                padding: 0.5rem;
            }

            .interview-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                border-bottom: 1px solid #f1f5f9;
                padding: 0.6rem;
            }

            .interview-table td::before {
                content: attr(data-label);
                font-weight: 600;
                width: 40%;
                color: #334155;
            }

            .form-jadwal {
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-calendar-alt"></i>
            <span>Admin SIRA</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="verifikasi_lamaran.php" class="nav-link">Verifikasi Lamaran</a>
            <a href="jadwal_interview.php" class="nav-link active">Jadwal Interview</a>
            <a href="profile_admin.php" class="nav-link">Profil Saya</a>
        </div>
        <div class="nav-buttons">
            <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <main class="main-content">
        <div class="welcome-header">
            <div>
                <h1>Jadwal Interview</h1>
                <p class="greeting-sub">Kelola jadwal interview untuk pelamar yang sudah diverifikasi</p>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <div class="interview-schedule">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table class="interview-table">
                    <thead>
                        <tr>
                            <th>Pelamar</th>
                            <th>Posisi</th>
                            <th>Tanggal Lamar</th>
                            <th>Status Jadwal</th>
                            <th>Atur Jadwal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)):
                            $jadwal_display = !empty($row['jadwal_interview']) ? date('d/m/Y H:i', strtotime($row['jadwal_interview'])) : 'Belum dijadwalkan';
                            $badge_class = !empty($row['jadwal_interview']) ? 'badge-scheduled' : 'badge-unscheduled';
                            $badge_text = !empty($row['jadwal_interview']) ? 'Terjadwal' : 'Menunggu Jadwal';
                            $datetime_value = !empty($row['jadwal_interview']) ? date('Y-m-d\TH:i', strtotime($row['jadwal_interview'])) : '';
                        ?>
                            <tr>
                                <td data-label="Pelamar">
                                    <strong><?= htmlspecialchars($row['pelamar']) ?></strong><br>
                                    <small><?= htmlspecialchars($row['email']) ?> | <?= htmlspecialchars($row['no_hp']) ?></small>
                                </td>
                                <td data-label="Posisi"><?= htmlspecialchars($row['posisi']) ?></td>
                                <td data-label="Tanggal Lamar"><?= date('d/m/Y', strtotime($row['tanggal_lamaran'])) ?></td>
                                <td data-label="Status Jadwal">
                                    <span class="badge <?= $badge_class ?>"><?= $badge_text ?></span>
                                    <?php if (!empty($row['jadwal_interview'])): ?>
                                        <div><small><?= $jadwal_display ?></small></div>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Atur Jadwal">
                                    <form method="POST" class="form-jadwal" onsubmit="return confirm('Simpan jadwal interview?')">
                                        <input type="hidden" name="action" value="update_jadwal">
                                        <input type="hidden" name="lamaran_id" value="<?= $row['id'] ?>">
                                        <input type="datetime-local" name="jadwal_interview" value="<?= $datetime_value ?>" required>
                                        <button type="submit" class="btn-icon btn-save" title="Simpan Jadwal"><i class="fas fa-save"></i></button>
                                        <?php if (!empty($row['jadwal_interview'])): ?>
                                            <a href="?hapus_jadwal=<?= $row['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Hapus jadwal interview ini?')" title="Hapus Jadwal"><i class="fas fa-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-schedule">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: #cbd5e1;"></i>
                    <p style="margin-top: 1rem;">Belum ada lamaran dengan status interview.</p>
                    <a href="verifikasi_lamaran.php" class="btn btn-primary" style="margin-top: 1rem;">Verifikasi Lamaran</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 SIRA | Sistem Informasi Rekrutmen & Administrasi</p>
    </footer>
</body>

</html>