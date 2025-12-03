<?php
// Hardcode credentials for this script
$db_host = 'localhost';
$db_name = 'tour_management';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Checking tour_assignments table...\n";

    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM tour_assignments LIKE 'tour_schedule_id'");
    $col = $stmt->fetch();

    if (!$col) {
        echo "Adding tour_schedule_id column...\n";
        // Add column
        $sql = "ALTER TABLE tour_assignments 
                ADD COLUMN tour_schedule_id INT NULL AFTER id,
                ADD INDEX idx_tour_assignments_schedule (tour_schedule_id)";
        $pdo->exec($sql);
        echo "Column added.\n";

        // Make booking_id nullable if it exists
        $stmt = $pdo->query("SHOW COLUMNS FROM tour_assignments LIKE 'booking_id'");
        if ($stmt->fetch()) {
            $sql = "ALTER TABLE tour_assignments MODIFY COLUMN booking_id INT NULL";
            $pdo->exec($sql);
            echo "booking_id made nullable.\n";
        }

    } else {
        echo "Column tour_schedule_id already exists.\n";
    }

    echo "Database schema updated successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
