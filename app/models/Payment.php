<?php
class Payment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tạo thanh toán mới
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO payments (
                booking_id, payment_method, amount, payment_type, 
                transaction_id, receipt_number, payment_date, 
                status, notes, created_by
            ) VALUES (
                :booking_id, :payment_method, :amount, :payment_type,
                :transaction_id, :receipt_number, :payment_date,
                :status, :notes, :created_by
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'booking_id' => $data['booking_id'],
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'payment_type' => $data['payment_type'] ?? 'deposit',
                'transaction_id' => $data['transaction_id'] ?? null,
                'receipt_number' => $data['receipt_number'] ?? null,
                'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
                'status' => $data['status'] ?? 'completed',
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'] ?? null
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Payment::create Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách thanh toán của Booking
     */
    public function getByBookingId($bookingId)
    {
        $sql = "SELECT p.*, u.full_name as creator_name 
                FROM payments p
                LEFT JOIN users u ON p.created_by = u.id
                WHERE p.booking_id = :booking_id 
                ORDER BY p.payment_date DESC, p.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tính tổng tiền đã thanh toán (chỉ tính status completed)
     */
    public function getTotalPaid($bookingId)
    {
        $sql = "SELECT SUM(amount) FROM payments 
                WHERE booking_id = :booking_id AND status = 'completed'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return (float) $stmt->fetchColumn();
    }
}
