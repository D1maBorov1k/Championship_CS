<?php include 'db_connect.php'; ?>

<h2>Перегляд даних</h2>
<form action="view.php" method="get">
    <select name="table">
        <option value="team">Team</option>
        <option value="match">Match</option>
        <option value="organizer">Organizer</option>
        <option value="player">Player</option>
        <option value="referee">Referee</option>
        <option value="result">Result</option>
        <option value="tournament">Tournament</option>
    </select>
    <input type="submit" value="Переглянути">
</form>

<?php
if (isset($_GET['table'])) {
    $table = $_GET['table'];
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
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>$value</td>";
                }
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
