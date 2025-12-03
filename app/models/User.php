<?php
/**
 * ==============================================================================
 * USER MODEL
 * ==============================================================================
 * 
 * Xử lý CRUD operations cho bảng users
 * 
 * Methods:
 * - findByEmail(): Tìm user theo email (cho login)
 * - findById(): Tìm user theo ID
 * - verifyPassword(): Xác thực password
 * - updateLastLogin(): Cập nhật last_login timestamp
 * - create(): Tạo user mới
 * - update(): Cập nhật thông tin user
 * - delete(): Xóa user
 * - getAll(): Lấy danh sách users (có pagination, filter)
 * 
 * Theo Vibe Coding: Keep it simple
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tìm user theo email
     * Dùng cho login
     * 
     * @param string $email
     * @return array|null User data hoặc null nếu không tìm thấy
     */
    public function findByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id, u.role_id, u.email, u.password, u.full_name, 
                    u.phone, u.date_of_birth, u.gender, u.address, u.avatar, 
                    u.status, u.last_login, u.created_by, u.created_at, u.updated_at,
                    r.name as role, r.display_name as role_display
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email
                LIMIT 1
            ");

            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            return $user ?: null;

        } catch (PDOException $e) {
            logError("User::findByEmail() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra email đã tồn tại chưa (exclude user ID nếu update)
     * 
     * @param string $email
     * @param int|null $excludeId User ID để exclude (khi update)
     * @return bool
     */
    public function isEmailExists($email, $excludeId = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            $params = ['email' => $email];

            if ($excludeId) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $excludeId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();

            return $count > 0;

        } catch (PDOException $e) {
            logError("User::isEmailExists() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm user theo ID
     * 
     * @param int $id
     * @return array|null
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id, u.role_id, u.email, u.password, u.full_name, 
                    u.phone, u.date_of_birth, u.gender, u.address, u.avatar, 
                    u.status, u.last_login, u.created_by, u.created_at, u.updated_at,
                    r.name as role, r.display_name as role_display
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();

            return $user ?: null;

        } catch (PDOException $e) {
            logError("User::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify password
     * 
     * @param string $plain_password
     * @param string $hashed_password
     * @return bool
     */
    public function verifyPassword($plain_password, $hashed_password)
    {
        return password_verify($plain_password, $hashed_password);
    }

    /**
     * Cập nhật last_login timestamp
     * 
     * @param int $user_id
     * @return bool
     */
    public function updateLastLogin($user_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET last_login = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $user_id]);

        } catch (PDOException $e) {
            logError("User::updateLastLogin() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách users (cho admin quản lý)
     * 
     * @param array $filters ['role' => 'staff', 'status' => 'active', 'search' => 'keyword']
     * @param int $page
     * @param int $per_page
     * @return array ['data' => [...], 'total' => 100, 'pages' => 10]
     */
    public function getAll($filters = [], $page = 1, $per_page = 10)
    {
        try {
            // Build WHERE clause
            $where_conditions = [];
            $params = [];

            if (!empty($filters['role'])) {
                $where_conditions[] = "r.name = :role";
                $params['role'] = $filters['role'];
            }

            if (!empty($filters['status'])) {
                $where_conditions[] = "u.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.phone LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "
                SELECT COUNT(*) as total
                FROM users u
                JOIN roles r ON u.role_id = r.id
                {$where_clause}
            ";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get paginated data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT 
                    u.id, u.role_id, u.email, u.password, u.full_name, 
                    u.phone, u.date_of_birth, u.gender, u.address, u.avatar, 
                    u.status, u.last_login, u.created_by, u.created_at, u.updated_at,
                    r.name as role, r.display_name as role_display
                FROM users u
                JOIN roles r ON u.role_id = r.id
                {$where_clause}
                ORDER BY u.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll();

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            logError("User::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tạo user mới (Admin function)
     * 
     * @param array $data
     * @return int|false User ID nếu thành công, false nếu lỗi
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (
                    role_id, email, password, full_name, phone,
                    date_of_birth, gender, address, status, created_by
                ) VALUES (
                    :role_id, :email, :password, :full_name, :phone,
                    :date_of_birth, :gender, :address, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'role_id' => $data['role_id'],
                'email' => $data['email'],
                'password' => hash_password($data['password']),
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'gender' => $data['gender'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            logError("User::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            // Build SET clause dynamically
            $allowed_fields = ['role_id', 'full_name', 'phone', 'date_of_birth', 'gender', 'address', 'status', 'avatar'];
            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            // Update password nếu có
            if (!empty($data['password'])) {
                $set_parts[] = "password = :password";
                $params['password'] = hash_password($data['password']);
            }

            if (empty($set_parts)) {
                return false; // Nothing to update
            }

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE users
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            logError("User::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user (soft delete - change status)
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            logError("User::delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hard delete user (CAREFUL!)
     * 
     * @param int $id
     * @return bool
     */
    public function hardDelete($id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            logError("User::hardDelete() Error: " . $e->getMessage());
            return false;
        }
    }
}
