function updateStatus(id, newStatus) {
    fetch('../assets/php/script/update_transfer_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: id, status: newStatus })
    }).then(res => res.json()).then(data => {
        alert(data.message);
        if (data.status === 'success') location.reload();
    });
}

function deleteTransfer(id) {
    if (confirm('Вы уверены, что хотите удалить этот трансфер?')) {
        fetch('../assets/php/script/delete_transfer.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id })
        }).then(res => res.json()).then(data => {
            alert(data.message);
            if (data.status === 'success') location.reload();
        });
    }
}

function showDetails(id) {
        fetch('../assets/php/script/get_transfer_details.php?id=' + id)
        .then(res => res.json())
        .then(response => {
            if (response.status === 'success') {
                const data = response.data;
                alert("Деталь: " + data.part_name + "\n" +
                    "Количество: " + data.quantity + "\n" +
                    "Отправитель: " + data.from_department + "\n" +
                    "Получатель: " + data.to_department + "\n" +
                    "Пользователь: " + data.user_name + "\n" +
                    "Статус: " + data.status);
            } else {
                alert("Ошибка: " + response.message);
            }
        });

}
