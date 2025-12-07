<?php
/**
 * ==============================================================================
 * JOURNAL MODEL
 * ==============================================================================
 * 
 * Quản lý nhật ký tour của hướng dẫn viên
 * Sử dụng bảng: journals, journal_images
 * 
 * @version 2.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Journal
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách journals
     * 
     * @param array $filters ['guide_id', 'booking_id', 'journal_date']
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        $sql = "SELECT j.*, 
                       b.booking_code, b.start_date as booking_start_date,
                       t.name as tour_name, t.tour_code, t.id as tour_id, 
                       t.duration_days, t.duration_nights, t.departure_location,
                       ts.id as schedule_id, ts.start_date as schedule_start_date, 
                       ts.start_date as start_date,
                       ts.end_date as schedule_end_date, ts.status as schedule_status,
                       u.full_name as guide_name, u.full_name as author_name
                FROM journals j
                LEFT JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
                LEFT JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON j.booking_id = b.id
                LEFT JOIN users u ON j.guide_id = u.id
                WHERE 1=1";
        
        $params = [];

        if (!empty($filters['guide_id'])) {
            $sql .= " AND j.guide_id = :guide_id";
            $params['guide_id'] = $filters['guide_id'];
        }

        if (!empty($filters['booking_id'])) {
            $sql .= " AND j.booking_id = :booking_id";
            $params['booking_id'] = $filters['booking_id'];
        }

        if (!empty($filters['journal_date'])) {
            $sql .= " AND j.journal_date = :journal_date";
            $params['journal_date'] = $filters['journal_date'];
        }

        if (!empty($filters['tour_schedule_id'])) {
            // Filter by schedule (direct join)
            $sql .= " AND j.tour_schedule_id = :schedule_id";
            $params['schedule_id'] = $filters['tour_schedule_id'];
        }

        // Filter by schedule status (for admin: only completed or ongoing tours)
        if (!empty($filters['schedule_status'])) {
            if (is_array($filters['schedule_status'])) {
                $status_placeholders = [];
                foreach ($filters['schedule_status'] as $idx => $status) {
                    $key = 'schedule_status_' . $idx;
                    $status_placeholders[] = ":{$key}";
                    $params[$key] = $status;
                }
                $sql .= " AND ts.status IN (" . implode(', ', $status_placeholders) . ")";
            } else {
                $sql .= " AND ts.status = :schedule_status";
                $params['schedule_status'] = $filters['schedule_status'];
            }
        }

        // Filter for ongoing tours (closed status and current date between start_date and end_date)
        if (!empty($filters['only_ongoing_or_completed'])) {
            // Use CURDATE() instead of parameter to avoid binding same parameter twice
            $sql .= " AND (ts.status = 'completed' OR (ts.status = 'closed' AND CURDATE() >= ts.start_date AND CURDATE() <= ts.end_date))";
        }

        $sql .= " ORDER BY j.journal_date DESC, j.created_at DESC";

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
     * Lấy chi tiết journal
     */
    public function getById($id)
    {
        $sql = "SELECT j.*, 
                       b.booking_code, b.start_date as booking_start_date,
                       t.name as tour_name, t.tour_code, t.id as tour_id,
                       t.duration_days, t.duration_nights, t.departure_location,
                       ts.id as schedule_id, ts.start_date as schedule_start_date,
                       ts.start_date as start_date,
                       ts.end_date as schedule_end_date, ts.status as schedule_status,
                       u.full_name as guide_name, u.full_name as author_name
                FROM journals j
                LEFT JOIN tour_schedules ts ON j.tour_schedule_id = ts.id
                LEFT JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON j.booking_id = b.id
                LEFT JOIN users u ON j.guide_id = u.id
                WHERE j.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $required = ['tour_schedule_id', 'guide_id', 'journal_date', 'title', 'content'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Thiếu thông tin bắt buộc: $field");
            }
        }

        // 2. Validate schedule belongs to guide
        $scheduleSql = "SELECT ts.*, t.id as tour_id 
                       FROM tour_schedules ts
                       JOIN tours t ON ts.tour_id = t.id
                       WHERE ts.id = :schedule_id";
        $scheduleStmt = $this->pdo->prepare($scheduleSql);
        $scheduleStmt->execute(['schedule_id' => $data['tour_schedule_id']]);
        $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schedule) {
            throw new Exception("Lịch tour không tồn tại");
        }
        
        // Check if guide is assigned to this schedule
        if (empty($schedule['guide_id']) || $schedule['guide_id'] != $data['guide_id']) {
            throw new Exception("Bạn không được phân công tour này");
        }

        // 3. Insert journal (booking_id có thể NULL)
        $sql = "INSERT INTO journals (tour_schedule_id, booking_id, guide_id, journal_date, day_number, title, content, weather, highlights, issues) 
                VALUES (:tour_schedule_id, :booking_id, :guide_id, :journal_date, :day_number, :title, :content, :weather, :highlights, :issues)";

        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([
            'tour_schedule_id' => $data['tour_schedule_id'],
            'booking_id' => $data['booking_id'] ?? null,
            'guide_id' => $data['guide_id'],
            'journal_date' => $data['journal_date'],
            'day_number' => !empty($data['day_number']) ? (int) $data['day_number'] : null,
            'title' => sanitize($data['title']),
            'content' => $data['content'], // Allow HTML
            'weather' => !empty($data['weather']) ? sanitize($data['weather']) : null,
            'highlights' => !empty($data['highlights']) ? $data['highlights'] : null,
            'issues' => !empty($data['issues']) ? $data['issues'] : null
        ]);

        if ($success) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }

    /**
     * Cập nhật journal
     */
    public function update($id, $data)
    {
        // Validate guide ownership
        $journal = $this->getById($id);
        if (!$journal || $journal['guide_id'] != $data['guide_id']) {
            throw new Exception("Bạn không có quyền sửa nhật ký này");
        }

        $sql = "UPDATE journals 
                SET journal_date = :journal_date, 
                    day_number = :day_number,
                    title = :title, 
                    content = :content, 
                    weather = :weather,
                    highlights = :highlights,
                    issues = :issues,
                    updated_at = NOW()
                WHERE id = :id AND guide_id = :guide_id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'guide_id' => $data['guide_id'],
            'journal_date' => $data['journal_date'],
            'day_number' => !empty($data['day_number']) ? (int) $data['day_number'] : null,
            'title' => sanitize($data['title']),
            'content' => $data['content'],
            'weather' => !empty($data['weather']) ? sanitize($data['weather']) : null,
            'highlights' => !empty($data['highlights']) ? $data['highlights'] : null,
            'issues' => !empty($data['issues']) ? $data['issues'] : null
        ]);
    }

    /**
     * Xóa journal
     */
    public function delete($id, $guide_id)
    {
        // Validate guide ownership
        $journal = $this->getById($id);
        if (!$journal || $journal['guide_id'] != $guide_id) {
            throw new Exception("Bạn không có quyền xóa nhật ký này");
        }

        // Delete images first (will be auto-deleted by CASCADE, but we do it explicitly to clean up files)
        $images = $this->getImages($id);
        foreach ($images as $img) {
            $file_path = PUBLIC_PATH . '/' . $img['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $sql = "DELETE FROM journals WHERE id = :id AND guide_id = :guide_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id, 'guide_id' => $guide_id]);
    }

    /**
     * Lấy images của journal
     */
    public function getImages($journal_id)
    {
        $sql = "SELECT * FROM journal_images 
                WHERE journal_id = :journal_id 
                ORDER BY display_order ASC, id ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['journal_id' => $journal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm image vào journal
     */
    public function addImage($journal_id, $image_url, $caption = null, $display_order = 0)
    {
        $sql = "INSERT INTO journal_images (journal_id, image_url, caption, display_order) 
                VALUES (:journal_id, :image_url, :caption, :display_order)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'journal_id' => $journal_id,
            'image_url' => $image_url,
            'caption' => $caption,
            'display_order' => (int) $display_order
        ]);
    }

    /**
     * Xóa image
     */
    public function deleteImage($image_id)
    {
        // Get image info before deleting
        $sql = "SELECT image_url FROM journal_images WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            // Delete file
            $file_path = PUBLIC_PATH . '/' . $image['image_url'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // Delete from database
        $sql = "DELETE FROM journal_images WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $image_id]);
    }

    /**
     * Xóa tất cả images của journal
     */
    public function deleteAllImages($journal_id)
    {
        $images = $this->getImages($journal_id);
        foreach ($images as $img) {
            $this->deleteImage($img['id']);
        }
    }
}
