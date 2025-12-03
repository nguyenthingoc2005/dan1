<?php
/**
 * ==============================================================================
 * TOUR ASSIGNMENT MODEL
 * ==============================================================================
 */

class TourAssignment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }

    /**
     * Tạo bảng nếu chưa có
     */
    private function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS tour_assignments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tour_schedule_id INT NOT NULL,
            guide_id INT NOT NULL,
            status ENUM('assigned', 'confirmed', 'completed', 'cancelled') DEFAULT 'assigned',
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (tour_schedule_id) REFERENCES tour_schedules(id) ON DELETE CASCADE,
            FOREIGN KEY (guide_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_assignment (tour_schedule_id, guide_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->pdo->exec($sql);
    }

    /**
     * Phân công guide cho schedule
     */
    public function assign($schedule_id, $guide_id)
    {
        $sql = "INSERT INTO tour_assignments (tour_schedule_id, guide_id, assignment_date) 
                VALUES (:schedule_id, :guide_id, :assignment_date)
                ON DUPLICATE KEY UPDATE status = 'assigned', assignment_date = VALUES(assignment_date)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'schedule_id' => $schedule_id,
            'guide_id' => $guide_id,
            'assignment_date' => date('Y-m-d')
        ]);
    }

    /**
     * Lấy danh sách assignments theo schedule
     */
    public function getBySchedule($schedule_id)
    {
        $sql = "SELECT ta.*, u.full_name, u.phone, u.email 
                FROM tour_assignments ta
                JOIN users u ON ta.guide_id = u.id
                WHERE ta.tour_schedule_id = :schedule_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy assignment theo guide (Lịch làm việc của guide)
     */
    public function getByGuide($guide_id)
    {
        $sql = "SELECT ta.*, ts.start_date, ts.end_date, t.name as tour_name
                FROM tour_assignments ta
                JOIN tour_schedules ts ON ta.tour_schedule_id = ts.id
                JOIN tours t ON ts.tour_id = t.id
                WHERE ta.guide_id = :guide_id
                ORDER BY ts.start_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['guide_id' => $guide_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
