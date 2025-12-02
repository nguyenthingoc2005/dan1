<?php
/**
 * ==============================================================================
 * TOUR MODEL
 * ==============================================================================
 * 
 * Quản lý toàn bộ dữ liệu về Tour:
 * - Thông tin cơ bản (tours table)
 * - Lịch trình (itineraries)
 * - Hình ảnh (tour_images)
 * - Chính sách, FAQ, Highlights...
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class Tour
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách tours (có phân trang & lọc)
     */
    public function getAll($filters = [], $page = 1, $per_page = 10)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Filter by Status
            if (!empty($filters['status'])) {
                $where_conditions[] = "t.status = :status";
                $params['status'] = $filters['status'];
            }

            // Filter by Category
            if (!empty($filters['category_id'])) {
                $where_conditions[] = "t.category_id = :category_id";
                $params['category_id'] = $filters['category_id'];
            }

            // Search by Name or Code
            if (!empty($filters['search'])) {
                $where_conditions[] = "(t.name LIKE :search OR t.tour_code LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM tours t {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get Data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $sql = "
                SELECT t.*, c.name as category_name,
                       (SELECT image_url FROM tour_images WHERE tour_id = t.id AND is_primary = 1 LIMIT 1) as thumbnail
                FROM tours t
                LEFT JOIN categories c ON t.category_id = c.id
                {$where_clause}
                ORDER BY t.created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll();

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Tour::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Lấy chi tiết Tour (kèm tất cả quan hệ)
     */
    public function findById($id)
    {
        try {
            // 1. Basic Info
            $stmt = $this->pdo->prepare("
                SELECT t.*, c.name as category_name 
                FROM tours t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.id = :id LIMIT 1
            ");
            $stmt->execute(['id' => $id]);
            $tour = $stmt->fetch();

            if (!$tour)
                return null;

            // 2. Images
            $tour['images'] = $this->getImages($id);

            // 3. Itinerary
            $tour['itinerary'] = $this->getItinerary($id);

            // 4. Highlights
            $tour['highlights'] = $this->getHighlights($id);

            // 5. Included/Excluded
            $tour['includes'] = $this->getIncludedExcluded($id, 'included');
            $tour['excludes'] = $this->getIncludedExcluded($id, 'excluded');

            return $tour;

        } catch (PDOException $e) {
            error_log("Tour::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo Tour mới (Transaction)
     */
    public function create($data)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Insert Tours Table
            $sql = "INSERT INTO tours (
                tour_code, category_id, name, description, duration_days, duration_nights,
                departure_location, adult_price, child_price, infant_price,
                status, created_by
            ) VALUES (
                :code, :category_id, :name, :description, :duration_days, :duration_nights,
                :departure_location, :adult_price, :child_price, :infant_price,
                :status, :created_by
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'code' => $data['code'],
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'departure_location' => $data['departure_location'],
                'adult_price' => $data['adult_price'],
                'child_price' => $data['child_price'] ?? 0,
                'infant_price' => $data['infant_price'] ?? 0,
                'status' => $data['status'] ?? 'draft',
                'created_by' => get_user_id()
            ]);

            $tour_id = $this->pdo->lastInsertId();

            // 2. Insert Itinerary
            if (!empty($data['itinerary'])) {
                $this->saveItinerary($tour_id, $data['itinerary']);
            }

            // 3. Insert Highlights
            if (!empty($data['highlights'])) {
                $this->saveHighlights($tour_id, $data['highlights']);
            }

            $this->pdo->commit();
            return $tour_id;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Tour::create() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật Tour (Transaction)
     */
    public function update($id, $data)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Update Tours Table
            $sql = "UPDATE tours SET 
                category_id = :category_id,
                name = :name,
                description = :description,
                duration_days = :duration_days,
                duration_nights = :duration_nights,
                departure_location = :departure_location,
                adult_price = :adult_price,
                child_price = :child_price,
                infant_price = :infant_price,
                status = :status,
                updated_at = NOW()
                WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                'departure_location' => $data['departure_location'],
                'adult_price' => $data['adult_price'],
                'child_price' => $data['child_price'] ?? 0,
                'infant_price' => $data['infant_price'] ?? 0,
                'status' => $data['status']
            ]);

            // 2. Update Itinerary
            if (isset($data['itinerary'])) {
                $this->pdo->exec("DELETE FROM itineraries WHERE tour_id = $id");
                $this->saveItinerary($id, $data['itinerary']);
            }

            // 3. Update Highlights
            if (isset($data['highlights'])) {
                $this->pdo->exec("DELETE FROM tour_highlights WHERE tour_id = $id");
                $this->saveHighlights($id, $data['highlights']);
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Tour::update() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái Tour (Partial Update)
     */
    public function updateStatus($id, $data)
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        if (empty($fields))
            return false;

        $sql = "UPDATE tours SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    // ========================================================================
    // SUB-FUNCTIONS (PRIVATE)
    // ========================================================================

    private function saveItinerary($tour_id, $items)
    {
        $sql = "INSERT INTO itineraries (tour_id, day_number, title, description, destination_id) 
                VALUES (:tour_id, :day_number, :title, :description, :destination_id)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($items as $item) {
            $stmt->execute([
                'tour_id' => $tour_id,
                'day_number' => $item['day'],
                'title' => $item['title'],
                'description' => $item['description'],
                'destination_id' => !empty($item['destination_id']) ? $item['destination_id'] : null
            ]);
        }
    }

    private function saveHighlights($tour_id, $highlights)
    {
        $sql = "INSERT INTO tour_highlights (tour_id, highlight) VALUES (:tour_id, :highlight)";
        $stmt = $this->pdo->prepare($sql);

        foreach ($highlights as $hl) {
            if (!empty(trim($hl))) {
                $stmt->execute([
                    'tour_id' => $tour_id,
                    'highlight' => trim($hl)
                ]);
            }
        }
    }

    // ========================================================================
    // GETTERS FOR RELATIONS
    // ========================================================================

    public function getImages($tour_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tour_images WHERE tour_id = :id ORDER BY is_primary DESC, display_order ASC");
        $stmt->execute(['id' => $tour_id]);
        return $stmt->fetchAll();
    }

    public function getItinerary($tour_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT i.*, d.name as destination_name 
            FROM itineraries i
            LEFT JOIN destinations d ON i.destination_id = d.id
            WHERE i.tour_id = :id 
            ORDER BY i.day_number ASC
        ");
        $stmt->execute(['id' => $tour_id]);
        return $stmt->fetchAll();
    }

    public function getHighlights($tour_id)
    {
        $stmt = $this->pdo->prepare("SELECT highlight FROM tour_highlights WHERE tour_id = :id");
        $stmt->execute(['id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getIncludedExcluded($tour_id, $type)
    {
        $stmt = $this->pdo->prepare("SELECT item FROM tour_included_excluded WHERE tour_id = :id AND type = :type");
        $stmt->execute(['id' => $tour_id, 'type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function addImage($tour_id, $path, $is_primary = false)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tour_images (tour_id, image_url, is_primary) VALUES (:id, :url, :primary)");
        return $stmt->execute(['id' => $tour_id, 'url' => $path, 'primary' => $is_primary ? 1 : 0]);
    }

    public function delete($id)
    {
        // Simple hard delete for now. In real app, check for bookings first.
        $stmt = $this->pdo->prepare("DELETE FROM tours WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
