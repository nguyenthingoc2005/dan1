<?php
/**
 * ==============================================================================
 * ROOM ASSIGNMENT MODEL
 * ==============================================================================
 * 
 * Quản lý phân phòng cho tour schedule
 * - Phân phòng tự động theo giới tính
 * - Xử lý yêu cầu đặc biệt (đơn phòng, cùng phòng, tránh cùng phòng)
 * - Quản lý khách hàng trong từng phòng
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class RoomAssignment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả phân phòng cho một tour schedule (theo từng đêm)
     */
    public function getByScheduleId($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    ra.id AS room_id,
                    ra.room_number,
                    ra.room_type,
                    ra.actual_occupancy,
                    ra.max_capacity,
                    ra.status,
                    ra.check_in_date,
                    ra.check_out_date,
                    ra.notes AS room_notes,
                    i.id AS itinerary_id,
                    i.day_number,
                    i.title AS day_title,
                    sp.id AS service_provider_id,
                    sp.name AS hotel_name,
                    GROUP_CONCAT(
                        CONCAT(c.full_name, ' (', bc.age_type, ')') 
                        ORDER BY rac.role DESC, c.full_name
                        SEPARATOR ', '
                    ) AS customers,
                    GROUP_CONCAT(c.id) AS customer_ids,
                    GROUP_CONCAT(bc.id) AS booking_customer_ids
                FROM room_assignments ra
                JOIN itineraries i ON ra.itinerary_id = i.id
                LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
                LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
                LEFT JOIN booking_customers bc ON rac.booking_customer_id = bc.id
                LEFT JOIN customers c ON rac.customer_id = c.id
                WHERE ra.tour_schedule_id = :schedule_id
                GROUP BY ra.id
                ORDER BY i.day_number, ra.room_number
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by itinerary (đêm)
            $grouped = [];
            foreach ($rooms as $room) {
                $day_key = $room['day_number'];
                if (!isset($grouped[$day_key])) {
                    $grouped[$day_key] = [
                        'itinerary_id' => $room['itinerary_id'],
                        'day_number' => $room['day_number'],
                        'day_title' => $room['day_title'],
                        'hotel_name' => $room['hotel_name'],
                        'rooms' => []
                    ];
                }
                $grouped[$day_key]['rooms'][] = $room;
            }

            return $grouped;

        } catch (PDOException $e) {
            logError("RoomAssignment::getByScheduleId() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy phân phòng theo đêm cụ thể
     */
    public function getByItinerary($schedule_id, $itinerary_id)
    {
        try {
            $sql = "
                SELECT 
                    ra.*,
                    i.day_number,
                    sp.name AS hotel_name,
                    GROUP_CONCAT(c.full_name SEPARATOR ', ') AS customers
                FROM room_assignments ra
                JOIN itineraries i ON ra.itinerary_id = i.id
                LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
                LEFT JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
                LEFT JOIN customers c ON rac.customer_id = c.id
                WHERE ra.tour_schedule_id = :schedule_id
                  AND ra.itinerary_id = :itinerary_id
                GROUP BY ra.id
                ORDER BY ra.room_number
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'schedule_id' => $schedule_id,
                'itinerary_id' => $itinerary_id
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("RoomAssignment::getByItinerary() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách khách hàng chưa được phân phòng
     */
    public function getUnassignedCustomers($schedule_id, $itinerary_id = null)
    {
        try {
            $sql = "
                SELECT 
                    bc.id AS booking_customer_id,
                    bc.booking_id,
                    bc.customer_id,
                    bc.age_type,
                    c.full_name,
                    c.gender,
                    c.date_of_birth,
                    b.booking_code
                FROM booking_customers bc
                JOIN customers c ON bc.customer_id = c.id
                JOIN bookings b ON bc.booking_id = b.id
                WHERE b.tour_schedule_id = :schedule_id_main
                  AND b.payment_status = 'paid'
                  AND bc.id NOT IN (
                      SELECT rac.booking_customer_id
                      FROM room_assignment_customers rac
                      JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                      WHERE ra.tour_schedule_id = :schedule_id_sub
                        " . ($itinerary_id ? "AND ra.itinerary_id = :itinerary_id_sub" : "") . "
                  )
                ORDER BY c.gender, bc.age_type, c.full_name
            ";

            $params = [
                'schedule_id_main' => $schedule_id,
                'schedule_id_sub' => $schedule_id
            ];
            if ($itinerary_id) {
                $params['itinerary_id_sub'] = $itinerary_id;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("RoomAssignment::getUnassignedCustomers() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy yêu cầu phòng đặc biệt
     */
    public function getRoomRequests($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    rr.*,
                    b.booking_code,
                    c.full_name AS customer_name,
                    tc.full_name AS target_customer_name
                FROM room_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN customers c ON rr.customer_id = c.id
                LEFT JOIN customers tc ON rr.target_customer_id = tc.id
                WHERE b.tour_schedule_id = :schedule_id
                  AND rr.status IN ('pending', 'approved')
                ORDER BY rr.request_type, rr.created_at
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("RoomAssignment::getRoomRequests() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin khách sạn từ itinerary
     */
    public function getHotelsBySchedule($schedule_id)
    {
        try {
            // Lấy tour_id từ schedule
            $scheduleStmt = $this->pdo->prepare("SELECT tour_id FROM tour_schedules WHERE id = :id");
            $scheduleStmt->execute(['id' => $schedule_id]);
            $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

            if (!$schedule) {
                return [];
            }

            $sql = "
                SELECT DISTINCT
                    i.id AS itinerary_id,
                    i.day_number,
                    i.title AS day_title,
                    ids.service_provider_id,
                    sp.name AS hotel_name,
                    sp.address AS hotel_address
                FROM itineraries i
                JOIN itinerary_day_services ids ON i.id = ids.itinerary_id
                JOIN service_providers sp ON ids.service_provider_id = sp.id
                JOIN services s ON ids.service_id = s.id
                WHERE i.tour_id = :tour_id
                  AND (s.name LIKE '%khách sạn%' OR s.name LIKE '%hotel%' OR s.name LIKE '%resort%' OR ids.service_provider_id IS NOT NULL)
                ORDER BY i.day_number
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['tour_id' => $schedule['tour_id']]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("RoomAssignment::getHotelsBySchedule() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo phòng mới
     */
    public function createRoom($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Tính check_in_date và check_out_date từ itinerary và tour schedule
            // Nếu không có trong data, tự động tính từ itinerary_id và tour_schedule_id
            if (empty($data['check_in_date']) || empty($data['check_out_date'])) {
                // Lấy thông tin tour schedule và itinerary
                $scheduleSql = "SELECT ts.start_date, i.day_number 
                               FROM tour_schedules ts
                               JOIN itineraries i ON ts.tour_id = i.tour_id
                               WHERE ts.id = :schedule_id AND i.id = :itinerary_id";
                $scheduleStmt = $this->pdo->prepare($scheduleSql);
                $scheduleStmt->execute([
                    'schedule_id' => $data['tour_schedule_id'],
                    'itinerary_id' => $data['itinerary_id']
                ]);
                $scheduleInfo = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

                if (!$scheduleInfo) {
                    throw new Exception("Không tìm thấy thông tin tour schedule hoặc itinerary.");
                }

                // check_in_date = start_date + (day_number - 1) ngày
                // VD: Tour bắt đầu 01/01, đêm 1 (day 1) → check-in 01/01
                //     Tour bắt đầu 01/01, đêm 2 (day 2) → check-in 02/01
                $check_in_date = date('Y-m-d', strtotime($scheduleInfo['start_date'] . ' + ' . ($scheduleInfo['day_number'] - 1) . ' days'));

                // check_out_date = check_in_date + 1 ngày (check out ngày hôm sau)
                $check_out_date = date('Y-m-d', strtotime($check_in_date . ' + 1 day'));

                $data['check_in_date'] = $check_in_date;
                $data['check_out_date'] = $check_out_date;
            }

            $sql = "
                INSERT INTO room_assignments (
                    tour_schedule_id, itinerary_id, service_provider_id,
                    room_number, room_type, max_capacity, actual_occupancy,
                    check_in_date, check_out_date, status, notes
                ) VALUES (
                    :tour_schedule_id, :itinerary_id, :service_provider_id,
                    :room_number, :room_type, :max_capacity, :actual_occupancy,
                    :check_in_date, :check_out_date, :status, :notes
                )
            ";

            // Mặc định: double room, 2 người, không cần số phòng và khách sạn
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'tour_schedule_id' => $data['tour_schedule_id'],
                'itinerary_id' => $data['itinerary_id'],
                'service_provider_id' => null, // Không cần khách sạn
                'room_number' => null, // Không cần số phòng
                'room_type' => $data['room_type'] ?? 'double',
                'max_capacity' => $data['max_capacity'] ?? 2,
                'actual_occupancy' => $data['actual_occupancy'] ?? 0,
                'check_in_date' => $data['check_in_date'],
                'check_out_date' => $data['check_out_date'],
                'status' => $data['status'] ?? 'pending',
                'notes' => $data['notes'] ?? null
            ]);

            $room_id = $this->pdo->lastInsertId();

            // Log history
            $this->logHistory($room_id, 'created', null, $data, $_SESSION['user_id'] ?? null);

            $this->pdo->commit();
            return $room_id;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            logError("RoomAssignment::createRoom() Error: " . $e->getMessage());
            throw new Exception("Không thể tạo phòng: " . $e->getMessage());
        }
    }

    /**
     * Gán khách vào phòng
     */
    public function assignCustomerToRoom($room_id, $booking_customer_id, $role = 'companion')
    {
        try {
            $this->pdo->beginTransaction();

            // Lấy thông tin booking_customer
            $bcStmt = $this->pdo->prepare("
                SELECT bc.*, b.id AS booking_id, c.id AS customer_id
                FROM booking_customers bc
                JOIN bookings b ON bc.booking_id = b.id
                JOIN customers c ON bc.customer_id = c.id
                WHERE bc.id = :id
            ");
            $bcStmt->execute(['id' => $booking_customer_id]);
            $bookingCustomer = $bcStmt->fetch(PDO::FETCH_ASSOC);

            if (!$bookingCustomer) {
                throw new Exception("Booking customer không tồn tại");
            }

            // Kiểm tra khách đã được phân phòng trong đêm này chưa
            $roomStmt = $this->pdo->prepare("
                SELECT ra.itinerary_id, ra.id
                FROM room_assignments ra
                WHERE ra.id = :room_id
            ");
            $roomStmt->execute(['room_id' => $room_id]);
            $room = $roomStmt->fetch(PDO::FETCH_ASSOC);

            if (!$room) {
                throw new Exception("Phòng không tồn tại");
            }

            // Kiểm tra đã phân phòng trong đêm này chưa
            $checkStmt = $this->pdo->prepare("
                SELECT rac.id
                FROM room_assignment_customers rac
                JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                WHERE rac.booking_customer_id = :booking_customer_id
                  AND ra.itinerary_id = :itinerary_id
            ");
            $checkStmt->execute([
                'booking_customer_id' => $booking_customer_id,
                'itinerary_id' => $room['itinerary_id']
            ]);

            if ($checkStmt->fetch()) {
                throw new Exception("Khách này đã được phân phòng trong đêm này");
            }

            // Kiểm tra capacity
            $capacityStmt = $this->pdo->prepare("
                SELECT actual_occupancy, max_capacity
                FROM room_assignments
                WHERE id = :room_id
            ");
            $capacityStmt->execute(['room_id' => $room_id]);
            $capacity = $capacityStmt->fetch(PDO::FETCH_ASSOC);

            if ($capacity['actual_occupancy'] >= $capacity['max_capacity']) {
                throw new Exception("Phòng đã đầy");
            }

            // Thêm khách vào phòng
            $insertSql = "
                INSERT INTO room_assignment_customers (
                    room_assignment_id, booking_customer_id, customer_id, booking_id,
                    role, room_preference, special_notes
                ) VALUES (
                    :room_assignment_id, :booking_customer_id, :customer_id, :booking_id,
                    :role, :room_preference, :special_notes
                )
            ";

            $insertStmt = $this->pdo->prepare($insertSql);
            $insertStmt->execute([
                'room_assignment_id' => $room_id,
                'booking_customer_id' => $booking_customer_id,
                'customer_id' => $bookingCustomer['customer_id'],
                'booking_id' => $bookingCustomer['booking_id'],
                'role' => $role,
                'room_preference' => null,
                'special_notes' => null
            ]);

            // Cập nhật actual_occupancy
            $updateSql = "
                UPDATE room_assignments
                SET actual_occupancy = actual_occupancy + 1,
                    status = 'assigned'
                WHERE id = :room_id
            ";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute(['room_id' => $room_id]);

            // Log history
            $this->logHistory(
                $room_id,
                'customer_added',
                null,
                ['booking_customer_id' => $booking_customer_id, 'customer_name' => $bookingCustomer['customer_id']],
                $_SESSION['user_id'] ?? null
            );

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            logError("RoomAssignment::assignCustomerToRoom() Error: " . $e->getMessage());
            throw new Exception("Không thể gán khách vào phòng: " . $e->getMessage());
        }
    }

    /**
     * Xóa khách khỏi phòng
     */
    public function removeCustomerFromRoom($room_assignment_customer_id)
    {
        try {
            $this->pdo->beginTransaction();

            // Lấy thông tin
            $racStmt = $this->pdo->prepare("
                SELECT rac.*, ra.id AS room_id
                FROM room_assignment_customers rac
                JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                WHERE rac.id = :id
            ");
            $racStmt->execute(['id' => $room_assignment_customer_id]);
            $rac = $racStmt->fetch(PDO::FETCH_ASSOC);

            if (!$rac) {
                throw new Exception("Không tìm thấy phân phòng");
            }

            // Xóa khách
            $deleteSql = "DELETE FROM room_assignment_customers WHERE id = :id";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute(['id' => $room_assignment_customer_id]);

            // Cập nhật actual_occupancy
            $updateSql = "
                UPDATE room_assignments
                SET actual_occupancy = GREATEST(0, actual_occupancy - 1)
                WHERE id = :room_id
            ";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute(['room_id' => $rac['room_id']]);

            // Log history
            $this->logHistory(
                $rac['room_id'],
                'customer_removed',
                ['booking_customer_id' => $rac['booking_customer_id']],
                null,
                $_SESSION['user_id'] ?? null
            );

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            logError("RoomAssignment::removeCustomerFromRoom() Error: " . $e->getMessage());
            throw new Exception("Không thể xóa khách khỏi phòng: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin phòng
     */
    public function updateRoom($room_id, $data)
    {
        try {
            $this->pdo->beginTransaction();

            // Lấy giá trị cũ
            $oldStmt = $this->pdo->prepare("SELECT * FROM room_assignments WHERE id = :id");
            $oldStmt->execute(['id' => $room_id]);
            $oldData = $oldStmt->fetch(PDO::FETCH_ASSOC);

            if (!$oldData) {
                throw new Exception("Phòng không tồn tại");
            }

            // Cập nhật
            $fields = [];
            $params = ['id' => $room_id];

            $allowedFields = ['room_number', 'room_type', 'max_capacity', 'service_provider_id', 'status', 'notes'];
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "$field = :$field";
                    $params[$field] = $data[$field];
                }
            }

            if (!empty($fields)) {
                $sql = "UPDATE room_assignments SET " . implode(', ', $fields) . " WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);

                // Log history
                $this->logHistory(
                    $room_id,
                    'updated',
                    $oldData,
                    array_merge($oldData, $data),
                    $_SESSION['user_id'] ?? null
                );
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            logError("RoomAssignment::updateRoom() Error: " . $e->getMessage());
            throw new Exception("Không thể cập nhật phòng: " . $e->getMessage());
        }
    }

    /**
     * Lấy chi tiết phòng (kèm danh sách khách)
     */
    public function getRoomDetails($room_id)
    {
        try {
            $sql = "
                SELECT 
                    ra.*,
                    i.day_number,
                    i.title AS day_title,
                    sp.name AS hotel_name,
                    sp.address AS hotel_address
                FROM room_assignments ra
                JOIN itineraries i ON ra.itinerary_id = i.id
                LEFT JOIN service_providers sp ON ra.service_provider_id = sp.id
                WHERE ra.id = :room_id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['room_id' => $room_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$room) {
                return null;
            }

            // Lấy danh sách khách
            $customersSql = "
                SELECT 
                    rac.*,
                    c.full_name,
                    c.gender,
                    bc.age_type,
                    b.booking_code
                FROM room_assignment_customers rac
                JOIN booking_customers bc ON rac.booking_customer_id = bc.id
                JOIN customers c ON rac.customer_id = c.id
                JOIN bookings b ON rac.booking_id = b.id
                WHERE rac.room_assignment_id = :room_id
                ORDER BY rac.role DESC, c.full_name
            ";

            $customersStmt = $this->pdo->prepare($customersSql);
            $customersStmt->execute(['room_id' => $room_id]);
            $room['customers'] = $customersStmt->fetchAll(PDO::FETCH_ASSOC);

            return $room;

        } catch (PDOException $e) {
            logError("RoomAssignment::getRoomDetails() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ghi log lịch sử
     */
    private function logHistory($room_assignment_id, $action, $old_values = null, $new_values = null, $changed_by = null, $reason = null)
    {
        try {
            // Kiểm tra bảng có tồn tại không
            $checkTable = $this->pdo->query("SHOW TABLES LIKE 'room_assignment_history'");
            if ($checkTable->rowCount() == 0) {
                return; // Bảng chưa tồn tại
            }

            $sql = "
                INSERT INTO room_assignment_history (
                    room_assignment_id, action, old_values, new_values, changed_by, reason
                ) VALUES (
                    :room_assignment_id, :action, :old_values, :new_values, :changed_by, :reason
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'room_assignment_id' => $room_assignment_id,
                'action' => $action,
                'old_values' => $old_values ? json_encode($old_values) : null,
                'new_values' => $new_values ? json_encode($new_values) : null,
                'changed_by' => $changed_by,
                'reason' => $reason
            ]);

        } catch (PDOException $e) {
            logError("RoomAssignment::logHistory() Error: " . $e->getMessage());
        }
    }

    /**
     * Phân phòng tự động
     * Logic: Nam/Nam, Nữ/Nữ, xử lý gia đình (cùng booking)
     * 
     * @param int $schedule_id
     * @param array $options [
     *     'manual_customer_ids' => [], // ID khách xử lý thủ công (booking_customer_id)
     *     'max_customers_per_room' => 2, // Số người/phòng mặc định
     *     'prioritize_same_booking' => true, // Ưu tiên ghép cùng booking
     *     'auto_single_room' => true // Tự động tạo phòng đơn nếu lẻ
     * ]
     */
    public function autoAssign($schedule_id, $options = [])
    {
        try {
            $this->pdo->beginTransaction();

            // Parse options
            $manual_customer_ids = $options['manual_customer_ids'] ?? [];
            $max_customers_per_room = (int) ($options['max_customers_per_room'] ?? 2);
            $prioritize_same_booking = $options['prioritize_same_booking'] ?? true;
            $auto_single_room = $options['auto_single_room'] ?? true;

            // Đảm bảo max_customers_per_room >= 1
            if ($max_customers_per_room < 1) {
                $max_customers_per_room = 2; // Default
            }

            // 1. Lấy thông tin tour schedule
            $scheduleSql = "SELECT ts.*, t.duration_days 
                          FROM tour_schedules ts
                          JOIN tours t ON ts.tour_id = t.id
                          WHERE ts.id = :schedule_id";
            $scheduleStmt = $this->pdo->prepare($scheduleSql);
            $scheduleStmt->execute(['schedule_id' => $schedule_id]);
            $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);

            if (!$schedule) {
                throw new Exception("Không tìm thấy tour schedule.");
            }

            // 2. Lấy tất cả itinerary (đêm) của tour
            $itinerarySql = "SELECT id, day_number FROM itineraries WHERE tour_id = :tour_id ORDER BY day_number";
            $itineraryStmt = $this->pdo->prepare($itinerarySql);
            $itineraryStmt->execute(['tour_id' => $schedule['tour_id']]);
            $itineraries = $itineraryStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($itineraries)) {
                throw new Exception("Tour này chưa có lịch trình (itinerary).");
            }

            // 3. Lấy danh sách khách đã thanh toán (chưa phân phòng) - chỉ để kiểm tra tổng số
            $customersSql = "
                SELECT 
                    bc.id AS booking_customer_id,
                    bc.booking_id,
                    bc.customer_id,
                    bc.age_type,
                    c.full_name,
                    c.gender,
                    c.date_of_birth
                FROM booking_customers bc
                JOIN customers c ON bc.customer_id = c.id
                JOIN bookings b ON bc.booking_id = b.id
                WHERE b.tour_schedule_id = :schedule_id_main
                  AND b.payment_status = 'paid'
                  AND bc.id NOT IN (
                      SELECT rac.booking_customer_id
                      FROM room_assignment_customers rac
                      JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                      WHERE ra.tour_schedule_id = :schedule_id_sub
                  )
            ";

            // Loại bỏ khách đã chọn xử lý thủ công
            $params = [
                'schedule_id_main' => $schedule_id,
                'schedule_id_sub' => $schedule_id
            ];

            if (!empty($manual_customer_ids)) {
                $manualParams = [];
                foreach ($manual_customer_ids as $idx => $id) {
                    $paramName = ':manual_id_all_' . $idx;
                    $manualParams[] = $paramName;
                    $params[$paramName] = $id;
                }
                $placeholders = implode(',', $manualParams);
                $customersSql .= " AND bc.id NOT IN ($placeholders)";
            }

            $customersSql .= " ORDER BY bc.booking_id, bc.id";

            $customersStmt = $this->pdo->prepare($customersSql);
            $customersStmt->execute($params);
            $allCustomers = $customersStmt->fetchAll(PDO::FETCH_ASSOC);

            // Nếu không có khách để phân phòng tự động
            if (empty($allCustomers)) {
                // Kiểm tra xem có phải do tất cả đều được chọn manual không
                if (!empty($manual_customer_ids)) {
                    // Kiểm tra tổng số khách đã thanh toán
                    $totalCustomersSql = "
                        SELECT COUNT(*) as total
                        FROM booking_customers bc
                        JOIN bookings b ON bc.booking_id = b.id
                        WHERE b.tour_schedule_id = :schedule_id_check
                          AND b.payment_status = 'paid'
                    ";
                    $totalStmt = $this->pdo->prepare($totalCustomersSql);
                    $totalStmt->execute(['schedule_id_check' => $schedule_id]);
                    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
                    $totalCustomers = $totalResult['total'] ?? 0;

                    if ($totalCustomers > 0 && count($manual_customer_ids) >= $totalCustomers) {
                        // Tất cả khách đều được chọn manual - không có gì để phân phòng tự động
                        // Vẫn commit và return true (không throw error)
                        $this->pdo->commit();
                        return true; // Hoàn thành (không có gì để làm)
                    }
                }

                // Thực sự không có khách đã thanh toán
                throw new Exception("Không có khách hàng nào đã thanh toán để phân phòng.");
            }

            // 4. Lấy yêu cầu đặc biệt
            $requestsSql = "
                SELECT 
                    rr.*,
                    b.booking_code,
                    c.full_name AS customer_name,
                    tc.full_name AS target_customer_name
                FROM room_requests rr
                JOIN bookings b ON rr.booking_id = b.id
                JOIN customers c ON rr.customer_id = c.id
                LEFT JOIN customers tc ON rr.target_customer_id = tc.id
                WHERE b.tour_schedule_id = :schedule_id
                  AND rr.status IN ('pending', 'approved')
            ";
            $requestsStmt = $this->pdo->prepare($requestsSql);
            $requestsStmt->execute(['schedule_id' => $schedule_id]);
            $roomRequests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Phân phòng cho từng đêm
            foreach ($itineraries as $itinerary) {
                $day_number = $itinerary['day_number'];
                $itinerary_id = $itinerary['id'];

                // Tính check_in_date và check_out_date
                $check_in_date = date('Y-m-d', strtotime($schedule['start_date'] . ' + ' . ($day_number - 1) . ' days'));
                $check_out_date = date('Y-m-d', strtotime($check_in_date . ' + 1 day'));

                // Lấy khách chưa phân phòng cho đêm này (tất cả khách đã thanh toán)
                $unassignedCustomersSql = "
                    SELECT 
                        bc.id AS booking_customer_id,
                        bc.booking_id,
                        bc.customer_id,
                        bc.age_type,
                        c.full_name,
                        c.gender,
                        c.date_of_birth
                    FROM booking_customers bc
                    JOIN customers c ON bc.customer_id = c.id
                    JOIN bookings b ON bc.booking_id = b.id
                    WHERE b.tour_schedule_id = :schedule_id_main
                      AND b.payment_status = 'paid'
                      AND bc.id NOT IN (
                          SELECT rac.booking_customer_id
                          FROM room_assignment_customers rac
                          JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                          WHERE ra.tour_schedule_id = :schedule_id_sub
                            AND ra.itinerary_id = :itinerary_id_sub
                      )
                ";

                // Loại bỏ khách đã chọn xử lý thủ công
                $params = [
                    'schedule_id_main' => $schedule_id,
                    'schedule_id_sub' => $schedule_id,
                    'itinerary_id_sub' => $itinerary_id
                ];

                if (!empty($manual_customer_ids)) {
                    // Tạo named parameters cho từng manual_customer_id (unique cho mỗi itinerary)
                    $manualParams = [];
                    foreach ($manual_customer_ids as $idx => $id) {
                        $paramName = ':manual_id_day' . $day_number . '_' . $idx;
                        $manualParams[] = $paramName;
                        $params[$paramName] = $id;
                    }
                    $placeholders = implode(',', $manualParams);
                    $unassignedCustomersSql .= " AND bc.id NOT IN ($placeholders)";
                }

                $unassignedCustomersSql .= " ORDER BY bc.booking_id, bc.id";

                $unassignedCustomersStmt = $this->pdo->prepare($unassignedCustomersSql);
                $unassignedCustomersStmt->execute($params);
                $unassignedCustomers = $unassignedCustomersStmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($unassignedCustomers)) {
                    continue; // Đã phân hết cho đêm này
                }

                // 5.1. Xử lý yêu cầu đơn phòng trước
                $singleRoomRequests = array_filter($roomRequests, function ($r) {
                    return $r['request_type'] == 'single_room';
                });

                foreach ($singleRoomRequests as $request) {
                    // Tìm khách trong danh sách chưa phân phòng
                    $customer = array_filter($unassignedCustomers, function ($c) use ($request) {
                        return $c['customer_id'] == $request['customer_id'];
                    });

                    if (!empty($customer)) {
                        $customer = reset($customer);

                        // Tạo phòng đơn
                        $roomData = [
                            'tour_schedule_id' => $schedule_id,
                            'itinerary_id' => $itinerary_id,
                            'service_provider_id' => null,
                            'room_number' => null,
                            'room_type' => 'single',
                            'max_capacity' => 1,
                            'actual_occupancy' => 0,
                            'check_in_date' => $check_in_date,
                            'check_out_date' => $check_out_date,
                            'status' => 'assigned'
                        ];
                        $room_id = $this->createRoomForAutoAssign($roomData);

                        // Gán khách vào phòng
                        $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);

                        // Xóa khách khỏi danh sách chưa phân phòng
                        $unassignedCustomers = array_filter($unassignedCustomers, function ($c) use ($customer) {
                            return $c['booking_customer_id'] != $customer['booking_customer_id'];
                        });
                    }
                }

                // 5.2. Phân phòng theo booking (gia đình/nhóm) trước
                $customersByBooking = [];
                foreach ($unassignedCustomers as $customer) {
                    $booking_id = $customer['booking_id'];
                    if (!isset($customersByBooking[$booking_id])) {
                        $customersByBooking[$booking_id] = [];
                    }
                    $customersByBooking[$booking_id][] = $customer;
                }

                // Ưu tiên ghép khách cùng booking vào cùng phòng (nếu cùng giới tính hoặc gia đình)
                foreach ($customersByBooking as $booking_id => $bookingCustomers) {
                    if (count($bookingCustomers) <= 1) {
                        continue; // Chỉ có 1 người, để phân sau
                    }

                    // Nhóm theo giới tính trong cùng booking
                    $maleInBooking = array_filter($bookingCustomers, function ($c) {
                        return strtolower($c['gender'] ?? '') == 'male' || strtolower($c['gender'] ?? '') == 'nam';
                    });
                    $femaleInBooking = array_filter($bookingCustomers, function ($c) {
                        return strtolower($c['gender'] ?? '') == 'female' || strtolower($c['gender'] ?? '') == 'nữ';
                    });
                    $otherInBooking = array_filter($bookingCustomers, function ($c) {
                        $g = strtolower($c['gender'] ?? '');
                        return $g != 'male' && $g != 'female' && $g != 'nam' && $g != 'nữ';
                    });

                    // Ghép nam trong cùng booking
                    if (count($maleInBooking) >= 2) {
                        $chunks = array_chunk($maleInBooking, $max_customers_per_room);
                        foreach ($chunks as $chunk) {
                            if (count($chunk) > 0 && count($chunk) <= 3) {
                                $maxCapacity = count($chunk) == 1 ? 1 : (count($chunk) == 2 ? 2 : 3);
                                $roomType = $maxCapacity == 1 ? 'single' : ($maxCapacity == 2 ? 'double' : 'triple');

                                $roomData = [
                                    'tour_schedule_id' => $schedule_id,
                                    'itinerary_id' => $itinerary_id,
                                    'service_provider_id' => null,
                                    'room_number' => null,
                                    'room_type' => $roomType,
                                    'max_capacity' => $maxCapacity,
                                    'actual_occupancy' => 0,
                                    'check_in_date' => $check_in_date,
                                    'check_out_date' => $check_out_date,
                                    'status' => 'assigned'
                                ];
                                $room_id = $this->createRoomForAutoAssign($roomData);

                                foreach ($chunk as $customer) {
                                    $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);
                                    // Xóa khỏi danh sách chưa phân phòng
                                    $unassignedCustomers = array_filter($unassignedCustomers, function ($c) use ($customer) {
                                        return $c['booking_customer_id'] != $customer['booking_customer_id'];
                                    });
                                }
                            }
                        }
                    }

                    // Ghép nữ trong cùng booking
                    if (count($femaleInBooking) >= 2) {
                        $chunks = array_chunk($femaleInBooking, $max_customers_per_room);
                        foreach ($chunks as $chunk) {
                            if (count($chunk) > 0 && count($chunk) <= 3) {
                                $maxCapacity = count($chunk) == 1 ? 1 : (count($chunk) == 2 ? 2 : 3);
                                $roomType = $maxCapacity == 1 ? 'single' : ($maxCapacity == 2 ? 'double' : 'triple');

                                $roomData = [
                                    'tour_schedule_id' => $schedule_id,
                                    'itinerary_id' => $itinerary_id,
                                    'service_provider_id' => null,
                                    'room_number' => null,
                                    'room_type' => $roomType,
                                    'max_capacity' => $maxCapacity,
                                    'actual_occupancy' => 0,
                                    'check_in_date' => $check_in_date,
                                    'check_out_date' => $check_out_date,
                                    'status' => 'assigned'
                                ];
                                $room_id = $this->createRoomForAutoAssign($roomData);

                                foreach ($chunk as $customer) {
                                    $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);
                                    $unassignedCustomers = array_filter($unassignedCustomers, function ($c) use ($customer) {
                                        return $c['booking_customer_id'] != $customer['booking_customer_id'];
                                    });
                                }
                            }
                        }
                    }
                }

                // 5.3. Phân phòng tự động cho số khách còn lại (theo giới tính)
                $unassignedCustomers = array_values($unassignedCustomers); // Reset keys

                if (!empty($unassignedCustomers)) {
                    // Nhóm theo giới tính
                    $maleCustomers = array_filter($unassignedCustomers, function ($c) {
                        return strtolower($c['gender'] ?? '') == 'male' || strtolower($c['gender'] ?? '') == 'nam';
                    });
                    $femaleCustomers = array_filter($unassignedCustomers, function ($c) {
                        return strtolower($c['gender'] ?? '') == 'female' || strtolower($c['gender'] ?? '') == 'nữ';
                    });
                    $otherCustomers = array_filter($unassignedCustomers, function ($c) {
                        $g = strtolower($c['gender'] ?? '');
                        return $g != 'male' && $g != 'female' && $g != 'nam' && $g != 'nữ';
                    });

                    // Phân phòng cho nam
                    $maleChunks = array_chunk(array_values($maleCustomers), $max_customers_per_room);
                    foreach ($maleChunks as $chunk) {
                        if (count($chunk) > 0) {
                            $maxCapacity = count($chunk) == 1 ? 1 : (count($chunk) >= 2 ? min(count($chunk), 3) : 2);
                            $roomType = $maxCapacity == 1 ? 'single' : ($maxCapacity == 2 ? 'double' : 'triple');

                            $roomData = [
                                'tour_schedule_id' => $schedule_id,
                                'itinerary_id' => $itinerary_id,
                                'service_provider_id' => null,
                                'room_number' => null,
                                'room_type' => $roomType,
                                'max_capacity' => $maxCapacity,
                                'actual_occupancy' => 0,
                                'check_in_date' => $check_in_date,
                                'check_out_date' => $check_out_date,
                                'status' => 'assigned'
                            ];
                            $room_id = $this->createRoomForAutoAssign($roomData);

                            foreach ($chunk as $customer) {
                                $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);
                            }
                        }
                    }

                    // Phân phòng cho nữ
                    $femaleChunks = array_chunk(array_values($femaleCustomers), $max_customers_per_room);
                    foreach ($femaleChunks as $chunk) {
                        if (count($chunk) > 0) {
                            $maxCapacity = count($chunk) == 1 ? 1 : (count($chunk) >= 2 ? min(count($chunk), 3) : 2);
                            $roomType = $maxCapacity == 1 ? 'single' : ($maxCapacity == 2 ? 'double' : 'triple');

                            $roomData = [
                                'tour_schedule_id' => $schedule_id,
                                'itinerary_id' => $itinerary_id,
                                'service_provider_id' => null,
                                'room_number' => null,
                                'room_type' => $roomType,
                                'max_capacity' => $maxCapacity,
                                'actual_occupancy' => 0,
                                'check_in_date' => $check_in_date,
                                'check_out_date' => $check_out_date,
                                'status' => 'assigned'
                            ];
                            $room_id = $this->createRoomForAutoAssign($roomData);

                            foreach ($chunk as $customer) {
                                $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);
                            }
                        }
                    }

                    // Phân phòng cho other (có thể ghép với nam hoặc nữ, hoặc đơn phòng)
                    foreach ($otherCustomers as $customer) {
                        // Tạm thời tạo phòng đơn cho other
                        $roomData = [
                            'tour_schedule_id' => $schedule_id,
                            'itinerary_id' => $itinerary_id,
                            'service_provider_id' => null,
                            'room_number' => null,
                            'room_type' => 'single',
                            'max_capacity' => 1,
                            'actual_occupancy' => 0,
                            'check_in_date' => $check_in_date,
                            'check_out_date' => $check_out_date,
                            'status' => 'assigned'
                        ];
                        $room_id = $this->createRoomForAutoAssign($roomData);
                        $this->assignCustomerToRoomForAutoAssign($room_id, $customer['booking_customer_id']);
                    }
                }

                // 5.4. Xử lý yêu cầu cùng phòng (share_with)
                $shareRequests = array_filter($roomRequests, function ($r) {
                    return $r['request_type'] == 'share_with' && $r['status'] == 'pending';
                });

                foreach ($shareRequests as $request) {
                    // Tìm 2 khách cần ghép cùng phòng
                    $customer1 = null;
                    $customer2 = null;

                    foreach ($unassignedCustomers as $c) {
                        if ($c['customer_id'] == $request['customer_id']) {
                            $customer1 = $c;
                        }
                        if ($request['target_customer_id'] && $c['customer_id'] == $request['target_customer_id']) {
                            $customer2 = $c;
                        }
                    }

                    if ($customer1 && $customer2) {
                        // Tìm phòng đã có customer1 hoặc customer2 và còn chỗ
                        $existingRoomSql = "
                            SELECT ra.id, ra.max_capacity, ra.actual_occupancy
                            FROM room_assignments ra
                            JOIN room_assignment_customers rac ON ra.id = rac.room_assignment_id
                            WHERE ra.itinerary_id = :itinerary_id_share
                              AND (rac.customer_id = :customer_id1_share OR rac.customer_id = :customer_id2_share)
                              AND ra.actual_occupancy < ra.max_capacity
                            LIMIT 1
                        ";
                        $existingRoomStmt = $this->pdo->prepare($existingRoomSql);
                        $existingRoomStmt->execute([
                            'itinerary_id_share' => $itinerary_id,
                            'customer_id1_share' => $customer1['customer_id'],
                            'customer_id2_share' => $customer2['customer_id']
                        ]);
                        $existingRoom = $existingRoomStmt->fetch(PDO::FETCH_ASSOC);

                        if ($existingRoom && $existingRoom['actual_occupancy'] < $existingRoom['max_capacity']) {
                            // Thêm vào phòng đã có
                            if ($existingRoom['actual_occupancy'] == 0) {
                                $this->assignCustomerToRoomForAutoAssign($existingRoom['id'], $customer1['booking_customer_id']);
                            }
                            $this->assignCustomerToRoomForAutoAssign($existingRoom['id'], $customer2['booking_customer_id']);
                        } else {
                            // Tạo phòng mới
                            $roomData = [
                                'tour_schedule_id' => $schedule_id,
                                'itinerary_id' => $itinerary_id,
                                'service_provider_id' => null,
                                'room_number' => null,
                                'room_type' => 'double',
                                'max_capacity' => 2,
                                'actual_occupancy' => 0,
                                'check_in_date' => $check_in_date,
                                'check_out_date' => $check_out_date,
                                'status' => 'assigned'
                            ];
                            $room_id = $this->createRoomForAutoAssign($roomData);
                            $this->assignCustomerToRoomForAutoAssign($room_id, $customer1['booking_customer_id']);
                            $this->assignCustomerToRoomForAutoAssign($room_id, $customer2['booking_customer_id']);
                        }

                        // Cập nhật status request
                        $updateRequestSql = "UPDATE room_requests SET status = 'fulfilled' WHERE id = :id";
                        $updateRequestStmt = $this->pdo->prepare($updateRequestSql);
                        $updateRequestStmt->execute(['id' => $request['id']]);
                    }
                }
            }

            $this->pdo->commit();
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("RoomAssignment::autoAssign() Error: " . $e->getMessage() . " | SQL State: " . $e->getCode());
            throw new Exception("Không thể phân phòng tự động: " . $e->getMessage());
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("RoomAssignment::autoAssign() Exception: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tạo phòng cho phân phòng tự động (không log history)
     */
    private function createRoomForAutoAssign($data)
    {
        $sql = "
            INSERT INTO room_assignments (
                tour_schedule_id, itinerary_id, service_provider_id,
                room_number, room_type, max_capacity, actual_occupancy,
                check_in_date, check_out_date, status, notes
            ) VALUES (
                :tour_schedule_id, :itinerary_id, :service_provider_id,
                :room_number, :room_type, :max_capacity, :actual_occupancy,
                :check_in_date, :check_out_date, :status, :notes
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'tour_schedule_id' => $data['tour_schedule_id'],
            'itinerary_id' => $data['itinerary_id'],
            'service_provider_id' => $data['service_provider_id'] ?? null,
            'room_number' => $data['room_number'] ?? null,
            'room_type' => $data['room_type'],
            'max_capacity' => $data['max_capacity'],
            'actual_occupancy' => $data['actual_occupancy'] ?? 0,
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'status' => $data['status'] ?? 'assigned',
            'notes' => $data['notes'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Gán khách vào phòng cho phân phòng tự động (không log history)
     */
    private function assignCustomerToRoomForAutoAssign($room_id, $booking_customer_id, $role = 'companion')
    {
        // Lấy thông tin booking_customer
        $bcStmt = $this->pdo->prepare("
            SELECT bc.*, b.id AS booking_id, c.id AS customer_id
            FROM booking_customers bc
            JOIN bookings b ON bc.booking_id = b.id
            JOIN customers c ON bc.customer_id = c.id
            WHERE bc.id = :id
        ");
        $bcStmt->execute(['id' => $booking_customer_id]);
        $bookingCustomer = $bcStmt->fetch(PDO::FETCH_ASSOC);

        if (!$bookingCustomer) {
            return false;
        }

        // Kiểm tra đã phân phòng trong đêm này chưa
        $roomStmt = $this->pdo->prepare("SELECT itinerary_id FROM room_assignments WHERE id = :room_id");
        $roomStmt->execute(['room_id' => $room_id]);
        $room = $roomStmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            return false;
        }

        $checkStmt = $this->pdo->prepare("
            SELECT rac.id
            FROM room_assignment_customers rac
            JOIN room_assignments ra ON rac.room_assignment_id = ra.id
            WHERE rac.booking_customer_id = :booking_customer_id
              AND ra.itinerary_id = :itinerary_id
        ");
        $checkStmt->execute([
            'booking_customer_id' => $booking_customer_id,
            'itinerary_id' => $room['itinerary_id']
        ]);

        if ($checkStmt->fetch()) {
            return false; // Đã phân phòng
        }

        // Kiểm tra capacity
        $capacityStmt = $this->pdo->prepare("
            SELECT actual_occupancy, max_capacity
            FROM room_assignments
            WHERE id = :room_id
        ");
        $capacityStmt->execute(['room_id' => $room_id]);
        $capacity = $capacityStmt->fetch(PDO::FETCH_ASSOC);

        if ($capacity['actual_occupancy'] >= $capacity['max_capacity']) {
            return false; // Phòng đã đầy
        }

        // Thêm khách vào phòng
        $insertSql = "
            INSERT INTO room_assignment_customers (
                room_assignment_id, booking_customer_id, customer_id, booking_id,
                role, room_preference, special_notes
            ) VALUES (
                :room_assignment_id, :booking_customer_id, :customer_id, :booking_id,
                :role, :room_preference, :special_notes
            )
        ";

        $insertStmt = $this->pdo->prepare($insertSql);
        $insertStmt->execute([
            'room_assignment_id' => $room_id,
            'booking_customer_id' => $booking_customer_id,
            'customer_id' => $bookingCustomer['customer_id'],
            'booking_id' => $bookingCustomer['booking_id'],
            'role' => $role,
            'room_preference' => null,
            'special_notes' => null
        ]);

        // Cập nhật actual_occupancy
        $updateSql = "
            UPDATE room_assignments
            SET actual_occupancy = actual_occupancy + 1,
                status = 'assigned'
            WHERE id = :room_id
        ";
        $updateStmt = $this->pdo->prepare($updateSql);
        $updateStmt->execute(['room_id' => $room_id]);

        return true;
    }
}

