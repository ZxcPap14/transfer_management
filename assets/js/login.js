document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Отменяем стандартную отправку формы

    let username = document.getElementById('username').value;
    let password = document.getElementById('password').value;

    // Отправляем данные на сервер с помощью fetch
    fetch('auth_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.php'; // Редирект на главную страницу
        } else {
            document.getElementById('error-message').textContent = data.message;
        }
    })
    .catch(error => console.error('Ошибка:', error));})