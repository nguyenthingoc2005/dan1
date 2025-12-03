<?php
require_once 'config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM tour_schedules LIKE 'guide_id'");
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE tour_schedules 
                ADD COLUMN guide_id INT NULL AFTER status,
                ADD COLUMN guide_notes TEXT NULL AFTER guide_id,
                ADD CONSTRAINT fk_schedule_guide FOREIGN KEY (guide_id) REFERENCES users(id) ON DELETE SET NULL";

        $pdo->exec($sql);
        echo "Successfully added guide_id and guide_notes to tour_schedules.\n";
    } else {
        echo "Columns already exist.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
