<?php
/**
 * ==============================================================================
 * PROFILE CONTROLLER
 * ==============================================================================
 * 
 * Quản lý profile cá nhân - TẤT CẢ ROLES có thể truy cập
 * 
 * Functions:
 * - index(): Xem profile của mình
 * - edit(): Form sửa profile
 * - update(): Xử lý cập nhật profile
 * - changePassword(): Form đổi password
 * - updatePassword(): Xử lý đổi password
 * 
 * User CHỈ được sửa thông tin cá nhân, KHÔNG được sửa: email, role, status
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class ProfileController
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
     * Xem profile của mình
     * GET /profile
     */
    public function index()
    {
        // Require login
        require_login();

        // Get current user
        $user_id = get_user_id();
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_error("Không tìm thấy thông tin người dùng.");
            redirect_to_dashboard();
            return;
        }

        $page_title = 'Thông tin cá nhân';
        $content_file = VIEWS_PATH . '/profile/index.php';

        // Load layout based on role
        $role = get_user_role();
        $layout_file = match ($role) {
            'admin' => 'admin_layout.php',
            'staff' => 'staff_layout.php',
            'guide' => 'guide_layout.php',
            default => 'main_layout.php'
        };

        require VIEWS_PATH . '/layouts/' . $layout_file;
    }

    /**
     * Form sửa profile
     * GET /profile/edit
     */
    public function edit()
    {
        // Require login
        require_login();

        // Get current user
        $user_id = get_user_id();
        $user = $this->userModel->findById($user_id);

        if (!$user) {
            set_error("Không tìm thấy thông tin người dùng.");
            redirect_to_dashboard();
            return;
        }

        $page_title = 'Sửa thông tin cá nhân';
        $content_file = VIEWS_PATH . '/profile/edit.php';

        // Load layout based on role
        $role = get_user_role();
        $layout_file = match ($role) {
            'admin' => 'admin_layout.php',
            'staff' => 'staff_layout.php',
            'guide' => 'guide_layout.php',
            default => 'main_layout.php'
        };

        require VIEWS_PATH . '/layouts/' . $layout_file;
    }

    /**
     * Xử lý cập nhật profile
     * POST /profile/update
     */
    public function update()
    {
        // Require login
        require_login();

        try {
            $user_id = get_user_id();
            $user = $this->userModel->findById($user_id);

            if (!$user) {
                throw new Exception("Không tìm thấy thông tin người dùng.");
            }

            // Validate required fields
            if (empty($_POST['full_name'])) {
                throw new Exception("Vui lòng nhập họ tên.");
            }

            // Validate phone (if provided)
            if (!empty($_POST['phone'])) {
                $phone = $_POST['phone'];
                if (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
                    throw new Exception("Số điện thoại không hợp lệ.");
                }
            }

            // Handle avatar upload (if any)
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatar_path = $this->uploadAvatar($_FILES['avatar']);
            }

            // Prepare data (CHỈ cho sửa các field này)
            $data = [
                'full_name' => sanitize($_POST['full_name']),
                'phone' => isset($_POST['phone']) ? sanitize($_POST['phone']) : null,
                'date_of_birth' => !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null,
                'gender' => isset($_POST['gender']) ? $_POST['gender'] : null,
                'address' => isset($_POST['address']) ? sanitize($_POST['address']) : null
            ];

            // Add avatar if uploaded
            if (isset($avatar_path)) {
                $data['avatar'] = $avatar_path;
            }

            // Update user
            $success = $this->userModel->update($user_id, $data);

            if ($success) {
                // Update session data
                $_SESSION['full_name'] = $data['full_name'];
                if (isset($avatar_path)) {
                    $_SESSION['avatar'] = $avatar_path;
                }

                set_success("Cập nhật thông tin thành công!");
            } else {
                throw new Exception("Không thể cập nhật thông tin. Vui lòng thử lại.");
            }

            redirect('?act=profile');

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=profile/edit');
        }
    }

    /**
     * Form đổi password
     * GET /profile/change-password
     */
    public function changePassword()
    {
        // Require login
        require_login();

        $page_title = 'Đổi mật khẩu';
        $content_file = VIEWS_PATH . '/profile/change-password.php';

        // Load layout based on role
        $role = get_user_role();
        $layout_file = match ($role) {
            'admin' => 'admin_layout.php',
            'staff' => 'staff_layout.php',
            'guide' => 'guide_layout.php',
            default => 'main_layout.php'
        };

        require VIEWS_PATH . '/layouts/' . $layout_file;
    }

    /**
     * Xử lý đổi password
     * POST /profile/update-password
     */
    public function updatePassword()
    {
        // Require login
        require_login();

        try {
            $user_id = get_user_id();
            $user = $this->userModel->findById($user_id);

            if (!$user) {
                throw new Exception("Không tìm thấy thông tin người dùng.");
            }

            // Validate required fields
            if (empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
                throw new Exception("Vui lòng điền đầy đủ thông tin.");
            }

            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Verify old password
            if (!verify_password($old_password, $user['password'])) {
                throw new Exception("Mật khẩu hiện tại không đúng.");
            }

            // Check new password confirmation
            if ($new_password !== $confirm_password) {
                throw new Exception("Mật khẩu mới không khớp.");
            }

            // Validate new password strength
            if (strlen($new_password) < 8) {
                throw new Exception("Mật khẩu mới phải có ít nhất 8 ký tự.");
            }
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $new_password)) {
                throw new Exception("Mật khẩu mới phải có chữ hoa, chữ thường và số.");
            }

            // Check if new password is different from old
            if ($old_password === $new_password) {
                throw new Exception("Mật khẩu mới phải khác mật khẩu cũ.");
            }

            // Update password
            $success = $this->userModel->update($user_id, [
                'password' => $new_password
            ]);

            if ($success) {
                set_success("Đổi mật khẩu thành công!");
                redirect('?act=profile');
            } else {
                throw new Exception("Không thể đổi mật khẩu. Vui lòng thử lại.");
            }

        } catch (Exception $e) {
            set_error($e->getMessage());
            redirect('?act=profile/change-password');
        }
    }

    /**
     * Upload avatar
     * 
     * @param array $file $_FILES['avatar']
     * @return string Path to uploaded file
     * @throws Exception
     */
    private function uploadAvatar($file)
    {
        // Check file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception("File ảnh không được vượt quá 2MB.");
        }

        // Check file type
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            throw new Exception("Chỉ chấp nhận file JPG, PNG.");
        }

        // Create upload directory if not exists
        $upload_dir = 'public/uploads/avatars/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception("Không thể upload file. Vui lòng thử lại.");
        }

        return $filepath;
    }
}

// ============================================================================
// END OF PROFILE CONTROLLER
// ============================================================================
