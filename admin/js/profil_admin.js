// Profil Admin JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                if (icon) {
                    if (type === 'text') {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            }
        });
    });

    // Validasi form ganti password (client-side)
    const changePasswordForm = document.getElementById('formChangePassword');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                showAlert('Password baru dan konfirmasi tidak cocok.', 'error');
            } else if (newPassword.length < 6) {
                e.preventDefault();
                showAlert('Password baru minimal 6 karakter.', 'error');
            }
        });
    }

    // Auto hide alert after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) alert.remove();
            }, 300);
        }, 5000);
    });

    // Fungsi untuk menampilkan alert dinamis (optional)
    function showAlert(message, type) {
        const mainContent = document.querySelector('.main-content');
        if (!mainContent) return;
        
        const existingAlert = mainContent.querySelector('.alert');
        if (existingAlert) existingAlert.remove();
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
        
        const pageHeader = mainContent.querySelector('.page-header');
        if (pageHeader) {
            pageHeader.insertAdjacentElement('afterend', alertDiv);
        } else {
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
        }
        
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }

    // Konfirmasi sebelum menyimpan perubahan password
    const passwordSubmitBtn = changePasswordForm?.querySelector('button[type="submit"]');
    if (passwordSubmitBtn) {
        passwordSubmitBtn.addEventListener('click', function(e) {
            const currentPass = document.getElementById('current_password')?.value;
            if (currentPass && currentPass.length > 0) {
                const confirmMsg = confirm('Apakah Anda yakin ingin mengubah password? Anda akan menggunakan password baru untuk login berikutnya.');
                if (!confirmMsg) {
                    e.preventDefault();
                }
            }
        });
    }
});