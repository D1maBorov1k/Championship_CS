<?php include 'db_connect.php'; ?>

<h2>Insert Data</h2>
<form action="insert.php" method="post">
    <select name="table" id="insert-table-select" onchange="showFields(this.value)">
        <option value="">Виберіть таблицю</option>
        <option value="team">Team</option>
        <option value="match">Match</option>
        <option value="organizer">Organizer</option>
        <option value="player">Player</option>
        <option value="referee">Referee</option>
        <option value="result">Result</option>
        <option value="tournament">Tournament</option>
    </select>
    <div id="fields"></div>
    <input type="submit" value="Додати">
</form>

<script src="script.js"></script>

<?php
function validateInput($data) {
    foreach ($data as $key => $value) {
        if (empty($value)) {
            return false; 
        }
    }
    return true; 
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data)); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];

    if (!empty($table)) {
        // Збираємо дані
        $columns = [];
        $values = [];
        $placeholders = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'field_') === 0 && !empty($value)) {
                $column = substr($key, 6); // Отримуємо назву колонки
                if ($column !== 'id') { // Пропускаємо первинний ключ, якщо він є
                    $columns[] = $column;
                    $values[] = $value;
                    $placeholders[] = ":$column";
                }
            }
        }

        if (!empty($columns) && !empty($values)) {
            $columnsList = implode(', ', $columns);
            $placeholdersList = implode(', ', $placeholders);

            // Отримуємо максимальне значення id
            $maxIdQuery = "SELECT MAX(id) AS max_id FROM `$table`";
            $maxIdStmt = $db->prepare($maxIdQuery);
            $maxIdStmt->execute();
            $maxIdResult = $maxIdStmt->fetch(PDO::FETCH_ASSOC);

            $newId = isset($maxIdResult['max_id']) ? $maxIdResult['max_id'] + 1 : 1;

            // Додаємо новий запис з новим id
            $query = "INSERT INTO `$table` (id, $columnsList) VALUES (:id, $placeholdersList)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $newId, PDO::PARAM_INT);

            foreach ($columns as $index => $column) {
                if (strpos($column, 'date') !== false || strpos($column, 'Date') !== false) {
                    // Перетворюємо дату в формат Y-m-d
                    $stmt->bindValue(":$column", date('Y-m-d', strtotime($values[$index])));
                } elseif (strpos($column, 'time') !== false || strpos($column, 'Time') !== false) {
                    // Якщо поле типу час, зберігаємо значення без змін
                    $stmt->bindValue(":$column", $values[$index]);
                } else {
                    $stmt->bindValue(":$column", $values[$index]);
                }
            }

            try {
                if ($stmt->execute()) {
                    echo "Дані успішно додано до таблиці '$table' з id = $newId!";
                } else {
                    echo "Помилка: не вдалося додати дані.";
                }
            } catch (PDOException $e) {
                echo "Помилка SQL: " . $e->getMessage();
            }
        } else {
            echo "Будь ласка, заповніть усі необхідні поля.";
        }
    } else {
        echo "Будь ласка, виберіть таблицю.";
    }
}
?>
