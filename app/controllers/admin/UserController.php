<?php
/**
 * ==============================================================================
 * USER CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý users - CHỈ ADMIN mới được truy cập
 * Validation: CHỈ email, tên, vai trò. Các trường khác optional.
 * 
 * @version 1.1
 * @date 2024-12-02
 * ==============================================================================
 */

class UserController
{
    private $db;
    private $userModel;

    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/User.php';
        $this->userModel = new User($pdo);
    }

    /**
     * Danh sách users
     */
    public function index()
    {
        require_admin();

        // Get filters
        $filters = [];
        if (!empty($_GET['role']))
            $filters['role'] = sanitize($_GET['role']);
        if (!empty($_GET['status']))
            $filters['status'] = sanitize($_GET['status']);
        if (!empty($_GET['search']))
            $filters['search'] = sanitize($_GET['search']);

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $result = $this->userModel->getAll($filters, $page, 20);

        $users = $result['data'];
        $total = $result['total'];
        $total_pages = $result['pages'];
        $current_page = $result['current_page'];

        $page_title = 'Quản lý nhân viên';
        $content_file = VIEWS_PATH . '/admin/users/index.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Form tạo user
     */
    public function create()
    {
        require_admin();

        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY id");
        $stmt->execute();
        $roles = $stmt->fetchAll();

        $page_title = 'Thêm nhân viên mới';
        $content_file = VIEWS_PATH . '/admin/users/create.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý tạo user - VALIDATION ĐƠN GIẢN
     */
    public function store()
    {
        require_admin();

        try {
            // CHỈ validate: email, tên, password, vai trò
            if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['full_name']) || empty($_POST['role_id'])) {
                throw new Exception("Vui lòng điền Email, Tên, Mật khẩu và Vai trò.");
            }

            // Validate email
            $email = trim($_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Email không hợp lệ.");
            }

            // Check unique email
            if ($this->userModel->findByEmail($email)) {
                throw new Exception("Email đã tồn tại.");
            }

            // Password: chỉ check độ dài
            $password = $_POST['password'];
            if (strlen($password) < 6) {
                throw new Exception("Mật khẩu tối thiểu 6 ký tự.");
            }

            // Avatar upload (optional)
            $avatar_path = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatar_path = $this->uploadAvatar($_FILES['avatar']);
            }

            // Prepare data
            $data = [
                'role_id' => (int) $_POST['role_id'],
                'email' => $email,
                'password' => $password,
                'full_name' => sanitize($_POST['full_name']),
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender' => isset($_POST['gender']) ? $_POST['gender'] : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            if ($avatar_path)
                $data['avatar'] = $avatar_path;

            if ($this->userModel->create($data)) {
                set_success("Tạo nhân viên thành công!");
                redirect('?act=admin&module=users');
            } else {
                throw new Exception("Không thể tạo nhân viên.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=users&action=create');
        }
    }

    /**
     * Form sửa user
     */
    public function edit()
    {
        require_admin();

        if (empty($_GET['id'])) {
            set_error("Không tìm thấy nhân viên.");
            redirect('?act=admin&module=users');
            return;
        }

        $user = $this->userModel->findById((int) $_GET['id']);
        if (!$user) {
            set_error("Không tìm thấy nhân viên.");
            redirect('?act=admin&module=users');
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY id");
        $stmt->execute();
        $roles = $stmt->fetchAll();

        $page_title = 'Sửa thông tin nhân viên';
        $content_file = VIEWS_PATH . '/admin/users/edit.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xử lý update user - VALIDATION ĐƠN GIẢN
     */
    public function update()
    {
        require_admin();

        try {
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy nhân viên.");
            }

            $user_id = (int) $_POST['id'];
            $user = $this->userModel->findById($user_id);

            if (!$user) {
                throw new Exception("Không tìm thấy nhân viên.");
            }

            // CHỈ validate: tên và vai trò
            if (empty($_POST['full_name']) || empty($_POST['role_id'])) {
                throw new Exception("Vui lòng điền Tên và Vai trò.");
            }

            // Validate email unique (nếu có thay đổi email)
            if (!empty($_POST['email']) && $_POST['email'] != $user['email']) {
                $email = trim($_POST['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email không hợp lệ.");
                }

                if ($this->userModel->isEmailExists($email, $user_id)) {
                    throw new Exception("Email đã tồn tại.");
                }
            }

            // Password: chỉ check NẾU có nhập
            if (!empty($_POST['password']) && strlen($_POST['password']) < 6) {
                throw new Exception("Mật khẩu tối thiểu 6 ký tự.");
            }

            // Avatar upload (optional)
            $avatar_path = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatar_path = $this->uploadAvatar($_FILES['avatar']);
            }

            // Prepare data
            $data = [
                'role_id' => (int) $_POST['role_id'],
                'full_name' => sanitize($_POST['full_name']),
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender' => isset($_POST['gender']) ? $_POST['gender'] : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null,
                'status' => isset($_POST['status']) ? $_POST['status'] : 'active'
            ];

            // Update email nếu có thay đổi
            if (!empty($_POST['email']) && $_POST['email'] != $user['email']) {
                $data['email'] = trim($_POST['email']);
            }

            if (!empty($_POST['password']))
                $data['password'] = $_POST['password'];
            if ($avatar_path)
                $data['avatar'] = $avatar_path;

            if ($this->userModel->update($user_id, $data)) {
                set_success("Cập nhật thành công!");
            } else {
                throw new Exception("Không thể cập nhật.");
            }

            redirect('?act=admin&module=users');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=admin&module=users&action=edit&id=' . ($user_id ?? 0));
        }
    }

    /**
     * Xóa user (soft delete)
     */
    public function delete()
    {
        require_admin();

        try {
            if (empty($_GET['id']))
                throw new Exception("Không tìm thấy nhân viên.");

            $user_id = (int) $_GET['id'];

            if ($user_id == get_user_id()) {
                throw new Exception("Bạn không thể xóa chính mình!");
            }

            if ($this->userModel->delete($user_id)) {
                set_success("Đã vô hiệu hóa nhân viên.");
            } else {
                throw new Exception("Không thể xóa.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
        }

        redirect('?act=admin&module=users');
    }

    /**
     * Toggle status
     */
    public function toggleStatus()
    {
        require_admin();

        try {
            if (empty($_POST['id']))
                throw new Exception("Không tìm thấy nhân viên.");

            $user_id = (int) $_POST['id'];

            if ($user_id == get_user_id()) {
                throw new Exception("Không thể thay đổi trạng thái của chính mình!");
            }

            $user = $this->userModel->findById($user_id);
            if (!$user)
                throw new Exception("Không tìm thấy nhân viên.");

            $new_status = ($user['status'] == 'active') ? 'inactive' : 'active';

            if ($this->userModel->update($user_id, ['status' => $new_status])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã thay đổi trạng thái',
                    'new_status' => $new_status
                ]);
            } else {
                throw new Exception("Không thể thay đổi trạng thái.");
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
     * Upload avatar
     */
    private function uploadAvatar($file)
    {
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception("File ảnh tối đa 2MB.");
        }

        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Chỉ chấp nhận JPG, PNG.");
        }

        $upload_dir = 'public/uploads/avatars/';
        if (!is_dir($upload_dir))
            mkdir($upload_dir, 0755, true);

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Không thể upload file.");
        }

        return $filepath;
    }
}
