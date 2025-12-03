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
        if (empty($email)) return false;
        
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
        if (empty($idCard)) return false;
        
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
     * Format: KH-YYYYMM-XXXX
     */
    public function generateCustomerCode()
    {
        $prefix = 'KH-' . date('Ym') . '-';
        
        $sql = "SELECT customer_code FROM customers WHERE customer_code LIKE :prefix ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['prefix' => $prefix . '%']);
        $lastCode = $stmt->fetchColumn();
        
        if ($lastCode) {
            $number = (int) substr($lastCode, -4);
            return $prefix . str_pad($number + 1, 4, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '0001';
    }

    // ========================================================================
    // CRUD METHODS
    // ========================================================================

    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        $sql = "SELECT * FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        if (!empty($filters['search'])) {
            $countSql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
        }
        if (!empty($filters['created_by'])) {
            $countSql .= " AND created_by = :created_by";
        }

        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Pagination
        $sql .= " ORDER BY created_at DESC LIMIT :offset, :limit";
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
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
        $sql = "SELECT * FROM customers WHERE full_name LIKE :kw OR phone LIKE :kw OR email LIKE :kw LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['kw' => "%$keyword%"]);
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
}
