-- Buat database
CREATE DATABASE IF NOT EXISTS sira_db;
USE sira_db;

-- Tabel users (admin, pimpinan, pelamar)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pimpinan', 'pelamar') NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel lowongan pekerjaan
CREATE TABLE lowongan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    kualifikasi TEXT,
    tanggal_posting DATE DEFAULT CURRENT_DATE,
    status ENUM('buka', 'tutup') DEFAULT 'buka'
);

-- Tabel lamaran (pelamar melamar lowongan)
CREATE TABLE lamaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pelamar_id INT(11) NOT NULL,
    lowongan_id INT(11) NOT NULL,
    cv_file VARCHAR(255),
    status_lamaran ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    tanggal_lamaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelamar_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lowongan_id) REFERENCES lowongan(id) ON DELETE CASCADE
);

-- Data contoh lowongan
INSERT INTO lowongan (judul, deskripsi, kualifikasi) VALUES
('Dokter Umum', 'Melayani pasien rawat jalan dan IGD', 'Memiliki STR, pengalaman minimal 1 tahun'),
('Perawat', 'Memberikan asuhan keperawatan', 'Lulusan D3 Keperawatan, memiliki SIK'),
('Administrasi Rumah Sakit', 'Mengelola data pasien dan administrasi', 'Lulusan D3/S1 Administrasi, menguasai MS Office');