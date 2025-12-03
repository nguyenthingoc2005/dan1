<?php
/**
 * ==============================================================================
 * JOURNAL MODEL
 * ==============================================================================
 */

class Journal
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
        $sql = "CREATE TABLE IF NOT EXISTS tour_journals (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tour_schedule_id INT NOT NULL,
            author_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT,
            images TEXT, -- JSON array of image paths
            status ENUM('draft', 'published') DEFAULT 'published',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tour_schedule_id) REFERENCES tour_schedules(id) ON DELETE CASCADE,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->pdo->exec($sql);
    }

    /**
     * Lấy danh sách journals
     */
    public function getAll()
    {
        $sql = "SELECT j.*, u.full_name as author_name, t.name as tour_name, ts.start_date
                FROM tour_journals j
                JOIN users u ON j.author_id = u.id
                JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
                JOIN tours t ON ts.tour_id = t.id
                ORDER BY j.created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo journal mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO tour_journals (tour_schedule_id, author_id, title, content, status) 
                VALUES (:tour_schedule_id, :author_id, :title, :content, :status)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'tour_schedule_id' => $data['tour_schedule_id'],
            'author_id' => $data['author_id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'status' => $data['status']
        ]);
    }

    /**
     * Lấy chi tiết journal
     */
    public function getById($id)
    {
        $sql = "SELECT j.*, u.full_name as author_name, t.name as tour_name, ts.start_date
                FROM tour_journals j
                JOIN users u ON j.author_id = u.id
                JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
                JOIN tours t ON ts.tour_id = t.id
                WHERE j.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
