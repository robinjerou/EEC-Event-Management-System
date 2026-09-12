document.getElementById('login').addEventListener('submit', function(e) {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    let valid = true;

    if (!validateEmail(email.value)) {
        setError(email, 'Invalid email address');
        valid = false;
    } else {
        setSuccess(email);
    }

    if (password.value.length < 6) {
        setError(password, 'Password must be at least 6 characters');
        valid = false;
    } else {
        setSuccess(password);
    }

    if (!valid) {
        e.preventDefault();
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function setError(element, message) {
    const parent = element.parentElement;
    parent.classList.add('error');
    parent.querySelector('small').innerText = message;
}

function setSuccess(element) {
    const parent = element.parentElement;
    parent.classList.remove('error');
    parent.querySelector('small').innerText = '';
}
