const registerForm = document.getElementById('registerForm');
const firstNameInput = document.getElementById('firstName');
const lastNameInput = document.getElementById('lastName');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmPassword');
const togglePassword = document.getElementById('togglePassword');
const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
const acceptTerms = document.getElementById('acceptTerms');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});

toggleConfirmPassword.addEventListener('click', function () {
    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPasswordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye-slash');
});

[firstNameInput, lastNameInput, emailInput, passwordInput].forEach(input => {
    input.addEventListener('input', function () {
        if (this.validity.valid) {
            this.classList.remove('is-invalid');
        }
    });
});

passwordInput.addEventListener('input', function () {
    if (confirmPasswordInput.value && this.value !== confirmPasswordInput.value) {
        confirmPasswordInput.classList.add('is-invalid');
    } else {
        confirmPasswordInput.classList.remove('is-invalid');
    }
});

confirmPasswordInput.addEventListener('input', function () {
    if (this.value === passwordInput.value) {
        this.classList.remove('is-invalid');
    }
});

registerForm.addEventListener('submit', function (e) {
    e.preventDefault();

    let isValid = true;

    if (!firstNameInput.value) {
        firstNameInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!lastNameInput.value) {
        lastNameInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!emailInput.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        emailInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!passwordInput.value || passwordInput.value.length < 8) {
        passwordInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!confirmPasswordInput.value || confirmPasswordInput.value !== passwordInput.value) {
        confirmPasswordInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!acceptTerms.checked) {
        acceptTerms.parentElement.style.color = 'var(--error-500)';
        isValid = false;
    } else {
        acceptTerms.parentElement.style.color = '';
    }

    if (isValid) {
        registerForm.submit(); // 🔧 PHP'ye POST ile gönder
    }
});

document.querySelector('.btn-google').addEventListener('click', function () {
    alert('Google registration not available.');
});

document.querySelector('.btn-apple').addEventListener('click', function () {
    alert('Apple registration not available.');
});

document.querySelector('.btn-facebook').addEventListener('click', function () {
    alert('Facebook registration not available.');
});

    