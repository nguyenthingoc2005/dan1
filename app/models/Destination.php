<?php
/**
 * ==============================================================================
 * DESTINATION MODEL
 * ==============================================================================
 * 
 * Quản lý địa điểm du lịch & Hình ảnh
 * 
 * Tables: 
 * - destinations (Main info)
 * - destination_images (Gallery)
 * 
 * Relationships:
 * - category_id → categories (Optional)
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class Destination
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả destinations (kèm primary image)
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Filter by category
            if (!empty($filters['category_id'])) {
                $where_conditions[] = "d.category_id = :category_id";
                $params['category_id'] = $filters['category_id'];
            }

            // Filter by status
            if (!empty($filters['status'])) {
                $where_conditions[] = "d.status = :status";
                $params['status'] = $filters['status'];
            }

            // Search
            if (!empty($filters['search'])) {
                $where_conditions[] = "(d.name LIKE :search OR d.description LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM destinations d {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data with joins
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            // Subquery to get primary image
            $data_sql = "
                SELECT 
                    d.*,
                    c.name as category_name,
                    (SELECT image_url FROM destination_images WHERE destination_id = d.id AND is_primary = 1 LIMIT 1) as thumbnail
                FROM destinations d
                LEFT JOIN categories c ON d.category_id = c.id
                {$where_clause}
                ORDER BY d.created_at DESC
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
            error_log("Destination::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm destination theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT d.*, c.name as category_name
                FROM destinations d
                LEFT JOIN categories c ON d.category_id = c.id
                WHERE d.id = :id
                LIMIT 1
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Destination::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách ảnh của destination
     */
    public function getImages($destination_id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM destination_images 
                WHERE destination_id = :id 
                ORDER BY is_primary DESC, display_order ASC
            ");
            $stmt->execute(['id' => $destination_id]);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("Destination::getImages() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo destination mới
     */
    public function create($data)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO destinations (
                    category_id, name, description, locations, status, created_by
                ) VALUES (
                    :category_id, :name, :description, :locations, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'category_id' => $data['category_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'locations' => $data['locations'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Destination::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật destination
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = ['category_id', 'name', 'description', 'locations', 'status'];

            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            $set_parts[] = "updated_by = :updated_by";
            $params['updated_by'] = get_user_id();

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE destinations
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Destination::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm ảnh
     */
    public function addImage($destination_id, $image_url, $is_primary = false)
    {
        try {
            // Nếu set primary, bỏ primary của các ảnh khác
            if ($is_primary) {
                $this->pdo->prepare("
                    UPDATE destination_images 
                    SET is_primary = 0 
                    WHERE destination_id = :id
                ")->execute(['id' => $destination_id]);
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO destination_images (destination_id, image_url, is_primary)
                VALUES (:destination_id, :image_url, :is_primary)
            ");

            return $stmt->execute([
                'destination_id' => $destination_id,
                'image_url' => $image_url,
                'is_primary' => $is_primary ? 1 : 0
            ]);

        } catch (PDOException $e) {
            error_log("Destination::addImage() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa ảnh
     */
    public function deleteImage($image_id)
    {
        try {
            // Lấy info ảnh để xóa file (nếu cần xử lý file cleanup sau này)
            $stmt = $this->pdo->prepare("DELETE FROM destination_images WHERE id = :id");
            return $stmt->execute(['id' => $image_id]);

        } catch (PDOException $e) {
            error_log("Destination::deleteImage() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage($image_id, $destination_id)
    {
        try {
            $this->pdo->beginTransaction();

            // Reset all
            $this->pdo->prepare("
                UPDATE destination_images 
                SET is_primary = 0 
                WHERE destination_id = :id
            ")->execute(['id' => $destination_id]);

            // Set new primary
            $this->pdo->prepare("
                UPDATE destination_images 
                SET is_primary = 1 
                WHERE id = :id
            ")->execute(['id' => $image_id]);

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Destination::setPrimaryImage() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa destination (soft delete)
     */
    public function delete($id)
    {
        try {
            // Check usage in tours (nếu có bảng tours)
            // TODO: Add check when Tours module is ready

            $stmt = $this->pdo->prepare("
                UPDATE destinations
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("Destination::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }
}
