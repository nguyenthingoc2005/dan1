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

        $tours = $this->tourModel->getAll(['status' => 'active'], 1, 1000)['data'];

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

                $duration = $tour['duration_days'] ?? 1;
                $end_date = date('Y-m-d', strtotime($start_date . " + $duration days"));

                // 1. Kiểm tra tour_type
                $tour_type = $tour['tour_type'] ?? 'public';

                if ($tour_type == 'custom') {
                    // Custom tour: Kiểm tra xem đã có schedule chưa
                    $existing = $this->scheduleModel->getAll(['tour_id' => $tour_id], 1, 1000);
                    if (!empty($existing)) {
                        throw new Exception("Tour tùy chỉnh (Custom) chỉ có thể có 1 lịch khởi hành. Vui lòng sử dụng lịch hiện có hoặc xóa lịch cũ.");
                    }
                }

                // 2. Kiểm tra quota không vượt max participants của tour
                $quota = $_POST['quota'] ?? 20;
                $max_participants = $tour['max_participants'] ?? 45;
                if ($quota > $max_participants) {
                    throw new Exception("Quota không được vượt quá số người tối đa của tour ($max_participants)");
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

                set_success("Đã tạo lịch khởi hành thành công!");
                redirect('?act=admin&module=schedules');

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

                // Validate quota không vượt max_participants
                $max_participants = $tour['max_participants'] ?? 45;
                if ($quota > $max_participants) {
                    throw new Exception("Số chỗ không được vượt quá số người tối đa của tour ($max_participants)");
                }

                // Calculate end_date
                $duration = $tour['duration_days'] ?? 1;
                $end_date = date('Y-m-d', strtotime($start_date . " + $duration days"));

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
        $bookings = $bookingModel->getAll([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date'],
            'exact_date' => true  // Exact match for start_date
        ], 1, 100);

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

        $allowed_statuses = ['open', 'closed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            set_error('Trạng thái không hợp lệ');
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
}
