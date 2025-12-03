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

        if (!empty($filters['status'])) {
            // Assuming status column exists or logic derived from dates
            // For now, ignore status filter if column doesn't exist or map it
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM tour_schedules ts WHERE " . implode(" AND ", $where);
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Get Data
        $offset = ($page - 1) * $limit;
        $sql = "SELECT ts.*, t.name as tour_name, t.tour_code 
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
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
        $sql = "INSERT INTO tour_schedules (tour_id, start_date, end_date, quota, adult_price, child_price, infant_price)
                VALUES (:tour_id, :start_date, :end_date, :quota, :adult_price, :child_price, :infant_price)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
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
    /**
     * Increment booked count for a schedule.
     */
    public function incrementBooked($schedule_id, $increment = 1)
    {
        $sql = "UPDATE tour_schedules SET booked = booked + :inc WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['inc' => $increment, 'id' => $schedule_id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM tour_schedules WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
