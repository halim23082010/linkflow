const loginForm = document.getElementById('authForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');

togglePassword.addEventListener('click', function () {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    this.classList.toggle('fa-eye-slash');
});

loginForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    fetch('/auth/login_register/login_system/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Login successful!');
                window.location.href = '/dashboard.html';
            } else {
                alert(data.message || 'Login failed.');
            }
        })
        .catch(error => {
            alert('An error occurred while logging in.');
            console.error(error);
        });
});

