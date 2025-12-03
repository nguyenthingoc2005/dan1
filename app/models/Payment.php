<?php
/**
 * ==============================================================================
 * PAYMENT MODEL
 * ==============================================================================
 */

class Payment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách payments
     */
    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        $sql = "SELECT p.*, b.booking_code, c.full_name as customer_name 
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                JOIN users c ON b.customer_id = c.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['start_date'])) {
            $sql .= " AND p.payment_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND p.payment_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['payment_method'])) {
            $sql .= " AND p.payment_method = :method";
            $params['method'] = $filters['payment_method'];
        }

        if (!empty($filters['payment_type'])) {
            $sql .= " AND p.payment_type = :type";
            $params['type'] = $filters['payment_type'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM (" . $sql . ") as count_table";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Get data
        $sql .= " ORDER BY p.payment_date DESC, p.created_at DESC LIMIT :offset, :limit";
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    /**
     * Lấy chi tiết payment
     */
    public function getById($id)
    {
        $sql = "SELECT p.*, b.booking_code, b.tour_id, t.name as tour_name, c.full_name as customer_name, c.phone, c.email, c.address
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                JOIN tours t ON b.tour_id = t.id
                JOIN users c ON b.customer_id = c.id
                WHERE p.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo payment mới (Đã có logic trong BookingController, có thể move về đây sau)
     */
    public function create($data)
    {
        // Logic create payment
    }

    /**
     * Lấy danh sách payments theo booking_id
     */
    public function getByBookingId($booking_id)
    {
        $sql = "SELECT p.*, u.full_name as creator_name 
                FROM payments p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.booking_id = :booking_id 
                ORDER BY p.payment_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
