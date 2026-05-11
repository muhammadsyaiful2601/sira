document.addEventListener('DOMContentLoaded', function() {
    // Contoh: konfirmasi logout jika ada link logout dengan class tertentu
    const logoutBtn = document.querySelector('.btn-login[href="../logout.php"]');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin logout?')) {
                e.preventDefault();
            }
        });
    }
});