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
     * 
     * @param array $filters ['tour_schedule_id', 'author_id', 'status']
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        $sql = "SELECT j.*, u.full_name as author_name, t.name as tour_name, ts.start_date, ts.end_date
                FROM tour_journals j
                JOIN users u ON j.author_id = u.id
                JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
                JOIN tours t ON ts.tour_id = t.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['tour_schedule_id'])) {
            $sql .= " AND j.tour_schedule_id = :tour_schedule_id";
            $params['tour_schedule_id'] = $filters['tour_schedule_id'];
        }

        if (!empty($filters['author_id'])) {
            $sql .= " AND j.author_id = :author_id";
            $params['author_id'] = $filters['author_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND j.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY j.created_at DESC";

        // Pagination
        if ($limit > 0) {
            $offset = ($page - 1) * $limit;
            $sql .= " LIMIT :offset, :limit";
            $params['offset'] = $offset;
            $params['limit'] = $limit;
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            if ($key == 'offset' || $key == 'limit') {
                $stmt->bindValue(':' . $key, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $val);
            }
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy journals theo tour_schedule_id (for guide)
     */
    public function getByScheduleId($schedule_id)
    {
        return $this->getAll(['tour_schedule_id' => $schedule_id], 1, 100);
    }

    /**
     * Lấy journals theo author_id (for guide)
     */
    public function getByAuthorId($author_id, $page = 1, $limit = 20)
    {
        return $this->getAll(['author_id' => $author_id], $page, $limit);
    }

    /**
     * Tạo journal mới
     * 
     * @param array $data
     * @return int Journal ID
     * @throws Exception nếu validation fail
     */
    public function create($data)
    {
        // 1. Validate required fields
        $required = ['tour_schedule_id', 'author_id', 'title', 'content'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Thiếu thông tin bắt buộc: $field");
            }
        }

        // 2. Validate guide is assigned to this schedule
        $scheduleSql = "SELECT guide_id, status, start_date FROM tour_schedules WHERE id = :schedule_id";
        $scheduleStmt = $this->pdo->prepare($scheduleSql);
        $scheduleStmt->execute(['schedule_id' => $data['tour_schedule_id']]);
        $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schedule) {
            throw new Exception("Tour schedule không tồn tại");
        }
        
        // Only allow if:
        // - Author is the assigned guide, OR
        // - Author is admin (for admin override)
        $isAdmin = false;
        if (isset($data['is_admin']) && $data['is_admin']) {
            $isAdmin = true;
        }
        
        if (!$isAdmin && $schedule['guide_id'] != $data['author_id']) {
            throw new Exception("Chỉ guide được phân công mới có thể viết nhật ký cho tour này");
        }
        
        // 3. Validate schedule status (only allow for ongoing or completed tours)
        // Tours can be written during or after the tour
        $today = date('Y-m-d');
        if ($schedule['status'] == 'cancelled') {
            throw new Exception("Không thể viết nhật ký cho tour đã bị hủy");
        }

        // 4. Handle images (JSON array)
        $images = [];
        if (!empty($data['images']) && is_array($data['images'])) {
            $images = $data['images'];
        } elseif (!empty($data['images']) && is_string($data['images'])) {
            // If it's already JSON string, decode it
            $decoded = json_decode($data['images'], true);
            $images = $decoded ?: [];
        }
        $imagesJson = !empty($images) ? json_encode($images) : null;

        // 5. Insert journal
        $sql = "INSERT INTO tour_journals (tour_schedule_id, author_id, title, content, images, status) 
                VALUES (:tour_schedule_id, :author_id, :title, :content, :images, :status)";

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            'tour_schedule_id' => $data['tour_schedule_id'],
            'author_id' => $data['author_id'],
            'title' => sanitize($data['title']),
            'content' => $data['content'], // Allow HTML for rich content
            'images' => $imagesJson,
            'status' => $data['status'] ?? 'draft'
        ]);

        if ($success) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
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
