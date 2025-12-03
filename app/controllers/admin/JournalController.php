<?php
/**
 * ==============================================================================
 * JOURNAL CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý nhật ký tour (Tour Journals)
 * Routing: ?act=admin&module=journals&action=index
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class JournalController
{
    private $db;
    private $journalModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        // Check if Journal model exists, if not create it
        if (file_exists(MODELS_PATH . '/Journal.php')) {
            require_once MODELS_PATH . '/Journal.php';
            $this->journalModel = new Journal($pdo);
        }
    }

    /**
     * Danh sách nhật ký
     */
    public function index()
    {
        require_admin();

        // Get journals
        // Need JournalModel->getAll()
        // For now, mock data or empty
        $journals = [];
        if (isset($this->journalModel)) {
            $journals = $this->journalModel->getAll();
        } else {
            // Create model if not exists
            require_once MODELS_PATH . '/Journal.php';
            $this->journalModel = new Journal($this->db);
            $journals = $this->journalModel->getAll();
        }

        $page_title = 'Nhật ký Tour';
        $content_file = VIEWS_PATH . '/admin/journals/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo nhật ký mới
     */
    public function create()
    {
        require_admin();

        // Get completed/ongoing schedules to write journal for
        require_once MODELS_PATH . '/TourSchedule.php';
        $scheduleModel = new TourSchedule($this->db);
        $schedules = $scheduleModel->getAll(['status' => 'completed'], 1, 100); // Only completed tours

        $page_title = 'Viết Nhật ký Tour';
        $content_file = VIEWS_PATH . '/admin/journals/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý lưu nhật ký
     */
    public function store()
    {
        require_admin();

        try {
            if (empty($_POST['schedule_id']) || empty($_POST['content'])) {
                throw new Exception("Vui lòng chọn tour và nhập nội dung.");
            }

            $data = [
                'tour_schedule_id' => (int) $_POST['schedule_id'],
                'title' => sanitize($_POST['title']),
                'content' => $_POST['content'], // Allow HTML? Maybe sanitize basic tags
                'author_id' => $_SESSION['user_id'] ?? 1,
                'status' => 'published'
            ];

            // Handle file upload (images)
            // ...

            if (!isset($this->journalModel)) {
                require_once MODELS_PATH . '/Journal.php';
                $this->journalModel = new Journal($this->db);
            }

            if ($this->journalModel->create($data)) {
                set_success("Đã lưu nhật ký tour!");
                redirect('?act=admin&module=journals');
            } else {
                throw new Exception("Không thể lưu nhật ký.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=journals&action=create');
        }
    }

    /**
     * Xem chi tiết nhật ký
     */
    public function show()
    {
        require_admin();

        if (empty($_GET['id'])) {
            redirect('?act=admin&module=journals');
        }

        $id = (int) $_GET['id'];

        if (!isset($this->journalModel)) {
            require_once MODELS_PATH . '/Journal.php';
            $this->journalModel = new Journal($this->db);
        }

        $journal = $this->journalModel->getById($id);

        if (!$journal) {
            set_error("Nhật ký không tồn tại.");
            redirect('?act=admin&module=journals');
        }

        $page_title = 'Chi tiết Nhật ký: ' . $journal['title'];
        $content_file = VIEWS_PATH . '/admin/journals/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }
}
