<?php
/**
 * ==============================================================================
 * SERVICE MODEL
 * ==============================================================================
 * 
 * Quản lý dịch vụ - Kết nối service_types và suppliers
 * 
 * Relationships:
 * - service_type_id → service_types (REQUIRED)
 * - supplier_id → suppliers (REQUIRED)
 * 
 * Fields:
 * - name, description, unit_price
 * - capacity (sức chứa/số lượng)
 * - availability (trạng thái sẵn có)
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class Service
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả services với join service_types và suppliers
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Filter by service type
            if (!empty($filters['service_type_id'])) {
                $where_conditions[] = "s.service_type_id = :service_type_id";
                $params['service_type_id'] = $filters['service_type_id'];
            }

            // Filter by supplier
            if (!empty($filters['supplier_id'])) {
                $where_conditions[] = "s.supplier_id = :supplier_id";
                $params['supplier_id'] = $filters['supplier_id'];
            }

            // Filter by status
            if (!empty($filters['status'])) {
                $where_conditions[] = "s.status = :status";
                $params['status'] = $filters['status'];
            }

            // Search
            if (!empty($filters['search'])) {
                $where_conditions[] = "(s.name LIKE :search OR s.description LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "
                SELECT COUNT(*) as total 
                FROM services s
                {$where_clause}
            ";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data with joins
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT 
                    s.*,
                    st.name as service_type_name,
                    st.code as service_type_code,
                    sup.company_name as supplier_name,
                    sup.supplier_code
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                {$where_clause}
                ORDER BY s.created_at DESC
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
            error_log("Service::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm service theo ID (with joins)
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    s.*,
                    st.name as service_type_name,
                    sup.company_name as supplier_name
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                WHERE s.id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Service::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo service mới
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO services (
                    service_type_id, supplier_id, name, description,
                    unit_price, capacity, availability, notes, status, created_by
                ) VALUES (
                    :service_type_id, :supplier_id, :name, :description,
                    :unit_price, :capacity, :availability, :notes, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'service_type_id' => $data['service_type_id'],
                'supplier_id' => $data['supplier_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'unit_price' => $data['unit_price'] ?? null,
                'capacity' => $data['capacity'] ?? null,
                'availability' => $data['availability'] ?? 'available',
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Service::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật service
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = [
                'service_type_id',
                'supplier_id',
                'name',
                'description',
                'unit_price',
                'capacity',
                'availability',
                'notes',
                'status'
            ];

            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            // Add updated_by
            $set_parts[] = "updated_by = :updated_by";
            $params['updated_by'] = get_user_id();

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE services
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Service::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa service (soft delete với FK check)
     */
    public function delete($id)
    {
        try {
            // Check if being used in tour_services
            $check_stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM tour_services WHERE service_id = :id
            ");
            $check_stmt->execute(['id' => $id]);
            $count = $check_stmt->fetch()['count'];

            if ($count > 0) {
                throw new Exception("Không thể xóa dịch vụ đang được sử dụng trong {$count} tour.");
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE services
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("Service::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $service = $this->findById($id);
            if (!$service)
                return false;

            $new_status = ($service['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("Service::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy services cho dropdown (by service type)
     */
    public function getByServiceType($service_type_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, unit_price, supplier_id
                FROM services
                WHERE service_type_id = :type_id AND status = 'active' AND availability = 'available'
                ORDER BY name ASC
            ");

            $stmt->execute(['type_id' => $service_type_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Service::getByServiceType() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy services cho dropdown (by supplier)
     */
    public function getBySupplier($supplier_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, unit_price, service_type_id
                FROM services
                WHERE supplier_id = :supplier_id AND status = 'active'
                ORDER BY name ASC
            ");

            $stmt->execute(['supplier_id' => $supplier_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Service::getBySupplier() Error: " . $e->getMessage());
            return [];
        }
    }
}
