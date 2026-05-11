document.addEventListener('DOMContentLoaded', function() {
    // ===== Dynamic Footer Year =====
    const yearSpan = document.getElementById('currentYear');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // ===== Mobile Menu Toggle =====
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileBtn && navLinks) {
        mobileBtn.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });
    }

    // ===== Copy Interview Schedule =====
    const copyBtn = document.getElementById('copyScheduleBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const scheduleText = this.getAttribute('data-schedule');
            if (scheduleText) {
                navigator.clipboard.writeText(scheduleText + ' WIB').then(() => {
                    // Temporary tooltip feedback
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Tersalin!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 1500);
                }).catch(err => {
                    console.error('Gagal menyalin: ', err);
                    alert('Gagal menyalin jadwal, silakan salin manual.');
                });
            }
        });
    }

    // ===== Optional: Tambahkan efek smooth pada link internal =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });

    // ===== Notifikasi Ringan untuk Demo / Info Status =====
    const statusCard = document.querySelector('.status-card');
    if (statusCard && statusCard.getAttribute('data-status') === 'interview') {
        console.log('📅 Segera persiapkan diri Anda untuk interview!');
    }
});