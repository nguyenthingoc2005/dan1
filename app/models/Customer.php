<?php
/**
 * ==============================================================================
 * CUSTOMER MODEL
 * ==============================================================================
 * 
 * Quản lý khách hàng với validation đầy đủ
 * 
 * @version 1.1
 * @date 2024-12-03
 * ==============================================================================
 */

class Customer
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // ========================================================================
    // VALIDATION METHODS
    // ========================================================================

    /**
     * Kiểm tra phone đã tồn tại chưa
     * @param string $phone
     * @param int|null $excludeId - ID cần loại trừ khi update
     * @return bool
     */
    public function isPhoneExists($phone, $excludeId = null)
    {
        $sql = "SELECT id FROM customers WHERE phone = :phone";
        $params = ['phone' => $phone];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailExists($email, $excludeId = null)
    {
        if (empty($email))
            return false;

        $sql = "SELECT id FROM customers WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra CMND/CCCD đã tồn tại chưa
     * @param string $idCard
     * @param int|null $excludeId
     * @return bool
     */
    public function isIdCardExists($idCard, $excludeId = null)
    {
        if (empty($idCard))
            return false;

        $sql = "SELECT id FROM customers WHERE id_card = :id_card";
        $params = ['id_card' => $idCard];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Validate dữ liệu khách hàng
     * @param array $data
     * @param int|null $excludeId - Khi update
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate($data, $excludeId = null)
    {
        $errors = [];

        // 1. Full name - Required
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Vui lòng nhập họ tên khách hàng';
        } elseif (mb_strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Họ tên phải có ít nhất 2 ký tự';
        } elseif (mb_strlen($data['full_name']) > 100) {
            $errors['full_name'] = 'Họ tên không được quá 100 ký tự';
        }

        // 2. Phone - Required, Format, Unique
        if (empty($data['phone'])) {
            $errors['phone'] = 'Vui lòng nhập số điện thoại';
        } else {
            $phone = preg_replace('/[\s\-\(\)]/', '', $data['phone']);
            if (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone)) {
                $errors['phone'] = 'Số điện thoại không hợp lệ (VD: 0901234567)';
            } elseif ($this->isPhoneExists($phone, $excludeId)) {
                $errors['phone'] = 'Số điện thoại đã được sử dụng bởi khách hàng khác';
            }
        }

        // 3. Email - Optional, Format, Unique
        if (!empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email không hợp lệ';
            } elseif ($this->isEmailExists($data['email'], $excludeId)) {
                $errors['email'] = 'Email đã được sử dụng bởi khách hàng khác';
            }
        }

        // 4. ID Card - Optional, Format (9 or 12 digits), Unique
        if (!empty($data['id_card'])) {
            $idCard = preg_replace('/\s/', '', $data['id_card']);
            if (!preg_match('/^[0-9]{9}$|^[0-9]{12}$/', $idCard)) {
                $errors['id_card'] = 'CMND/CCCD phải là 9 hoặc 12 chữ số';
            } elseif ($this->isIdCardExists($idCard, $excludeId)) {
                $errors['id_card'] = 'CMND/CCCD đã được sử dụng bởi khách hàng khác';
            }
        }

        // 5. Passport - Optional, Format
        if (!empty($data['passport'])) {
            if (!preg_match('/^[A-Z][0-9]{7,8}$/', strtoupper($data['passport']))) {
                $errors['passport'] = 'Số hộ chiếu không hợp lệ (VD: B12345678)';
            }
        }

        // 6. Date of Birth - Optional, must be in past, reasonable age
        if (!empty($data['date_of_birth'])) {
            $dob = strtotime($data['date_of_birth']);
            $today = strtotime('today');
            $minDate = strtotime('-120 years'); // Max 120 tuổi

            if ($dob >= $today) {
                $errors['date_of_birth'] = 'Ngày sinh phải là ngày trong quá khứ';
            } elseif ($dob < $minDate) {
                $errors['date_of_birth'] = 'Ngày sinh không hợp lệ';
            }
        }

        // 7. Gender - Optional, whitelist
        if (!empty($data['gender'])) {
            if (!in_array($data['gender'], ['male', 'female', 'other'])) {
                $errors['gender'] = 'Giới tính không hợp lệ';
            }
        }

        // 8. Customer Type - Optional, whitelist
        if (!empty($data['customer_type'])) {
            if (!in_array($data['customer_type'], ['individual', 'group', 'corporate'])) {
                $errors['customer_type'] = 'Loại khách hàng không hợp lệ';
            }
        }

        // 9. Source - Optional, whitelist
        if (!empty($data['source'])) {
            if (!in_array($data['source'], ['phone', 'email', 'facebook', 'zalo', 'walk_in', 'other'])) {
                $errors['source'] = 'Nguồn khách hàng không hợp lệ';
            }
        }

        // 10. Status - Optional, whitelist
        if (!empty($data['status'])) {
            if (!in_array($data['status'], ['active', 'inactive', 'blacklist'])) {
                $errors['status'] = 'Trạng thái không hợp lệ';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate customer code
     * Format: CUS-YYYYMMDD-XXX
     */
    public function generateCustomerCode()
    {
        $date = date('Ymd'); // YYYYMMDD
        $prefix = 'CUS-' . $date . '-';

        $sql = "SELECT customer_code FROM customers 
                WHERE customer_code LIKE :prefix 
                ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastCode = $stmt->fetchColumn();

        if ($lastCode) {
            $number = (int) substr($lastCode, -3); // Lấy 3 số cuối
            $nextNumber = $number + 1;

            // Edge case: Nếu vượt quá 999, tăng lên 4 số
            if ($nextNumber > 999) {
                return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . '001';
    }

    // ========================================================================
    // CRUD METHODS
    // ========================================================================

    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        try {
            // Build WHERE conditions
            $where_conditions = [];
            $params = [];

            // Search filter - tìm kiếm theo: tên, SĐT, email, mã KH, CMND/CCCD, hộ chiếu
            // Dùng % ở cả đầu và cuối: %keyword%
            if (!empty($filters['search'])) {
                $where_conditions[] = "(full_name LIKE :search 
                                        OR phone LIKE :search 
                                        OR email LIKE :search 
                                        OR customer_code LIKE :search 
                                        OR id_card LIKE :search 
                                        OR passport LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            // Status filter
            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            // Created by filter
            if (!empty($filters['created_by'])) {
                $where_conditions[] = "created_by = :created_by";
                $params['created_by'] = (int) $filters['created_by'];
            }

            // Build WHERE clause
            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total - dùng cùng WHERE clause và params
            $count_sql = "SELECT COUNT(*) as total FROM customers {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'] ?? 0;

            // Get paginated data
            $offset = ($page - 1) * $limit;
            $params['offset'] = $offset;
            $params['limit'] = $limit;

            $data_sql = "
                SELECT * 
                FROM customers 
                {$where_clause}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Customer::getAll() Error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => $page
            ];
        }
    }

    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $searchValue = '%' . $filters['search'] . '%';
            // Tìm kiếm theo: tên, SĐT, email, mã KH, CMND/CCCD, hộ chiếu
            $sql .= " AND (full_name LIKE :search 
                        OR phone LIKE :search 
                        OR email LIKE :search 
                        OR customer_code LIKE :search 
                        OR id_card LIKE :search 
                        OR passport LIKE :search)";
            $params['search'] = $searchValue;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function search($keyword)
    {
        // Dùng % ở cả đầu và cuối: %keyword%
        $searchValue = '%' . $keyword . '%';
        $sql = "SELECT * FROM customers 
                WHERE full_name LIKE :kw 
                   OR phone LIKE :kw 
                   OR email LIKE :kw 
                   OR customer_code LIKE :kw 
                   OR id_card LIKE :kw 
                   OR passport LIKE :kw 
                LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['kw' => $searchValue]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM customers WHERE phone = :phone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Generate customer code
        $customerCode = $this->generateCustomerCode();

        // Normalize phone number
        $phone = preg_replace('/[\s\-\(\)]/', '', $data['phone']);

        $sql = "INSERT INTO customers (
                    customer_code, full_name, phone, email, address, 
                    gender, date_of_birth, id_card, passport, nationality,
                    customer_type, source, special_requirements, notes, 
                    status, created_by
                ) VALUES (
                    :customer_code, :full_name, :phone, :email, :address,
                    :gender, :date_of_birth, :id_card, :passport, :nationality,
                    :customer_type, :source, :special_requirements, :notes,
                    :status, :created_by
                )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'customer_code' => $customerCode,
            'full_name' => $data['full_name'],
            'phone' => $phone,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'id_card' => isset($data['id_card']) ? preg_replace('/\s/', '', $data['id_card']) : null,
            'passport' => isset($data['passport']) ? strtoupper($data['passport']) : null,
            'nationality' => $data['nationality'] ?? 'Vietnam',
            'customer_type' => $data['customer_type'] ?? 'individual',
            'source' => $data['source'] ?? 'walk_in',
            'special_requirements' => $data['special_requirements'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'active',
            'created_by' => $data['created_by'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $sql = "UPDATE customers SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        // Check if has bookings
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return false; // Cannot delete
        }

        $stmt = $this->pdo->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ========================================================================
    // STATISTICS METHODS
    // ========================================================================

    /**
     * Update customer statistics (total_bookings, total_spent)
     * Should be called when booking status changes (approve, cancel, reject)
     * 
     * Logic:
     * - total_bookings: Count of bookings where customer is primary (customer_id)
     * - total_spent: Sum of final_amount of approved/completed bookings
     * 
     * @param int $customer_id
     * @return bool
     */
    public function updateCustomerStats($customer_id)
    {
        // Calculate total_bookings (only approved/completed bookings)
        $bookingsSql = "SELECT COUNT(*) 
                        FROM bookings 
                        WHERE customer_id = :customer_id 
                          AND payment_status IN ('partial', 'paid')";
        $stmt = $this->pdo->prepare($bookingsSql);
        $stmt->execute(['customer_id' => $customer_id]);
        $total_bookings = (int) $stmt->fetchColumn();

        // Calculate total_spent (sum of final_amount of approved/completed bookings)
        $spentSql = "SELECT COALESCE(SUM(final_amount), 0) 
                     FROM bookings 
                     WHERE customer_id = :customer_id 
                       AND payment_status IN ('partial', 'paid')";
        $stmt = $this->pdo->prepare($spentSql);
        $stmt->execute(['customer_id' => $customer_id]);
        $total_spent = (float) $stmt->fetchColumn();

        // Update customer
        $updateSql = "UPDATE customers 
                      SET total_bookings = :total_bookings, 
                          total_spent = :total_spent,
                          updated_at = NOW()
                      WHERE id = :customer_id";
        $stmt = $this->pdo->prepare($updateSql);
        return $stmt->execute([
            'customer_id' => $customer_id,
            'total_bookings' => $total_bookings,
            'total_spent' => $total_spent
        ]);
    }

    /**
     * Get customer statistics (real-time calculation, không lưu DB)
     * Useful for displaying accurate stats without updating DB
     * 
     * @param int $customer_id
     * @return array ['total_bookings' => int, 'total_spent' => float]
     */
    public function getCustomerStats($customer_id)
    {
        // Total bookings
        $bookingsSql = "SELECT COUNT(*) 
                        FROM bookings 
                        WHERE customer_id = :customer_id 
                          AND payment_status IN ('partial', 'paid')";
        $stmt = $this->pdo->prepare($bookingsSql);
        $stmt->execute(['customer_id' => $customer_id]);
        $total_bookings = (int) $stmt->fetchColumn();

        // Total spent
        $spentSql = "SELECT COALESCE(SUM(final_amount), 0) 
                     FROM bookings 
                     WHERE customer_id = :customer_id 
                       AND payment_status IN ('partial', 'paid')";
        $stmt = $this->pdo->prepare($spentSql);
        $stmt->execute(['customer_id' => $customer_id]);
        $total_spent = (float) $stmt->fetchColumn();

        return [
            'total_bookings' => $total_bookings,
            'total_spent' => $total_spent
        ];
    }

    /**
     * Recalculate stats for all customers (for fixing existing data)
     * 
     * @return int Number of customers updated
     */
    public function recalculateAllCustomerStats()
    {
        $sql = "SELECT id FROM customers";
        $stmt = $this->pdo->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $updated = 0;
        foreach ($customers as $customer_id) {
            if ($this->updateCustomerStats($customer_id)) {
                $updated++;
            }
        }

        return $updated;
    }
}
