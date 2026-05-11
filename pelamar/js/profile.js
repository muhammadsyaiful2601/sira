// profile.js - Halaman Profil Pelamar

document.addEventListener('DOMContentLoaded', function() {
    // Toggle visibility password
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            }
        });
    });

    // Optional: Confirm before leaving if form has unsaved changes
    let formModified = false;
    const form = document.getElementById('profileForm');
    const formInputs = form.querySelectorAll('input, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', () => { formModified = true; });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formModified) {
            e.preventDefault();
            e.returnValue = 'Anda belum menyimpan perubahan. Yakin ingin keluar?';
            return e.returnValue;
        }
    });

    form.addEventListener('submit', function() {
        formModified = false;
    });

    // Auto-hide alert after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});