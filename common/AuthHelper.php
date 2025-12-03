<?php
/**
 * ==============================================================================
 * AUTH HELPER - Authentication & Authorization Utilities
 * ==============================================================================
 * 
 * Chức năng:
 * - Session management (login status check)
 * - Role-based access control (admin/staff/guide)
 * - Get current user info
 * - Flash messages (error/success)
 * - Password utilities
 * - Redirect helpers based on roles
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

// ============================================================================
// SESSION MANAGEMENT
// ============================================================================

/**
 * Kiểm tra user đã login chưa
 * 
 * @return bool
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin user hiện tại đang login
 * 
 * @return array|null ['id', 'email', 'full_name', 'role', 'role_display', 'avatar']
 */
function get_auth_user()
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'role_display' => $_SESSION['role_display'] ?? null,
        'avatar' => $_SESSION['avatar'] ?? null,
    ];
}

/**
 * Lấy current user ID
 * 
 * @return int|null
 */
function get_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy current user role
 * 
 * @return string|null 'admin', 'staff', 'guide'
 */
function get_user_role()
{
    return $_SESSION['role'] ?? null;
}

/**
 * Lấy current user full name
 * 
 * @return string
 */
function get_user_name()
{
    return $_SESSION['full_name'] ?? 'Guest';
}

// ============================================================================
// ROLE CHECKING
// ============================================================================

/**
 * Kiểm tra user có role cụ thể không
 * 
 * @param string|array $required_roles Single role hoặc array multiple roles
 * @return bool
 */
function has_role($required_roles)
{
    if (!is_logged_in()) {
        return false;
    }

    $current_role = get_user_role();

    if (is_string($required_roles)) {
        return $current_role === $required_roles;
    }

    if (is_array($required_roles)) {
        return in_array($current_role, $required_roles);
    }

    return false;
}

/**
 * Kiểm tra user có phải admin không
 * 
 * @return bool
 */
function is_admin()
{
    return has_role('admin');
}

/**
 * Kiểm tra user có phải staff không
 * 
 * @return bool
 */
function is_staff()
{
    return has_role('staff');
}

/**
 * Kiểm tra user có phải guide không
 * 
 * @return bool
 */
function is_guide()
{
    return has_role('guide');
}

// ============================================================================
// ACCESS CONTROL & REDIRECTS
// ============================================================================

/**
 * Require user phải login
 * Redirect về ?act=login nếu chưa login
 * 
 * @param string|null $redirect_to URL để redirect sau khi login
 * @return void
 */
function require_login($redirect_to = null)
{
    if (!is_logged_in()) {
        // Save redirect URL để sau khi login sẽ quay về
        $_SESSION['redirect_to'] = $redirect_to ?? $_SERVER['REQUEST_URI'];

        // Redirect to login page
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $base_url . '/?act=login');
        exit;
    }
}

/**
 * Require user phải là ADMIN
 * Redirect về ?act=access-denied nếu không phải admin
 * 
 * @return void
 */
function require_admin()
{
    require_login();

    if (!is_admin()) {
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $base_url . '/?act=access-denied');
        exit;
    }
}

/**
 * Require user phải là STAFF
 * 
 * @return void
 */
function require_staff()
{
    require_login();

    if (!is_staff()) {
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $base_url . '/?act=access-denied');
        exit;
    }
}

/**
 * Require user phải là GUIDE
 * 
 * @return void
 */
function require_guide()
{
    require_login();

    if (!is_guide()) {
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $base_url . '/?act=access-denied');
        exit;
    }
}

/**
 * Require user phải là ADMIN hoặc STAFF
 * 
 * @return void
 */
function require_admin_or_staff()
{
    require_login();

    if (!has_role(['admin', 'staff'])) {
        $base_url = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $base_url . '/?act=access-denied');
        exit;
    }
}

/**
 * Alias for require_admin_or_staff()
 * 
 * @return void
 */
function require_staff_or_admin()
{
    require_admin_or_staff();
}

/**
 * Redirect user về dashboard theo role
 * 
 * @return void
 */
function redirect_to_dashboard()
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    $role = get_user_role();

    switch ($role) {
        case 'admin':
            header('Location: ' . $base_url . '/?act=admin-dashboard');
            break;
        case 'staff':
            header('Location: ' . $base_url . '/?act=staff-dashboard');
            break;
        case 'guide':
            header('Location: ' . $base_url . '/?act=guide-dashboard');
            break;
        default:
            header('Location: ' . $base_url . '/?act=logout');
    }
    exit;
}


// ============================================================================
// PASSWORD UTILITIES
// ============================================================================

/**
 * Hash password
 * 
 * @param string $password
 * @return string
 */
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 * 
 * @param string $password Plain password
 * @param string $hash Hashed password
 * @return bool
 */
function verify_password($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Validate password strength
 * Minimum: 6 characters (theo .env PASSWORD_MIN_LENGTH)
 * 
 * @param string $password
 * @return bool
 */
function validate_password($password)
{
    $min_length = defined('PASSWORD_MIN_LENGTH') ? (int) PASSWORD_MIN_LENGTH : 6;
    return strlen($password) >= $min_length;
}

// ============================================================================
// FLASH MESSAGES (Success/Error)
// ============================================================================

/**
 * Set error message (lưu vào session)
 * 
 * @param string $message
 * @return void
 */
function set_error($message)
{
    $_SESSION['error'] = $message;
}

/**
 * Set success message
 * 
 * @param string $message
 * @return void
 */
function set_success($message)
{
    $_SESSION['success'] = $message;
}

/**
 * Get & clear error message
 * 
 * @return string|null
 */
function get_error()
{
    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    return $error;
}

/**
 * Get & clear success message
 * 
 * @return string|null
 */
function get_success()
{
    $success = $_SESSION['success'] ?? null;
    unset($_SESSION['success']);
    return $success;
}

/**
 * Kiểm tra có error message không
 * 
 * @return bool
 */
function has_error()
{
    return isset($_SESSION['error']);
}

/**
 * Kiểm tra có success message không
 * 
 * @return bool
 */
function has_success()
{
    return isset($_SESSION['success']);
}

// ============================================================================
// STATUS & ENUM HELPERS
// ============================================================================

/**
 * Get status label với color (cho UI badges)
 * 
 * @param string $status
 * @return array ['label' => '...', 'color' => 'green|yellow|red|gray']
 */
function get_status_badge($status)
{
    $statuses = [
        'active' => ['label' => 'Hoạt động', 'color' => 'green'],
        'inactive' => ['label' => 'Không hoạt động', 'color' => 'gray'],
        'suspended' => ['label' => 'Tạm khóa', 'color' => 'red'],
        'pending' => ['label' => 'Chờ duyệt', 'color' => 'yellow'],
        'approved' => ['label' => 'Đã duyệt', 'color' => 'green'],
        'rejected' => ['label' => 'Từ chối', 'color' => 'red'],
        'paid' => ['label' => 'Đã thanh toán', 'color' => 'green'],
        'partial' => ['label' => 'Thanh toán một phần', 'color' => 'yellow'],
        'unpaid' => ['label' => 'Chưa thanh toán', 'color' => 'red'],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'gray'],
        'completed' => ['label' => 'Hoàn thành', 'color' => 'green'],
        'in_progress' => ['label' => 'Đang thực hiện', 'color' => 'blue'],
        'assigned' => ['label' => 'Đã phân công', 'color' => 'blue'],
    ];

    return $statuses[$status] ?? ['label' => ucfirst($status), 'color' => 'gray'];
}

/**
 * Get role display name
 * 
 * @param string $role 'admin', 'staff', 'guide'
 * @return string
 */
function get_role_display($role)
{
    $roles = [
        'admin' => 'Quản trị viên',
        'staff' => 'Nhân viên',
        'guide' => 'Hướng dẫn viên',
    ];

    return $roles[$role] ?? ucfirst($role);
}

// ============================================================================
// HELPER: Tạo select options từ array
// ============================================================================

/**
 * Tạo HTML options cho select dropdown
 * 
 * @param array $items Array of items
 * @param string $value_field Tên field làm value
 * @param string $label_field Tên field làm label
 * @param mixed $selected_value Giá trị được chọn
 * @return string HTML options
 */
function make_select_options($items, $value_field, $label_field, $selected_value = null)
{
    $html = '';
    foreach ($items as $item) {
        $value = is_array($item) ? $item[$value_field] : $item->$value_field;
        $label = is_array($item) ? $item[$label_field] : $item->$label_field;
        $selected = ($value == $selected_value) ? 'selected' : '';

        $html .= "<option value='{$value}' {$selected}>" . sanitize($label) . "</option>";
    }
    return $html;
}

// ============================================================================
// END OF AUTH HELPER
// ============================================================================
