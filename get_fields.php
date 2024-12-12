<?php
include 'db_connect.php';

if (isset($_GET['table'])) {
    $table = $_GET['table'];
    try {
        $columnsQuery = $db->query("DESCRIBE `$table`");
        $columns = $columnsQuery->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($columns);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
