// Login page interactions (toggle password, client validation, remember me)
(function() {
    const togglePwdBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    if (togglePwdBtn && passwordInput) {
        togglePwdBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye-slash');
                icon.classList.toggle('fa-eye');
            }
        });
    }

    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const clientErrorDiv = document.getElementById('client-error');
    const clientErrorMsg = document.getElementById('client-error-msg');
    const serverErrorDiv = document.getElementById('server-error');

    function hideServerError() {
        if (serverErrorDiv) serverErrorDiv.style.display = 'none';
    }
    if (emailInput) emailInput.addEventListener('input', hideServerError);
    if (passwordInput) passwordInput.addEventListener('input', hideServerError);

    if (form) {
        form.addEventListener('submit', function(e) {
            let errorMessage = '';
            const email = emailInput ? emailInput.value.trim() : '';
            const password = passwordInput ? passwordInput.value : '';

            if (!email) {
                errorMessage = 'Email wajib diisi.';
            } 
            // REGEX BARU: mendukung subdomain dan multi-level TLD (contoh: nama@domain.co.id)
            else if (!/^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(email)) {
                errorMessage = 'Masukkan alamat email yang valid.';
            } 
            else if (!password) {
                errorMessage = 'Kata sandi tidak boleh kosong.';
            }

            if (errorMessage) {
                e.preventDefault();
                if (clientErrorDiv && clientErrorMsg) {
                    clientErrorMsg.innerText = errorMessage;
                    clientErrorDiv.style.display = 'flex';
                }
                if (clientErrorDiv) clientErrorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                const btn = document.getElementById('btnLogin');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Memproses...';
                    btn.disabled = true;
                }
            }
        });
    }

    // Remember me (simpan email di localStorage)
    const rememberCheckbox = document.getElementById('remember');
    if (rememberCheckbox && localStorage.getItem('rememberEmail')) {
        const savedEmail = localStorage.getItem('rememberEmail');
        if (emailInput && savedEmail) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
            // trigger floating label
            emailInput.dispatchEvent(new Event('input'));
        }
    }

    if (rememberCheckbox && emailInput) {
        rememberCheckbox.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('rememberEmail', emailInput.value.trim());
            } else {
                localStorage.removeItem('rememberEmail');
            }
        });
        emailInput.addEventListener('change', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('rememberEmail', emailInput.value.trim());
            }
        });
    }
})();