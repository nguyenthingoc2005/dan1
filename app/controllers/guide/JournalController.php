<?php
namespace Guide;

/**
 * ==============================================================================
 * JOURNAL CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý nhật ký tour cho Guide
 * Sử dụng bảng: journals, journal_images
 * 
 * @version 2.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class JournalController
{
    private $db;
    private $journalModel;
    private $scheduleModel;
    private $bookingModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Journal.php';
        require_once MODELS_PATH . '/TourSchedule.php';
        require_once MODELS_PATH . '/Booking.php';
        $this->journalModel = new \Journal($pdo);
        $this->scheduleModel = new \TourSchedule($pdo);
        $this->bookingModel = new \Booking($pdo);
    }

    /**
     * Danh sách nhật ký của guide
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        // Filters
        $filters = ['guide_id' => $user_id];
        if (!empty($_GET['schedule_id'])) {
            $filters['tour_schedule_id'] = (int) $_GET['schedule_id'];
        }
        if (!empty($_GET['booking_id'])) {
            $filters['booking_id'] = (int) $_GET['booking_id'];
        }
        if (!empty($_GET['journal_date'])) {
            $filters['journal_date'] = sanitize($_GET['journal_date']);
        }

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10;
        $journals = $this->journalModel->getAll($filters, $page, $limit);

        // Get total count for pagination
        $all_journals = $this->journalModel->getAll($filters, 1, 0); // limit = 0 means no limit
        $total = count($all_journals);
        $total_pages = ceil($total / $limit);
        $current_page = $page;

        // Get images for each journal
        foreach ($journals as &$journal) {
            $journal['images'] = $this->journalModel->getImages($journal['id']);
        }

        $page_title = 'Nhật ký Tour của tôi';
        $content_file = VIEWS_PATH . '/guide/journals/index.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Form tạo nhật ký mới
     */
    public function create()
    {
        require_guide();
        $user_id = get_user_id();

        // Get schedule_id from query (if coming from tour detail page)
        $schedule_id = $_GET['schedule_id'] ?? null;

        // Get available schedules (only assigned to this guide, and started or completed)
        $filters = [
            'guide_id' => $user_id,
            'start_date' => date('Y-m-d') // Only tours that have started
        ];
        $schedules = $this->scheduleModel->getAll($filters, 1, 100)['data'];

        // Filter out cancelled tours
        $schedules = array_filter($schedules, function($s) {
            return $s['status'] != 'cancelled';
        });

        // If schedule_id provided, validate it belongs to this guide
        $selected_schedule = null;
        if ($schedule_id) {
            foreach ($schedules as $s) {
                if ($s['id'] == $schedule_id) {
                    $selected_schedule = $s;
                    break;
                }
            }
            if (!$selected_schedule) {
                set_error("Bạn không được phân công tour này.");
                redirect('?act=guide-journals');
                return;
            }
        }

        $page_title = 'Viết Nhật ký Tour';
        $content_file = VIEWS_PATH . '/guide/journals/create.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Xử lý lưu nhật ký
     */
    public function store()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            // Validation
            if (empty($_POST['tour_schedule_id'])) {
                throw new \Exception("Vui lòng chọn tour.");
            }
            if (empty($_POST['journal_date'])) {
                throw new \Exception("Vui lòng chọn ngày viết nhật ký.");
            }
            if (empty($_POST['title'])) {
                throw new \Exception("Vui lòng nhập tiêu đề.");
            }
            if (empty($_POST['content'])) {
                throw new \Exception("Vui lòng nhập nội dung nhật ký.");
            }

            $schedule_id = (int) $_POST['tour_schedule_id'];

            // Verify guide is assigned to this schedule
            $schedule = $this->scheduleModel->getById($schedule_id);
            if (!$schedule || $schedule['guide_id'] != $user_id) {
                throw new \Exception("Bạn không được phân công tour này.");
            }

            // Get first paid booking from this schedule (for database constraint)
            // Journal is for the tour, but we need a booking_id for the database
            $bookings = $this->bookingModel->getAll([
                'tour_id' => $schedule['tour_id'],
                'start_date' => $schedule['start_date'],
                'exact_date' => true,
                'status' => 'paid'
            ], 1, 1);

            if (empty($bookings)) {
                throw new \Exception("Tour này chưa có booking nào đã thanh toán.");
            }

            $booking_id = $bookings[0]['id'];

            // Handle image uploads
            $uploaded_images = [];
            if (!empty($_FILES['images']['name'][0])) {
                $uploaded_images = $this->handleImageUploads($_FILES['images']);
            }

            // Prepare data (sử dụng tour_schedule_id làm chính)
            $data = [
                'tour_schedule_id' => $schedule_id,
                'booking_id' => $booking_id, // Giữ lại để backward compatible
                'guide_id' => $user_id,
                'journal_date' => sanitize($_POST['journal_date']),
                'day_number' => !empty($_POST['day_number']) ? (int) $_POST['day_number'] : null,
                'title' => sanitize($_POST['title']),
                'content' => $_POST['content'], // Allow HTML
                'weather' => !empty($_POST['weather']) ? sanitize($_POST['weather']) : null,
                'highlights' => !empty($_POST['highlights']) ? $_POST['highlights'] : null,
                'issues' => !empty($_POST['issues']) ? $_POST['issues'] : null
            ];

            // Create journal
            $journal_id = $this->journalModel->create($data);

            if (!$journal_id) {
                throw new \Exception("Không thể lưu nhật ký.");
            }

            // Add images
            foreach ($uploaded_images as $index => $image_path) {
                $this->journalModel->addImage($journal_id, $image_path, null, $index);
            }

            set_success("Đã lưu nhật ký tour!");
            redirect('?act=guide-journals&action=show&id=' . $journal_id);

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-journals&action=create' . ($schedule_id ? '&schedule_id=' . $schedule_id : ''));
        }
    }

    /**
     * Xem chi tiết nhật ký
     */
    public function show()
    {
        require_guide();
        $user_id = get_user_id();

        if (empty($_GET['id'])) {
            redirect('?act=guide-journals');
            return;
        }

        $id = (int) $_GET['id'];
        $journal = $this->journalModel->getById($id);

        if (!$journal) {
            set_error("Nhật ký không tồn tại.");
            redirect('?act=guide-journals');
            return;
        }

        // Verify ownership
        if ($journal['guide_id'] != $user_id) {
            set_error("Bạn không có quyền xem nhật ký này.");
            redirect('?act=guide-journals');
            return;
        }

        // Get images
        $images = $this->journalModel->getImages($id);

        $page_title = 'Chi tiết Nhật ký: ' . htmlspecialchars($journal['title']);
        $content_file = VIEWS_PATH . '/guide/journals/show.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Form sửa nhật ký
     */
    public function edit()
    {
        require_guide();
        $user_id = get_user_id();

        if (empty($_GET['id'])) {
            redirect('?act=guide-journals');
            return;
        }

        $id = (int) $_GET['id'];
        $journal = $this->journalModel->getById($id);

        if (!$journal) {
            set_error("Nhật ký không tồn tại.");
            redirect('?act=guide-journals');
            return;
        }

        // Verify ownership
        if ($journal['guide_id'] != $user_id) {
            set_error("Bạn không có quyền sửa nhật ký này.");
            redirect('?act=guide-journals');
            return;
        }

        // Get images
        $images = $this->journalModel->getImages($id);

        // Get schedule info (from booking)
        $booking = $this->bookingModel->getById($journal['booking_id']);
        $schedule = null;
        if ($booking) {
            $schedule = $this->scheduleModel->getByTourAndStartDate($booking['tour_id'], $booking['start_date']);
        }

        $page_title = 'Sửa Nhật ký: ' . htmlspecialchars($journal['title']);
        $content_file = VIEWS_PATH . '/guide/journals/edit.php';
        require VIEWS_PATH . '/layouts/guide_layout.php';
    }

    /**
     * Xử lý cập nhật nhật ký
     */
    public function update()
    {
        require_guide();
        require_csrf_token();
        $user_id = get_user_id();

        try {
            if (empty($_POST['id'])) {
                throw new \Exception("ID nhật ký không hợp lệ.");
            }

            $id = (int) $_POST['id'];
            $journal = $this->journalModel->getById($id);

            if (!$journal) {
                throw new \Exception("Nhật ký không tồn tại.");
            }

            // Verify ownership
            if ($journal['guide_id'] != $user_id) {
                throw new \Exception("Bạn không có quyền sửa nhật ký này.");
            }

            // Validation
            if (empty($_POST['journal_date'])) {
                throw new \Exception("Vui lòng chọn ngày viết nhật ký.");
            }
            if (empty($_POST['title'])) {
                throw new \Exception("Vui lòng nhập tiêu đề.");
            }
            if (empty($_POST['content'])) {
                throw new \Exception("Vui lòng nhập nội dung nhật ký.");
            }

            // Handle existing images (keep or delete)
            if (!empty($_POST['keep_images']) && is_array($_POST['keep_images'])) {
                // Get all current images
                $current_images = $this->journalModel->getImages($id);
                $keep_image_ids = array_map('intval', $_POST['keep_images']);
                
                // Delete images not in keep list
                foreach ($current_images as $img) {
                    if (!in_array($img['id'], $keep_image_ids)) {
                        $this->journalModel->deleteImage($img['id']);
                    }
                }
            } else {
                // Delete all existing images if none selected
                $this->journalModel->deleteAllImages($id);
            }

            // Add new images
            if (!empty($_FILES['images']['name'][0])) {
                $uploaded_images = $this->handleImageUploads($_FILES['images']);
                $current_count = count($this->journalModel->getImages($id));
                foreach ($uploaded_images as $index => $image_path) {
                    $this->journalModel->addImage($id, $image_path, null, $current_count + $index);
                }
            }

            // Update journal
            $data = [
                'guide_id' => $user_id,
                'journal_date' => sanitize($_POST['journal_date']),
                'day_number' => !empty($_POST['day_number']) ? (int) $_POST['day_number'] : null,
                'title' => sanitize($_POST['title']),
                'content' => $_POST['content'],
                'weather' => !empty($_POST['weather']) ? sanitize($_POST['weather']) : null,
                'highlights' => !empty($_POST['highlights']) ? $_POST['highlights'] : null,
                'issues' => !empty($_POST['issues']) ? $_POST['issues'] : null
            ];

            $success = $this->journalModel->update($id, $data);

            if ($success) {
                set_success("Đã cập nhật nhật ký!");
                redirect('?act=guide-journals&action=show&id=' . $id);
            } else {
                throw new \Exception("Không thể cập nhật nhật ký.");
            }

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-journals&action=edit&id=' . ($_POST['id'] ?? ''));
        }
    }

    /**
     * Xóa nhật ký
     */
    public function delete()
    {
        require_guide();
        $user_id = get_user_id();

        if (empty($_GET['id'])) {
            redirect('?act=guide-journals');
            return;
        }

        try {
            $id = (int) $_GET['id'];
            $journal = $this->journalModel->getById($id);

            if (!$journal) {
                throw new \Exception("Nhật ký không tồn tại.");
            }

            // Verify ownership
            if ($journal['guide_id'] != $user_id) {
                throw new \Exception("Bạn không có quyền xóa nhật ký này.");
            }

            // Delete journal (images will be auto-deleted by CASCADE)
            $this->journalModel->delete($id, $user_id);

            set_success("Đã xóa nhật ký!");
            redirect('?act=guide-journals');

        } catch (\Exception $e) {
            set_error($e->getMessage());
            redirect('?act=guide-journals');
        }
    }

    /**
     * Handle image uploads
     * @param array $files $_FILES['images']
     * @return array Array of image paths
     */
    private function handleImageUploads($files)
    {
        $uploaded_images = [];
        $upload_dir = PUBLIC_PATH . '/uploads/journals/';

        // Create directory if not exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] != UPLOAD_ERR_OK) {
                continue;
            }

            // Validate file type
            $file_type = $files['type'][$i];
            if (!in_array($file_type, $allowed_types)) {
                continue;
            }

            // Validate file size
            if ($files['size'][$i] > $max_size) {
                continue;
            }

            // Generate unique filename
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = uniqid('journal_') . '_' . time() . '.' . $extension;
            $file_path = $upload_dir . $filename;

            // Move uploaded file
            if (move_uploaded_file($files['tmp_name'][$i], $file_path)) {
                $uploaded_images[] = 'uploads/journals/' . $filename;
            }
        }

        return $uploaded_images;
    }
}
