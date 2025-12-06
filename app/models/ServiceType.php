<?php
/**
 * ==============================================================================
 * SERVICE TYPE MODEL
 * ==============================================================================
 * 
 * Quản lý các loại dịch vụ: HOTEL, RESTAURANT, VEHICLE, TICKET, GUIDE, INSURANCE
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class ServiceType
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả service types
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(name LIKE :search OR description LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM service_types {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT id, name, description, status, display_order, created_at
                FROM service_types
                {$where_clause}
                ORDER BY display_order ASC, name ASC
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
            error_log("ServiceType::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm service type theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description, status, display_order, created_at
                FROM service_types
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("ServiceType::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm service type theo name (thay vì code)
     */
    public function findByName($name)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description FROM service_types WHERE name = :name LIMIT 1
            ");
            $stmt->execute(['name' => $name]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("ServiceType::findByName() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo service type mới
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO service_types (name, description, status, display_order)
                VALUES (:name, :description, :status, :display_order)
            ");

            $success = $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'display_order' => $data['display_order'] ?? 0
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("ServiceType::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật service type
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = ['name', 'description', 'status'];
            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            // KHÔNG cho phép sửa code (vì đã có trong system)

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE service_types
                SET {$set_clause}
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("ServiceType::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa service type (soft delete)
     * Kiểm tra FK constraint trước
     */
    public function delete($id)
    {
        try {
            // Check if being used by services
            $check_stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM services WHERE service_type_id = :id
            ");
            $check_stmt->execute(['id' => $id]);
            $count = $check_stmt->fetch()['count'];

            if ($count > 0) {
                throw new Exception("Không thể xóa loại dịch vụ đang được sử dụng bởi {$count} dịch vụ.");
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE service_types
                SET status = 'inactive'
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("ServiceType::delete() Error: " . $e->getMessage());
            throw $e; // Re-throw to controller
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $service_type = $this->findById($id);
            if (!$service_type)
                return false;

            $new_status = ($service_type['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("ServiceType::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy service types cho dropdown (id => name)
     */
    public function getForDropdown()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, description
                FROM service_types
                WHERE status = 'active'
                ORDER BY display_order ASC, name ASC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("ServiceType::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }
}
