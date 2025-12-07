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
     * Admin chỉ xem được nhật ký từ tour đã hoàn thành hoặc đang diễn ra
     */
    public function index()
    {
        require_admin();

        // Get journals - chỉ lấy từ tour completed hoặc đang diễn ra
        if (!isset($this->journalModel)) {
            require_once MODELS_PATH . '/Journal.php';
            $this->journalModel = new Journal($this->db);
        }

        // Filter: chỉ lấy nhật ký từ tour completed hoặc ongoing (closed + date trong khoảng)
        $journals = $this->journalModel->getAll([
            'only_ongoing_or_completed' => true
        ], 1, 50);

        $page_title = 'Nhật ký Tour';
        $content_file = VIEWS_PATH . '/admin/journals/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xem chi tiết nhật ký
     * Admin chỉ xem được nhật ký từ tour đã hoàn thành hoặc đang diễn ra
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

        // Kiểm tra tour status - chỉ cho xem nếu tour completed hoặc đang diễn ra
        $today = date('Y-m-d');
        $schedule_status = $journal['schedule_status'] ?? null;
        $start_date = $journal['schedule_start_date'] ?? null;
        $end_date = $journal['schedule_end_date'] ?? null;

        $can_view = false;
        if ($schedule_status === 'completed') {
            $can_view = true;
        } elseif ($schedule_status === 'closed' && $start_date && $end_date) {
            // Tour đang diễn ra: status = closed và ngày hiện tại trong khoảng start_date - end_date
            if ($today >= $start_date && $today <= $end_date) {
                $can_view = true;
            }
        }

        if (!$can_view) {
            set_error("Chỉ có thể xem nhật ký của tour đã hoàn thành hoặc đang diễn ra.");
            redirect('?act=admin&module=journals');
        }

        $page_title = 'Chi tiết Nhật ký: ' . $journal['title'];
        $content_file = VIEWS_PATH . '/admin/journals/show.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }
}
