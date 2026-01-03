<?php
require_once __DIR__ . '/config/database.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // Check if column exists
    $check = $conn->query("SHOW COLUMNS FROM events LIKE 'schedule'");
    if ($check->rowCount() == 0) {
        // Add column
        $conn->exec("ALTER TABLE events ADD COLUMN schedule JSON DEFAULT NULL");
        echo "Column 'schedule' added successfully.\n";
    } else {
        echo "Column 'schedule' already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
