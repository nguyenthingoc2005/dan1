<?php
require_once 'app/models/TourSchedule.php';
require_once 'app/models/Tour.php';

class TourScheduleController
{
    private $scheduleModel;
    private $tourModel;

    public function __construct($pdo)
    {
        $this->scheduleModel = new TourSchedule($pdo);
        $this->tourModel = new Tour($pdo);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $filters = [
            'tour_id' => $_GET['tour_id'] ?? '',
            'start_date' => $_GET['start_date'] ?? ''
        ];

        $result = $this->scheduleModel->getAll($filters, $page);
        $schedules = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $tours = $this->tourModel->getAll(['status' => 'active'])['data']; // For filter dropdown

        $page_title = 'Quản lý Lịch Khởi Hành';
        $content_file = 'app/views/admin/schedules/index.php';
        require_once 'app/views/layouts/admin_layout.php';
    }

    public function create()
    {
        $tours = $this->tourModel->getAll(['status' => 'active'])['data'];

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

                $data = [
                    'tour_id' => $tour_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $quota,
                    'adult_price' => !empty($_POST['adult_price']) ? $_POST['adult_price'] : $tour['adult_price'],
                    'child_price' => !empty($_POST['child_price']) ? $_POST['child_price'] : $tour['child_price'],
                    'infant_price' => !empty($_POST['infant_price']) ? $_POST['infant_price'] : $tour['infant_price']
                ];

                $this->scheduleModel->create($data);
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
        $id = $_GET['id'] ?? 0;
        $schedule = $this->scheduleModel->findById($id);

        if (!$schedule) {
            set_error("Lịch khởi hành không tồn tại");
            redirect('?act=admin&module=schedules');
        }

        $tours = $this->tourModel->getAll(['status' => 'active'])['data'];

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

                // Get tour info
                $tour = $this->tourModel->findById($tour_id);
                if (!$tour)
                    throw new Exception("Tour không tồn tại");

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

                $data = [
                    'tour_id' => $tour_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quota' => $_POST['quota'] ?? 20,
                    'adult_price' => $_POST['adult_price'],
                    'child_price' => $_POST['child_price'],
                    'infant_price' => $_POST['infant_price'],
                    'status' => $_POST['status']
                ];

                $this->scheduleModel->update($id, $data);
                set_success("Đã cập nhật lịch khởi hành!");
                redirect('?act=admin&module=schedules');

            } catch (Exception $e) {
                set_error($e->getMessage());
                redirect("?act=admin&module=schedules&action=edit&id=$id");
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
}
