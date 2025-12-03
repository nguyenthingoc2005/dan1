<?php
class Booking
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách Booking (có phân trang & lọc)
     */
    public function getAll($filters = [], $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT b.*, 
                       t.name as tour_name, t.tour_code,
                       c.full_name as customer_name, c.phone as customer_phone, c.email as customer_email
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN customers c ON b.customer_id = c.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (b.booking_code LIKE :search OR c.full_name LIKE :search OR c.phone LIKE :search)";
            $params['search'] = "%" . $filters['search'] . "%";
        }

        if (!empty($filters['status'])) {
            // Filter by approval_status or payment_status
            // For simplicity in UI tabs, we might map 'pending', 'approved', 'paid', 'cancelled'
            if (in_array($filters['status'], ['pending', 'approved', 'rejected', 'cancelled'])) {
                $sql .= " AND b.approval_status = :status";
                $params['status'] = $filters['status'];
            } elseif (in_array($filters['status'], ['unpaid', 'partial', 'paid', 'refunded'])) {
                $sql .= " AND b.payment_status = :status";
                $params['status'] = $filters['status'];
            }
        }

        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND b.start_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND b.created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        // Order by newest
        $sql .= " ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số booking (cho phân trang)
     */
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM bookings b 
                LEFT JOIN customers c ON b.customer_id = c.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (b.booking_code LIKE :search OR c.full_name LIKE :search OR c.phone LIKE :search)";
            $params['search'] = "%" . $filters['search'] . "%";
        }

        if (!empty($filters['status'])) {
            if (in_array($filters['status'], ['pending', 'approved', 'rejected', 'cancelled'])) {
                $sql .= " AND b.approval_status = :status";
                $params['status'] = $filters['status'];
            } elseif (in_array($filters['status'], ['unpaid', 'partial', 'paid', 'refunded'])) {
                $sql .= " AND b.payment_status = :status";
                $params['status'] = $filters['status'];
            }
        }

        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = :tour_id";
            $params['tour_id'] = $filters['tour_id'];
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND b.created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Lấy chi tiết Booking
     */
    public function getById($id)
    {
        $sql = "SELECT b.*, 
                       t.name as tour_name, t.tour_code, t.duration_days, t.duration_nights,
                       c.full_name as customer_name, c.phone as customer_phone, c.email as customer_email, c.address as customer_address,
                       u.full_name as creator_name
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN customers c ON b.customer_id = c.id
                LEFT JOIN users u ON b.created_by = u.id
                WHERE b.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            // Get passengers
            $sql_passengers = "SELECT * FROM booking_customers WHERE booking_id = :id";
            $stmt_p = $this->pdo->prepare($sql_passengers);
            $stmt_p->execute(['id' => $id]);
            $booking['passengers'] = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
        }

        return $booking;
    }

    /**
     * Lấy danh sách Booking theo Customer ID
     */
    public function getByCustomerId($customerId)
    {
        $sql = "SELECT b.*, t.name as tour_name, t.tour_code 
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                WHERE b.customer_id = :customer_id
                ORDER BY b.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra duplicate booking (cùng customer, tour, ngày)
     */
    public function checkDuplicate($customer_id, $tour_id, $start_date)
    {
        $sql = "SELECT id, booking_code FROM bookings
                WHERE customer_id = :customer_id
                  AND tour_id = :tour_id
                  AND start_date = :start_date
                  AND approval_status NOT IN ('cancelled', 'rejected')
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'customer_id' => $customer_id,
            'tour_id' => $tour_id,
            'start_date' => $start_date
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Tạo Booking mới (Transaction)
     */
    public function create($data, $passengers = [])
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Generate Code
            $booking_code = $this->generateBookingCode();

            // 2. Insert Booking
            $sql = "INSERT INTO bookings (
                booking_code, tour_id, customer_id, 
                adult_count, child_count, infant_count,
                start_date, end_date,
                total_amount, discount_amount, final_amount, 
                deposit_amount, remaining_amount,
                payment_status, approval_status,
                notes, created_by" .
                (!empty($data['tour_schedule_id']) ? ", tour_schedule_id" : "") . "
            ) VALUES (
                :booking_code, :tour_id, :customer_id,
                :adult_count, :child_count, :infant_count,
                :start_date, :end_date,
                :total_amount, :discount_amount, :final_amount,
                :deposit_amount, :remaining_amount,
                :payment_status, :approval_status,
                :notes, :created_by" .
                (!empty($data['tour_schedule_id']) ? ", :tour_schedule_id" : "") . "
            )";

            $params = [
                'booking_code' => $booking_code,
                'tour_id' => $data['tour_id'],
                'customer_id' => $data['customer_id'],
                'adult_count' => $data['adult_count'],
                'child_count' => $data['child_count'],
                'infant_count' => $data['infant_count'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_amount' => $data['total_amount'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'final_amount' => $data['final_amount'],
                'deposit_amount' => $data['deposit_amount'] ?? 0,
                'remaining_amount' => $data['remaining_amount'],
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'approval_status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by']
            ];

            if (!empty($data['tour_schedule_id'])) {
                $params['tour_schedule_id'] = $data['tour_schedule_id'];
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $booking_id = $this->pdo->lastInsertId();

            // 3. Insert Passengers (if any)
            if (!empty($passengers)) {
                $sql_p = "INSERT INTO booking_customers (booking_id, customer_id, age_type, is_primary) VALUES (:booking_id, :customer_id, :age_type, :is_primary)";
                $stmt_p = $this->pdo->prepare($sql_p);

                // Add primary customer as passenger if not in list (logic handled in controller usually, but here we assume passengers list is complete or we add primary)
                // For now, assume passengers array contains all info
                foreach ($passengers as $p) {
                    // If passenger is a new customer, we might need to create them first? 
                    // For simplicity, let's assume passengers are just names linked to the main customer or we just store them as simple records if the DB allowed.
                    // Checking schema: booking_customers links to customers(id). 
                    // So every passenger must be a customer in DB. 
                    // This might be too complex for "Simple Vibe". 
                    // Let's revisit: usually simple booking just needs names. 
                    // Schema says: customer_id INT NOT NULL. 
                    // So we must create customer records for everyone? That's heavy.
                    // Let's check schema again. Yes, customer_id is FK.
                    // OK, for V1, maybe we only link the MAIN customer in booking_customers as is_primary=1.
                    // And other passengers we might skip or auto-create?
                    // Let's stick to: Insert the MAIN customer into booking_customers first.

                    $stmt_p->execute([
                        'booking_id' => $booking_id,
                        'customer_id' => $p['customer_id'], // Must exist
                        'age_type' => $p['age_type'],
                        'is_primary' => $p['is_primary'] ?? 0
                    ]);
                }
            } else {
                // At least add the booker as a passenger
                $sql_p = "INSERT INTO booking_customers (booking_id, customer_id, age_type, is_primary) VALUES (:booking_id, :customer_id, 'adult', 1)";
                $stmt_p = $this->pdo->prepare($sql_p);
                $stmt_p->execute([
                    'booking_id' => $booking_id,
                    'customer_id' => $data['customer_id']
                ]);
            }

            $this->pdo->commit();
            return $booking_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái Booking
     */
    public function updateStatus($id, $status, $type = 'approval', $userId = null, $reason = null)
    {
        $sql = "";
        $params = ['id' => $id, 'status' => $status];

        if ($type == 'approval') {
            $sql = "UPDATE bookings SET approval_status = :status, approved_by = :user_id, approved_at = NOW() WHERE id = :id";
            if ($status == 'rejected' || $status == 'cancelled') {
                $sql = "UPDATE bookings SET approval_status = :status, rejection_reason = :reason WHERE id = :id";
                $params['reason'] = $reason;
            } else {
                $params['user_id'] = $userId;
            }
        } elseif ($type == 'payment') {
            $sql = "UPDATE bookings SET payment_status = :status WHERE id = :id";
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Lấy lịch sử trạng thái
     */
    public function getHistory($id)
    {
        $sql = "SELECT h.*, u.full_name as user_name 
                FROM booking_status_history h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.booking_id = :id 
                ORDER BY h.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ghi log lịch sử
     */
    public function logHistory($bookingId, $oldStatus, $newStatus, $userId, $reason = null, $notes = null)
    {
        $sql = "INSERT INTO booking_status_history (booking_id, old_status, new_status, changed_by, reason, notes)
                VALUES (:booking_id, :old_status, :new_status, :changed_by, :reason, :notes)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'booking_id' => $bookingId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'reason' => $reason,
            'notes' => $notes
        ]);
    }

    /**
     * Cập nhật trạng thái thanh toán dựa trên tổng tiền đã đóng
     */
    public function updatePaymentStatus($id)
    {
        // 1. Get Booking Info
        $stmt = $this->pdo->prepare("SELECT final_amount, deposit_amount FROM bookings WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking)
            return;

        // 2. Sum Completed Payments
        $stmt = $this->pdo->prepare("SELECT SUM(amount) FROM payments WHERE booking_id = :id AND status = 'completed'");
        $stmt->execute(['id' => $id]);
        $paidAmount = (float) $stmt->fetchColumn();

        // 3. Calculate Status
        $finalAmount = (float) $booking['final_amount'];
        $remaining = max(0, $finalAmount - $paidAmount);

        $status = 'unpaid';
        if ($paidAmount >= $finalAmount) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        }

        // 4. Update Booking
        $sql = "UPDATE bookings SET 
                paid_amount = :paid, 
                remaining_amount = :remaining, 
                payment_status = :status 
                WHERE id = :id";

        $this->pdo->prepare($sql)->execute([
            'paid' => $paidAmount,
            'remaining' => $remaining,
            'status' => $status,
            'id' => $id
        ]);

        // 5. AUTO-APPROVE LOGIC
        // If payment is made (partial or paid) AND booking is pending -> Approve it
        if ($paidAmount > 0) {
            $stmt = $this->pdo->prepare("SELECT approval_status FROM bookings WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $currentStatus = $stmt->fetchColumn();

            if ($currentStatus == 'pending') {
                $this->updateStatus($id, 'approved', 'approval', null); // null user for System
                $this->logHistory($id, 'pending', 'approved', null, "Tự động duyệt do đã thanh toán");
            }
        }
    }

    /**
     * Hủy Booking (Có tính phí)
     */
    public function cancel($id, $reason, $userId)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Get Booking Info
            $booking = $this->getById($id);
            if (!$booking)
                throw new Exception("Booking not found");

            // 2. Calculate Days Before Departure
            $startDate = new DateTime($booking['start_date']);
            $today = new DateTime();
            $interval = $today->diff($startDate);
            $daysBefore = (int) $interval->format('%r%a'); // %r for sign (negative if passed)

            if ($daysBefore < 0)
                $daysBefore = 0;

            // 3. Find Policy
            $sql = "SELECT * FROM cancellation_policies 
                    WHERE days_before <= :days 
                    ORDER BY days_before DESC 
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['days' => $daysBefore]);
            $policy = $stmt->fetch(PDO::FETCH_ASSOC);

            $feePercentage = $policy ? (float) $policy['fee_percentage'] : 0;
            $feeAmount = ($booking['final_amount'] * $feePercentage) / 100;
            $refundAmount = $booking['paid_amount'] - $feeAmount;

            // 4. Update Booking
            $sql = "UPDATE bookings SET 
                    approval_status = 'cancelled',
                    cancellation_date = NOW(),
                    cancellation_reason = :reason,
                    cancellation_policy_id = :policy_id,
                    cancellation_fee = :fee,
                    refund_amount = :refund
                    WHERE id = :id";

            $this->pdo->prepare($sql)->execute([
                'reason' => $reason,
                'policy_id' => $policy['id'] ?? null,
                'fee' => $feeAmount,
                'refund' => $refundAmount,
                'id' => $id
            ]);

            // 5. Log History
            $this->logHistory($id, $booking['approval_status'], 'cancelled', $userId, $reason, "Phí hủy: " . number_format($feeAmount) . " (" . $feePercentage . "%)");

            // 6. Return Quota (Optional - if needed)
            // Logic to increase schedule quota back goes here if using TourSchedule

            $this->pdo->commit();
            return [
                'fee' => $feeAmount,
                'refund' => $refundAmount,
                'policy' => $policy['name'] ?? 'Mặc định'
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function generateBookingCode()
    {
        // Format: BK-YYYYMMDD-XXXX
        $prefix = "BK-" . date("Ymd") . "-";
        $sql = "SELECT booking_code FROM bookings WHERE booking_code LIKE :prefix ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prefix' => $prefix . "%"]);
        $lastCode = $stmt->fetchColumn();

        if ($lastCode) {
            $number = intval(substr($lastCode, -4));
            return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . "0001";
    }
}
