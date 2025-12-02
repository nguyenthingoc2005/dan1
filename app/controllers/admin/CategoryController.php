<?php
/**
 * ==============================================================================
 * CATEGORY CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Routing mới: ?act=admin&module=categories&action=index
 * 
 * @version 1.1
 * @date 2024-12-02
 * ==============================================================================
 */

class CategoryController
{
    private $db;
    private $categoryModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Category.php';
        $this->categoryModel = new Category($pdo);
    }

    /**
     * Danh sách categories
     */
    public function index()
    {
        require_admin();

        $result = $this->categoryModel->getAll([], 1, 100);
        $categories = $result['data'];

        $page_title = 'Quản lý danh mục';
        $content_file = VIEWS_PATH . '/admin/categories/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo category
     */
    public function create()
    {
        require_admin();

        $page_title = 'Thêm danh mục mới';
        $content_file = VIEWS_PATH . '/admin/categories/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo category
     */
    public function store()
    {
        require_admin();

        try {
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên danh mục.");
            }

            $name = sanitize($_POST['name']);
            if ($this->categoryModel->findByName($name)) {
                throw new Exception("Tên danh mục đã tồn tại.");
            }

            $data = [
                'name' => $name,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'display_order' => isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->categoryModel->create($data)) {
                set_success("Tạo danh mục thành công!");
                redirect('?act=admin&module=categories');
            } else {
                throw new Exception("Không thể tạo danh mục.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=categories&action=create');
        }
    }

    /**
     * Form sửa category
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy danh mục.");
            redirect('?act=admin&module=categories');
            return;
        }

        $category = $this->categoryModel->findById((int) $_GET['id']);
        if (!$category) {
            set_error("Không tìm thấy danh mục.");
            redirect('?act=admin&module=categories');
            return;
        }

        $page_title = 'Sửa danh mục';
        $content_file = VIEWS_PATH . '/admin/categories/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update category
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy danh mục.");
            }

            $category_id = (int) $_POST['id'];
            $category = $this->categoryModel->findById($category_id);

            if (!$category) {
                throw new Exception("Không tìm thấy danh mục.");
            }

            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên danh mục.");
            }

            $name = sanitize($_POST['name']);
            $existing = $this->categoryModel->findByName($name);
            if ($existing && $existing['id'] != $category_id) {
                throw new Exception("Tên danh mục đã tồn tại.");
            }

            $data = [
                'name' => $name,
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'display_order' => isset($_POST['display_order']) ? (int) $_POST['display_order'] : 0,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($this->categoryModel->update($category_id, $data)) {
                set_success("Cập nhật thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=categories');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=categories&action=edit&id=' . ($category_id ?? 0));
        }
    }

    /**
     * Xóa category (soft delete)
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy danh mục.");
            }

            $category_id = (int) $_GET['id'];

            if ($this->categoryModel->delete($category_id)) {
                set_success("Đã vô hiệu hóa danh mục.");
            } else {
                throw new Exception("Không thể xóa danh mục.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=categories');
    }
}
