<?php
/**
 * ==============================================================================
 * AUTH CONTROLLER - Authentication Logic
 * ==============================================================================
 * 
 * Xử lý đăng nhập, đăng xuất, quên mật khẩu
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

class AuthController
{
    private $db;
    private $userModel;

    /**
     * Constructor
     */
    public function __construct($pdo)
    {
        $this->db = $pdo;
        require_once MODELS_PATH . '/User.php';
        $this->userModel = new User($pdo);
    }

    /**
     * Show login form
     * GET /login
     * 
     * @return void
     */
    public function showLogin()
    {
        // Nếu đã login rồi thì redirect về dashboard
        if (is_logged_in()) {
            redirect_to_dashboard();
            return;
        }

        // Render login view
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Handle login submission
     * POST /login
     * 
     * @return void
     */
    public function handleLogin()
    {
        // Check if already logged in
        if (is_logged_in()) {
            redirect_to_dashboard();
            return;
        }

        // Validate input
        if (empty($_POST['email']) || empty($_POST['password'])) {
            set_error('Vui lòng nhập đầy đủ email và mật khẩu.');
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
            exit;
        }

        $email = trim($_POST['email']);
        $password = $_POST['password'];

        try {
            // Find user by email
            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                set_error('Email hoặc mật khẩu không đúng.');
                header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
                exit;
            }

            // Check if user is active
            if ($user['status'] !== 'active') {
                set_error('Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');
                header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
                exit;
            }

            // Verify password
            if (!verify_password($password, $user['password'])) {
                set_error('Email hoặc mật khẩu không đúng.');
                header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
                exit;
            }

            // Password correct - create session
            // Clear any old flash messages first
            unset($_SESSION['error']);
            unset($_SESSION['success']);

            $this->createUserSession($user);

            // Update last login time
            $this->userModel->updateLastLogin($user['id']);

            // Check if there's a redirect URL saved
            $redirect_to = $_SESSION['redirect_to'] ?? null;
            unset($_SESSION['redirect_to']);

            // Redirect to saved URL or dashboard
            if ($redirect_to) {
                header('Location: ' . $redirect_to);
            } else {
                redirect_to_dashboard();
            }
            exit;

        } catch (Exception $e) {
            // Log error (trong production nên log vào file)
            error_log('Login error: ' . $e->getMessage());

            set_error('Đã xảy ra lỗi. Vui lòng thử lại sau.');
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
            exit;
        }
    }

    /**
     * Handle logout
     * GET /logout
     * 
     * @return void
     */
    public function logout()
    {
        // Destroy session
        session_unset();
        session_destroy();

        // Redirect to login with success message
        session_start();
        set_success('Bạn đã đăng xuất thành công.');

        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
        exit;
    }

    /**
     * Create user session after successful login
     * 
     * @param array $user User data from database
     * @return void
     */
    private function createUserSession($user)
    {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role']; // From JOIN: r.name as role
        $_SESSION['role_display'] = $user['role_display']; // From JOIN: r.display_name as role_display
        $_SESSION['avatar'] = $user['avatar'] ?? null;
        $_SESSION['login_time'] = time();
    }

    /**
     * Show register form
     * GET /register
     * (Optional - có thể disable trong production)
     * 
     * @return void
     */
    public function showRegister()
    {
        // Nếu đã login rồi thì redirect về dashboard
        if (is_logged_in()) {
            redirect_to_dashboard();
            return;
        }

        // Render register view
        require VIEWS_PATH . '/auth/register.php';
    }

    /**
     * Handle register submission
     * POST /register
     * (Optional - có thể disable trong production)
     * 
     * @return void
     */
    public function handleRegister()
    {
        // TODO: Implement registration logic if needed
        // Trong tour management system, registration thường do admin tạo
        // Nên function này có thể để trống hoặc redirect về login

        set_error('Chức năng đăng ký hiện không khả dụng. Vui lòng liên hệ quản trị viên.');
        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/?act=login');
        exit;
    }
}

// ============================================================================
// END OF AUTH CONTROLLER
// ============================================================================
