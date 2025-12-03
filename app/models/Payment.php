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
                JOIN customers c ON b.customer_id = c.id
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

        if (!empty($filters['created_by_booking'])) {
            $sql .= " AND b.created_by = :created_by_booking";
            $params['created_by_booking'] = $filters['created_by_booking'];
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

    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM payments p
                JOIN bookings b ON p.booking_id = b.id
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
        if (!empty($filters['created_by_booking'])) {
            $sql .= " AND b.created_by = :created_by_booking";
            $params['created_by_booking'] = $filters['created_by_booking'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
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
                JOIN customers c ON b.customer_id = c.id
                WHERE p.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        return $this->getById($id);
    }

    /**
     * Tạo payment mới
     * 
     * @param array $data - Dữ liệu payment cần tạo
     * @return int Payment ID vừa tạo
     * @throws Exception nếu thiếu dữ liệu bắt buộc
     */
    public function create($data)
    {
        // 1. Validate required fields
        $required = ['booking_id', 'amount', 'payment_method', 'payment_type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Thiếu thông tin bắt buộc: $field");
            }
        }

        // 2. Validate amount > 0
        if ($data['amount'] <= 0) {
            throw new Exception("Số tiền phải lớn hơn 0");
        }

        // 3. Prepare SQL
        $sql = "INSERT INTO payments (
            booking_id, payment_method, amount, payment_type,
            transaction_id, receipt_number, payment_date,
            notes, status, created_by, created_at
        ) VALUES (
            :booking_id, :payment_method, :amount, :payment_type,
            :transaction_id, :receipt_number, :payment_date,
            :notes, 'completed', :created_by, NOW()
        )";

        // 4. Prepare params
        $params = [
            'booking_id' => $data['booking_id'],
            'payment_method' => $data['payment_method'],
            'amount' => $data['amount'],
            'payment_type' => $data['payment_type'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'receipt_number' => $data['receipt_number'] ?? null,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? null
        ];

        // 5. Execute
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // 6. Return payment ID
        return $this->pdo->lastInsertId();
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
