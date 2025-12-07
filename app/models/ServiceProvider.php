<?php
/**
 * ==============================================================================
 * SERVICE PROVIDER MODEL
 * ==============================================================================
 * 
 * Quản lý Nhà dịch vụ (Service Providers)
 * 
 * Relationships:
 * - province_id → provinces (REQUIRED)
 * - country_id → countries (REQUIRED)
 * - Services (1-nhiều) - mỗi service có service_type_id riêng
 * 
 * @version 2.0
 * @date 2024-12-06
 * ==============================================================================
 */

class ServiceProvider
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Auto-generate service code: SP-YYYYMMDD-XXX
     */
    public function generateServiceCode()
    {
        try {
            $date = date('Ymd');
            $prefix = "SP-{$date}-";

            $stmt = $this->pdo->prepare("
                SELECT service_code 
                FROM service_providers 
                WHERE service_code LIKE :pattern 
                ORDER BY service_code DESC 
                LIMIT 1
            ");
            $stmt->execute(['pattern' => $prefix . '%']);
            $latest = $stmt->fetch();

            if ($latest) {
                $num = (int) substr($latest['service_code'], -3);
                $num++;
            } else {
                $num = 1;
            }

            return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);

        } catch (PDOException $e) {
            error_log("ServiceProvider::generateServiceCode() Error: " . $e->getMessage());
            return 'SP-' . date('Ymd') . '-001';
        }
    }

    /**
     * Lấy tất cả service providers
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($filters['province_id'])) {
                $where_conditions[] = "sp.province_id = :province_id";
                $params['province_id'] = $filters['province_id'];
            }

            if (!empty($filters['country_id'])) {
                $where_conditions[] = "sp.country_id = :country_id";
                $params['country_id'] = $filters['country_id'];
            }

            // Filter by service_type_id thông qua services (một nhà cung cấp có thể có nhiều loại dịch vụ)
            if (!empty($filters['service_type_id'])) {
                $where_conditions[] = "EXISTS (
                    SELECT 1 FROM services s 
                    WHERE s.service_provider_id = sp.id 
                    AND s.service_type_id = :service_type_id
                )";
                $params['service_type_id'] = $filters['service_type_id'];
            }

            if (!empty($filters['status'])) {
                $where_conditions[] = "sp.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(sp.name LIKE :search OR sp.service_code LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM service_providers sp {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data with joins
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT 
                    sp.*,
                    p.name as province_name,
                    c.name as country_name
                FROM service_providers sp
                LEFT JOIN provinces p ON sp.province_id = p.id
                LEFT JOIN countries c ON sp.country_id = c.id
                {$where_clause}
                ORDER BY sp.created_at DESC
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
            error_log("ServiceProvider::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm service provider theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    sp.*,
                    p.name as province_name,
                    c.name as country_name
                FROM service_providers sp
                LEFT JOIN provinces p ON sp.province_id = p.id
                LEFT JOIN countries c ON sp.country_id = c.id
                WHERE sp.id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("ServiceProvider::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo service provider mới
     */
    public function create($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Validate required fields
            if (empty($data['province_id'])) {
                throw new Exception("province_id is required.");
            }
            if (empty($data['country_id'])) {
                throw new Exception("country_id is required.");
            }

            // Auto-generate service code if not provided
            if (empty($data['service_code'])) {
                $data['service_code'] = $this->generateServiceCode();
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO service_providers (
                    service_code, name, description,
                    province_id, country_id,
                    contact_person, email, phone, website, address,
                    status, created_by
                ) VALUES (
                    :service_code, :name, :description,
                    :province_id, :country_id,
                    :contact_person, :email, :phone, :website, :address,
                    :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'service_code' => $data['service_code'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'province_id' => $data['province_id'],
                'country_id' => $data['country_id'],
                'contact_person' => $data['contact_person'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            if (!$success) {
                throw new Exception("Không thể tạo service provider.");
            }

            $provider_id = $this->pdo->lastInsertId();

            $this->pdo->commit();
            return $provider_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("ServiceProvider::create() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật service provider
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = [
                'name',
                'description',
                'service_code',
                'province_id',
                'country_id',
                'contact_person',
                'email',
                'phone',
                'website',
                'address',
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

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE service_providers
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("ServiceProvider::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa service provider (soft delete với FK check)
     */
    public function delete($id)
    {
        try {
            // Check if being used by services
            $check_services = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM services WHERE service_provider_id = :id
            ");
            $check_services->execute(['id' => $id]);
            $services_count = $check_services->fetch()['count'];

            if ($services_count > 0) {
                throw new Exception("Không thể xóa nhà dịch vụ đang cung cấp {$services_count} dịch vụ.");
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE service_providers
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("ServiceProvider::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy service providers cho dropdown
     */
    public function getForDropdown($province_id = null, $country_id = null)
    {
        try {
            $sql = "
                SELECT sp.id, sp.name, sp.service_code
                FROM service_providers sp
                WHERE sp.status = 'active'
            ";
            $params = [];

            if ($province_id) {
                $sql .= " AND sp.province_id = :province_id";
                $params['province_id'] = $province_id;
            }

            if ($country_id) {
                $sql .= " AND sp.country_id = :country_id";
                $params['country_id'] = $country_id;
            }

            $sql .= " ORDER BY sp.name ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("ServiceProvider::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy số lượng services của service provider
     */
    public function getServiceCount($service_provider_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM services
                WHERE service_provider_id = :service_provider_id AND status = 'active'
            ");

            $stmt->execute(['service_provider_id' => $service_provider_id]);
            $result = $stmt->fetch();
            return (int) ($result['count'] ?? 0);

        } catch (PDOException $e) {
            error_log("ServiceProvider::getServiceCount() Error: " . $e->getMessage());
            return 0;
        }
    }
}
