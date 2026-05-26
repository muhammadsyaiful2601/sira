<?php
session_start();
require_once '../config/koneksi.php';

// Validasi dan ambil data lamaran
$id_lamaran = intval($_GET['id'] ?? 0);
if ($id_lamaran <= 0) {
    die("ID lamaran tidak valid.");
}

// Gunakan prepared statement untuk keamanan
$stmt = mysqli_prepare($conn, "SELECT l.*, u.nama as pelamar, u.email, u.no_hp, low.judul as lowongan 
                               FROM lamaran l 
                               JOIN users u ON l.pelamar_id = u.id 
                               JOIN lowongan low ON l.lowongan_id = low.id 
                               WHERE l.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_lamaran);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Data lamaran tidak ditemukan.");
}
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Hanya untuk status diterima/ditolak
if (!in_array($data['status_lamaran'], ['diterima', 'ditolak'])) {
    die("Surat keputusan hanya dapat dicetak untuk lamaran yang sudah diputus (diterima/ditolak).");
}
?>
<!DOCTYPE html>
<html lang="id">
<link rel="icon" href="../asset/image/icon/1.png" type="image/png">

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
            font-family: 'Times New Roman', Times, serif;
            background: #e9ecef;
            padding: 30px 20px;
        }

        /* Container surat */
        .surat-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px 35px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        /* ========= HEADER SURAT ========= */
        .header-surat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1e3c72;
            padding-bottom: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        /* Logo kiri */
        .logo-kiri {
            flex: 0 0 90px;
        }

        .logo-kiri img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* Nama institusi dan alamat (tengah) */
        .institusi {
            flex: 1;
            text-align: center;
            padding: 0 15px;
        }

        .institusi h1 {
            font-size: 26px;
            letter-spacing: 1px;
            color: #1e3c72;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .institusi h3 {
            font-size: 14px;
            font-weight: normal;
            color: #2c3e66;
            margin-bottom: 3px;
        }

        .institusi p {
            font-size: 12px;
            color: #4a627a;
        }

        /* Nomor surat (kanan) */
        .nomor-surat {
            flex: 0 0 auto;
            text-align: right;
            font-size: 13px;
            border: 1px solid #ccc;
            padding: 6px 12px;
            background: #f8f9fc;
            border-radius: 6px;
        }

        .nomor-surat p {
            margin: 0;
            font-style: normal;
            font-weight: 500;
        }

        /* ========= ISI SURAT ========= */
        .isi-surat {
            margin: 30px 0;
            line-height: 1.7;
            text-align: justify;
            font-size: 15px;
        }

        .isi-surat strong {
            color: #1e3c72;
        }

        .judul-keputusan {
            text-align: center;
            margin: 15px 0 20px;
        }

        .judul-keputusan p {
            font-weight: bold;
            font-size: 16px;
            letter-spacing: 1px;
        }

        table.info-pelamar {
            width: 100%;
            margin: 18px 0;
            border-collapse: collapse;
        }

        table.info-pelamar td {
            padding: 6px 8px;
            vertical-align: top;
            border-bottom: 1px dotted #ddd;
        }

        table.info-pelamar td:first-child {
            width: 160px;
            font-weight: 500;
        }

        .catatan {
            margin-top: 20px;
            background: #fef9e6;
            padding: 12px 16px;
            border-left: 4px solid #e6b422;
            font-style: italic;
        }

        /* Tanda tangan */
        .ttd {
            margin-top: 45px;
            text-align: right;
            font-size: 14px;
        }

        .ttd p {
            margin: 5px 0;
        }

        .ttd .garis-nama {
            margin-top: 50px;
            border-top: 1px solid #000;
            display: inline-block;
            width: 220px;
            text-align: center;
            padding-top: 5px;
        }

        /* Tombol cetak */
        .tombol-print {
            text-align: center;
            margin-top: 35px;
        }

        .tombol-print button {
            padding: 8px 20px;
            background: #1e3c72;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            border-radius: 30px;
            margin: 0 8px;
            transition: 0.2s;
            font-family: inherit;
        }

        .tombol-print button:hover {
            background: #0f2a4a;
        }

        /* Print styling */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .surat-container {
                box-shadow: none;
                padding: 0.8cm 1.2cm;
                max-width: 100%;
            }

            .tombol-print {
                display: none;
            }

            .header-surat {
                border-bottom-width: 2px;
            }

            .logo-kiri img {
                max-width: 65px;
            }

            .institusi h1 {
                font-size: 22px;
            }
        }

        /* Responsif */
        @media (max-width: 650px) {
            .header-surat {
                flex-direction: column;
                text-align: center;
            }

            .logo-kiri {
                margin-bottom: 10px;
            }

            .nomor-surat {
                margin-top: 12px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="surat-container">
        <!-- Header dengan logo kiri -->
        <div class="header-surat">
            <div class="logo-kiri">
                <img src="../asset/image/icon/sira.png" alt="Logo RS Ar-Rasyid" onerror="this.src='../asset/image/icon/sira-icon.png'">
            </div>
            <div class="institusi">
                <h1>RS AR-RASYID</h1>
                <h3>Jalan Kesehatan No. 1, Kota Baru</h3>
                <p>Telp. (021) 1234567 | Email: rs@arasyid.id</p>
            </div>
            <div class="nomor-surat">
                <p>Nomor: 800/SK-RS/<?= date('m') ?>/<?= date('Y') ?></p>
            </div>
        </div>

        <!-- Isi Surat -->
        <div class="isi-surat">
            <div class="judul-keputusan">
                <p><strong>SURAT KEPUTUSAN</strong><br>
                    PIMPINAN RS AR-RASYID<br>
                    TENTANG<br>
                    PENERIMAAN / PENOLAKAN TENAGA KERJA</p>
            </div>

            <p>Dengan ini diputuskan bahwa:</p>

            <table class="info-pelamar">
                <tr>
                    <td>Nama</td>
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

            <p>Setelah melalui tahapan seleksi dan wawancara, dengan ini pimpinan menyatakan bahwa pelamar tersebut <strong><?= strtoupper($data['status_lamaran'] == 'diterima' ? 'DITERIMA' : 'DITOLAK') ?></strong> sebagai tenaga kerja di RS Ar-Rasyid.</p>

            <?php if (!empty($data['catatan_pimpinan'])): ?>
                <div class="catatan">
                    <strong>Catatan Pimpinan:</strong><br>
                    <?= nl2br(htmlspecialchars($data['catatan_pimpinan'])) ?>
                </div>
            <?php endif; ?>

            <p style="margin-top: 25px;">Demikian surat keputusan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>

        <!-- Tanda tangan -->
        <div class="ttd">
            <p>Kota Baru, <?= date('d F Y') ?></p>
            <p>Pimpinan RS Ar-Rasyid,</p>
            <br><br><br>
            <div class="garis-nama">( <?= htmlspecialchars($_SESSION['nama'] ?? 'Pimpinan') ?> )</div>
        </div>
    </div>

    <div class="tombol-print">
        <button onclick="window.print()">🖨 Cetak Surat</button>
        <button onclick="window.close()">✖ Tutup</button>
    </div>
</body>

</html>