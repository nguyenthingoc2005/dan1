<?php
/**
 * ==============================================================================
 * DRIVER MODEL
 * ==============================================================================
 * 
 * Quản lý tài xế
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Driver
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả drivers (có phân trang & lọc)
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Filter by Status
            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            // Filter by License Type
            if (!empty($filters['license_type'])) {
                $where_conditions[] = "license_type = :license_type";
                $params['license_type'] = $filters['license_type'];
            }

            // Search by Name, Code, Phone, License Number
            if (!empty($filters['search'])) {
                $where_conditions[] = "(driver_code LIKE :search OR full_name LIKE :search OR phone LIKE :search OR license_number LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM drivers {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT * 
                FROM drivers 
                {$where_clause}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            $total_pages = ceil($total / $per_page);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => $total_pages,
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            logError("Driver::getAll() Error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1
            ];
        }
    }

    /**
     * Tìm driver theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM drivers WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            logError("Driver::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách tài xế có sẵn (không trùng lịch)
     * 
     * @param string $start_date Ngày bắt đầu
     * @param string $end_date Ngày kết thúc
     * @param array $license_types Mảng các loại bằng lái phù hợp (VD: ['D', 'E'])
     * @return array
     */
    public function getAvailable($start_date, $end_date, $license_types = [])
    {
        try {
            $license_condition = '';
            $params = [
                'start_date' => $start_date,
                'end_date' => $end_date
            ];

            if (!empty($license_types)) {
                $placeholders = [];
                foreach ($license_types as $index => $type) {
                    $key = 'license_type_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $type;
                }
                $license_condition = 'AND d.license_type IN (' . implode(',', $placeholders) . ')';
            }

            $sql = "
                SELECT d.*
                FROM drivers d
                WHERE d.status = 'active'
                  {$license_condition}
                  AND d.id NOT IN (
                      SELECT ds.driver_id
                      FROM driver_schedules ds
                      WHERE ds.status IN ('scheduled', 'confirmed', 'in_progress')
                        AND ds.schedule_date BETWEEN :start_date AND :end_date
                  )
                ORDER BY d.full_name ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("Driver::getAvailable() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo driver mới
     */
    public function create($data)
    {
        try {
            $sql = "
                INSERT INTO drivers (
                    driver_code, full_name, phone, email, id_card,
                    license_number, license_type, license_issue_date, license_expiry_date,
                    date_of_birth, address, emergency_contact_name, emergency_contact_phone,
                    status, hire_date, notes
                ) VALUES (
                    :driver_code, :full_name, :phone, :email, :id_card,
                    :license_number, :license_type, :license_issue_date, :license_expiry_date,
                    :date_of_birth, :address, :emergency_contact_name, :emergency_contact_phone,
                    :status, :hire_date, :notes
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'driver_code' => $data['driver_code'] ?? null,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'id_card' => $data['id_card'] ?? null,
                'license_number' => $data['license_number'],
                'license_type' => $data['license_type'] ?? null,
                'license_issue_date' => $data['license_issue_date'] ?? null,
                'license_expiry_date' => $data['license_expiry_date'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'status' => $data['status'] ?? 'active',
                'hire_date' => $data['hire_date'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            logError("Driver::create() Error: " . $e->getMessage());
            throw new Exception("Không thể tạo tài xế mới: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật driver
     */
    public function update($id, $data)
    {
        try {
            $sql = "
                UPDATE drivers SET
                    driver_code = :driver_code,
                    full_name = :full_name,
                    phone = :phone,
                    email = :email,
                    id_card = :id_card,
                    license_number = :license_number,
                    license_type = :license_type,
                    license_issue_date = :license_issue_date,
                    license_expiry_date = :license_expiry_date,
                    date_of_birth = :date_of_birth,
                    address = :address,
                    emergency_contact_name = :emergency_contact_name,
                    emergency_contact_phone = :emergency_contact_phone,
                    status = :status,
                    hire_date = :hire_date,
                    notes = :notes
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'driver_code' => $data['driver_code'] ?? null,
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'id_card' => $data['id_card'] ?? null,
                'license_number' => $data['license_number'],
                'license_type' => $data['license_type'] ?? null,
                'license_issue_date' => $data['license_issue_date'] ?? null,
                'license_expiry_date' => $data['license_expiry_date'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address' => $data['address'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'status' => $data['status'],
                'hire_date' => $data['hire_date'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);

            return true;

        } catch (PDOException $e) {
            logError("Driver::update() Error: " . $e->getMessage());
            throw new Exception("Không thể cập nhật tài xế: " . $e->getMessage());
        }
    }

    /**
     * Xóa driver (soft delete - cập nhật status)
     */
    public function delete($id)
    {
        try {
            $sql = "UPDATE drivers SET status = 'inactive' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            logError("Driver::delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra driver_code đã tồn tại chưa
     */
    public function isCodeExists($code, $excludeId = null)
    {
        $sql = "SELECT id FROM drivers WHERE driver_code = :code";
        $params = ['code' => $code];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra license_number đã tồn tại chưa
     */
    public function isLicenseNumberExists($license_number, $excludeId = null)
    {
        $sql = "SELECT id FROM drivers WHERE license_number = :license_number";
        $params = ['license_number' => $license_number];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
}

