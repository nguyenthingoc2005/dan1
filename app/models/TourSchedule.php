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

        $offset = ($page - 1) * $limit;
        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code 
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY ts.start_date ASC
                LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
        $sql = "INSERT INTO tour_schedules (tour_id, start_date, end_date, quota, adult_price, child_price, infant_price)
                VALUES (:tour_id, :start_date, :end_date, :quota, :adult_price, :child_price, :infant_price)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Kiểm tra lịch trùng cho một tour.
     * Trả về true nếu có lịch nào có ngày bắt đầu hoặc kết thúc chồng lấn.
     */
    public function checkOverlap($tour_id, $start_date, $end_date)
    {
        $sql = "SELECT COUNT(*) FROM tour_schedules
                WHERE tour_id = :tour_id
                  AND (start_date <= :end_date AND end_date >= :start_date)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'tour_id' => $tour_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tour_schedules WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
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

}
