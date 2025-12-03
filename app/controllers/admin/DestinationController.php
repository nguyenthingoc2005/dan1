<?php
/**
 * ==============================================================================
 * DESTINATION CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý địa điểm du lịch & Hình ảnh
 * Routing: ?act=admin&module=destinations&action=index
 * 
 * Features:
 * - CRUD Destinations
 * - Upload multiple images
 * - Manage gallery (Delete, Set Primary)
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class DestinationController
{
    private $db;
    private $destinationModel;
    private $categoryModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/Destination.php';
        require_once MODELS_PATH . '/Category.php';

        $this->destinationModel = new Destination($pdo);
        $this->categoryModel = new Category($pdo);
    }

    /**
     * Danh sách destinations
     */
    public function index()
    {
        require_admin();

        // Filters
        $filters = [];
        if (!empty($_GET['category_id']))
            $filters['category_id'] = (int) $_GET['category_id'];
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->destinationModel->getAll($filters, $page, 10); // 10 items per page for better grid view

        $destinations = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        // Dropdown data
        $categories = $this->categoryModel->getForDropdown();

        $page_title = 'Quản lý địa điểm';
        $content_file = VIEWS_PATH . '/admin/destinations/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo destination
     */
    public function create()
    {
        require_admin();

        $categories = $this->categoryModel->getForDropdown();

        $page_title = 'Thêm địa điểm mới';
        $content_file = VIEWS_PATH . '/admin/destinations/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo destination + upload ảnh
     */
    public function store()
    {
        require_admin();

        try {
            if (empty($_POST['name'])) {
                throw new Exception("Vui lòng nhập tên địa điểm.");
            }

            // Validate category_id if provided
            if (!empty($_POST['category_id'])) {
                $category = $this->categoryModel->findById((int) $_POST['category_id']);
                if (!$category) {
                    throw new Exception("Danh mục không tồn tại trong hệ thống.");
                }
                if ($category['status'] != 'active') {
                    throw new Exception("Danh mục không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // 1. Create Destination
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $destination_id = $this->destinationModel->create($data);

            if (!$destination_id) {
                throw new Exception("Không thể tạo địa điểm.");
            }

            // 2. Handle Image Uploads
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($destination_id, $_FILES['images']);
            }

            set_success("Tạo địa điểm thành công!");
            redirect('?act=admin&module=destinations');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=destinations&action=create');
        }
    }

    /**
     * Form sửa destination
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy địa điểm.");
            redirect('?act=admin&module=destinations');
            return;
        }

        $id = (int) $_GET['id'];
        $destination = $this->destinationModel->findById($id);

        if (!$destination) {
            set_error("Không tìm thấy địa điểm.");
            redirect('?act=admin&module=destinations');
            return;
        }

        $images = $this->destinationModel->getImages($id);
        $categories = $this->categoryModel->getForDropdown();

        $page_title = 'Sửa địa điểm';
        $content_file = VIEWS_PATH . '/admin/destinations/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update destination
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id']))
                throw new Exception("ID không hợp lệ.");

            $id = (int) $_POST['id'];

            // Validate category_id if provided
            if (!empty($_POST['category_id'])) {
                $category = $this->categoryModel->findById((int) $_POST['category_id']);
                if (!$category) {
                    throw new Exception("Danh mục không tồn tại trong hệ thống.");
                }
                if ($category['status'] != 'active') {
                    throw new Exception("Danh mục không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // Validate category_id if provided
            if (!empty($_POST['category_id'])) {
                $category = $this->categoryModel->findById((int) $_POST['category_id']);
                if (!$category) {
                    throw new Exception("Danh mục không tồn tại trong hệ thống.");
                }
                if ($category['status'] != 'active') {
                    throw new Exception("Danh mục không khả dụng (đã bị vô hiệu hóa).");
                }
            }

            // 1. Update Info
            $data = [
                'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
                'name' => sanitize($_POST['name']),
                'description' => isset($_POST['description']) ? sanitize($_POST['description']) : null,
                'locations' => isset($_POST['locations']) ? sanitize($_POST['locations']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            $this->destinationModel->update($id, $data);

            // 2. Handle New Images
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($id, $_FILES['images']);
            }

            set_success("Cập nhật thành công!");
            redirect('?act=admin&module=destinations');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=destinations&action=edit&id=' . ($_POST['id'] ?? 0));
        }
    }

    /**
     * Xóa destination
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id'])) {
                throw new Exception("Không tìm thấy địa điểm.");
            }

            $id = (int) $_GET['id'];

            // Delete sẽ tự động check usage trong Model
            if ($this->destinationModel->delete($id)) {
                set_success("Đã vô hiệu hóa địa điểm.");
            } else {
                throw new Exception("Không thể xóa địa điểm.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=destinations');
    }

    /**
     * AJAX: Set primary image
     */
    public function setPrimaryImage()
    {
        require_admin();

        header('Content-Type: application/json');

        try {
            if (empty($_POST['image_id']) || empty($_POST['destination_id'])) {
                throw new Exception("Thiếu thông tin ảnh hoặc địa điểm.");
            }

            $image_id = (int) $_POST['image_id'];
            $destination_id = (int) $_POST['destination_id'];

            // Validate destination exists
            $destination = $this->destinationModel->findById($destination_id);
            if (!$destination) {
                throw new Exception("Địa điểm không tồn tại.");
            }

            // Validate image belongs to destination
            $images = $this->destinationModel->getImages($destination_id);
            $image_exists = false;
            foreach ($images as $img) {
                if ($img['id'] == $image_id) {
                    $image_exists = true;
                    break;
                }
            }

            if (!$image_exists) {
                throw new Exception("Ảnh không thuộc về địa điểm này.");
            }

            if ($this->destinationModel->setPrimaryImage($image_id, $destination_id)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã đặt làm ảnh chính.'
                ]);
            } else {
                throw new Exception("Không thể đặt làm ảnh chính.");
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * AJAX: Delete image
     */
    public function deleteImage()
    {
        require_admin();

        header('Content-Type: application/json');

        try {
            if (empty($_POST['image_id'])) {
                throw new Exception("Thiếu thông tin ảnh.");
            }

            $image_id = (int) $_POST['image_id'];

            // Get image info before delete (to get file path)
            $stmt = $this->db->prepare("SELECT image_url, destination_id FROM destination_images WHERE id = :id");
            $stmt->execute(['id' => $image_id]);
            $image = $stmt->fetch();

            if (!$image) {
                throw new Exception("Ảnh không tồn tại.");
            }

            // Delete from database
            if ($this->destinationModel->deleteImage($image_id)) {
                // Delete physical file
                $file_path = $image['image_url'];
                if (file_exists($file_path)) {
                    @unlink($file_path);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã xóa ảnh.'
                ]);
            } else {
                throw new Exception("Không thể xóa ảnh.");
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // ========================================================================
    // PRIVATE HELPERS
    // ========================================================================

    private function handleImageUploads($destination_id, $files)
    {
        require_once COMMON_PATH . '/ValidationHelper.php';

        $upload_dir = 'public/uploads/destinations/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $count = count($files['name']);
        $uploaded_count = 0;

        // Check if destination already has images to determine primary
        $existing_images = $this->destinationModel->getImages($destination_id);
        $has_primary = false;
        foreach ($existing_images as $img) {
            if ($img['is_primary']) {
                $has_primary = true;
                break;
            }
        }

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $files['tmp_name'][$i];
                $name = basename($files['name'][$i]);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                // Basic extension check
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    continue;
                }

                // Size check
                if ($files['size'][$i] > 5 * 1024 * 1024) {
                    continue; // 5MB limit
                }

                // Validate image file (MIME type + dimensions)
                $validation = ValidationHelper::validateImageFile($tmp_name);
                if (!$validation['valid']) {
                    error_log("Image upload validation failed: " . $validation['error']);
                    continue;
                }

                // Generate unique name
                $new_name = 'dest_' . $destination_id . '_' . uniqid() . '.' . $ext;
                $destination_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $destination_path)) {
                    // Set primary for the first image if none exists
                    $is_primary = (!$has_primary && $uploaded_count === 0);

                    $this->destinationModel->addImage($destination_id, $destination_path, $is_primary);
                    $uploaded_count++;
                }
            }
        }

        return $uploaded_count;
    }
}
