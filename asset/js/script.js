// asset/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    
    // Ambil semua tautan internal yang menunjuk ke elemen dengan ID
    const internalLinks = document.querySelectorAll('a[href^="#"]');

    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const hash = this.getAttribute('href');
            
            // Abaikan jika hanya '#' atau kosong
            if (hash === '#' || hash === '') return;

            // Ambil target element berdasarkan ID (hilangkan tanda '#')
            const targetId = hash.substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault(); // Mencegah lompatan default browser

                // Hitung tinggi navbar (jika ada)
                const navbarHeight = navbar ? navbar.offsetHeight : 0;
                // Posisi scroll target dikurangi tinggi navbar
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navbarHeight;

                // Lakukan scroll halus
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });

                // Perbarui URL hash tanpa memicu lompatan (opsional)
                history.pushState(null, null, hash);
            }
        });
    });
});