  // Функция для выхода из системы
  function logout() {
    fetch("../assets/php/script/logout.php", {
        method: 'GET', // Запрос GET, чтобы выйти из системы
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json()) // Преобразуем ответ в JSON
    .then(data => {
        if (data.status === 'success') {
            // Перенаправляем на страницу логина
            window.location.href = "../pages/login.php";
        } else {
            alert('Ошибка выхода');
        }
    })
    .catch(error => {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при выходе');
    });
}