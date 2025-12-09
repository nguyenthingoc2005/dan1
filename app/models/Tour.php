<?php
/**
 * ==============================================================================
 * TOUR MODEL - HOÀN TOÀN MỚI THEO FLOW ANALYSIS
 * ==============================================================================
 * 
 * Quản lý toàn bộ dữ liệu về Tour theo flow mới:
 * - Thông tin cơ bản (tours table) - KHÔNG có category_id, price_based_on_pax
 * - Lịch trình (itineraries)
 * - Dịch vụ theo ngày (itinerary_day_services) - MỚI (thay thế tour_services)
 * - Chính sách (tour_policies) - MỚI
 * - Hình ảnh (tour_images)
 * - Highlights, Included/Excluded
 * 
 * @version 2.0
 * @date 2024-12-06
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
     * ĐÃ XÓA: category_id filter
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

            // Filter by Approval Status - REMOVED: Đã gộp vào status

            // Filter by Tour Type
            if (!empty($filters['tour_type'])) {
                $where_conditions[] = "t.tour_type = :tour_type";
                $params['tour_type'] = $filters['tour_type'];
            }

            // Search by Name or Code
            if (!empty($filters['search'])) {
                $where_conditions[] = "(t.name LIKE :search OR t.tour_code LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            // Filter by Creator
            if (!empty($filters['created_by'])) {
                $where_conditions[] = "t.created_by = :created_by";
                $params['created_by'] = $filters['created_by'];
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
                SELECT t.*,
                       (SELECT image_url FROM tour_images WHERE tour_id = t.id AND is_primary = 1 LIMIT 1) as thumbnail,
                       (SELECT MIN(start_date) FROM tour_schedules WHERE tour_id = t.id AND start_date >= CURDATE() AND status = 'open') as next_departure_date,
                       (SELECT COUNT(*) FROM tour_schedules WHERE tour_id = t.id AND start_date >= CURDATE() AND status = 'open') as upcoming_schedules_count
                FROM tours t
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
     * Tìm tour theo code
     */
    public function findByCode($code)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM tours
                WHERE tour_code = :code
                LIMIT 1
            ");
            $stmt->execute(['code' => $code]);
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("Tour::findByCode() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy chi tiết Tour (kèm tất cả quan hệ)
     */
    public function findById($id)
    {
        try {
            // 1. Basic Info
            $stmt = $this->pdo->prepare("SELECT * FROM tours WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $id]);
            $tour = $stmt->fetch();

            if (!$tour)
                return null;

            // 2. Images
            $tour['images'] = $this->getImages($id);

            // 3. Itinerary
            $tour['itinerary'] = $this->getItinerary($id);

            // 4. Itinerary Day Services (MỚI)
            $tour['itinerary_day_services'] = $this->getItineraryDayServices($id);

            // 6. Highlights
            $tour['highlights'] = $this->getHighlights($id);

            // 7. Included/Excluded
            $tour['includes'] = $this->getIncludedExcluded($id, 'included');
            $tour['excludes'] = $this->getIncludedExcluded($id, 'excluded');

            // 8. Policies (MỚI)
            $tour['policies'] = $this->getPolicies($id);

            return $tour;

        } catch (PDOException $e) {
            error_log("Tour::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách tours làm template (chỉ public tours đã approved)
     * ĐÃ XÓA: category_id references
     */
    public function getTemplates()
    {
        $sql = "SELECT t.id, t.tour_code, t.name, t.duration_days, t.duration_nights,
                       t.adult_price
                FROM tours t
                WHERE t.tour_type = 'public' 
                  AND t.status = 'active'
                ORDER BY t.name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin đầy đủ của tour để clone (bao gồm tất cả dữ liệu)
     */
    public function getForClone($id)
    {
        $tour = $this->findById($id);
        if (!$tour)
            return null;

        // Clone tất cả dữ liệu (đã có trong findById)
        // Không clone hình ảnh (phải upload mới)
        unset($tour['images']);

        return $tour;
    }

    /**
     * Tạo Tour mới (Transaction) - THEO FLOW MỚI
     */
    public function create($data)
    {
        try {
            // Begin transaction
            if ($this->pdo->inTransaction()) {
                throw new Exception("Already in transaction. Cannot start new transaction.");
            }

            $this->pdo->beginTransaction();

            // 1. Insert Tours Table - ĐÃ XÓA category_id, price_based_on_pax
            $sql = "INSERT INTO tours (
                tour_code, name, introduction, description, duration_days, duration_nights,
                departure_location, min_participants, max_participants,
                adult_price, child_price, infant_price, estimated_cost_per_person,
                deposit_percentage, booking_deadline_days,
                fixed_cost_total,
                tour_type, status, parent_tour_id, created_by
            ) VALUES (
                :code, :name, :introduction, :description, :duration_days, :duration_nights,
                :departure_location, :min_participants, :max_participants,
                :adult_price, :child_price, :infant_price, :estimated_cost_per_person,
                :deposit_percentage, :booking_deadline_days,
                :fixed_cost_total,
                :tour_type, :status, :parent_tour_id, :created_by
            )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'code' => $data['code'],
                'name' => $data['name'],
                'introduction' => $data['introduction'] ?? null,
                'description' => $data['description'] ?? null,
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'] ?? 0,
                'departure_location' => $data['departure_location'] ?? null,
                'min_participants' => $data['min_participants'] ?? 15,
                'max_participants' => $data['max_participants'] ?? 45,
                'adult_price' => $data['adult_price'],
                'child_price' => $data['child_price'] ?? 0,
                'infant_price' => $data['infant_price'] ?? 0,
                'estimated_cost_per_person' => $data['estimated_cost_per_person'] ?? null,
                'deposit_percentage' => $data['deposit_percentage'] ?? 30,
                'booking_deadline_days' => $data['booking_deadline_days'] ?? 1,
                // Backward compatible: Nếu có fixed_cost_total thì dùng, nếu không thì tính từ 4 cột cũ
                'fixed_cost_total' => $this->getFixedCostTotal($data),
                'tour_type' => $data['tour_type'] ?? 'public',
                // BUG FIX: Validate status để đảm bảo giá trị hợp lệ
                'status' => $this->sanitizeTourStatus($data['status'] ?? 'draft'),
                // BUG FIX: Validate parent_tour_id để đảm bảo foreign key constraint
                'parent_tour_id' => $this->validateParentTourId($data['parent_tour_id'] ?? null),
                'created_by' => get_user_id()
            ]);

            $tour_id = $this->pdo->lastInsertId();

            // 2. Insert Itinerary
            if (!empty($data['itinerary'])) {
                $this->saveItinerary($tour_id, $data['itinerary']);
            }

            // 3. Insert Itinerary Day Services
            if (!empty($data['itinerary_day_services'])) {
                $this->saveItineraryDayServices($tour_id, $data['itinerary_day_services']);
            }

            // 4. Insert Highlights
            if (!empty($data['highlights'])) {
                $this->saveHighlights($tour_id, $data['highlights']);
            }

            // 5. Insert Included/Excluded
            if (!empty($data['included'])) {
                $this->saveIncludedExcluded($tour_id, 'included', $data['included']);
            }
            if (!empty($data['excluded'])) {
                $this->saveIncludedExcluded($tour_id, 'excluded', $data['excluded']);
            }

            // 6. Insert Tour Policies
            if (!empty($data['policy_ids'])) {
                $this->saveTourPolicies($tour_id, $data['policy_ids']);
            }

            if (!$this->pdo->inTransaction()) {
                throw new Exception("No active transaction before commit");
            }

            $this->pdo->commit();
            return $tour_id;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Tour::create() Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Cập nhật Tour (Transaction) - THEO FLOW MỚI
     */
    public function update($id, $data)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Update Tours Table - ĐÃ XÓA category_id, price_based_on_pax
            $update_fields = [
                'name = :name',
                'introduction = :introduction',
                'description = :description',
                'duration_days = :duration_days',
                'duration_nights = :duration_nights',
                'departure_location = :departure_location',
                'min_participants = :min_participants',
                'max_participants = :max_participants',
                'adult_price = :adult_price',
                'child_price = :child_price',
                'infant_price = :infant_price',
                'estimated_cost_per_person = :estimated_cost_per_person',
                'deposit_percentage = :deposit_percentage',
                'booking_deadline_days = :booking_deadline_days',
                'fixed_cost_total = :fixed_cost_total',
                'tour_type = :tour_type',
                'status = :status',
                'updated_at = NOW()'
            ];

            $sql = "UPDATE tours SET " . implode(', ', $update_fields) . " WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'introduction' => $data['introduction'] ?? null,
                'description' => $data['description'] ?? null,
                'duration_days' => $data['duration_days'],
                'duration_nights' => $data['duration_nights'] ?? 0,
                'departure_location' => $data['departure_location'] ?? null,
                'min_participants' => $data['min_participants'] ?? 15,
                'max_participants' => $data['max_participants'] ?? 45,
                'adult_price' => $data['adult_price'],
                'child_price' => $data['child_price'] ?? 0,
                'infant_price' => $data['infant_price'] ?? 0,
                'estimated_cost_per_person' => $data['estimated_cost_per_person'] ?? null,
                'deposit_percentage' => $data['deposit_percentage'] ?? 30,
                'booking_deadline_days' => $data['booking_deadline_days'] ?? 1,
                // Backward compatible: Nếu có fixed_cost_total thì dùng, nếu không thì tính từ 4 cột cũ
                'fixed_cost_total' => $this->getFixedCostTotal($data),
                'tour_type' => $data['tour_type'] ?? 'public',
                // BUG FIX: Validate status để đảm bảo giá trị hợp lệ
                'status' => $this->sanitizeTourStatus($data['status'] ?? 'draft')
            ]);

            // 2. Update Itinerary
            if (isset($data['itinerary'])) {
                $this->pdo->exec("DELETE FROM itineraries WHERE tour_id = $id");
                if (!empty($data['itinerary'])) {
                    $this->saveItinerary($id, $data['itinerary']);
                }
            }

            // 3. Update Itinerary Day Services (MỚI)
            if (isset($data['itinerary_day_services'])) {
                require_once MODELS_PATH . '/ItineraryDayService.php';
                $dayServiceModel = new ItineraryDayService($this->pdo);
                $dayServiceModel->deleteByTourId($id);
                if (!empty($data['itinerary_day_services'])) {
                    $this->saveItineraryDayServices($id, $data['itinerary_day_services']);
                }
            }

            // 5. Update Highlights
            if (isset($data['highlights'])) {
                $this->pdo->exec("DELETE FROM tour_highlights WHERE tour_id = $id");
                if (!empty($data['highlights'])) {
                    $this->saveHighlights($id, $data['highlights']);
                }
            }

            // 6. Update Included/Excluded
            if (isset($data['included']) || isset($data['excluded'])) {
                $this->pdo->exec("DELETE FROM tour_included_excluded WHERE tour_id = $id");
                if (!empty($data['included'])) {
                    $this->saveIncludedExcluded($id, 'included', $data['included']);
                }
                if (!empty($data['excluded'])) {
                    $this->saveIncludedExcluded($id, 'excluded', $data['excluded']);
                }
            }

            // 7. Update Tour Policies (MỚI)
            if (isset($data['policy_ids'])) {
                require_once MODELS_PATH . '/Policy.php';
                $policyModel = new Policy($this->pdo);
                $policyModel->deleteByTourId($id);
                if (!empty($data['policy_ids'])) {
                    $this->saveTourPolicies($id, $data['policy_ids']);
                }
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
                'day_number' => $item['day_number'] ?? $item['day'],
                'title' => $item['title'] ?? null,
                'description' => $item['description'] ?? null,
                'destination_id' => !empty($item['destination_id']) ? $item['destination_id'] : null
            ]);
        }
    }

    /**
     * Lưu Itinerary Day Services (MỚI)
     */
    private function saveItineraryDayServices($tour_id, $services_data)
    {
        require_once MODELS_PATH . '/ItineraryDayService.php';
        $dayServiceModel = new ItineraryDayService($this->pdo);

        // Lấy itinerary_id từ day_number
        $itinerary_map = [];
        $stmt = $this->pdo->prepare("SELECT id, day_number FROM itineraries WHERE tour_id = :tour_id");
        $stmt->execute(['tour_id' => $tour_id]);
        foreach ($stmt->fetchAll() as $it) {
            $itinerary_map[$it['day_number']] = $it['id'];
        }

        foreach ($services_data as $service) {
            $day_number = $service['day_number'] ?? $service['day'];
            if (!isset($itinerary_map[$day_number])) {
                // Itinerary not found for this day_number - skip this service
                continue;
            }

            // Validation: service_id phải tồn tại
            $service_id = (int) ($service['service_id'] ?? 0);
            if ($service_id <= 0) {
                throw new Exception("Service ID không hợp lệ cho ngày $day_number");
            }

            // Kiểm tra service_id có tồn tại trong database
            $stmt = $this->pdo->prepare("SELECT id FROM services WHERE id = :id AND status = 'active'");
            $stmt->execute(['id' => $service_id]);
            if (!$stmt->fetch()) {
                throw new Exception("Service ID $service_id không tồn tại hoặc đã bị vô hiệu hóa (ngày $day_number)");
            }

            // Validation: unit_price > 0
            $unit_price = (float) ($service['unit_price'] ?? 0);
            if ($unit_price <= 0) {
                throw new Exception("Đơn giá phải lớn hơn 0 cho ngày $day_number");
            }

            // Validation: quantity > 0
            $quantity = (float) ($service['quantity'] ?? 0);
            if ($quantity <= 0) {
                throw new Exception("Số lượng phải lớn hơn 0 cho ngày $day_number");
            }

            // Validation: service_provider_id (nếu có) phải thuộc về service
            $service_provider_id = !empty($service['service_provider_id']) ? (int) $service['service_provider_id'] : null;
            if ($service_provider_id) {
                $stmt = $this->pdo->prepare("
                    SELECT s.id 
                    FROM services s 
                    WHERE s.id = :service_id 
                    AND s.service_provider_id = :provider_id
                ");
                $stmt->execute([
                    'service_id' => $service_id,
                    'provider_id' => $service_provider_id
                ]);
                if (!$stmt->fetch()) {
                    throw new Exception("Nhà dịch vụ không thuộc về dịch vụ đã chọn (ngày $day_number)");
                }
            }

            $dayServiceModel->create([
                'itinerary_id' => $itinerary_map[$day_number],
                'service_id' => $service_id,
                'service_provider_id' => $service_provider_id,
                'service_name' => $service['service_name'] ?? null,
                'unit_price' => $unit_price,
                'quantity' => $quantity,
                'unit' => $service['unit'] ?? null,
                'is_included_in_price' => $service['is_included_in_price'] ?? 1,
                'notes' => $service['notes'] ?? null
            ]);
        }
    }

    private function saveHighlights($tour_id, $highlights)
    {
        $sql = "INSERT INTO tour_highlights (tour_id, highlight, display_order) VALUES (:tour_id, :highlight, :order)";
        $stmt = $this->pdo->prepare($sql);

        $order = 0;
        foreach ($highlights as $hl) {
            if (!empty(trim($hl))) {
                $stmt->execute([
                    'tour_id' => $tour_id,
                    'highlight' => trim($hl),
                    'order' => $order++
                ]);
            }
        }
    }

    private function saveIncludedExcluded($tour_id, $type, $items)
    {
        $sql = "INSERT INTO tour_included_excluded (tour_id, type, item, display_order) VALUES (:tour_id, :type, :item, :order)";
        $stmt = $this->pdo->prepare($sql);

        $order = 0;
        foreach ($items as $item) {
            if (empty(trim($item)))
                continue;
            $stmt->execute([
                'tour_id' => $tour_id,
                'type' => $type,
                'item' => trim($item),
                'order' => $order++
            ]);
        }
    }

    /**
     * Lưu Tour Policies (MỚI)
     */
    private function saveTourPolicies($tour_id, $policy_ids)
    {
        require_once MODELS_PATH . '/Policy.php';
        $policyModel = new Policy($this->pdo);

        // Check if we're in a transaction - if so, don't let Policy model start its own
        $in_transaction = $this->pdo->inTransaction();

        if ($in_transaction) {
            // We're already in a transaction, so we need to manually insert
            // instead of calling assignMultipleToTour which has its own transaction
            $sql = "INSERT INTO tour_policies (tour_id, policy_id) VALUES (:tour_id, :policy_id)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($policy_ids as $policy_id) {
                $stmt->execute([
                    'tour_id' => $tour_id,
                    'policy_id' => (int) $policy_id
                ]);
            }
            return true;
        } else {
            // Not in transaction, safe to call assignMultipleToTour
            return $policyModel->assignMultipleToTour($tour_id, $policy_ids);
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
            SELECT i.*, 
                   d.name as destination_name,
                   d.description as destination_description,
                   d.locations as destination_locations,
                   p.name as province_name,
                   c.name as country_name
            FROM itineraries i
            LEFT JOIN destinations d ON i.destination_id = d.id
            LEFT JOIN provinces p ON d.province_id = p.id
            LEFT JOIN countries c ON d.country_id = c.id
            WHERE i.tour_id = :id 
            ORDER BY i.day_number ASC
        ");
        $stmt->execute(['id' => $tour_id]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy Itinerary Day Services (MỚI)
     */
    public function getItineraryDayServices($tour_id)
    {
        require_once MODELS_PATH . '/ItineraryDayService.php';
        $dayServiceModel = new ItineraryDayService($this->pdo);
        return $dayServiceModel->getByTourId($tour_id);
    }

    public function getHighlights($tour_id)
    {
        $stmt = $this->pdo->prepare("SELECT highlight FROM tour_highlights WHERE tour_id = :id ORDER BY display_order ASC");
        $stmt->execute(['id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getIncludedExcluded($tour_id, $type)
    {
        $stmt = $this->pdo->prepare("SELECT item FROM tour_included_excluded WHERE tour_id = :id AND type = :type ORDER BY display_order ASC");
        $stmt->execute(['id' => $tour_id, 'type' => $type]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Lấy Policies của tour (MỚI)
     */
    public function getPolicies($tour_id)
    {
        require_once MODELS_PATH . '/Policy.php';
        $policyModel = new Policy($this->pdo);
        return $policyModel->getByTourId($tour_id);
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

    /**
     * Sanitize và validate tour status
     * Chỉ cho phép các giá trị hợp lệ: draft, pending, active, rejected, inactive
     */
    private function sanitizeTourStatus($status)
    {
        $allowed_statuses = ['draft', 'pending', 'active', 'rejected', 'inactive'];
        $status = trim(strtolower($status ?? 'draft'));

        if (!in_array($status, $allowed_statuses)) {
            // Nếu không hợp lệ, trả về 'draft' làm default
            error_log("Tour::sanitizeTourStatus() - Invalid status: $status, defaulting to 'draft'");
            return 'draft';
        }

        return $status;
    }

    /**
     * Validate parent_tour_id - Kiểm tra xem tour parent có tồn tại không
     * Nếu không tồn tại hoặc không hợp lệ, trả về null để tránh foreign key constraint error
     * 
     * @param mixed $parent_tour_id
     * @return int|null
     */
    private function validateParentTourId($parent_tour_id)
    {
        // Nếu null hoặc rỗng, trả về null
        if (empty($parent_tour_id)) {
            return null;
        }

        // Chuyển sang int
        $parent_tour_id = (int) $parent_tour_id;

        // Nếu <= 0, không hợp lệ
        if ($parent_tour_id <= 0) {
            error_log("Tour::validateParentTourId() - Invalid parent_tour_id: $parent_tour_id, defaulting to null");
            return null;
        }

        // Kiểm tra xem tour có tồn tại trong database không
        try {
            $sql = "SELECT id FROM tours WHERE id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $parent_tour_id]);
            $result = $stmt->fetch();

            if (!$result) {
                // Tour không tồn tại, trả về null
                error_log("Tour::validateParentTourId() - Parent tour ID $parent_tour_id does not exist, defaulting to null");
                return null;
            }

            return $parent_tour_id;
        } catch (PDOException $e) {
            // Nếu có lỗi khi query, trả về null để an toàn
            error_log("Tour::validateParentTourId() - Error checking parent_tour_id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy fixed_cost_total từ data (backward compatible)
     * Nếu có fixed_cost_total thì dùng, nếu không thì tính từ 4 cột cũ
     * 
     * @param array $data
     * @return float
     */
    private function getFixedCostTotal($data)
    {
        // Ưu tiên dùng fixed_cost_total nếu có
        if (isset($data['fixed_cost_total']) && $data['fixed_cost_total'] > 0) {
            return (float) $data['fixed_cost_total'];
        }

        // Backward compatible: Tính từ 4 cột cũ nếu có
        $total = 0;
        $total += (float) ($data['fixed_cost_guide'] ?? 0);
        $total += (float) ($data['fixed_cost_management'] ?? 0);
        $total += (float) ($data['fixed_cost_marketing'] ?? 0);
        $total += (float) ($data['fixed_cost_other'] ?? 0);

        return $total;
    }
}
