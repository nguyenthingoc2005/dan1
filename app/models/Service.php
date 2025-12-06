<?php
/**
 * ==============================================================================
 * SERVICE MODEL - ĐÃ SỬA LẠI THEO DATABASE SCHEMA
 * ==============================================================================
 * 
 * Quản lý dịch vụ - Kết nối service_types và service_providers
 * 
 * Relationships:
 * - service_type_id → service_types (OPTIONAL, có thể NULL)
 * - service_provider_id → service_providers (REQUIRED, NOT NULL)
 * 
 * Fields trong database:
 * - id, service_provider_id, service_type_id, name, description, unit, status
 * - created_by, created_at, updated_at
 * 
 * @version 2.0
 * @date 2024-12-06
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
     * Lấy tất cả services với join service_types và service_providers
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

            // Filter by service provider
            if (!empty($filters['service_provider_id'])) {
                $where_conditions[] = "s.service_provider_id = :service_provider_id";
                $params['service_provider_id'] = (int) $filters['service_provider_id'];
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

            $total = $count_stmt->fetch()['total'] ?? 0;

            // Get data with joins
            $offset = ($page - 1) * $per_page;

            $data_sql = "
                SELECT 
                    s.*,
                    st.name as service_type_name,
                    sp.name as service_provider_name,
                    sp.service_code as service_provider_code
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN service_providers sp ON s.service_provider_id = sp.id
                {$where_clause}
                ORDER BY s.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $data_stmt = $this->pdo->prepare($data_sql);

            // Bind WHERE clause parameters
            foreach ($params as $key => $value) {
                $data_stmt->bindValue(':' . $key, $value);
            }

            // Bind LIMIT and OFFSET separately as integers
            $data_stmt->bindValue(':limit', (int) $per_page, PDO::PARAM_INT);
            $data_stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

            $data_stmt->execute();
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Service::getAll() Error: " . $e->getMessage());
            error_log("Service::getAll() Stack trace: " . $e->getTraceAsString());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        } catch (Exception $e) {
            error_log("Service::getAll() General Error: " . $e->getMessage());
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
                    sp.name as service_provider_name,
                    sp.service_code as service_provider_code,
                    sp.province_id,
                    sp.country_id,
                    p.name as province_name,
                    c.name as country_name
                FROM services s
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN service_providers sp ON s.service_provider_id = sp.id
                LEFT JOIN provinces p ON sp.province_id = p.id
                LEFT JOIN countries c ON sp.country_id = c.id
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
                    service_provider_id, service_type_id, name, description,
                    unit, status, created_by
                ) VALUES (
                    :service_provider_id, :service_type_id, :name, :description,
                    :unit, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'service_provider_id' => $data['service_provider_id'],
                'service_type_id' => !empty($data['service_type_id']) ? (int) $data['service_type_id'] : null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'unit' => $data['unit'] ?? null,
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
                'service_provider_id',
                'service_type_id',
                'name',
                'description',
                'unit',
                'status'
            ];

            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    if ($field === 'service_type_id' && empty($data[$field])) {
                        $set_parts[] = "{$field} = NULL";
                    } else {
                        $set_parts[] = "{$field} = :{$field}";
                        $params[$field] = $data[$field];
                    }
                }
            }

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
            $check_tour = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM tour_services WHERE service_id = :id
            ");
            $check_tour->execute(['id' => $id]);
            $tour_count = $check_tour->fetch()['count'];

            // Check if being used in booking_services
            $check_booking = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM booking_services WHERE service_id = :id
            ");
            $check_booking->execute(['id' => $id]);
            $booking_count = $check_booking->fetch()['count'];

            // Check if being used in itinerary_day_services
            $check_itinerary = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM itinerary_day_services WHERE service_id = :id
            ");
            $check_itinerary->execute(['id' => $id]);
            $itinerary_count = $check_itinerary->fetch()['count'];

            if ($tour_count > 0 || $booking_count > 0 || $itinerary_count > 0) {
                $message = "Không thể xóa dịch vụ đang được sử dụng";
                $parts = [];
                if ($tour_count > 0) {
                    $parts[] = "{$tour_count} tour";
                }
                if ($booking_count > 0) {
                    $parts[] = "{$booking_count} booking";
                }
                if ($itinerary_count > 0) {
                    $parts[] = "{$itinerary_count} itinerary day";
                }
                $message .= " trong " . implode(" và ", $parts) . ".";
                throw new Exception($message);
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
                SELECT id, name, unit, service_provider_id
                FROM services
                WHERE service_type_id = :type_id AND status = 'active'
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
     * Lấy services cho dropdown (by service provider)
     */
    public function getByServiceProvider($service_provider_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, unit, service_type_id
                FROM services
                WHERE service_provider_id = :service_provider_id AND status = 'active'
                ORDER BY name ASC
            ");

            $stmt->execute(['service_provider_id' => $service_provider_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Service::getByServiceProvider() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm service theo name + service_provider + type (check duplicate)
     * 
     * @param string $name
     * @param int $service_provider_id
     * @param int|null $service_type_id
     * @param int|null $exclude_id (exclude this ID when checking for update)
     * @return array|null
     */
    public function findByNameAndServiceProvider($name, $service_provider_id, $service_type_id = null, $exclude_id = null)
    {
        try {
            $sql = "
                SELECT * FROM services
                WHERE name = :name 
                  AND service_provider_id = :service_provider_id
            ";

            $params = [
                'name' => $name,
                'service_provider_id' => $service_provider_id
            ];

            if ($service_type_id !== null) {
                $sql .= " AND (service_type_id = :service_type_id OR service_type_id IS NULL)";
                $params['service_type_id'] = $service_type_id;
            } else {
                $sql .= " AND service_type_id IS NULL";
            }

            if ($exclude_id) {
                $sql .= " AND id != :exclude_id";
                $params['exclude_id'] = $exclude_id;
            }

            $sql .= " LIMIT 1";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Service::findByNameAndServiceProvider() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get for dropdown (simple list)
     */
    public function getForDropdown($filters = [])
    {
        try {
            $where_conditions = ["s.status = 'active'"];
            $params = [];

            if (!empty($filters['service_type_id'])) {
                $where_conditions[] = "s.service_type_id = :service_type_id";
                $params['service_type_id'] = $filters['service_type_id'];
            }

            if (!empty($filters['service_provider_id'])) {
                $where_conditions[] = "s.service_provider_id = :service_provider_id";
                $params['service_provider_id'] = $filters['service_provider_id'];
            }

            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

            $stmt = $this->pdo->prepare("
                SELECT s.id, s.name, s.unit
                FROM services s
                {$where_clause}
                ORDER BY s.name ASC
            ");

            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Service::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }
}
