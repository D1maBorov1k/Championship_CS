<?php include 'db_connect.php'; ?>

<h2>Delete Data</h2>
<form action="delete.php" method="post">
    <select name="table">
        <option value="">Виберіть таблицю</option>
        <option value="team">Team</option>
        <option value="match">Match</option>
        <option value="organizer">Organizer</option>
        <option value="player">Player</option>
        <option value="referee">Referee</option>
        <option value="result">Result</option>
        <option value="tournament">Tournament</option>
    </select>
    
    <input type="number" name="id" placeholder="ID запису" required>
    <input type="submit" value="Видалити">
</form>

<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data)); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $id = $_POST['id'];

    if (!empty($table) && !empty($id)) {
        // Обгортаємо назву таблиці в зворотні лапки
        $query = "DELETE FROM `$table` WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Запис успішно видалено!";
        } else {
            echo "Помилка видалення.";
        }
    } else {
        echo "Будь ласка, оберіть таблицю та введіть ID запису.";
    }
}
?>
