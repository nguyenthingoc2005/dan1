<?php
/**
 * ==============================================================================
 * TOUR OPERATIONS CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý tour đã chốt (sau deadline booking, có đủ booking đã thanh toán)
 * Routing: ?act=admin&module=tour-operations&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class TourOperationsController
{
    private $pdo;
    private $operationsModel;
    private $scheduleModel;
    private $tourModel;
    private $assignmentModel;
    private $vehicleAssignmentModel;
    private $roomAssignmentModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/TourOperations.php';
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/TourAssignment.php';
        require_once MODELS_PATH . '/VehicleAssignment.php';
        require_once MODELS_PATH . '/Vehicle.php';
        require_once MODELS_PATH . '/Driver.php';
        require_once MODELS_PATH . '/RoomAssignment.php';

        $this->operationsModel = new TourOperations($pdo);
        $this->scheduleModel = new TourSchedule($pdo);
        $this->tourModel = new Tour($pdo);
        $this->assignmentModel = new TourAssignment($pdo);
        $this->vehicleAssignmentModel = new VehicleAssignment($pdo);
        $this->roomAssignmentModel = new RoomAssignment($pdo);
    }

    /**
     * Danh sách tour đã chốt
     */
    public function index()
    {
        require_admin();

        $page = $_GET['page'] ?? 1;
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date_from' => $_GET['start_date_from'] ?? '',
            'start_date_to' => $_GET['start_date_to'] ?? '',
            'status' => $_GET['status'] ?? '',
            'has_guide' => $_GET['has_guide'] ?? '',
            'has_vehicle' => $_GET['has_vehicle'] ?? ''
        ];

        $result = $this->operationsModel->getReadyForOperations($filters, $page, 20);
        $tours = $result['data'] ?? [];
        $total = $result['total'] ?? 0;
        $total_pages = $result['pages'] ?? 0;
        $current_page = $result['current_page'] ?? 1;

        // Lấy danh sách tour cho filter
        $allTours = $this->tourModel->getAll(['status' => 'active'], 1, 1000)['data'] ?? [];

        // Debug: Log để kiểm tra
        error_log("TourOperationsController::index() - Total tours found: " . $total);
        error_log("TourOperationsController::index() - Tours data count: " . count($tours));
        error_log("TourOperationsController::index() - All tours count: " . count($allTours));

        // Đảm bảo biến $filters được truyền vào view
        // (biến đã được định nghĩa ở trên, sẽ tự động có trong scope của view)

        $page_title = 'Quản lý Tour Đã Chốt';
        $content_file = VIEWS_PATH . '/admin/tour-operations/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Chi tiết tour operations
     */
    public function show()
    {
        require_admin();

        // Xử lý lưu manual customers vào session (nếu có AJAX request)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_manual_customers') {
            $schedule_id = $_POST['schedule_id'] ?? null;
            $manual_customers = $_POST['manual_customers'] ?? [];

            if ($schedule_id) {
                $_SESSION['room_manual_customers_' . $schedule_id] = $manual_customers;
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Đã lưu danh sách khách xử lý thủ công.']);
                exit;
            }
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            set_error("Không tìm thấy tour.");
            redirect('?act=admin&module=tour-operations');
        }

        // Kiểm tra tour đã sẵn sàng chưa (để thao tác)
        // Cho phép xem nhưng chỉ cho thao tác khi đã đóng hoặc qua deadline
        $canOperate = $this->operationsModel->checkReadyForOperations($id);

        // Lấy thông tin để hiển thị cảnh báo nếu chưa đủ điều kiện
        $schedule = $this->scheduleModel->findById($id);
        $deadlineDate = null;
        if ($schedule) {
            $tour = $this->tourModel->findById($schedule['tour_id']);
            if ($tour) {
                $deadlineDate = date('Y-m-d', strtotime($schedule['start_date'] . ' -' . ($tour['booking_deadline_days'] ?? 1) . ' days'));
            }
        }

        // Lấy thông tin tổng hợp
        $summary = $this->operationsModel->getTourOperationsSummary($id);
        if (!$summary) {
            set_error("Không tìm thấy thông tin tour.");
            redirect('?act=admin&module=tour-operations');
        }

        // Lấy booking đã thanh toán
        $bookings = $this->operationsModel->getPaidBookings($id);

        // Lấy khách hàng
        $participants = $this->operationsModel->getPaidParticipants($id);

        // Lấy HDV hiện tại
        $currentGuide = null;
        if ($summary['guide_id']) {
            require_once MODELS_PATH . '/User.php';
            $userModel = new User($this->pdo);
            $currentGuide = $userModel->findById($summary['guide_id']);
        }

        // Lấy danh sách HDV có sẵn
        $availableGuides = $this->assignmentModel->getAvailableGuides($summary['start_date'], $summary['end_date']);

        // Lấy phân công xe hiện tại
        $vehicleAssignments = $this->vehicleAssignmentModel->getByScheduleId($id);

        // Lấy danh sách xe có sẵn
        $vehicleModel = new Vehicle($this->pdo);
        $totalParticipants = $summary['total_paid_participants'] ?? 0;

        // Debug: Log để kiểm tra
        error_log("TourOperations::show() - Total participants: " . $totalParticipants);
        error_log("TourOperations::show() - Start date: " . $summary['start_date']);
        error_log("TourOperations::show() - End date: " . $summary['end_date']);

        // Lấy tất cả xe active trước để debug
        $allVehicles = $vehicleModel->getAll(['status' => 'active'], 1, 1000);
        error_log("TourOperations::show() - Total active vehicles: " . ($allVehicles['total'] ?? 0));

        // Lấy xe khả dụng (không filter capacity quá chặt, chỉ filter nếu cần)
        // Nếu không có xe đủ capacity, vẫn hiển thị xe có capacity nhỏ hơn để admin biết
        $availableVehicles = $vehicleModel->getAvailable($summary['start_date'], $summary['end_date'], 0); // Bỏ filter capacity_min

        error_log("TourOperations::show() - Available vehicles count: " . count($availableVehicles));

        // Lấy danh sách tài xế có sẵn
        $driverModel = new Driver($this->pdo);
        // Xác định loại bằng lái cần (dựa trên loại xe)
        $licenseTypes = ['D', 'E']; // Mặc định cho xe lớn
        $availableDrivers = $driverModel->getAvailable($summary['start_date'], $summary['end_date'], $licenseTypes);

        // Lấy lịch sử thay đổi HDV
        $guideHistory = $this->getGuideHistory($id);

        // Lấy lịch sử thay đổi xe/tài xế
        $vehicleHistory = $this->vehicleAssignmentModel->getVehicleHistory($id);

        // Lấy phân phòng (theo từng đêm)
        $roomAssignments = $this->roomAssignmentModel->getByScheduleId($id);

        // Tính tổng số phòng đã phân
        $totalRooms = 0;
        if (!empty($roomAssignments)) {
            foreach ($roomAssignments as $day_data) {
                $totalRooms += count($day_data['rooms']);
            }
        }
        $summary['room_count'] = $totalRooms;

        // Lấy yêu cầu phòng đặc biệt
        $roomRequests = $this->roomAssignmentModel->getRoomRequests($id);

        // Lấy thông tin khách sạn từ itinerary
        $hotels = $this->roomAssignmentModel->getHotelsBySchedule($id);

        // Truyền roomAssignmentModel vào view để sử dụng trong tab "Danh sách khách"
        $roomAssignmentModel = $this->roomAssignmentModel;

        // Lấy manual customers từ session (nếu có)
        $session_manual_customers = $_SESSION['room_manual_customers_' . $id] ?? [];

        $page_title = 'Quản lý Tour: ' . $summary['tour_name'];
        $content_file = VIEWS_PATH . '/admin/tour-operations/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Gán HDV
     */
    public function assignGuide()
    {
        require_admin();
        require_csrf_token();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            $guide_id = $_POST['guide_id'] ?? null;

            if (!$schedule_id) {
                throw new Exception("Không tìm thấy tour schedule.");
            }

            if (!$guide_id) {
                throw new Exception("Vui lòng chọn hướng dẫn viên.");
            }

            // Kiểm tra tour đã sẵn sàng chưa (để thao tác)
            // Chỉ cho phép thao tác khi tour đã đóng hoặc đã qua deadline
            if (!$this->operationsModel->checkReadyForOperations($schedule_id)) {
                throw new Exception("Tour này chưa đủ điều kiện để thao tác. Cần đóng tour hoặc đợi đến deadline booking.");
            }

            // Lấy thông tin tour schedule
            $schedule = $this->scheduleModel->findById($schedule_id);
            if (!$schedule) {
                throw new Exception("Tour schedule không tồn tại.");
            }

            // Lấy thông tin tour
            $tour = $this->tourModel->findById($schedule['tour_id']);
            if (!$tour) {
                throw new Exception("Tour không tồn tại.");
            }

            // Kiểm tra xem có HDV cũ không (để lưu lý do thay đổi)
            $previous_guide_id = $schedule['guide_id'] ?? null;
            $is_changing_guide = $previous_guide_id && $previous_guide_id != $guide_id;

            // Nếu đang thay đổi HDV, yêu cầu nhập lý do
            if ($is_changing_guide && empty($_POST['change_reason'])) {
                throw new Exception("Vui lòng nhập lý do thay đổi HDV.");
            }

            $change_reason = $_POST['change_reason'] ?? null;

            // Tính phụ cấp HDV tự động
            $tour = $this->tourModel->findById($schedule['tour_id']);
            $duration_days = (strtotime($schedule['end_date']) - strtotime($schedule['start_date'])) / (60 * 60 * 24) + 1;

            // Tìm rule phù hợp từ tour_allowance_rules (nếu bảng tồn tại)
            $allowance = 0;
            try {
                // Kiểm tra xem bảng có tồn tại không
                $checkTable = $this->pdo->query("SHOW TABLES LIKE 'tour_allowance_rules'");
                if ($checkTable->rowCount() > 0) {
                    // Bảng tồn tại, tìm rule
                    $ruleSql = "
                        SELECT guide_allowance
                        FROM tour_allowance_rules
                        WHERE tour_type = :tour_type
                          AND (duration_days_min IS NULL OR :duration_days >= duration_days_min)
                          AND (duration_days_max IS NULL OR :duration_days <= duration_days_max)
                          AND status = 'active'
                        ORDER BY priority DESC
                        LIMIT 1
                    ";
                    $ruleStmt = $this->pdo->prepare($ruleSql);
                    $ruleStmt->execute([
                        'tour_type' => $tour['tour_type'] ?? 'domestic',
                        'duration_days' => $duration_days
                    ]);
                    $rule = $ruleStmt->fetch(PDO::FETCH_ASSOC);
                    $allowance = $rule ? (float) $rule['guide_allowance'] : 0;
                }
            } catch (PDOException $e) {
                // Bảng không tồn tại, bỏ qua
                error_log("tour_allowance_rules table not found, using default calculation");
            }

            // Nếu không có rule, dùng cách tính mặc định từ TourAssignment
            if ($allowance == 0) {
                $defaultDailySalary = TourAssignment::DEFAULT_DAILY_SALARY; // 500k/ngày
                $allowance = $defaultDailySalary * $duration_days;
            }

            // Cập nhật tour_schedules
            $updateSql = "UPDATE tour_schedules SET guide_id = :guide_id WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([
                'guide_id' => $guide_id,
                'id' => $schedule_id
            ]);

            // Tạo hoặc cập nhật tour_assignments
            // Lưu ý: ON DUPLICATE KEY UPDATE cần bind lại các parameter
            // Kiểm tra xem đã có assignment chưa
            $checkAssignment = $this->pdo->prepare("SELECT id, guide_id FROM tour_assignments WHERE tour_schedule_id = :schedule_id LIMIT 1");
            $checkAssignment->execute(['schedule_id' => $schedule_id]);
            $existingAssignment = $checkAssignment->fetch(PDO::FETCH_ASSOC);

            if ($existingAssignment) {
                // Cập nhật assignment hiện có
                $assignmentSql = "
                    UPDATE tour_assignments 
                    SET guide_id = :guide_id,
                        previous_guide_id = :previous_guide_id,
                        change_reason = :change_reason,
                        salary_amount = :salary_amount,
                        assignment_date = CURDATE()
                    WHERE id = :assignment_id
                ";

                $assignmentStmt = $this->pdo->prepare($assignmentSql);
                $assignmentStmt->execute([
                    'guide_id' => $guide_id,
                    'previous_guide_id' => $is_changing_guide ? $previous_guide_id : null,
                    'change_reason' => $is_changing_guide ? $change_reason : null,
                    'salary_amount' => $allowance,
                    'assignment_id' => $existingAssignment['id']
                ]);

                // Lưu lịch sử thay đổi vào schedule_guide_history (nếu đang thay đổi HDV)
                if ($is_changing_guide) {
                    // Lấy tên HDV cũ và mới
                    $oldGuideName = '';
                    $newGuideName = '';
                    if ($previous_guide_id) {
                        $oldGuideStmt = $this->pdo->prepare("SELECT full_name FROM users WHERE id = :id");
                        $oldGuideStmt->execute(['id' => $previous_guide_id]);
                        $oldGuide = $oldGuideStmt->fetch(PDO::FETCH_ASSOC);
                        $oldGuideName = $oldGuide['full_name'] ?? '';
                    }
                    if ($guide_id) {
                        $newGuideStmt = $this->pdo->prepare("SELECT full_name FROM users WHERE id = :id");
                        $newGuideStmt->execute(['id' => $guide_id]);
                        $newGuide = $newGuideStmt->fetch(PDO::FETCH_ASSOC);
                        $newGuideName = $newGuide['full_name'] ?? '';
                    }

                    $historySql = "
                        INSERT INTO schedule_guide_history (
                            schedule_id, old_guide_id, new_guide_id, 
                            old_guide_name, new_guide_name, changed_by, reason
                        ) VALUES (
                            :schedule_id, :old_guide_id, :new_guide_id,
                            :old_guide_name, :new_guide_name, :changed_by, :reason
                        )
                    ";
                    $historyStmt = $this->pdo->prepare($historySql);
                    $historyStmt->execute([
                        'schedule_id' => $schedule_id,
                        'old_guide_id' => $previous_guide_id,
                        'new_guide_id' => $guide_id,
                        'old_guide_name' => $oldGuideName,
                        'new_guide_name' => $newGuideName,
                        'changed_by' => get_user_id(),
                        'reason' => $change_reason
                    ]);
                }
            } else {
                // Tạo assignment mới
                $assignmentSql = "
                    INSERT INTO tour_assignments (
                        tour_schedule_id, guide_id, previous_guide_id, assignment_date, 
                        salary_amount, change_reason, status, created_by
                    ) VALUES (
                        :tour_schedule_id, :guide_id, :previous_guide_id, CURDATE(), 
                        :salary_amount, :change_reason, 'assigned', :created_by
                    )
                ";

                $assignmentStmt = $this->pdo->prepare($assignmentSql);
                $assignmentStmt->execute([
                    'tour_schedule_id' => $schedule_id,
                    'guide_id' => $guide_id,
                    'previous_guide_id' => $is_changing_guide ? $previous_guide_id : null,
                    'salary_amount' => $allowance,
                    'change_reason' => $is_changing_guide ? $change_reason : null,
                    'created_by' => get_user_id()
                ]);
            }

            set_success("Gán hướng dẫn viên thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Lấy lịch sử thay đổi HDV
     */
    private function getGuideHistory($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    sgh.*,
                    u.full_name AS changed_by_name
                FROM schedule_guide_history sgh
                LEFT JOIN users u ON sgh.changed_by = u.id
                WHERE sgh.schedule_id = :schedule_id
                ORDER BY sgh.created_at DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            logError("TourOperationsController::getGuideHistory() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Phân công xe và tài xế
     */
    public function assignVehicle()
    {
        require_admin();
        require_csrf_token();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            $vehicle_id = $_POST['vehicle_id'] ?? null;
            $driver_id = $_POST['driver_id'] ?? null;

            if (!$schedule_id) {
                throw new Exception("Không tìm thấy tour schedule.");
            }

            // Kiểm tra tour đã sẵn sàng chưa (để thao tác)
            // Chỉ cho phép thao tác khi tour đã đóng hoặc đã qua deadline
            if (!$this->operationsModel->checkReadyForOperations($schedule_id)) {
                throw new Exception("Tour này chưa đủ điều kiện để thao tác. Cần đóng tour hoặc đợi đến deadline booking.");
            }

            // Lấy thông tin tour schedule
            $schedule = $this->scheduleModel->findById($schedule_id);
            if (!$schedule) {
                throw new Exception("Tour schedule không tồn tại.");
            }

            // Kiểm tra xem đã có phân công chưa (để biết là tạo mới hay cập nhật)
            $existingAssignments = $this->vehicleAssignmentModel->getByScheduleId($schedule_id);
            $isUpdate = !empty($existingAssignments);

            if ($isUpdate) {
                // Cập nhật phân công hiện có - cho phép chỉ sửa một trong hai
                $oldAssignment = $existingAssignments[0];

                // Nếu không chọn xe, giữ nguyên xe cũ
                if (empty($vehicle_id)) {
                    $vehicle_id = $oldAssignment['vehicle_id'];
                }

                // Nếu không chọn tài xế, giữ nguyên tài xế cũ
                if (empty($driver_id)) {
                    $driver_id = $oldAssignment['driver_id'];
                }

                // Tính phụ cấp tài xế tự động
                $driverSalary = $this->vehicleAssignmentModel->calculateDriverSalary($schedule_id);

                $data = [
                    'vehicle_id' => $vehicle_id,
                    'driver_id' => $driver_id,
                    'start_date' => $schedule['start_date'],
                    'end_date' => $schedule['end_date'],
                    'pickup_location' => $_POST['pickup_location'] ?? null,
                    'return_location' => $_POST['return_location'] ?? null,
                    'estimated_distance' => 0,
                    'estimated_fuel_cost' => 0,
                    'driver_salary' => $driverSalary,
                    'notes' => $_POST['notes'] ?? null,
                    'reason' => $_POST['change_reason'] ?? null
                ];
                $this->vehicleAssignmentModel->update($oldAssignment['id'], $data);
            } else {
                // Tạo phân công mới - bắt buộc phải chọn cả hai
                if (!$vehicle_id) {
                    throw new Exception("Vui lòng chọn xe.");
                }

                if (!$driver_id) {
                    throw new Exception("Vui lòng chọn tài xế.");
                }

                // Tính phụ cấp tài xế tự động
                $driverSalary = $this->vehicleAssignmentModel->calculateDriverSalary($schedule_id);

                $data = [
                    'tour_schedule_id' => $schedule_id,
                    'vehicle_id' => $vehicle_id,
                    'driver_id' => $driver_id,
                    'start_date' => $schedule['start_date'],
                    'end_date' => $schedule['end_date'],
                    'pickup_location' => $_POST['pickup_location'] ?? null,
                    'return_location' => $_POST['return_location'] ?? null,
                    'estimated_distance' => 0, // Không cần nhập nữa
                    'estimated_fuel_cost' => 0, // Không cần nhập nữa
                    'driver_salary' => $driverSalary,
                    'status' => 'assigned',
                    'assigned_by' => get_user_id(),
                    'notes' => $_POST['notes'] ?? null
                ];
                $this->vehicleAssignmentModel->create($data);
            }
            set_success("Phân công xe và tài xế thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Phân phòng tự động
     */
    public function autoAssignRooms()
    {
        require_admin();
        require_csrf_token();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            if (!$schedule_id) {
                throw new Exception("Không tìm thấy tour schedule.");
            }

            // Kiểm tra tour đã sẵn sàng chưa
            if (!$this->operationsModel->checkReadyForOperations($schedule_id)) {
                throw new Exception("Tour này chưa đủ điều kiện để thao tác.");
            }

            // Lấy tham số từ form, ưu tiên lấy từ session nếu có
            $manual_customer_ids = $_POST['manual_customers'] ?? [];
            // Nếu form không có, lấy từ session
            if (empty($manual_customer_ids)) {
                $manual_customer_ids = $_SESSION['room_manual_customers_' . $schedule_id] ?? [];
            }
            // Đảm bảo là array
            if (!is_array($manual_customer_ids)) {
                $manual_customer_ids = [$manual_customer_ids];
            }

            $max_customers_per_room = (int) ($_POST['max_customers_per_room'] ?? 2);
            $prioritize_same_booking = isset($_POST['prioritize_same_booking']) && $_POST['prioritize_same_booking'] == '1';
            $auto_single_room = isset($_POST['auto_single_room']) && $_POST['auto_single_room'] == '1';

            // Kiểm tra nếu tất cả khách đều được chọn manual
            // Sử dụng model để lấy danh sách khách chưa phân phòng
            $unassignedCustomers = $this->roomAssignmentModel->getUnassignedCustomers($schedule_id);
            $totalUnassignedCustomers = count($unassignedCustomers);
            
            if (!empty($manual_customer_ids)) {
                // Đảm bảo manual_customer_ids là array
                if (!is_array($manual_customer_ids)) {
                    $manual_customer_ids = [$manual_customer_ids];
                }

                // Lấy danh sách booking_customer_id từ unassigned customers
                $unassignedCustomerIds = array_column($unassignedCustomers, 'booking_customer_id');
                
                // Kiểm tra xem tất cả khách chưa phân phòng có đều nằm trong manual không
                $allSelected = true;
                foreach ($unassignedCustomerIds as $unassignedId) {
                    if (!in_array($unassignedId, $manual_customer_ids)) {
                        $allSelected = false;
                        break;
                    }
                }
                
                // Nếu tất cả khách chưa phân phòng đều được chọn manual
                if ($allSelected && $totalUnassignedCustomers > 0) {
                    // Không throw error, chỉ thông báo và return
                    // Hoặc có thể cho phép tiếp tục (không làm gì cả)
                    set_success("Tất cả khách chưa phân phòng đều được chọn xử lý thủ công. Vui lòng phân phòng thủ công cho các khách này.");
                    redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);
                    return;
                }
            } else {
                // Không có khách nào được chọn manual, nhưng kiểm tra xem có khách chưa phân phòng không
                if ($totalUnassignedCustomers == 0) {
                    set_success("Không có khách hàng nào cần phân phòng.");
                    redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);
                    return;
                }
            }

            // Gọi method autoAssign với tham số
            $this->roomAssignmentModel->autoAssign($schedule_id, [
                'manual_customer_ids' => $manual_customer_ids,
                'max_customers_per_room' => $max_customers_per_room,
                'prioritize_same_booking' => $prioritize_same_booking,
                'auto_single_room' => $auto_single_room
            ]);

            set_success("Phân phòng tự động thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Gán khách vào phòng
     */
    public function assignCustomerToRoom()
    {
        require_admin();
        require_csrf_token();

        try {
            $room_id = $_POST['room_id'] ?? null;
            $booking_customer_id = $_POST['booking_customer_id'] ?? null;
            $role = $_POST['role'] ?? 'companion';

            if (!$room_id || !$booking_customer_id) {
                throw new Exception("Thiếu thông tin cần thiết.");
            }

            $this->roomAssignmentModel->assignCustomerToRoom($room_id, $booking_customer_id, $role);

            set_success("Gán khách vào phòng thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Xóa khách khỏi phòng
     */
    public function removeCustomerFromRoom()
    {
        require_admin();
        require_csrf_token();

        try {
            $room_assignment_customer_id = $_POST['room_assignment_customer_id'] ?? null;

            if (!$room_assignment_customer_id) {
                throw new Exception("Thiếu thông tin cần thiết.");
            }

            $this->roomAssignmentModel->removeCustomerFromRoom($room_assignment_customer_id);

            set_success("Xóa khách khỏi phòng thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Tạo phòng mới
     */
    public function createRoom()
    {
        require_admin();
        require_csrf_token();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            if (!$schedule_id) {
                throw new Exception("Không tìm thấy tour schedule.");
            }

            // Kiểm tra tour đã sẵn sàng chưa
            if (!$this->operationsModel->checkReadyForOperations($schedule_id)) {
                throw new Exception("Tour này chưa đủ điều kiện để thao tác.");
            }

            $data = [
                'tour_schedule_id' => $schedule_id,
                'itinerary_id' => $_POST['itinerary_id'] ?? null,
                'service_provider_id' => $_POST['service_provider_id'] ?? null,
                'room_number' => $_POST['room_number'] ?? null,
                'room_type' => $_POST['room_type'] ?? 'double',
                'max_capacity' => (int) ($_POST['max_capacity'] ?? 2),
                'actual_occupancy' => 0,
                // check_in_date và check_out_date sẽ được tự động tính từ itinerary và tour schedule
                'status' => 'pending',
                'notes' => $_POST['notes'] ?? null
            ];

            if (!$data['itinerary_id']) {
                throw new Exception("Vui lòng chọn đêm.");
            }

            $this->roomAssignmentModel->createRoom($data);

            set_success("Tạo phòng thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Cập nhật thông tin phòng
     */
    public function updateRoom()
    {
        require_admin();
        require_csrf_token();

        try {
            $room_id = $_POST['room_id'] ?? null;
            if (!$room_id) {
                throw new Exception("Không tìm thấy phòng.");
            }

            $data = [];
            if (isset($_POST['room_number']))
                $data['room_number'] = $_POST['room_number'];
            if (isset($_POST['room_type']))
                $data['room_type'] = $_POST['room_type'];
            if (isset($_POST['max_capacity']))
                $data['max_capacity'] = (int) $_POST['max_capacity'];
            if (isset($_POST['service_provider_id']))
                $data['service_provider_id'] = $_POST['service_provider_id'] ?: null;
            if (isset($_POST['status']))
                $data['status'] = $_POST['status'];
            if (isset($_POST['notes']))
                $data['notes'] = $_POST['notes'];

            $this->roomAssignmentModel->updateRoom($room_id, $data);

            set_success("Cập nhật phòng thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }

    /**
     * Cập nhật trạng thái tour schedule (xác nhận tour)
     */
    public function updateStatus()
    {
        require_admin();
        require_csrf_token();

        try {
            $schedule_id = $_POST['schedule_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$schedule_id || !$status) {
                throw new Exception("Thiếu thông tin.");
            }

            // Kiểm tra điều kiện xác nhận
            if ($status === 'confirmed') {
                $summary = $this->operationsModel->getTourOperationsSummary($schedule_id);
                if (!$summary['guide_id']) {
                    throw new Exception("Chưa gán hướng dẫn viên.");
                }
                if ($summary['vehicle_count'] == 0) {
                    throw new Exception("Chưa phân công xe và tài xế.");
                }
            }

            $this->scheduleModel->updateStatus($schedule_id, $status);
            set_success("Cập nhật trạng thái thành công!");
            redirect('?act=admin&module=tour-operations&action=show&id=' . $schedule_id);

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=tour-operations&action=show&id=' . ($_POST['schedule_id'] ?? ''));
        }
    }
}

