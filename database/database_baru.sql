-- Buat database
CREATE DATABASE IF NOT EXISTS sira_db;
USE sira_db;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    no_hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pimpinan', 'pelamar') NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel lowongan
CREATE TABLE IF NOT EXISTS lowongan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi TEXT NOT NULL,
    kualifikasi TEXT,
    tanggal_posting TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('buka', 'tutup') DEFAULT 'buka'
) ENGINE=InnoDB;

-- Tabel lamaran
CREATE TABLE IF NOT EXISTS lamaran (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pelamar_id INT(11) NOT NULL,
    lowongan_id INT(11) NOT NULL,
    cv_file VARCHAR(255),
    status_lamaran ENUM('pending','verifikasi','interview','diterima','ditolak') DEFAULT 'pending',
    catatan_admin TEXT DEFAULT NULL,
    jadwal_interview DATETIME DEFAULT NULL,
    catatan_pimpinan TEXT DEFAULT NULL,
    tanggal_lamaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pelamar_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lowongan_id) REFERENCES lowongan(id) ON DELETE CASCADE,
    UNIQUE KEY unique_lamaran (pelamar_id, lowongan_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengaturan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL
) ENGINE=InnoDB;

-- Nilai default: tampilkan semua lowongan (0 atau NULL berarti tanpa batas)
INSERT INTO pengaturan (setting_key, setting_value) 
VALUES ('lowongan_limit_depan', '0')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);