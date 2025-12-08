<?php
/**
 * DiscountCode Model
 * Quản lý mã giảm giá
 */
class DiscountCode
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tìm mã giảm giá theo code
     * @param string $code
     * @return array|null
     */
    public function findByCode($code)
    {
        if (empty($code)) {
            return null;
        }

        $sql = "SELECT * FROM discount_codes WHERE code = :code LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => trim($code)]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Validate mã giảm giá cho booking
     * 
     * @param string $code Mã giảm giá
     * @param float $totalAmount Tổng tiền booking
     * @param int|null $bookingId Booking ID (nếu đang update, để check mã đã dùng)
     * @return array ['valid' => bool, 'message' => string, 'discount_code' => array|null, 'discount_amount' => float]
     */
    public function validateForBooking($code, $totalAmount, $bookingId = null)
    {
        // Case 1: Mã rỗng - cho phép (giảm trực tiếp không cần mã)
        if (empty(trim($code))) {
            return [
                'valid' => true,
                'message' => 'Không có mã giảm giá (giảm trực tiếp)',
                'discount_code' => null,
                'discount_amount' => 0
            ];
        }

        // Case 2: Tìm mã trong database
        $discountCode = $this->findByCode($code);
        if (!$discountCode) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại',
                'discount_code' => null,
                'discount_amount' => 0
            ];
        }

        // Case 3: Kiểm tra status = 'active'
        if ($discountCode['status'] !== 'active') {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá đã bị vô hiệu hóa',
                'discount_code' => $discountCode,
                'discount_amount' => 0
            ];
        }

        // Case 4: Kiểm tra thời gian hiệu lực
        $today = date('Y-m-d');
        if (!empty($discountCode['start_date']) && $today < $discountCode['start_date']) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá chưa có hiệu lực (bắt đầu từ ' . date('d/m/Y', strtotime($discountCode['start_date'])) . ')',
                'discount_code' => $discountCode,
                'discount_amount' => 0
            ];
        }
        if (!empty($discountCode['end_date']) && $today > $discountCode['end_date']) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá đã hết hạn (hết hạn ngày ' . date('d/m/Y', strtotime($discountCode['end_date'])) . ')',
                'discount_code' => $discountCode,
                'discount_amount' => 0
            ];
        }

        // Case 5: Kiểm tra số lần sử dụng
        $usageLimit = (int) ($discountCode['usage_limit'] ?? 0);
        $usedCount = (int) ($discountCode['used_count'] ?? 0);
        if ($usageLimit > 0 && $usedCount >= $usageLimit) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá đã hết số lần sử dụng (' . $usedCount . '/' . $usageLimit . ')',
                'discount_code' => $discountCode,
                'discount_amount' => 0
            ];
        }

        // Case 6: Kiểm tra min_purchase
        $minPurchase = (float) ($discountCode['min_purchase'] ?? 0);
        if ($totalAmount < $minPurchase) {
            return [
                'valid' => false,
                'message' => 'Đơn hàng phải có giá trị tối thiểu ' . number_format($minPurchase, 0, ',', '.') . ' đ để sử dụng mã này',
                'discount_code' => $discountCode,
                'discount_amount' => 0
            ];
        }

        // Case 7: Kiểm tra booking đã dùng mã khác chưa (chỉ check khi update)
        if ($bookingId) {
            $sql = "SELECT discount_code FROM bookings WHERE id = :id AND discount_code IS NOT NULL AND discount_code != ''";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $bookingId]);
            $existingCode = $stmt->fetchColumn();
            
            if ($existingCode && $existingCode !== $code) {
                return [
                    'valid' => false,
                    'message' => 'Booking này đã sử dụng mã giảm giá khác (' . $existingCode . '). Mỗi booking chỉ được dùng 1 mã.',
                    'discount_code' => $discountCode,
                    'discount_amount' => 0
                ];
            }
        }

        // Case 8: Tính discount_amount
        $discountType = $discountCode['discount_type'];
        $discountValue = (float) $discountCode['discount_value'];
        $discountAmount = 0;

        if ($discountType === 'percentage') {
            // Percentage: tính theo %
            $discountAmount = round($totalAmount * ($discountValue / 100), 0);
        } else {
            // Fixed: số tiền cố định
            $discountAmount = min($discountValue, $totalAmount); // Không giảm quá tổng tiền
        }

        // Case 9: Validate discount_amount <= total_amount (đã check ở trên cho fixed)
        if ($discountAmount > $totalAmount) {
            $discountAmount = $totalAmount;
        }

        return [
            'valid' => true,
            'message' => 'Mã giảm giá hợp lệ',
            'discount_code' => $discountCode,
            'discount_amount' => $discountAmount
        ];
    }

    /**
     * Tăng số lần sử dụng mã giảm giá
     * @param string $code
     * @return bool
     */
    public function incrementUsage($code)
    {
        if (empty($code)) {
            return false;
        }

        $sql = "UPDATE discount_codes 
                SET used_count = used_count + 1 
                WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['code' => trim($code)]);
    }

    /**
     * Giảm số lần sử dụng mã giảm giá (khi hủy booking hoặc đổi mã)
     * @param string $code
     * @return bool
     */
    public function decrementUsage($code)
    {
        if (empty($code)) {
            return false;
        }

        $sql = "UPDATE discount_codes 
                SET used_count = GREATEST(0, used_count - 1) 
                WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['code' => trim($code)]);
    }

    /**
     * Lấy tất cả mã giảm giá active
     * @return array
     */
    public function getAllActive()
    {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM discount_codes 
                WHERE status = 'active'
                  AND (start_date IS NULL OR start_date <= :today1)
                  AND (end_date IS NULL OR end_date >= :today2)
                  AND (usage_limit = 0 OR used_count < usage_limit)
                ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'today1' => $today,
            'today2' => $today
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả mã giảm giá (có filter)
     * @param array $filters
     * @return array
     */
    public function getAll($filters = [])
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(code LIKE :search OR name LIKE :search2)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params['search'] = $searchTerm;
            $params['search2'] = $searchTerm;
        }

        $sql = "SELECT dc.*, u.full_name as creator_name 
                FROM discount_codes dc
                LEFT JOIN users u ON dc.created_by = u.id
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY dc.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy mã giảm giá theo ID
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        $sql = "SELECT dc.*, u.full_name as creator_name 
                FROM discount_codes dc
                LEFT JOIN users u ON dc.created_by = u.id
                WHERE dc.id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Kiểm tra mã đã tồn tại chưa
     * @param string $code
     * @param int|null $excludeId ID để loại trừ (khi update)
     * @return bool
     */
    public function codeExists($code, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM discount_codes WHERE code = :code";
        $params = ['code' => trim($code)];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Tạo mã giảm giá mới
     * @param array $data
     * @return int|false
     */
    public function create($data)
    {
        $sql = "INSERT INTO discount_codes 
                (code, name, discount_type, discount_value, min_purchase, start_date, end_date, usage_limit, status, created_by)
                VALUES 
                (:code, :name, :discount_type, :discount_value, :min_purchase, :start_date, :end_date, :usage_limit, :status, :created_by)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => (float) $data['discount_value'],
            'min_purchase' => !empty($data['min_purchase']) ? (float) $data['min_purchase'] : 0,
            'start_date' => !empty($data['start_date']) ? $data['start_date'] : null,
            'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
            'usage_limit' => !empty($data['usage_limit']) ? (int) $data['usage_limit'] : 0,
            'status' => $data['status'] ?? 'active',
            'created_by' => $data['created_by'] ?? null
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật mã giảm giá
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        $allowedFields = ['code', 'name', 'discount_type', 'discount_value', 'min_purchase', 
                         'start_date', 'end_date', 'usage_limit', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                if ($field === 'code') {
                    $params[$field] = strtoupper(trim($data[$field]));
                } elseif (in_array($field, ['discount_value', 'min_purchase'])) {
                    $params[$field] = (float) $data[$field];
                } elseif ($field === 'usage_limit') {
                    $params[$field] = (int) $data[$field];
                } elseif (in_array($field, ['start_date', 'end_date']) && empty($data[$field])) {
                    $params[$field] = null;
                } else {
                    $params[$field] = $data[$field];
                }
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE discount_codes SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa mã giảm giá
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // Kiểm tra xem mã có đang được sử dụng không
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE discount_code = (SELECT code FROM discount_codes WHERE id = :id)");
        $stmt->execute(['id' => $id]);
        $usageCount = $stmt->fetchColumn();

        if ($usageCount > 0) {
            throw new Exception("Không thể xóa mã giảm giá này vì đang được sử dụng bởi {$usageCount} booking. Vui lòng vô hiệu hóa thay vì xóa.");
        }

        $sql = "DELETE FROM discount_codes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Đếm số booking đã sử dụng mã
     * @param string $code
     * @return int
     */
    public function getUsageCount($code)
    {
        $sql = "SELECT COUNT(*) FROM bookings WHERE discount_code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        return (int) $stmt->fetchColumn();
    }
}

