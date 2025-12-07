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

        // 3. Validate payment_date (không được trong tương lai)
        $payment_date = $data['payment_date'] ?? date('Y-m-d');
        $today = date('Y-m-d');
        if ($payment_date > $today) {
            throw new Exception("Ngày thanh toán không được trong tương lai");
        }

        // 4. Validate amount không vượt quá remaining_amount (nếu không phải refund)
        if ($data['payment_type'] !== 'refund') {
            // Get booking info
            $bookingSql = "SELECT final_amount, paid_amount, remaining_amount, payment_status FROM bookings WHERE id = :booking_id";
            $bookingStmt = $this->pdo->prepare($bookingSql);
            $bookingStmt->execute(['booking_id' => $data['booking_id']]);
            $booking = $bookingStmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                throw new Exception("Booking không tồn tại");
            }

            if (in_array($booking['payment_status'], ['cancelled', 'rejected', 'refunded'])) {
                throw new Exception("Không thể thanh toán cho booking đã hủy/từ chối");
            }

            $remaining = (float) $booking['remaining_amount'];
            if ($data['amount'] > $remaining) {
                throw new Exception("Số tiền thanh toán (" . number_format($data['amount']) . ") vượt quá số tiền còn lại (" . number_format($remaining) . ")");
            }
        }

        // 5. Start transaction
        $this->pdo->beginTransaction();

        try {
            // 6. Insert payment
            $sql = "INSERT INTO payments (
                booking_id, payment_method, amount, payment_type,
                transaction_id, receipt_number, payment_date,
                notes, status, created_by, created_at
            ) VALUES (
                :booking_id, :payment_method, :amount, :payment_type,
                :transaction_id, :receipt_number, :payment_date,
                :notes, 'completed', :created_by, NOW()
            )";

            $params = [
                'booking_id' => $data['booking_id'],
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'payment_type' => $data['payment_type'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'receipt_number' => $data['receipt_number'] ?? null,
                'payment_date' => $payment_date,
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? null
            ];

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $paymentId = $this->pdo->lastInsertId();

            // 7. Update booking paid_amount and payment_status
            $this->updateBookingPaymentStatus($data['booking_id']);

            // 8. Log payment
            $this->logPayment($paymentId, 'created', null, $params, $data['created_by'] ?? null);

            // 9. Commit transaction
            $this->pdo->commit();

            // 10. Return payment ID
            return $paymentId;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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

    /**
     * Update booking payment status after payment created/updated/deleted
     */
    private function updateBookingPaymentStatus($booking_id)
    {
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new Booking($this->pdo);
        $bookingModel->updatePaymentStatus($booking_id);
    }

    /**
     * Log payment action to payment_logs
     */
    private function logPayment($payment_id, $action, $old_values = null, $new_values = null, $changed_by = null)
    {
        $sql = "INSERT INTO payment_logs (payment_id, action, old_values, new_values, changed_by, created_at)
                VALUES (:payment_id, :action, :old_values, :new_values, :changed_by, NOW())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'payment_id' => $payment_id,
            'action' => $action,
            'old_values' => $old_values ? json_encode($old_values) : null,
            'new_values' => $new_values ? json_encode($new_values) : null,
            'changed_by' => $changed_by
        ]);
    }

    /**
     * Update payment
     */
    public function update($id, $data)
    {
        // Get old values for logging
        $oldPayment = $this->getById($id);
        if (!$oldPayment) {
            throw new Exception("Payment không tồn tại");
        }

        // Validate amount > 0 if provided
        if (isset($data['amount']) && $data['amount'] <= 0) {
            throw new Exception("Số tiền phải lớn hơn 0");
        }

        // Validate payment_date if provided
        if (isset($data['payment_date'])) {
            $today = date('Y-m-d');
            if ($data['payment_date'] > $today) {
                throw new Exception("Ngày thanh toán không được trong tương lai");
            }
        }

        // Start transaction
        $this->pdo->beginTransaction();

        try {
            // Build update SQL
            $fields = [];
            $params = ['id' => $id];

            $allowedFields = ['amount', 'payment_method', 'payment_type', 'transaction_id', 'receipt_number', 'payment_date', 'notes', 'status'];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (empty($fields)) {
                throw new Exception("Không có dữ liệu để cập nhật");
            }

            $sql = "UPDATE payments SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            // Update booking payment status
            $this->updateBookingPaymentStatus($oldPayment['booking_id']);

            // Log payment
            $this->logPayment($id, 'updated', $oldPayment, $data, $data['changed_by'] ?? null);

            // Commit transaction
            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Delete payment (soft delete - set status to cancelled)
     */
    public function delete($id, $deleted_by = null)
    {
        // Get old values for logging
        $oldPayment = $this->getById($id);
        if (!$oldPayment) {
            throw new Exception("Payment không tồn tại");
        }

        // Start transaction
        $this->pdo->beginTransaction();

        try {
            // Soft delete: set status to cancelled
            $sql = "UPDATE payments SET status = 'cancelled' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);

            // Update booking payment status
            $this->updateBookingPaymentStatus($oldPayment['booking_id']);

            // Log payment
            $this->logPayment($id, 'deleted', $oldPayment, null, $deleted_by);

            // Commit transaction
            $this->pdo->commit();

            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Process refund
     */
    public function refund($booking_id, $amount, $data = [])
    {
        // Get booking info
        $bookingSql = "SELECT paid_amount, final_amount FROM bookings WHERE id = :booking_id";
        $bookingStmt = $this->pdo->prepare($bookingSql);
        $bookingStmt->execute(['booking_id' => $booking_id]);
        $booking = $bookingStmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception("Booking không tồn tại");
        }

        // Validate refund amount <= paid_amount
        $paidAmount = (float) $booking['paid_amount'];
        if ($amount > $paidAmount) {
            throw new Exception("Số tiền hoàn lại (" . number_format($amount) . ") không được vượt quá số tiền đã thanh toán (" . number_format($paidAmount) . ")");
        }

        if ($amount <= 0) {
            throw new Exception("Số tiền hoàn lại phải lớn hơn 0");
        }

        // Create refund payment
        $refundData = array_merge([
            'booking_id' => $booking_id,
            'amount' => $amount,
            'payment_method' => $data['payment_method'] ?? 'bank_transfer',
            'payment_type' => 'refund',
            'payment_date' => date('Y-m-d'),
            'notes' => $data['notes'] ?? 'Hoàn tiền',
            'created_by' => $data['created_by'] ?? null
        ], $data);

        return $this->create($refundData);
    }
}
