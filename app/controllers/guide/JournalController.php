<?php
namespace Guide;

/**
 * ==============================================================================
 * JOURNAL CONTROLLER (GUIDE)
 * ==============================================================================
 * 
 * Quản lý nhật ký tour cho Guide
 * Routing: ?act=guide-journals&action=index
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class JournalController
{
    private $db;
    private $journalModel;
    private $scheduleModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Journal.php';
        require_once MODELS_PATH . '/TourSchedule.php';
        $this->journalModel = new \Journal($pdo);
        $this->scheduleModel = new \TourSchedule($pdo);
    }

    /**
     * Danh sách nhật ký của guide
     */
    public function index()
    {
        require_guide();
        $user_id = get_user_id();

        // Filters
        $filters = ['author_id' => $user_id];
        if (!empty($_GET['status'])) {
            $filters['status'] = sanitize($_GET['status']);
        }
        if (!empty($_GET['schedule_id'])) {
            $filters['tour_schedule_id'] = (int) $_GET['schedule_id'];
        }
        if (!empty($_GET['tour_schedule_id'])) {
            $filters['tour_schedule_id'] = (int) $_GET['tour_schedule_id'];
        }

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10;
        $journals = $this->journalModel->getAll($filters, $page, $limit);

        // Get total count for pagination
        $count_filters = $filters;
        $all_journals = $this->journalModel->getAll($count_filters, 1, 0); // limit = 0 means no limit
        $total = count($all_journals);
        $total_pages = ceil($total / $limit);
        $current_page = $page;

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
        $user_id = get_user_id();

        try {
            // Validation
            if (empty($_POST['tour_schedule_id'])) {
                throw new \Exception("Vui lòng chọn tour.");
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

            // Handle image uploads
            $images = [];
            if (!empty($_FILES['images']['name'][0])) {
                $images = $this->handleImageUploads($_FILES['images']);
            }

            // Prepare data
            $data = [
                'tour_schedule_id' => $schedule_id,
                'author_id' => $user_id,
                'title' => sanitize($_POST['title']),
                'content' => $_POST['content'], // Allow HTML
                'images' => $images,
                'status' => $_POST['status'] ?? 'draft'
            ];

            // Create journal
            $journal_id = $this->journalModel->create($data);

            if ($journal_id) {
                set_success("Đã lưu nhật ký tour!");
                redirect('?act=guide-journals&action=show&id=' . $journal_id);
            } else {
                throw new \Exception("Không thể lưu nhật ký.");
            }

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
        if ($journal['author_id'] != $user_id) {
            set_error("Bạn không có quyền xem nhật ký này.");
            redirect('?act=guide-journals');
            return;
        }

        // Parse images
        $images = [];
        if (!empty($journal['images'])) {
            $decoded = json_decode($journal['images'], true);
            $images = $decoded ?: [];
        }

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
        if ($journal['author_id'] != $user_id) {
            set_error("Bạn không có quyền sửa nhật ký này.");
            redirect('?act=guide-journals');
            return;
        }

        // Only allow editing draft journals
        if ($journal['status'] != 'draft') {
            set_error("Chỉ có thể sửa nhật ký ở trạng thái Nháp.");
            redirect('?act=guide-journals&action=show&id=' . $id);
            return;
        }

        // Parse images
        $images = [];
        if (!empty($journal['images'])) {
            $decoded = json_decode($journal['images'], true);
            $images = $decoded ?: [];
        }

        // Get schedule info
        $schedule = $this->scheduleModel->getById($journal['tour_schedule_id']);

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
            if ($journal['author_id'] != $user_id) {
                throw new \Exception("Bạn không có quyền sửa nhật ký này.");
            }

            // Only allow editing draft journals
            if ($journal['status'] != 'draft') {
                throw new \Exception("Chỉ có thể sửa nhật ký ở trạng thái Nháp.");
            }

            // Validation
            if (empty($_POST['title'])) {
                throw new \Exception("Vui lòng nhập tiêu đề.");
            }
            if (empty($_POST['content'])) {
                throw new \Exception("Vui lòng nhập nội dung nhật ký.");
            }

            // Handle image uploads
            $existing_images = [];
            if (!empty($journal['images'])) {
                $decoded = json_decode($journal['images'], true);
                $existing_images = $decoded ?: [];
            }

            // Keep existing images if not deleted
            if (!empty($_POST['existing_images'])) {
                $keep_images = is_array($_POST['existing_images']) 
                    ? $_POST['existing_images'] 
                    : [$_POST['existing_images']];
                $existing_images = array_intersect($existing_images, $keep_images);
            } else {
                $existing_images = [];
            }

            // Add new images
            $new_images = [];
            if (!empty($_FILES['images']['name'][0])) {
                $new_images = $this->handleImageUploads($_FILES['images']);
            }

            $all_images = array_merge($existing_images, $new_images);

            // Update journal
            $sql = "UPDATE tour_journals 
                    SET title = :title, content = :content, images = :images, 
                        status = :status, updated_at = NOW()
                    WHERE id = :id AND author_id = :author_id AND status = 'draft'";

            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute([
                'id' => $id,
                'author_id' => $user_id,
                'title' => sanitize($_POST['title']),
                'content' => $_POST['content'],
                'images' => !empty($all_images) ? json_encode($all_images) : null,
                'status' => $_POST['status'] ?? 'draft'
            ]);

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
     * Xóa nhật ký (chỉ draft)
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
            if ($journal['author_id'] != $user_id) {
                throw new \Exception("Bạn không có quyền xóa nhật ký này.");
            }

            // Only allow deleting draft journals
            if ($journal['status'] != 'draft') {
                throw new \Exception("Chỉ có thể xóa nhật ký ở trạng thái Nháp.");
            }

            // Delete images
            if (!empty($journal['images'])) {
                $images = json_decode($journal['images'], true);
                if ($images) {
                    foreach ($images as $image) {
                        $file_path = PUBLIC_PATH . '/' . $image;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }

            // Delete journal
            $sql = "DELETE FROM tour_journals WHERE id = :id AND author_id = :author_id AND status = 'draft'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id, 'author_id' => $user_id]);

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

