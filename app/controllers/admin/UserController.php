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

        // Chỉ lấy roles: staff và guide (bỏ admin)
        $stmt = $this->db->prepare("SELECT * FROM roles WHERE name IN ('staff', 'guide') ORDER BY id");
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

            // Validate role_id: Không cho phép tạo user với role admin
            $role_id = (int) $_POST['role_id'];
            $stmt = $this->db->prepare("SELECT name FROM roles WHERE id = :id");
            $stmt->execute(['id' => $role_id]);
            $role = $stmt->fetch();
            
            if (!$role) {
                throw new Exception("Vai trò không hợp lệ.");
            }
            
            if ($role['name'] === 'admin') {
                throw new Exception("Không được phép tạo tài khoản với vai trò Quản trị viên.");
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
            if (strlen($password) < 8) {
                throw new Exception("Mật khẩu tối thiểu 8 ký tự.");
            }

            // Password confirmation
            if (empty($_POST['password_confirmation'])) {
                throw new Exception("Vui lòng xác nhận mật khẩu.");
            }

            if ($password !== $_POST['password_confirmation']) {
                throw new Exception("Mật khẩu xác nhận không khớp.");
            }

            // Avatar upload (optional)
            $avatar_path = null;
            if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
                // Check for upload errors
                if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                    $error_msg = $this->getUploadErrorMessage($_FILES['avatar']['error']);
                    throw new Exception("Lỗi upload ảnh: " . $error_msg);
                }
                
                // Validate file exists and has content
                if (empty($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                    throw new Exception("File không hợp lệ hoặc không được upload thành công.");
                }
                
                $avatar_path = $this->uploadAvatar($_FILES['avatar']);
            }

            // Prepare data
            $data = [
                'role_id' => $role_id, // Đã validate ở trên (không phải admin)
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
            // QUAN TRỌNG: CHỈ lấy user_id từ POST (từ hidden input trong form)
            // KHÔNG lấy từ GET vì có thể bị nhầm với ID trong URL
            if (empty($_POST['id'])) {
                throw new Exception("Không tìm thấy ID nhân viên trong form. Vui lòng thử lại.");
            }

            $user_id = (int) $_POST['id'];
            
            // Validate user_id phải là số dương
            if ($user_id <= 0) {
                throw new Exception("ID nhân viên không hợp lệ: $user_id");
            }

            // Lấy thông tin user cần sửa (QUAN TRỌNG: Dùng $user_id từ POST, KHÔNG phải get_user_id() từ session)
            $user = $this->userModel->findById($user_id);

            if (!$user) {
                throw new Exception("Không tìm thấy nhân viên với ID: $user_id");
            }

            // CHỈ validate: tên và vai trò
            if (empty($_POST['full_name']) || empty($_POST['role_id'])) {
                throw new Exception("Vui lòng điền Tên và Vai trò.");
            }

            // Business rule: Không được thay đổi role của chính mình
            $current_user_id = get_user_id();
            if ($user_id == $current_user_id && $_POST['role_id'] != $user['role_id']) {
                throw new Exception("Bạn không thể thay đổi vai trò của chính mình!");
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
            if (!empty($_POST['password']) && strlen($_POST['password']) < 8) {
                throw new Exception("Mật khẩu tối thiểu 8 ký tự.");
            }

            // Avatar upload (optional)
            $avatar_path = null;
            if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
                // Check for upload errors
                if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                    $error_msg = $this->getUploadErrorMessage($_FILES['avatar']['error']);
                    throw new Exception("Lỗi upload ảnh: " . $error_msg);
                }
                
                // Validate file exists and has content
                if (empty($_FILES['avatar']['tmp_name']) || !is_uploaded_file($_FILES['avatar']['tmp_name'])) {
                    throw new Exception("File không hợp lệ hoặc không được upload thành công.");
                }
                
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

            // Update email nếu có thay đổi (chỉ khi admin thay đổi)
            // Lưu ý: Form edit không cho phép sửa email (disabled), nhưng vẫn có thể update qua code
            // Nếu cần update email, uncomment dòng dưới
            // if (!empty($_POST['email']) && $_POST['email'] != $user['email']) {
            //     $data['email'] = trim($_POST['email']);
            // }

            if (!empty($_POST['password']))
                $data['password'] = $_POST['password'];
            if ($avatar_path)
                $data['avatar'] = $avatar_path;

            // QUAN TRỌNG: Đảm bảo update đúng user_id từ POST (không phải get_user_id() từ session)
            // $user_id phải là ID của user đang được sửa, không phải ID của admin đang login
            $update_result = $this->userModel->update($user_id, $data);
            
            if (!$update_result) {
                throw new Exception("Không thể cập nhật thông tin nhân viên ID: $user_id. Vui lòng thử lại.");
            }

            set_success("Cập nhật thông tin nhân viên thành công!");
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
        // Check file size
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception("File ảnh tối đa 2MB.");
        }

        // Check if file is empty
        if ($file['size'] == 0) {
            throw new Exception("File ảnh rỗng hoặc không hợp lệ.");
        }

        // Check file type
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            throw new Exception("Không thể kiểm tra loại file.");
        }
        
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Chỉ chấp nhận JPG, PNG. Loại file hiện tại: " . ($mime ?: 'unknown'));
        }

        // Create upload directory if not exists
        $upload_dir = 'public/uploads/avatars/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Không thể tạo thư mục upload. Vui lòng kiểm tra quyền ghi.");
            }
        }

        // Check if directory is writable
        if (!is_writable($upload_dir)) {
            throw new Exception("Thư mục upload không có quyền ghi. Vui lòng kiểm tra quyền.");
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Không thể upload file. Vui lòng kiểm tra quyền ghi hoặc dung lượng ổ đĩa.");
        }

        return $filepath;
    }

    /**
     * Get upload error message
     */
    private function getUploadErrorMessage($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "File quá lớn. Vui lòng chọn file nhỏ hơn 2MB.";
            case UPLOAD_ERR_PARTIAL:
                return "File chỉ được upload một phần. Vui lòng thử lại.";
            case UPLOAD_ERR_NO_FILE:
                return "Không có file được chọn.";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Thiếu thư mục tạm. Vui lòng liên hệ quản trị viên.";
            case UPLOAD_ERR_CANT_WRITE:
                return "Không thể ghi file. Vui lòng kiểm tra quyền ghi.";
            case UPLOAD_ERR_EXTENSION:
                return "Upload bị chặn bởi extension. Vui lòng liên hệ quản trị viên.";
            default:
                return "Lỗi không xác định (mã lỗi: $error_code).";
        }
    }
}
