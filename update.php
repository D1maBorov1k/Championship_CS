<?php include 'db_connect.php'; ?>

<h2>Оновлення даних</h2>
<form action="update.php" method="post">
    <select name="table" onchange="this.form.submit()" required>
        <option value="">Виберіть таблицю</option>
        <option value="team">Team</option>
        <option value="match">Match</option>
        <option value="organizer">Organizer</option>
        <option value="player">Player</option>
        <option value="referee">Referee</option>
        <option value="result">Result</option>
        <option value="tournament">Tournament</option>
    </select>
</form>

<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data)); // Видаляємо HTML-теги та спеціальні символи
}

if (isset($_POST['table'])) {
    $table = $_POST['table'];
    try {
        $query = "SELECT * FROM `$table`"; // Обгортаємо назву таблиці в зворотні лапки
        $stmt = $db->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            echo "<table border='1'>";
            echo "<tr>";
            foreach (array_keys($rows[0]) as $column) {
                echo "<th>$column</th>";
            }
            echo "<th>Дії</th>"; // Додатковий стовпець для кнопок редагування
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>$value</td>";
                }
                echo "<td><button onclick='editRow(" . json_encode($row) . ")'>Редагувати</button></td>"; // Кнопка редагування
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Таблиця $table порожня.";
        }
    } catch (PDOException $e) {
        echo "Помилка: " . $e->getMessage();
    }
}
?>

<div id="edit-form" style="display:none;">
    <h2>Редагувати дані</h2>
    <form id="update-data-form" method="post">
        <input type="hidden" name="table" id="edit-table">
        <input type="hidden" name="id" id="edit-id">
        <div id="edit-fields"></div>
        <input type="submit" value="Оновити">
    </form>
</div>

<script>
function editRow(rowData) {
    document.getElementById('edit-table').value = "<?php echo isset($_POST['table']) ? $_POST['table'] : ''; ?>"; // Встановлюємо таблицю
    document.getElementById('edit-id').value = rowData.id; // Встановлюємо ID

    const editFieldsContainer = document.getElementById('edit-fields');
    editFieldsContainer.innerHTML = ''; // Очищаємо попередні поля

    for (const [key, value] of Object.entries(rowData)) {
        if (key !== 'id') { // Пропускаємо ID
            const label = document.createElement('label');
            label.textContent = key;

            const input = document.createElement('input');
            input.type = 'text';
            input.name = key;
            input.value = value; // Встановлюємо значення поля
            input.placeholder = `Введіть ${key}`;

            editFieldsContainer.appendChild(label);
            editFieldsContainer.appendChild(input);
            editFieldsContainer.appendChild(document.createElement('br'));
        }
    }

    document.getElementById('edit-form').style.display = 'block'; // Показуємо форму редагування
}
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $table = $_POST['table'];
    $id = $_POST['id'];
    $updateFields = [];

    foreach ($_POST as $key => $value) {
        if ($key !== 'table' && $key !== 'id') {
            $updateFields[] = "$key = :$key"; // Формуємо масив для оновлення
        }
    }

    if (!empty($updateFields)) {
        $updateQuery = "UPDATE `$table` SET " . implode(', ', $updateFields) . " WHERE id = :id";
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Прив'язуємо ID

        // Прив'язуємо значення для кожного поля
        foreach ($_POST as $key => $value) {
            if ($key !== 'table' && $key !== 'id') {
                $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
            }
        }

        try {
            if ($stmt->execute()) {
                echo "Запис з ID $id у таблиці $table успішно оновлено.";
            } else {
                echo "Помилка оновлення даних.";
            }
        } catch (PDOException $e) {
            echo "Помилка: " . $e->getMessage();
        }
    } else {
        echo "Будь ласка, заповніть усі поля.";
    }
}
?>
