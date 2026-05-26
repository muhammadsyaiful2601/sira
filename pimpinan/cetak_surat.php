<?php
require_once '../config/koneksi.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pimpinan') {
    header("Location: ../login.php");
    exit;
}

$id_lamaran = intval($_GET['id'] ?? 0);
$query = "SELECT l.*, u.nama as pelamar, u.email, u.no_hp, low.judul as lowongan 
          FROM lamaran l 
          JOIN users u ON l.pelamar_id = u.id 
          JOIN lowongan low ON l.lowongan_id = low.id 
          WHERE l.id = $id_lamaran";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    die("Data lamaran tidak ditemukan.");
}
$data = mysqli_fetch_assoc($result);
if (!in_array($data['status_lamaran'], ['diterima', 'ditolak'])) {
    die("Surat keputusan hanya dapat dicetak untuk lamaran yang sudah diputus (diterima/ditolak).");
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keputusan - <?= htmlspecialchars($data['pelamar']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            padding: 2rem;
            background: #f4f4f4;
        }

        .surat-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header-surat {
            text-align: center;
            border-bottom: 3px solid #1e3c72;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .header-surat h1 {
            font-size: 1.8rem;
            margin-bottom: 0.2rem;
        }

        .header-surat h3 {
            font-weight: normal;
        }

        .nomor-surat {
            text-align: center;
            margin: 1rem 0;
            font-style: italic;
        }

        .isi-surat {
            margin: 2rem 0;
            line-height: 1.6;
            text-align: justify;
        }

        .ttd {
            margin-top: 3rem;
            text-align: right;
        }

        .tombol-print {
            text-align: center;
            margin-top: 1rem;
        }

        .tombol-print button {
            padding: 0.6rem 1.2rem;
            background: #1e3c72;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 6px;
            margin: 0 0.5rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .tombol-print {
                display: none;
            }

            .surat-container {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="surat-container">
        <div class="header-surat">
            <h1>RS AR-RASYID</h1>
            <h3>Jalan Kesehatan No. 1, Kota Baru</h3>
            <p>Telp. (021) 1234567 | Email: rs@arasyid.id</p>
        </div>
        <div class="nomor-surat">
            <p>Nomor: 800/SK-RS/<?= date('m') ?>/<?= date('Y') ?></p>
        </div>
        <div class="isi-surat">
            <p style="margin-bottom: 1rem;"><strong>SURAT KEPUTUSAN</strong><br>
                PIMPINAN RS AR-RASYID<br>
                TENTANG<br>
                PENERIMAAN / PENOLAKAN TENAGA KERJA</p>

            <p>Dengan ini diputuskan bahwa:</p>
            <table style="margin: 1rem 0; width: 100%;">
                <tr>
                    <td width="150">Nama</td>
                    <td>: <?= htmlspecialchars($data['pelamar']) ?></td>
                </tr>
                <tr>
                    <td>Posisi Dilamar</td>
                    <td>: <?= htmlspecialchars($data['lowongan']) ?></td>
                </tr>
                <tr>
                    <td>Tanggal Keputusan</td>
                    <td>: <?= date('d F Y') ?></td>
                </tr>
            </table>
            <p>Setelah melalui tahapan seleksi dan interview, dengan ini pimpinan menyatakan bahwa pelamar tersebut <strong><?= strtoupper($data['status_lamaran'] == 'diterima' ? 'DITERIMA' : 'DITOLAK') ?></strong> sebagai tenaga kerja di RS Ar-Rasyid.</p>
            <?php if (!empty($data['catatan_pimpinan'])): ?>
                <p>Catatan: <?= nl2br(htmlspecialchars($data['catatan_pimpinan'])) ?></p>
            <?php endif; ?>
            <p>Demikian surat keputusan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        <div class="ttd">
            <p>Kota Baru, <?= date('d F Y') ?></p>
            <p>Pimpinan RS Ar-Rasyid,</p>
            <br><br><br>
            <p><u>(<?= htmlspecialchars($_SESSION['nama']) ?>)</u></p>
        </div>
    </div>
    <div class="tombol-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Cetak Surat</button>
        <button onclick="window.close()">Tutup</button>
    </div>
</body>

</html>