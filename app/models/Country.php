<?php
/**
 * ==============================================================================
 * COUNTRY MODEL
 * ==============================================================================
 * 
 * Quản lý Quốc gia (Countries)
 * 
 * Relationships:
 * - Provinces (1-nhiều)
 * - Destinations (qua provinces)
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Country
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả countries
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
                $where_conditions[] = "(name LIKE :search OR name_en LIKE :search OR code LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM countries {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);
            $total = $count_result ? (int) $count_result['total'] : 0;

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT id, code, name, name_en, status, display_order, created_at, updated_at
                FROM countries
                {$where_clause}
                ORDER BY display_order ASC, name ASC
                LIMIT :limit OFFSET :offset
            ";
            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Country::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm country theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, code, name, name_en, status, display_order, created_at, updated_at
                FROM countries
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Country::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm country theo code
     */
    public function findByCode($code)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, code, name FROM countries WHERE code = :code LIMIT 1
            ");
            $stmt->execute(['code' => strtoupper($code)]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Country::findByCode() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo country mới
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO countries (code, name, name_en, status, display_order)
                VALUES (:code, :name, :name_en, :status, :display_order)
            ");

            $success = $stmt->execute([
                'code' => strtoupper($data['code']),
                'name' => $data['name'],
                'name_en' => $data['name_en'] ?? null,
                'status' => $data['status'] ?? 'active',
                'display_order' => $data['display_order'] ?? 0
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Country::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật country
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

            // KHÔNG cho phép sửa code (vì đã được set khi tạo)

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE countries
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Country::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa country (soft delete với FK check)
     */
    public function delete($id)
    {
        try {
            // Check if being used by provinces
            $check_stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM provinces WHERE country_id = :id
            ");
            $check_stmt->execute(['id' => $id]);
            $count = $check_stmt->fetch()['count'];

            if ($count > 0) {
                throw new Exception("Không thể xóa quốc gia đang có {$count} tỉnh thành.");
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE countries
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("Country::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $country = $this->findById($id);
            if (!$country)
                return false;

            $new_status = ($country['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("Country::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy countries cho dropdown (id => name)
     */
    public function getForDropdown()
    {
        try {
            $sql = "SELECT id, name, code FROM countries WHERE status = 'active'";
            $params = [];

            $sql .= " ORDER BY display_order ASC, name ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Country::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy số lượng provinces của country
     */
    public function getProvinceCount($country_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count
                FROM provinces
                WHERE country_id = :country_id AND status = 'active'
            ");

            $stmt->execute(['country_id' => $country_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int) ($result['count'] ?? 0) : 0;

        } catch (PDOException $e) {
            error_log("Country::getProvinceCount() Error: " . $e->getMessage());
            return 0;
        }
    }
}

