// Konfirmasi hapus lowongan (jika nanti ditambahkan), dan efek interaktif sederhana
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan efek smooth alert untuk tombol aksi (contoh)
    const formKeputusan = document.querySelectorAll('.form-keputusan');
    formKeputusan.forEach(form => {
        form.addEventListener('submit', function(e) {
            const btnClicked = e.submitter;
            if (btnClicked && btnClicked.value === 'tolak') {
                if (!confirm('Yakin menolak lamaran ini? Keputusan ini final.')) {
                    e.preventDefault();
                }
            } else if (btnClicked && btnClicked.value === 'terima') {
                if (!confirm('Terima pelamar ini? Data akan tersimpan.'));
            }
        });
    });

    // Validasi form tambah admin (sederhana)
    const adminForm = document.querySelector('.form-admin');
    if (adminForm) {
        adminForm.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]');
            if (password && password.value.length < 6) {
                alert('Password minimal 6 karakter!');
                e.preventDefault();
            }
        });
    }

    // Tooltip interaktif (opsional)
    console.log("Dashboard Pimpinan siap");
});

document.addEventListener('DOMContentLoaded', function() {
    // Toggle Form Tambah Admin
    const toggleBtn = document.getElementById('toggleAdminFormBtn');
    const formContainer = document.getElementById('adminFormContainer');
    if (toggleBtn && formContainer) {
        toggleBtn.addEventListener('click', function() {
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-times"></i> Tutup Form';
            } else {
                formContainer.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Tambah Admin';
            }
        });
    }

    // Konfirmasi hapus lowongan (akan mengecek via alert sebelum redirect)
    window.confirmHapus = function(event, url) {
        event.preventDefault();
        if (confirm('Apakah Anda yakin ingin menghapus lowongan ini?\n\nPERINGATAN: Lowongan hanya bisa dihapus jika belum ada pelamar yang diterima.')) {
            window.location.href = url;
        }
        return false;
    };

    // Konfirmasi untuk keputusan tolak/terima (opsional, sudah ada sebelumnya)
    const formKeputusan = document.querySelectorAll('.form-keputusan');
    formKeputusan.forEach(form => {
        form.addEventListener('submit', function(e) {
            const btnClicked = e.submitter;
            if (btnClicked && btnClicked.value === 'tolak') {
                if (!confirm('Yakin menolak lamaran ini? Keputusan ini final.')) {
                    e.preventDefault();
                }
            } else if (btnClicked && btnClicked.value === 'terima') {
                if (!confirm('Terima pelamar ini? Data akan tersimpan.')) {
                    e.preventDefault();
                }
            }
        });
    });

    // Validasi password form admin
    const adminForm = document.querySelector('.form-admin');
    if (adminForm) {
        adminForm.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]');
            if (password && password.value.length < 6) {
                alert('Password minimal 6 karakter!');
                e.preventDefault();
            }
        });
    }
});