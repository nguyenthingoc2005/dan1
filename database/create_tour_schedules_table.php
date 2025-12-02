<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = require __DIR__ . '/../config/database.php';

    $sql = "CREATE TABLE IF NOT EXISTS tour_schedules (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        quota INT DEFAULT 20,
        booked INT DEFAULT 0,
        adult_price DECIMAL(15,2),
        child_price DECIMAL(15,2),
        infant_price DECIMAL(15,2),
        status ENUM('open', 'closed', 'completed', 'cancelled') DEFAULT 'open',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "Table 'tour_schedules' created successfully!";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
