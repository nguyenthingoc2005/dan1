<?php
/**
 * ==============================================================================
 * BOOKING SERVICE MODEL
 * ==============================================================================
 * 
 * Quản lý dịch vụ thực tế cho từng booking
 * - Đặt dịch vụ từ suppliers (khách sạn, xe, vé...)
 * - Theo dõi thanh toán cho suppliers
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class BookingService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ========================================================================
    // CRUD METHODS
    // ========================================================================

    /**
     * Lấy danh sách dịch vụ của booking
     */
    public function getByBookingId($bookingId)
    {
        $sql = "SELECT bs.*, 
                       s.name as service_name_original,
                       st.name as service_type_name,
                       sp.name as supplier_name, sp.service_code as supplier_code
                FROM booking_services bs
                LEFT JOIN services s ON bs.service_id = s.id
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN service_providers sp ON bs.service_provider_id = sp.id
                WHERE bs.booking_id = :booking_id
                ORDER BY bs.service_date ASC, bs.created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết một booking service
     */
    public function getById($id)
    {
        $sql = "SELECT bs.*, 
                       s.name as service_name_original,
                       st.name as service_type_name,
                       sp.name as supplier_name
                FROM booking_services bs
                LEFT JOIN services s ON bs.service_id = s.id
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN service_providers sp ON bs.service_provider_id = sp.id
                WHERE bs.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm dịch vụ cho booking
     */
    public function create($data)
    {
        $sql = "INSERT INTO booking_services (
                    booking_id, service_id, service_provider_id, service_name,
                    quantity, unit, unit_price, total_price,
                    service_date, from_date, to_date,
                    payment_status, paid_amount, notes, created_by
                ) VALUES (
                    :booking_id, :service_id, :service_provider_id, :service_name,
                    :quantity, :unit, :unit_price, :total_price,
                    :service_date, :from_date, :to_date,
                    :payment_status, :paid_amount, :notes, :created_by
                )";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'booking_id' => $data['booking_id'],
            'service_id' => $data['service_id'],
            'service_provider_id' => $data['service_provider_id'] ?? $data['supplier_id'] ?? null,
            'service_name' => $data['service_name'] ?? null,
            'quantity' => $data['quantity'] ?? 1,
            'unit' => $data['unit'] ?? null,
            'unit_price' => $data['unit_price'] ?? 0,
            'total_price' => $data['total_price'] ?? ($data['quantity'] * ($data['unit_price'] ?? 0)),
            'service_date' => $data['service_date'] ?? null,
            'from_date' => $data['from_date'] ?? null,
            'to_date' => $data['to_date'] ?? null,
            'payment_status' => $data['payment_status'] ?? 'pending',
            'paid_amount' => $data['paid_amount'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'created_by' => $data['created_by'] ?? ($_SESSION['user_id'] ?? null)
        ]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = [
            'service_id',
            'service_provider_id',
            'service_name',
            'quantity',
            'unit',
            'unit_price',
            'total_price',
            'service_date',
            'from_date',
            'to_date',
            'payment_status',
            'paid_amount',
            'notes'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE booking_services SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa dịch vụ
     */
    public function delete($id)
    {
        // Check if already paid
        $service = $this->getById($id);
        if ($service && $service['paid_amount'] > 0) {
            return false; // Cannot delete if already paid
        }

        $sql = "DELETE FROM booking_services WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // ========================================================================
    // STATISTICS & REPORTS
    // ========================================================================

    /**
     * Tổng chi phí dịch vụ của booking
     */
    public function getTotalCostByBooking($bookingId)
    {
        $sql = "SELECT 
                    SUM(total_price) as total_cost,
                    SUM(paid_amount) as total_paid,
                    SUM(total_price) - SUM(paid_amount) as total_remaining
                FROM booking_services
                WHERE booking_id = :booking_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['booking_id' => $bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy dịch vụ chưa thanh toán cho supplier
     */
    public function getUnpaidBySupplier($serviceProviderId)
    {
        $sql = "SELECT bs.*, b.booking_code, t.name as tour_name
                FROM booking_services bs
                JOIN bookings b ON bs.booking_id = b.id
                JOIN tours t ON b.tour_id = t.id
                WHERE bs.service_provider_id = :service_provider_id
                  AND bs.payment_status != 'paid'
                ORDER BY bs.service_date ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['service_provider_id' => $serviceProviderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updatePaymentStatus($id, $paidAmount)
    {
        $service = $this->getById($id);
        if (!$service)
            return false;

        $newPaidAmount = $service['paid_amount'] + $paidAmount;
        $status = 'pending';

        if ($newPaidAmount >= $service['total_price']) {
            $status = 'paid';
            $newPaidAmount = $service['total_price']; // Cap at total
        } elseif ($newPaidAmount > 0) {
            $status = 'partial';
        }

        $sql = "UPDATE booking_services 
                SET paid_amount = :paid_amount, payment_status = :status 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'paid_amount' => $newPaidAmount,
            'status' => $status,
            'id' => $id
        ]);
    }

    // ========================================================================
    // VALIDATION
    // ========================================================================

    /**
     * Validate dữ liệu booking service
     */
    public function validate($data)
    {
        $errors = [];

        // Booking ID required
        if (empty($data['booking_id'])) {
            $errors['booking_id'] = 'Booking ID là bắt buộc';
        }

        // Service ID required
        if (empty($data['service_id'])) {
            $errors['service_id'] = 'Vui lòng chọn dịch vụ';
        }

        // Supplier ID required
        if (empty($data['service_provider_id']) && empty($data['supplier_id'])) {
            $errors['service_provider_id'] = 'Vui lòng chọn nhà cung cấp';
        }

        // Quantity > 0
        if (isset($data['quantity']) && $data['quantity'] < 1) {
            $errors['quantity'] = 'Số lượng phải >= 1';
        }

        // Unit price >= 0
        if (isset($data['unit_price']) && $data['unit_price'] < 0) {
            $errors['unit_price'] = 'Đơn giá phải >= 0';
        }

        // Date validation
        if (!empty($data['from_date']) && !empty($data['to_date'])) {
            if (strtotime($data['to_date']) < strtotime($data['from_date'])) {
                $errors['to_date'] = 'Ngày kết thúc phải sau ngày bắt đầu';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Copy services từ tour template sang booking
     * (Dùng khi tạo booking để auto-add các dịch vụ mặc định của tour)
     */
    public function copyFromTourServices($bookingId, $tourId, $quantity = 1)
    {
        try {
            // Get tour services
            $sql = "SELECT ts.*, s.service_provider_id
                    FROM tour_services ts
                    JOIN services s ON ts.service_id = s.id
                    WHERE ts.tour_id = :tour_id AND ts.is_included_in_price = 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['tour_id' => $tourId]);
            $tourServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tourServices as $ts) {
                // Calculate quantity based on calculation_type
                $qty = $quantity;
                switch ($ts['calculation_type']) {
                    case 'per_person':
                        $qty = $quantity;
                        break;
                    case 'per_group':
                        $qty = ceil($quantity / ($ts['group_size'] ?? 10));
                        break;
                    case 'fixed':
                        $qty = $ts['fixed_quantity'] ?? 1;
                        break;
                }

                $this->create([
                    'booking_id' => $bookingId,
                    'service_id' => $ts['service_id'],
                    'service_provider_id' => $ts['service_provider_id'] ?? null,
                    'service_name' => $ts['service_name'],
                    'quantity' => $qty,
                    'unit' => $ts['unit'],
                    'unit_price' => $ts['unit_price'],
                    'total_price' => $qty * $ts['unit_price'],
                    'notes' => '[TOUR_ORIGINAL] Auto-copied from tour template' // Prefix để phân biệt dịch vụ gốc
                ]);
            }

            return true;

        } catch (Exception $e) {
            error_log("BookingService::copyFromTourServices Error: " . $e->getMessage());
            return false;
        }
    }
}

