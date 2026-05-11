// admin/js/detail.js
document.addEventListener('DOMContentLoaded', function() {
    // Validasi form penerimaan (jadwal interview wajib)
    const btnTerima = document.getElementById('btnTerima');
    if (btnTerima) {
        btnTerima.addEventListener('click', function(e) {
            const jadwalInput = document.getElementById('jadwal_interview');
            if (jadwalInput && !jadwalInput.value) {
                e.preventDefault();
                alert('Harap isi jadwal interview terlebih dahulu!');
                return false;
            }
            if (!confirm('Verifikasi lamaran ini dan jadwalkan interview? Status akan menjadi INTERVIEW dan diteruskan ke pimpinan.')) {
                e.preventDefault();
            }
        });
    }

    // Konfirmasi penolakan
    const btnTolak = document.getElementById('btnTolak');
    if (btnTolak) {
        btnTolak.addEventListener('click', function(e) {
            if (!confirm('Anda yakin ingin MENOLAK lamaran ini? Pelamar akan diberitahu.')) {
                e.preventDefault();
            }
        });
    }

    // Opsional: tambahan validasi jika ada elemen lain (misal preview file)
    const catatanField = document.getElementById('catatan_admin');
    if (catatanField) {
        // hanya contoh, tidak ada validasi khusus
    }
});