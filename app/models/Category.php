<?php
/**
 * ==============================================================================
 * CATEGORY MODEL
 * ==============================================================================
 * 
 * Quản lý 3 categories cố định:
 * - Trong nước (Domestic)
 * - Ngoài nước (International)
 * - Custom Tour
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class Category
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả categories
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            // Build WHERE clause
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
            $count_sql = "SELECT COUNT(*) as total FROM categories {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT id, parent_id, name, description, display_order, status, created_at, updated_at
                FROM categories
                {$where_clause}
                ORDER BY display_order ASC, id ASC
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
            error_log("Category::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm category theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, parent_id, name, description, display_order, status, created_at, updated_at
                FROM categories
                WHERE id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Category::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm category theo tên
     */
    public function findByName($name)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name FROM categories WHERE name = :name LIMIT 1
            ");
            $stmt->execute(['name' => $name]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Category::findByName() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo category mới
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO categories (name, description, display_order, status, created_by)
                VALUES (:name, :description, :display_order, :status, :created_by)
            ");

            $success = $stmt->execute([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'display_order' => $data['display_order'] ?? 0,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Category::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật category
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = ['name', 'description', 'display_order', 'status'];
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
                UPDATE categories
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Category::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa category (soft delete)
     */
    public function delete($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE categories
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            error_log("Category::delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            // Get current status
            $category = $this->findById($id);
            if (!$category)
                return false;

            $new_status = ($category['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("Category::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy categories cho dropdown (id => name)
     */
    public function getForDropdown()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, name
                FROM categories
                WHERE status = 'active'
                ORDER BY display_order ASC, name ASC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => name]

        } catch (PDOException $e) {
            error_log("Category::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }
}
