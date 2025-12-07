<?php
require_once 'app/models/TourSchedule.php';
require_once 'app/models/Tour.php';

class TourScheduleController
{
    private $scheduleModel;
    private $tourModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->scheduleModel = new TourSchedule($pdo);
        $this->tourModel = new Tour($pdo);
    }

    public function index()
    {
        require_admin();

        $page = $_GET['page'] ?? 1;
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $result = $this->scheduleModel->getAll($filters, $page, 20);
        $schedules = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];
        $tours = $this->tourModel->getAll(['status' => 'active'], 1, 1000)['data']; // For filter dropdown

        // Get guides for display
        require_once MODELS_PATH . '/TourAssignment.php';
        $assignmentModel = new TourAssignment($this->pdo);
        $guides = $assignmentModel->getAvailableGuides(date('Y-m-d'), date('Y-m-d', strtotime('+1 year')));

        $page_title = 'Quản lý Lịch Khởi Hành';
        $content_file = 'app/views/admin/schedules/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function create()
    {
        require_admin();

        // Filter tours: status = 'active' AND approval_status = 'approved'
        $tours = $this->tourModel->getAll(['status' => 'active'], 1, 1000)['data'];
        $tours = array_filter($tours, function ($tour) {
            return (!isset($tour['approval_status']) || $tour['approval_status'] === 'approved');
        });

        // Get all guides for dropdown
        require_once MODELS_PATH . '/TourAssignment.php';
        $assignmentModel = new TourAssignment($this->pdo);
        $sql = "SELECT u.id, u.full_name, u.phone, u.email
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'guide' AND u.status = 'active'
                ORDER BY u.full_name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = 'Thêm Lịch Khởi Hành';
        $content_file = 'app/views/admin/schedules/create.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tour_id = $_POST['tour_id'];
                $start_date = $_POST['start_date'];

                // Get tour duration to calculate end_date
                $tour = $this->tourModel->findById($tour_id);
                if (!$tour)
                    throw new Exception("Tour không tồn tại");

                // Validate tour status
                if ($tour['status'] !== 'active') {
                    throw new Exception("Chỉ có thể tạo lịch cho tour đang hoạt động (Active)");
                }

                // Validate tour approval (nếu cần)
                if (isset($tour['approval_status']) && $tour['approval_status'] !== 'approved') {
                    throw new Exception("Tour chưa được duyệt. Vui lòng duyệt tour trước khi tạo lịch");
                }

                // Validate start_date >= today
                $today = date('Y-m-d');
                if ($start_date < $today) {
                    throw new Exception("Ngày khởi hành phải từ hôm nay trở đi");
                }

                // Calculate end_date: start_date + duration_days - 1
                $duration = $tour['duration_days'] ?? 1;
                $end_date = date('Y-m-d', strtotime($start_date . " + " . ($duration - 1) . " days"));

                // Validate end_date if provided manually
                if (!empty($_POST['end_date'])) {
                    $manual_end_date = $_POST['end_date'];
                    $expected_days = (strtotime($manual_end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
                    if ($expected_days != $duration) {
                        throw new Exception("Ngày kết thúc không đúng. Tour này có {$duration} ngày, ngày kết thúc phải là: " . $end_date);
                    }
                    $end_date = $manual_end_date;
                }

                // 1. Kiểm tra tour_type
                $tour_type = $tour['tour_type'] ?? 'public';

                if ($tour_type == 'custom') {
                    // Custom tour: Kiểm tra xem đã có schedule chưa
                    $existing = $this->scheduleModel->getAll(['tour_id' => $tour_id], 1, 1000);
                    if (!empty($existing)) {
                        throw new Exception("Tour tùy chỉnh (Custom) chỉ có thể có 1 lịch khởi hành. Vui lòng sử dụng lịch hiện có hoặc xóa lịch cũ.");
                    }
                }

                // 2. Validate quota: >= min_participants, <= max_participants
                $quota = (int) ($_POST['quota'] ?? 20);
                $min_participants = $tour['min_participants'] ?? 15;
                $max_participants = $tour['max_participants'] ?? 45;

                if ($quota < $min_participants) {
                    throw new Exception("Số chỗ không được nhỏ hơn số người tối thiểu của tour ($min_participants)");
                }
                if ($quota > $max_participants) {
                    throw new Exception("Số chỗ không được vượt quá số người tối đa của tour ($max_participants)");
                }

                // 3. Kiểm tra lịch trùng (cùng tour, ngày bắt đầu hoặc ngày kết thúc chồng lấn)
                // Chỉ check overlap cho public tour (custom tour đã check ở trên)
                if ($tour_type == 'public') {
                    $overlap = $this->scheduleModel->checkOverlap($tour_id, $start_date, $end_date);
                    if ($overlap) {
                        throw new Exception("Lịch này trùng với lịch đã tồn tại cho tour này");
                    }
                }

                // 4. Kiểm tra guide availability nếu có gán guide
                $guide_id = !empty($_POST['guide_id']) ? (int) $_POST['guide_id'] : null;
                if ($guide_id) {
                    require_once MODELS_PATH . '/TourAssignment.php';
                    $assignmentModel = new TourAssignment($this->pdo);

                    // Validate guide is actually a guide
                    if (!$assignmentModel->isGuide($guide_id)) {
                        throw new Exception("Người dùng này không phải là Hướng dẫn viên hoặc đã bị vô hiệu hóa");
                    }

                    // Check guide availability
                    $availability = $assignmentModel->checkGuideAvailability($guide_id, $start_date, $end_date);
                    if (!$availability['available']) {
                        $conflict = $availability['conflict'];
                        $conflictInfo = '';
                        if (isset($conflict['tour_name'])) {
                            $conflictInfo = $conflict['tour_name'] . " ({$conflict['start_date']} - {$conflict['end_date']})";
                        } else {
                            $conflictInfo = "Lịch khác ({$conflict['start_date']} - {$conflict['end_date']})";
                        }
                        throw new Exception("HDV đã có lịch trùng: " . $conflictInfo);
                    }
                }

                $data = [
                    'tour_id' => $tour_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $quota,
                    'booked' => 0, // DEFAULT 0
                    'status' => $_POST['status'] ?? 'open', // DEFAULT 'open'
                    'adult_price' => !empty($_POST['adult_price']) ? $_POST['adult_price'] : $tour['adult_price'],
                    'child_price' => !empty($_POST['child_price']) ? $_POST['child_price'] : $tour['child_price'],
                    'infant_price' => !empty($_POST['infant_price']) ? $_POST['infant_price'] : $tour['infant_price'],
                    'guide_id' => $guide_id,
                    'guide_notes' => !empty($_POST['guide_notes']) ? sanitize($_POST['guide_notes']) : null
                ];

                $schedule_id = $this->scheduleModel->create($data);

                // Lưu lịch sử nếu có gán guide
                if ($guide_id) {
                    $this->scheduleModel->logGuideChange(
                        $schedule_id,
                        null, // old_guide_id = null (lần đầu gán)
                        $guide_id,
                        get_user_id(),
                        'Gán HDV lần đầu',
                        $_POST['guide_notes'] ?? null
                    );
                }

                set_success("Tạo lịch trình tour thành công!");
                // Redirect về danh sách lịch trình của tour đó
                redirect('?act=admin&module=schedules&tour_id=' . $tour_id);

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect('?act=admin&module=schedules&action=create');
            }
        }
    }
    public function edit()
    {
        require_admin();

        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        $tours = $this->tourModel->getAll(['status' => 'active'], 1, 1000)['data'];

        // Get all guides for dropdown
        $sql = "SELECT u.id, u.full_name, u.phone, u.email
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'guide' AND u.status = 'active'
                ORDER BY u.full_name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get guide change history
        $guide_history = $this->scheduleModel->getGuideHistory($id);

        $page_title = 'Chỉnh sửa Lịch Khởi Hành';
        $content_file = 'app/views/admin/schedules/edit.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $schedule = $this->scheduleModel->findById($id);
                if (!$schedule)
                    throw new Exception("Lịch khởi hành không tồn tại");

                $tour_id = $_POST['tour_id'];
                $start_date = $_POST['start_date'];
                $quota = (int) ($_POST['quota'] ?? 20);

                // Validate quota không được nhỏ hơn số đã đặt
                if ($quota < $schedule['booked']) {
                    throw new Exception("Số chỗ mở bán không được nhỏ hơn số đã đặt (" . $schedule['booked'] . " khách)");
                }

                // Get tour info
                $tour = $this->tourModel->findById($tour_id);
                if (!$tour)
                    throw new Exception("Tour không tồn tại");

                // Validate quota: >= min_participants, <= max_participants
                $min_participants = $tour['min_participants'] ?? 15;
                $max_participants = $tour['max_participants'] ?? 45;

                if ($quota < $min_participants) {
                    throw new Exception("Số chỗ không được nhỏ hơn số người tối thiểu của tour ($min_participants)");
                }
                if ($quota > $max_participants) {
                    throw new Exception("Số chỗ không được vượt quá số người tối đa của tour ($max_participants)");
                }

                // Calculate end_date: start_date + duration_days - 1
                $duration = $tour['duration_days'] ?? 1;
                $end_date = date('Y-m-d', strtotime($start_date . " + " . ($duration - 1) . " days"));

                // Validate end_date if provided manually
                if (!empty($_POST['end_date'])) {
                    $manual_end_date = $_POST['end_date'];
                    $expected_days = (strtotime($manual_end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
                    if ($expected_days != $duration) {
                        throw new Exception("Ngày kết thúc không đúng. Tour này có {$duration} ngày, ngày kết thúc phải là: " . $end_date);
                    }
                    $end_date = $manual_end_date;
                }

                // Validate start_date (only if changed)
                if ($start_date != $schedule['start_date']) {
                    $today = date('Y-m-d');
                    if ($start_date < $today) {
                        throw new Exception("Ngày khởi hành phải từ hôm nay trở đi");
                    }

                    // Check overlap
                    if ($tour['tour_type'] == 'public') {
                        $overlap = $this->scheduleModel->checkOverlap($tour_id, $start_date, $end_date, $id);
                        if ($overlap) {
                            throw new Exception("Lịch này trùng với lịch đã tồn tại");
                        }
                    }
                }

                // Validate status change: Không cho hủy nếu có booking
                $new_status = $_POST['status'];
                if ($new_status == 'cancelled' && $schedule['booked'] > 0) {
                    // Kiểm tra xem có booking confirmed không
                    require_once MODELS_PATH . '/Booking.php';
                    $bookingModel = new Booking($this->pdo);
                    $bookings = $bookingModel->getAll([
                        'tour_id' => $tour_id,
                        'start_date' => $start_date,
                        'exact_date' => true
                    ], 1, 100);

                    $confirmed_count = 0;
                    foreach ($bookings as $b) {
                        if (in_array($b['approval_status'] ?? '', ['approved', 'pending'])) {
                            $confirmed_count++;
                        }
                    }

                    if ($confirmed_count > 0) {
                        throw new Exception("Không thể hủy lịch khởi hành đã có " . $confirmed_count . " booking đã xác nhận. Vui lòng hủy các booking trước.");
                    }
                }

                // Handle guide assignment - Check availability if guide is changed or date is changed
                $new_guide_id = isset($_POST['guide_id']) && !empty($_POST['guide_id']) ? (int) $_POST['guide_id'] : null;
                $old_guide_id = $schedule['guide_id'] ?? null;
                $date_changed = ($start_date != $schedule['start_date'] || $end_date != $schedule['end_date']);
                $guide_changed = ($new_guide_id != $old_guide_id);

                // Nếu guide thay đổi hoặc ngày thay đổi (và có guide), cần check availability
                if ($new_guide_id && ($guide_changed || $date_changed)) {
                    require_once MODELS_PATH . '/TourAssignment.php';
                    $assignmentModel = new TourAssignment($this->pdo);

                    // Validate guide is actually a guide
                    if (!$assignmentModel->isGuide($new_guide_id)) {
                        throw new Exception("Người dùng này không phải là Hướng dẫn viên hoặc đã bị vô hiệu hóa");
                    }

                    // Check guide availability (exclude current schedule khi update)
                    $availability = $assignmentModel->checkGuideAvailability($new_guide_id, $start_date, $end_date, null, $id);
                    if (!$availability['available']) {
                        $conflict = $availability['conflict'];
                        $conflictInfo = '';
                        if (isset($conflict['tour_name'])) {
                            $conflictInfo = $conflict['tour_name'] . " ({$conflict['start_date']} - {$conflict['end_date']})";
                        } else {
                            $conflictInfo = "Lịch khác ({$conflict['start_date']} - {$conflict['end_date']})";
                        }
                        throw new Exception("HDV đã có lịch trùng: " . $conflictInfo);
                    }
                }

                $data = [
                    'tour_id' => $tour_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $quota,
                    'adult_price' => $_POST['adult_price'],
                    'child_price' => $_POST['child_price'],
                    'infant_price' => $_POST['infant_price'],
                    'status' => $new_status,
                    'guide_id' => $new_guide_id,
                    'guide_notes' => $_POST['guide_notes'] ?? null
                ];

                // Validate: Nếu đổi guide thì phải có lý do
                if ($guide_changed && empty($_POST['guide_change_reason'])) {
                    throw new Exception("Vui lòng chọn lý do khi đổi HDV");
                }

                $this->scheduleModel->update($id, $data);

                // Lưu lịch sử nếu guide thay đổi
                if ($guide_changed) {
                    $reason = $_POST['guide_change_reason'] ?? 'Đổi HDV';
                    $this->scheduleModel->logGuideChange(
                        $id,
                        $old_guide_id,
                        $new_guide_id,
                        get_user_id(),
                        $reason,
                        $_POST['guide_notes'] ?? null
                    );
                }

                set_success("Đã cập nhật lịch khởi hành!");
                redirect('?act=admin&module=schedules');

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=schedules&action=edit&id=" . ($_POST['id'] ?? 0));
            }
        }
    }
    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        if ($schedule['booked'] > 0) {
            set_error("Không thể xóa lịch khởi hành đã có khách đặt (" . $schedule['booked'] . " khách). Hãy đóng lịch thay vì xóa.");
            redirect('?act=admin&module=schedules');
        }

        if ($this->scheduleModel->delete($id)) {
            set_success("Đã xóa lịch khởi hành thành công!");
        } else {
            set_error("Có lỗi xảy ra khi xóa lịch.");
        }
        redirect('?act=admin&module=schedules');
    }

    /**
     * Xem chi tiết lịch khởi hành
     */
    public function show()
    {
        require_admin();

        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        // Get bookings for this schedule
        // Bookings are linked to schedule by tour_id + start_date (exact match)
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new Booking($this->pdo);
        
        // Get all bookings for this schedule (including cancelled/rejected for admin view)
        // Use direct SQL query to ensure we get all bookings
        $sql = "SELECT b.*, 
                       t.name as tour_name, t.tour_code,
                       c.full_name as customer_name, c.phone as customer_phone, c.email as customer_email
                FROM bookings b
                LEFT JOIN tours t ON b.tour_id = t.id
                LEFT JOIN customers c ON b.customer_id = c.id
                WHERE b.tour_id = :tour_id 
                  AND b.start_date = :start_date
                ORDER BY b.created_at DESC
                LIMIT 100";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date']
        ]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate actual booked count from SQL (chính xác hơn)
        // Tính tổng số người từ bookings approved/pending/completed (không tính cancelled/rejected)
        $countSql = "SELECT 
                        COUNT(*) as booking_count,
                        COALESCE(SUM(adult_count + child_count + infant_count), 0) as total_participants
                     FROM bookings
                     WHERE tour_id = :tour_id 
                       AND start_date = :start_date
                       AND approval_status IN ('approved', 'pending', 'completed')";
        
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date']
        ]);
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $actualBookedCount = (int) ($countResult['total_participants'] ?? 0);
        $approvedBookingsCount = (int) ($countResult['booking_count'] ?? 0);
        
        // Update schedule booked count if it's different (sync issue fix)
        if ($schedule['booked'] != $actualBookedCount) {
            $updateSql = "UPDATE tour_schedules SET booked = :booked WHERE id = :id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([
                'booked' => $actualBookedCount,
                'id' => $id
            ]);
            // Refresh schedule data
            $schedule = $this->scheduleModel->findById($id);
        }

        // Get guide change history
        $guide_history = $this->scheduleModel->getGuideHistory($id);

        $page_title = 'Chi tiết Lịch Khởi Hành';
        $content_file = 'app/views/admin/schedules/show.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * Thay đổi trạng thái nhanh (Đóng/Mở)
     */
    public function changeStatus()
    {
        require_admin();

        $id = $_POST['id'] ?? $_GET['id'] ?? 0;
        $status = $_POST['status'] ?? $_GET['status'] ?? '';

        if (!$id || !$status) {
            set_error('Thiếu thông tin');
            redirect('?act=admin&module=schedules');
            return;
        }

        $schedule = $this->scheduleModel->findById($id);
        if (!$schedule) {
            set_error('Lịch khởi hành không tồn tại');
            redirect('?act=admin&module=schedules');
            return;
        }

        // Validation: Chỉ cho phép đóng/mở (open ↔ closed)
        // Không cho phép đổi sang completed hoặc cancelled từ đây
        $current_status = $schedule['status'];
        if (!in_array($current_status, ['open', 'closed'])) {
            set_error('Chỉ có thể đóng/mở lịch trình khi trạng thái là "Mở đặt" hoặc "Đóng đặt"');
            redirect('?act=admin&module=schedules');
            return;
        }

        if (!in_array($status, ['open', 'closed'])) {
            set_error('Chỉ có thể chuyển sang trạng thái "Mở đặt" hoặc "Đóng đặt"');
            redirect('?act=admin&module=schedules');
            return;
        }

        // Validation: Kiểm tra bookings trước khi đóng/hủy
        $booked_count = $schedule['booked'] ?? 0;

        // Nếu hủy (cancelled) và có booking → Không cho phép
        if ($status == 'cancelled' && $booked_count > 0) {
            // Kiểm tra xem có booking confirmed không
            require_once MODELS_PATH . '/Booking.php';
            $bookingModel = new Booking($this->pdo);
            $bookings = $bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true
            ], 1, 100);

            $confirmed_count = 0;
            foreach ($bookings as $b) {
                if (in_array($b['approval_status'] ?? '', ['approved', 'pending'])) {
                    $confirmed_count++;
                }
            }

            if ($confirmed_count > 0) {
                set_error("Không thể hủy lịch khởi hành đã có " . $confirmed_count . " booking đã xác nhận. Vui lòng hủy các booking trước.");
                redirect('?act=admin&module=schedules');
                return;
            }
        }

        // Nếu đóng (closed) và có booking → Cảnh báo nhưng vẫn cho phép (để không nhận thêm booking mới)
        if ($status == 'closed' && $booked_count > 0) {
            // Vẫn cho phép đóng, nhưng sẽ có cảnh báo trong UI
        }

        if ($this->scheduleModel->updateStatus($id, $status)) {
            $status_names = [
                'open' => 'Mở bán',
                'closed' => 'Đóng bán',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy'
            ];

            $message = "Đã chuyển trạng thái sang: " . ($status_names[$status] ?? $status);
            if ($status == 'closed' && $booked_count > 0) {
                $message .= " (Lưu ý: Lịch này đang có " . $booked_count . " khách đã đặt)";
            }

            set_success($message);
        } else {
            set_error('Có lỗi xảy ra');
        }

        redirect('?act=admin&module=schedules');
    }

    /**
     * LUỒNG 2: PHÂN CÔNG GUIDE CHO LỊCH TRÌNH (TOUR-010)
     * Form phân công guide
     */
    public function assignGuideForm()
    {
        require_admin();

        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        // Get tour info for min_participants
        $tour = $this->tourModel->findById($schedule['tour_id']);
        if (!$tour) {
            set_error("Tour không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        // Kiểm tra điều kiện: booked >= min_participants
        $booked = $schedule['booked'] ?? 0;
        $min_participants = $tour['min_participants'] ?? 15;
        $can_assign = ($booked >= $min_participants);

        // Get all guides for dropdown
        $sql = "SELECT u.id, u.full_name, u.phone, u.email
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE r.name = 'guide' AND u.status = 'active'
                ORDER BY u.full_name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $page_title = 'Phân công Hướng dẫn viên';
        $content_file = 'app/views/admin/schedules/assign_guide.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * LUỒNG 2: PHÂN CÔNG GUIDE CHO LỊCH TRÌNH (TOUR-010)
     * Xử lý phân công guide
     */
    public function assignGuide()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                $schedule = $this->scheduleModel->findById($id);

                if (!$schedule) {
                    throw new Exception("Lịch khởi hành không tồn tại");
                }

                // Get tour info
                $tour = $this->tourModel->findById($schedule['tour_id']);
                if (!$tour) {
                    throw new Exception("Tour không tồn tại");
                }

                // Validation quan trọng: booked >= min_participants
                $booked = $schedule['booked'] ?? 0;
                $min_participants = $tour['min_participants'] ?? 15;

                if ($booked < $min_participants) {
                    throw new Exception("Tour này chưa đủ số người tối thiểu ($booked/$min_participants). Chỉ phân công guide khi đã đủ số người.");
                }

                // Validate guide
                $guide_id = !empty($_POST['guide_id']) ? (int) $_POST['guide_id'] : null;
                if (!$guide_id) {
                    throw new Exception("Vui lòng chọn hướng dẫn viên");
                }

                require_once MODELS_PATH . '/TourAssignment.php';
                $assignmentModel = new TourAssignment($this->pdo);

                // Validate guide is actually a guide
                if (!$assignmentModel->isGuide($guide_id)) {
                    throw new Exception("Người dùng này không phải là Hướng dẫn viên hoặc đã bị vô hiệu hóa");
                }

                // Check guide availability (optional - cảnh báo nhưng vẫn cho phép)
                $availability = $assignmentModel->checkGuideAvailability($guide_id, $schedule['start_date'], $schedule['end_date'], null, $id);
                if (!$availability['available']) {
                    $conflict = $availability['conflict'];
                    $conflictInfo = '';
                    if (isset($conflict['tour_name'])) {
                        $conflictInfo = $conflict['tour_name'] . " ({$conflict['start_date']} - {$conflict['end_date']})";
                    } else {
                        $conflictInfo = "Lịch khác ({$conflict['start_date']} - {$conflict['end_date']})";
                    }
                    // Cảnh báo nhưng vẫn cho phép lưu
                    set_error("Cảnh báo: HDV đã có lịch trùng: " . $conflictInfo . ". Bạn vẫn có thể tiếp tục.");
                }

                $old_guide_id = $schedule['guide_id'] ?? null;
                $guide_notes = !empty($_POST['guide_notes']) ? sanitize($_POST['guide_notes']) : null;

                // Update schedule
                $updateData = [
                    'guide_id' => $guide_id,
                    'guide_notes' => $guide_notes
                ];

                // Tự động set status = 'pending' nếu hiện tại là 'open' hoặc 'closed'
                $current_status = $schedule['status'];
                if (in_array($current_status, ['open', 'closed'])) {
                    $updateData['status'] = 'pending';
                }

                $this->scheduleModel->update($id, $updateData);

                // Lưu lịch sử thay đổi guide
                $this->scheduleModel->logGuideChange(
                    $id,
                    $old_guide_id,
                    $guide_id,
                    get_user_id(),
                    $old_guide_id ? 'Thay đổi HDV' : 'Gán HDV lần đầu',
                    $guide_notes
                );

                set_success("Phân công guide thành công! Tour đã sẵn sàng khởi hành (trạng thái: Pending).");
                redirect('?act=admin&module=schedules&action=show&id=' . $id);

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect('?act=admin&module=schedules&action=assignGuideForm&id=' . ($_POST['id'] ?? 0));
            }
        }
    }

    /**
     * LUỒNG 4: HỦY LỊCH TRÌNH (TOUR-013)
     * Form hủy schedule
     */
    public function cancelForm()
    {
        require_admin();

        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        // Validation: Chỉ cho phép hủy khi status != 'completed'
        if ($schedule['status'] === 'completed') {
            set_error("Không thể hủy lịch trình đã hoàn thành");
            redirect('?act=admin&module=schedules');
        }

        // Get bookings for this schedule
        // Bookings are linked to schedule by tour_id + start_date (exact match)
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new Booking($this->pdo);
        $bookings = $bookingModel->getAll([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date'],
            'exact_date' => true  // Exact match for start_date
        ], 1, 100);

        // Filter only active bookings (not cancelled/rejected)
        $active_bookings = array_filter($bookings, function ($b) {
            return !in_array($b['approval_status'] ?? '', ['cancelled', 'rejected']);
        });

        // Get other schedules of the same tour (for Option 2: chuyển schedule)
        $other_schedules = [];
        if (!empty($active_bookings)) {
            $result = $this->scheduleModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'status' => 'open,pending' // Multiple statuses
            ], 1, 100);
            $other_schedules = $result['data'] ?? [];

            // Exclude current schedule
            $other_schedules = array_filter($other_schedules, function ($s) use ($id) {
                return $s['id'] != $id;
            });
        }

        $page_title = 'Hủy Lịch Trình Tour';
        $content_file = 'app/views/admin/schedules/cancel.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    /**
     * LUỒNG 4: HỦY LỊCH TRÌNH (TOUR-013)
     * Xử lý hủy schedule với xử lý bookings
     */
    public function cancel()
    {
        require_admin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'] ?? 0;
                $schedule = $this->scheduleModel->findById($id);

                if (!$schedule) {
                    throw new Exception("Lịch khởi hành không tồn tại");
                }

                // Validation: Chỉ cho phép hủy khi status != 'completed'
                if ($schedule['status'] === 'completed') {
                    throw new Exception("Không thể hủy lịch trình đã hoàn thành");
                }

                $cancellation_reason = !empty($_POST['cancellation_reason']) ? sanitize($_POST['cancellation_reason']) : null;
                $action_type = $_POST['action_type'] ?? 'cancel_all'; // cancel_all, transfer, cancel_with_policy

                require_once MODELS_PATH . '/Booking.php';
                $bookingModel = new Booking($this->pdo);

                // Get bookings for this schedule
                // Bookings are linked to schedule by tour_id + start_date (exact match)
                $bookings = $bookingModel->getAll([
                    'tour_id' => $schedule['tour_id'],
                    'start_date' => $schedule['start_date'],
                    'exact_date' => true  // Exact match for start_date
                ], 1, 100);

                // Filter only active bookings
                $active_bookings = array_filter($bookings, function ($b) {
                    return !in_array($b['approval_status'] ?? '', ['cancelled', 'rejected']);
                });

                $this->pdo->beginTransaction();

                try {
                    // Option 1: Tự động hủy bookings & Hoàn tiền 100%
                    if ($action_type === 'cancel_all') {
                        foreach ($active_bookings as $booking) {
                            // Update booking: cancelled, hoàn tiền 100%
                            $total_participants = ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);
                            $paid_amount = (float) ($booking['paid_amount'] ?? 0);

                            $sql = "UPDATE bookings SET 
                                    approval_status = 'cancelled',
                                    cancellation_date = NOW(),
                                    cancellation_reason = :reason,
                                    cancellation_fee = 0,
                                    refund_amount = :refund,
                                    payment_status = CASE 
                                        WHEN :refund > 0 THEN 'refund_pending'
                                        ELSE payment_status
                                    END
                                    WHERE id = :id";

                            $reason = "Lịch trình tour bị hủy" . ($cancellation_reason ? ": " . $cancellation_reason : "");
                            $stmt = $this->pdo->prepare($sql);
                            $stmt->execute([
                                'reason' => $reason,
                                'refund' => $paid_amount,
                                'id' => $booking['id']
                            ]);

                            // Tạo refund record (nếu có bảng refunds)
                            if ($paid_amount > 0) {
                                try {
                                    $refundSql = "INSERT INTO refunds (booking_id, refund_amount, refund_reason, refund_status, created_at)
                                                  VALUES (:booking_id, :amount, :reason, 'pending', NOW())";
                                    $refundStmt = $this->pdo->prepare($refundSql);
                                    $refundStmt->execute([
                                        'booking_id' => $booking['id'],
                                        'amount' => $paid_amount,
                                        'reason' => "Tour bị hủy bởi công ty"
                                    ]);
                                } catch (Exception $e) {
                                    // Bảng refunds có thể chưa có, bỏ qua
                                }
                            }

                            // Trả lại quota
                            $this->scheduleModel->decrementBooked($schedule['id'], $total_participants);
                        }

                        // Option 2: Chuyển sang schedule khác
                    } elseif ($action_type === 'transfer') {
                        $new_schedule_id = (int) ($_POST['new_schedule_id'] ?? 0);
                        if (!$new_schedule_id) {
                            throw new Exception("Vui lòng chọn lịch trình mới");
                        }

                        $new_schedule = $this->scheduleModel->findById($new_schedule_id);
                        if (!$new_schedule) {
                            throw new Exception("Lịch trình mới không tồn tại");
                        }

                        // Kiểm tra quota của schedule mới
                        $total_participants_needed = 0;
                        foreach ($active_bookings as $booking) {
                            $total_participants_needed += ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);
                        }

                        $available_slots = ($new_schedule['quota'] ?? 0) - ($new_schedule['booked'] ?? 0);
                        if ($total_participants_needed > $available_slots) {
                            throw new Exception("Lịch trình mới không đủ chỗ. Cần $total_participants_needed chỗ, chỉ còn $available_slots chỗ.");
                        }

                        // Chuyển bookings sang schedule mới
                        foreach ($active_bookings as $booking) {
                            $total_participants = ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);

                            // Update tour_schedule_id
                            $updateSql = "UPDATE bookings SET tour_schedule_id = :new_schedule_id WHERE id = :id";
                            $updateStmt = $this->pdo->prepare($updateSql);
                            $updateStmt->execute([
                                'new_schedule_id' => $new_schedule_id,
                                'id' => $booking['id']
                            ]);

                            // Cập nhật quota
                            $this->scheduleModel->decrementBooked($schedule['id'], $total_participants);
                            $this->scheduleModel->incrementBooked($new_schedule_id, $total_participants);
                        }

                        // Option 3: Hủy bookings & Hoàn tiền theo chính sách hủy
                    } elseif ($action_type === 'cancel_with_policy') {
                        foreach ($active_bookings as $booking) {
                            // Sử dụng method cancel của Booking model (tính phí hủy)
                            $total_participants = ($booking['adult_count'] ?? 0) + ($booking['child_count'] ?? 0) + ($booking['infant_count'] ?? 0);

                            $reason = "Lịch trình tour bị hủy" . ($cancellation_reason ? ": " . $cancellation_reason : "");
                            $result = $bookingModel->cancel($booking['id'], $reason, get_user_id());

                            // Trả lại quota
                            $this->scheduleModel->decrementBooked($schedule['id'], $total_participants);
                        }
                    }

                    // Cập nhật trạng thái schedule
                    $this->scheduleModel->update($id, [
                        'status' => 'cancelled',
                        'booked' => 0 // Reset booked nếu đã hủy tất cả
                    ]);

                    $this->pdo->commit();

                    $message = "Đã hủy lịch trình thành công!";
                    if (!empty($active_bookings)) {
                        $message .= " Đã xử lý " . count($active_bookings) . " booking.";
                    }
                    set_success($message);
                    redirect('?act=admin&module=schedules');

                } catch (Exception $e) {
                    $this->pdo->rollBack();
                    throw $e;
                }

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect('?act=admin&module=schedules&action=cancelForm&id=' . ($_POST['id'] ?? 0));
            }
        }
    }
}
