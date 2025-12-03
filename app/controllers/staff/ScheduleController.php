<?php
namespace Staff;

/**
 * ==============================================================================
 * SCHEDULE CONTROLLER (STAFF) - READ ONLY
 * ==============================================================================
 * 
 * Staff xem lịch tour để tư vấn khách hàng
 * 
 * Routing: ?act=staff-schedules&action=index
 * 
 * Key Features:
 * - Xem TẤT CẢ schedules (không filter ownership)
 * - Filter theo tour, dates, status, category
 * - Xem chi tiết schedule (read-only)
 * - Link tạo booking với pre-filled data
 * 
 * Staff KHÔNG CÓ quyền:
 * - Create/Edit/Delete schedule
 * - Change status
 * - Assign guide
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ScheduleController
{
    private $scheduleModel;
    private $tourModel;
    private $categoryModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Tour.php';
        require_once MODELS_PATH . '/Category.php';

        $this->scheduleModel = new \TourSchedule($pdo);
        $this->tourModel = new \Tour($pdo);
        $this->categoryModel = new \Category($pdo);
    }

    /**
     * Danh sách lịch tour (để tư vấn khách hàng)
     */
    public function index()
    {
        // Filters
        $filters = [];
        
        if (!empty($_GET['tour_id'])) {
            $filters['tour_id'] = (int) $_GET['tour_id'];
        }
        
        if (!empty($_GET['start_date'])) {
            $filters['start_date'] = sanitize($_GET['start_date']);
        }
        
        if (!empty($_GET['end_date'])) {
            $filters['end_date'] = sanitize($_GET['end_date']);
        }
        
        if (!empty($_GET['status'])) {
            $filters['status'] = sanitize($_GET['status']);
        } else {
            // Mặc định chỉ hiển thị open và closed (không hiển thị cancelled)
            $filters['status'] = 'open,closed,completed';
        }
        
        if (!empty($_GET['category_id'])) {
            $filters['category_id'] = (int) $_GET['category_id'];
        }
        
        // Filter "Còn chỗ" - chỉ hiển thị schedules có available > 0
        $only_available = !empty($_GET['only_available']) && $_GET['only_available'] == '1';
        
        // Filter "Sắp hết chỗ" - chỉ hiển thị schedules có available < 10% quota
        $almost_full = !empty($_GET['almost_full']) && $_GET['almost_full'] == '1';

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->scheduleModel->getAll($filters, $page, 20);
        
        $schedules = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Apply additional filters (only_available, almost_full)
        if ($only_available || $almost_full) {
            $filtered_schedules = [];
            foreach ($schedules as $s) {
                $available = max(0, ($s['quota'] ?? 0) - ($s['booked'] ?? 0));
                $fill_rate = ($s['quota'] ?? 0) > 0 ? (($s['booked'] ?? 0) / ($s['quota'] ?? 1)) * 100 : 0;
                
                if ($only_available && $available <= 0) {
                    continue;
                }
                
                if ($almost_full && ($available >= ($s['quota'] ?? 0) * 0.1 || $fill_rate < 90)) {
                    continue;
                }
                
                $filtered_schedules[] = $s;
            }
            $schedules = $filtered_schedules;
            $total = count($filtered_schedules);
        }

        // Calculate stats
        $stats = [
            'open' => 0,
            'almost_full' => 0,
            'full' => 0,
            'closed' => 0
        ];
        
        foreach ($schedules as $s) {
            $available = max(0, ($s['quota'] ?? 0) - ($s['booked'] ?? 0));
            $fill_rate = ($s['quota'] ?? 0) > 0 ? (($s['booked'] ?? 0) / ($s['quota'] ?? 1)) * 100 : 0;
            
            if ($s['status'] == 'open') {
                $stats['open']++;
                if ($available <= 0) {
                    $stats['full']++;
                } elseif ($fill_rate >= 90) {
                    $stats['almost_full']++;
                }
            } elseif ($s['status'] == 'closed') {
                $stats['closed']++;
            }
        }

        // Get dropdown data
        $tours = $this->tourModel->getAll(['status' => 'active', 'approval_status' => 'approved'], 1, 1000)['data'] ?? [];
        $categories = $this->categoryModel->getForDropdown();

        // Pass filters to view
        $view_filters = [
            'tour_id' => $filters['tour_id'] ?? '',
            'start_date' => $filters['start_date'] ?? '',
            'end_date' => $filters['end_date'] ?? '',
            'status' => $_GET['status'] ?? '',
            'category_id' => $filters['category_id'] ?? ''
        ];

        $page_title = 'Lịch Tour Khởi Hành';
        $content_file = VIEWS_PATH . '/staff/schedules/index.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }

    /**
     * Xem chi tiết schedule (read-only)
     */
    public function show()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        
        if (!$id) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=staff-schedules');
            return;
        }

        // Get schedule with tour info
        $schedule = $this->scheduleModel->getById($id);
        
        if (!$schedule) {
            set_error("Không tìm thấy lịch tour.");
            redirect('?act=staff-schedules');
            return;
        }

        // Get tour details
        $tour = $this->tourModel->findById($schedule['tour_id']);
        
        if (!$tour) {
            set_error("Không tìm thấy tour.");
            redirect('?act=staff-schedules');
            return;
        }

        // Get bookings for this schedule
        require_once MODELS_PATH . '/Booking.php';
        $bookingModel = new \Booking($this->pdo);
        $bookings = $bookingModel->getAll([
            'tour_id' => $schedule['tour_id'],
            'start_date' => $schedule['start_date'],
            'exact_date' => true
        ], 1, 100)['data'] ?? [];

        $page_title = 'Chi tiết Lịch Tour: ' . htmlspecialchars($schedule['tour_name']);
        $content_file = VIEWS_PATH . '/staff/schedules/show.php';
        require VIEWS_PATH . '/layouts/staff_layout.php';
    }
}

