<?php
class TourSchedule
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($filters = [], $page = 1, $limit = 10)
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['tour_id'])) {
            $where[] = "ts.tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['start_date'])) {
            $where[] = "ts.start_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $where[] = "ts.start_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['guide_id'])) {
            $where[] = "ts.guide_id = :guide_id";
            $params['guide_id'] = $filters['guide_id'];
        }

        if (!empty($filters['status'])) {
            // Support multiple statuses (comma-separated)
            if (strpos($filters['status'], ',') !== false) {
                $statuses = explode(',', $filters['status']);
                $status_placeholders = [];
                foreach ($statuses as $idx => $status) {
                    $key = 'status_' . $idx;
                    $status_placeholders[] = ":{$key}";
                    $params[$key] = trim($status);
                }
                $where[] = "ts.status IN (" . implode(', ', $status_placeholders) . ")";
            } else {
                $where[] = "ts.status = :status";
                $params['status'] = $filters['status'];
            }
        }

        if (!empty($filters['category_id'])) {
            $where[] = "t.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM tour_schedules ts WHERE " . implode(" AND ", $where);
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Get Data
        $offset = ($page - 1) * $limit;
        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code, t.duration_days, t.duration_nights,
                       t.departure_location, t.category_id, c.name as category_name,
                       u.full_name as guide_name, u.phone as guide_phone, u.email as guide_email
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON ts.guide_id = u.id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY ts.start_date ASC
                LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    public function count($filters = [])
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['tour_id'])) {
            $where[] = "tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        $sql = "SELECT COUNT(*) FROM tour_schedules ts WHERE " . implode(" AND ", $where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data)
    {
        $sql = "INSERT INTO tour_schedules (tour_id, start_date, end_date, quota, adult_price, child_price, infant_price, guide_id, guide_notes)
                VALUES (:tour_id, :start_date, :end_date, :quota, :adult_price, :child_price, :infant_price, :guide_id, :guide_notes)";
        $stmt = $this->pdo->prepare($sql);
        
        // Ensure guide_id and guide_notes are set (can be null)
        $params = [
            'tour_id' => $data['tour_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'quota' => $data['quota'],
            'adult_price' => $data['adult_price'],
            'child_price' => $data['child_price'],
            'infant_price' => $data['infant_price'],
            'guide_id' => $data['guide_id'] ?? null,
            'guide_notes' => $data['guide_notes'] ?? null
        ];
        
        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function findById($id)
    {
        return $this->getById($id);
    }

    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = "UPDATE tour_schedules SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function checkOverlap($tour_id, $start_date, $end_date, $exclude_id = null)
    {
        $sql = "SELECT COUNT(*) FROM tour_schedules
                WHERE tour_id = :tour_id
                  AND (start_date <= :end_date AND end_date >= :start_date)";

        $params = [
            'tour_id' => $tour_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        if ($exclude_id) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $exclude_id;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getById($id)
    {
        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code, t.duration_days, t.duration_nights,
                       t.min_participants, t.max_participants, t.tour_type,
                       u.full_name as guide_name, u.phone as guide_phone, u.email as guide_email
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN users u ON ts.guide_id = u.id
                WHERE ts.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get schedule by tour ID and start date.
     */
    public function getByTourAndStartDate($tour_id, $start_date)
    {
        $sql = "SELECT * FROM tour_schedules WHERE tour_id = :tour_id AND start_date = :start_date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id, 'start_date' => $start_date]);
        return $stmt->fetch();
    }

    /**
     * Increment booked count for a schedule.
     */
    public function incrementBooked($schedule_id, $increment = 1)
    {
        $sql = "UPDATE tour_schedules SET booked = booked + :inc WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['inc' => $increment, 'id' => $schedule_id]);
    }

    /**
     * Decrement booked count for a schedule (when booking is cancelled/rejected).
     * Ensures booked doesn't go below 0.
     */
    public function decrementBooked($schedule_id, $decrement = 1)
    {
        // First check current booked value to prevent negative
        $schedule = $this->getById($schedule_id);
        if (!$schedule) {
            return false;
        }
        
        $newBooked = max(0, $schedule['booked'] - $decrement);
        
        $sql = "UPDATE tour_schedules SET booked = :booked WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['booked' => $newBooked, 'id' => $schedule_id]);
    }

    /**
     * Get schedule by tour ID and date range.
     * Useful for finding which schedule a booking belongs to.
     */
    public function getByTourAndDateRange($tour_id, $start_date, $end_date)
    {
        $sql = "SELECT * FROM tour_schedules 
                WHERE tour_id = :tour_id 
                AND start_date = :start_date 
                AND end_date = :end_date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'tour_id' => $tour_id, 
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
        return $stmt->fetch();
    }

    public function assignGuide($schedule_id, $guide_id, $notes = null)
    {
        $sql = "UPDATE tour_schedules SET guide_id = :guide_id, guide_notes = :notes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'guide_id' => $guide_id,
            'notes' => $notes,
            'id' => $schedule_id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tour_schedules WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy schedules còn chỗ (available)
     */
    public function getAvailable($tour_id = null, $start_date = null)
    {
        $where = ["ts.status = 'open'", "(ts.quota - ts.booked) > 0"];
        $params = [];

        if ($tour_id) {
            $where[] = "ts.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        if ($start_date) {
            $where[] = "ts.start_date >= :start_date";
            $params['start_date'] = $start_date;
        }

        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY ts.start_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra số chỗ còn lại
     */
    public function checkQuotaAvailable($schedule_id, $requested_quantity = 1)
    {
        $schedule = $this->getById($schedule_id);
        if (!$schedule) {
            return false;
        }

        $available = $schedule['quota'] - $schedule['booked'];
        return $available >= $requested_quantity;
    }

    /**
     * Lấy số chỗ còn lại
     */
    public function getAvailableQuota($schedule_id)
    {
        $schedule = $this->getById($schedule_id);
        if (!$schedule) {
            return 0;
        }
        return max(0, $schedule['quota'] - $schedule['booked']);
    }

    /**
     * Cập nhật trạng thái schedule
     */
    public function updateStatus($id, $status)
    {
        $allowed_statuses = ['open', 'closed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            return false;
        }

        $sql = "UPDATE tour_schedules SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Lấy schedules theo status
     */
    public function getByStatus($status, $tour_id = null)
    {
        $where = ["ts.status = :status"];
        $params = ['status' => $status];

        if ($tour_id) {
            $where[] = "ts.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY ts.start_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lưu lịch sử thay đổi guide
     */
    public function logGuideChange($schedule_id, $old_guide_id, $new_guide_id, $changed_by, $reason = null, $notes = null)
    {
        // Lấy tên guide để lưu vào history
        $old_guide_name = null;
        $new_guide_name = null;

        if ($old_guide_id) {
            $stmt = $this->pdo->prepare("SELECT full_name FROM users WHERE id = :id");
            $stmt->execute(['id' => $old_guide_id]);
            $old_guide = $stmt->fetch(PDO::FETCH_ASSOC);
            $old_guide_name = $old_guide['full_name'] ?? null;
        }

        if ($new_guide_id) {
            $stmt = $this->pdo->prepare("SELECT full_name FROM users WHERE id = :id");
            $stmt->execute(['id' => $new_guide_id]);
            $new_guide = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_guide_name = $new_guide['full_name'] ?? null;
        }

        $sql = "INSERT INTO schedule_guide_history 
                (schedule_id, old_guide_id, new_guide_id, old_guide_name, new_guide_name, changed_by, reason, notes)
                VALUES (:schedule_id, :old_guide_id, :new_guide_id, :old_guide_name, :new_guide_name, :changed_by, :reason, :notes)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'schedule_id' => $schedule_id,
            'old_guide_id' => $old_guide_id,
            'new_guide_id' => $new_guide_id,
            'old_guide_name' => $old_guide_name,
            'new_guide_name' => $new_guide_name,
            'changed_by' => $changed_by,
            'reason' => $reason,
            'notes' => $notes
        ]);
    }

    /**
     * Lấy lịch sử thay đổi guide của schedule
     */
    public function getGuideHistory($schedule_id)
    {
        $sql = "SELECT h.*, 
                       u.full_name as changed_by_name,
                       u.email as changed_by_email
                FROM schedule_guide_history h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.schedule_id = :schedule_id
                ORDER BY h.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['schedule_id' => $schedule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
