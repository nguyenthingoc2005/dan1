<?php
/**
 * ==============================================================================
 * PROVINCE MODEL
 * ==============================================================================
 * 
 * Quản lý Tỉnh thành (Provinces)
 * 
 * Relationships:
 * - country_id → countries (REQUIRED)
 * - Destinations (1-nhiều)
 * - Service Providers (1-nhiều)
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Province
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả provinces
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($filters['country_id'])) {
                $where_conditions[] = "p.country_id = :country_id";
                $params['country_id'] = $filters['country_id'];
            }

            if (!empty($filters['status'])) {
                $where_conditions[] = "p.status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(p.name LIKE :search OR p.name_en LIKE :search OR p.code LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM provinces p {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data with country info
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT 
                    p.*,
                    c.name as country_name,
                    c.code as country_code
                FROM provinces p
                LEFT JOIN countries c ON p.country_id = c.id
                {$where_clause}
                ORDER BY p.display_order ASC, p.name ASC
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
            error_log("Province::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm province theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    p.*,
                    c.name as country_name,
                    c.code as country_code
                FROM provinces p
                LEFT JOIN countries c ON p.country_id = c.id
                WHERE p.id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Province::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm province theo code
     */
    public function findByCode($code)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name, code, country_id FROM provinces WHERE code = :code LIMIT 1
            ");
            $stmt->execute(['code' => $code]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Province::findByCode() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo province mới
     */
    public function create($data)
    {
        try {
            // Validate country_id exists
            $country_stmt = $this->pdo->prepare("SELECT id FROM countries WHERE id = :id AND status = 'active'");
            $country_stmt->execute(['id' => $data['country_id']]);
            if (!$country_stmt->fetch()) {
                throw new Exception("Quốc gia không tồn tại hoặc đã bị vô hiệu hóa.");
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO provinces (country_id, code, name, name_en, status, display_order)
                VALUES (:country_id, :code, :name, :name_en, :status, :display_order)
            ");

            $success = $stmt->execute([
                'country_id' => $data['country_id'],
                'code' => $data['code'] ?? null,
                'name' => $data['name'],
                'name_en' => $data['name_en'] ?? null,
                'status' => $data['status'] ?? 'active',
                'display_order' => $data['display_order'] ?? 0
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Province::create() Error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Province::create() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật province
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = ['name', 'name_en', 'status', 'display_order'];
            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            // KHÔNG cho phép sửa country_id và code (vì đã được set khi tạo)

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE provinces
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Province::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa province (soft delete với FK check)
     */
    public function delete($id)
    {
        try {
            // Check if being used by destinations
            $check_dest = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM destinations WHERE province_id = :id
            ");
            $check_dest->execute(['id' => $id]);
            $dest_count = $check_dest->fetch()['count'];

            // Check if being used by service_providers
            $check_providers = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM service_providers WHERE province_id = :id
            ");
            $check_providers->execute(['id' => $id]);
            $provider_count = $check_providers->fetch()['count'];

            if ($dest_count > 0 || $provider_count > 0) {
                $message = "Không thể xóa tỉnh thành";
                $parts = [];
                if ($dest_count > 0) {
                    $parts[] = "đang có {$dest_count} địa điểm";
                }
                if ($provider_count > 0) {
                    $parts[] = "đang có {$provider_count} nhà dịch vụ";
                }
                $message .= " " . implode(" và ", $parts) . ".";
                throw new Exception($message);
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE provinces
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("Province::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $province = $this->findById($id);
            if (!$province)
                return false;

            $new_status = ($province['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("Province::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy provinces cho dropdown (theo country_id)
     */
    public function getForDropdown($country_id = null)
    {
        try {
            $sql = "
                SELECT p.id, p.name, p.code, p.status, c.name as country_name
                FROM provinces p
                LEFT JOIN countries c ON p.country_id = c.id
                WHERE p.status = 'active'
            ";
            $params = [];

            if ($country_id) {
                $sql .= " AND p.country_id = :country_id";
                $params['country_id'] = $country_id;
            }

            $sql .= " ORDER BY p.display_order ASC, p.name ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Province::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy số lượng destinations của province
     */
    public function getDestinationCount($province_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM destinations
                WHERE province_id = :province_id AND status = 'active'
            ");

            $stmt->execute(['province_id' => $province_id]);
            $result = $stmt->fetch();
            return (int) ($result['count'] ?? 0);

        } catch (PDOException $e) {
            error_log("Province::getDestinationCount() Error: " . $e->getMessage());
            return 0;
        }
    }
}

