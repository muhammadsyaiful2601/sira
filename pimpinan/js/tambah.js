// Validasi frontend sederhana dan efek interaktif
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formTambahLowongan');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value.trim();
            const deskripsi = document.getElementById('deskripsi').value.trim();
            const kualifikasi = document.getElementById('kualifikasi').value.trim();
            
            if (judul === '' || deskripsi === '' || kualifikasi === '') {
                e.preventDefault();
                alert('Semua bidang harus diisi!');
                return false;
            }
            
            if (judul.length < 5) {
                e.preventDefault();
                alert('Judul minimal 5 karakter.');
                return false;
            }
            
            // Konfirmasi sebelum submit
            return confirm('Pastikan data sudah benar. Simpan lowongan ini?');
        });
    }
    
    // Efek auto-resize textarea (opsional)
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(ta => {
        ta.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});