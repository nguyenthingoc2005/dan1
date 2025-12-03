<?php
/**
 * ==============================================================================
 * COMMON FUNCTIONS - Utility & Helper Functions
 * ==============================================================================
 * 
 * Kho hàm tái sử dụng cho toàn ứng dụng:
 * - Format tiền, ngày tháng
 * - Validation (email, phone)
 * - Generate codes (booking, tour, customer)
 * - Calculations (deposit, remaining)
 * - Sanitize input
 * - Redirect helpers
 * 
 * Theo Vibe Coding: Viết hàm nhỏ, đơn giản, dễ hiểu
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

// ============================================================================
// FORMAT FUNCTIONS
// ============================================================================

/**
 * Format số tiền thành chuỗi có dấu phân cách
 * 
 * @param float|int $amount Số tiền
 * @return string "1,000,000 đ"
 */
function formatMoney($amount)
{
    if ($amount === null || $amount === '') {
        return '0 đ';
    }
    return number_format($amount, 0, '.', ',') . ' đ';
}

/**
 * Alias for formatMoney to fix legacy calls
 */
function format_currency($amount)
{
    return formatMoney($amount);
}

/**
 * Format ngày tháng
 * 
 * @param string $date Ngày dạng SQL (Y-m-d hoặc Y-m-d H:i:s)
 * @param string $format Format mong muốn (default: d/m/Y)
 * @return string Ngày đã format hoặc '-' nếu empty
 */
function formatDate($date, $format = 'd/m/Y')
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Alias for formatDate to fix legacy calls
 */
function format_date($date, $format = 'd/m/Y')
{
    return formatDate($date, $format);
}

/**
 * Format ngày giờ
 * 
 * @param string $datetime
 * @return string "01/12/2024 10:30"
 */
function formatDateTime($datetime)
{
    return formatDate($datetime, 'd/m/Y H:i');
}

/**
 * Format time (chỉ giờ:phút)
 * 
 * @param string $time 'HH:MM:SS'
 * @return string 'HH:MM'
 */
function formatTime($time)
{
    if (empty($time)) {
        return '-';
    }
    return substr($time, 0, 5);
}

// ============================================================================
// STATUS HELPERS
// ============================================================================

/**
 * Get status color class for Tailwind
 * 
 * @param string $status
 * @return string
 */
function get_status_color($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
        case 'confirmed':
        case 'completed':
        case 'active':
        case 'paid':
            return 'bg-green-100 text-green-800';
        case 'rejected':
        case 'cancelled':
        case 'inactive':
        case 'unpaid':
            return 'bg-red-100 text-red-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

/**
 * Get display text for status
 * 
 * @param string $status
 * @return string
 */
function approval_status_text($status)
{
    switch ($status) {
        case 'pending':
            return 'Chờ duyệt';
        case 'approved':
            return 'Đã duyệt';
        case 'rejected':
            return 'Từ chối';
        case 'confirmed':
            return 'Đã xác nhận';
        case 'cancelled':
            return 'Đã hủy';
        case 'completed':
            return 'Hoàn thành';
        case 'processing':
            return 'Đang xử lý';
        default:
            return ucfirst($status);
    }
}

// ============================================================================
// VALIDATION FUNCTIONS
// ============================================================================

/**
 * Kiểm tra email hợp lệ
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Kiểm tra số điện thoại Việt Nam
 * 
 * @param string $phone
 * @return bool
 */
function isValidPhone($phone)
{
    // Remove spaces, dashes
    $phone = preg_replace('/[\s\-]/', '', $phone);

    // Check format: 10 digits starting with 0
    // or 11 digits starting with +84 or 84
    return preg_match('/^(0|\+?84)[0-9]{9}$/', $phone);
}

/**
 * Sanitize string - XSS protection
 * 
 * @param string $str
 * @return string
 */
function sanitize($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input array (POST/GET data)
 * 
 * @param array $data
 * @return array
 */
function sanitizeArray($data)
{
    $clean = [];
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $clean[$key] = sanitizeArray($value);
        } else {
            $clean[$key] = sanitize($value);
        }
    }
    return $clean;
}

// ============================================================================
// CODE GENERATION FUNCTIONS
// ============================================================================

/**
 * Generate booking code
 * Format: BK20241201001
 * 
 * @return string
 */
function generateBookingCode()
{
    return 'BK' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate tour code unique theo năm: TOUR_2024_001
 * 
 * @param PDO $pdo Database connection
 * @return string
 */
function generateTourCodeUnique($pdo)
{
    $prefix = "TOUR";
    $year = date('Y');

    // Tìm code cao nhất trong năm
    $stmt = $pdo->prepare("
        SELECT tour_code FROM tours
        WHERE tour_code LIKE :pattern
        ORDER BY tour_code DESC
        LIMIT 1
    ");
    $stmt->execute(['pattern' => "{$prefix}_{$year}_%"]);
    $last = $stmt->fetchColumn();

    if ($last) {
        preg_match('/_(\d+)$/', $last, $matches);
        $next_num = isset($matches[1]) ? ((int) $matches[1] + 1) : 1;
    } else {
        $next_num = 1;
    }

    return sprintf("%s_%s_%03d", $prefix, $year, $next_num);
}

/**
 * Generate tour code (legacy - for backward compatibility)
 * Format: TOUR20241201001
 * 
 * @return string
 */
function generateTourCode()
{
    return 'TOUR' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate customer code
 * Format: CUS20241201001
 * 
 * @return string
 */
function generateCustomerCode()
{
    return 'CUS' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate supplier code
 * Format: SUP20241201001
 * 
 * @return string
 */
function generateSupplierCode()
{
    return 'SUP' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate service code
 * Format: SVC20241201001
 * 
 * @return string
 */
function generateServiceCode()
{
    return 'SVC' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate payment code
 * Format: PAY20241201001
 * 
 * @return string
 */
function generatePaymentCode()
{
    return 'PAY' . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}

/**
 * Generate service type code từ tên (VD: "Khách sạn" => "KHACH_SAN_2024_001")
 * 
 * @param string $name Tên loại dịch vụ
 * @param PDO $pdo Database connection
 * @return string Code unique theo năm
 */
function generateServiceTypeCode($name, $pdo)
{
    // Tạo slug từ tên (VD: "Khách sạn" -> "KHACH_SAN")
    $slug = createSlug($name);
    $code_prefix = strtoupper(str_replace('-', '_', $slug));
    $year = date('Y');

    // Tìm code gần nhất với prefix này trong năm
    $stmt = $pdo->prepare("
        SELECT code FROM service_types
        WHERE code LIKE :pattern
        ORDER BY code DESC
        LIMIT 1
    ");
    $stmt->execute(['pattern' => "{$code_prefix}_{$year}_%"]);
    $last = $stmt->fetchColumn();

    if ($last) {
        // Extract số thứ tự từ code cuối (VD: KHACH_SAN_2024_002 -> 2)
        preg_match('/_(\d+)$/', $last, $matches);
        $next_num = isset($matches[1]) ? ((int) $matches[1] + 1) : 1;
    } else {
        $next_num = 1;
    }

    // Format: KHACH_SAN_2024_001
    return sprintf("%s_%s_%03d", $code_prefix, $year, $next_num);
}

// ============================================================================
// CALCULATION FUNCTIONS
// ============================================================================

/**
 * Tính tiền đặt cọc
 * 
 * @param float $total Tổng tiền
 * @param float $percent Phần trăm cọc (VD: 30)
 * @return float
 */
function calculateDeposit($total, $percent)
{
    return round($total * ($percent / 100), 0);
}

/**
 * Tính tiền còn lại
 * 
 * @param float $total Tổng tiền
 * @param float $paid Đã thanh toán
 * @return float
 */
function calculateRemaining($total, $paid)
{
    return max(0, $total - $paid);
}

/**
 * Tính số ngày giữa 2 ngày
 * 
 * @param string $startDate
 * @param string $endDate
 * @return int
 */
function calculateDaysBetween($startDate, $endDate)
{
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $diff = $start->diff($end);
    return $diff->days;
}

/**
 * Tính tổng tiền booking
 * 
 * @param float $adultPrice Giá người lớn
 * @param float $childPrice Giá trẻ em
 * @param float $infantPrice Giá em bé
 * @param int $adultCount Số người lớn
 * @param int $childCount Số trẻ em
 * @param int $infantCount Số em bé
 * @return float
 */
function calculateBookingTotal($adultPrice, $childPrice, $infantPrice, $adultCount, $childCount, $infantCount)
{
    return ($adultPrice * $adultCount) + ($childPrice * $childCount) + ($infantPrice * $infantCount);
}

/**
 * Apply discount code
 * 
 * @param float $total Tổng tiền
 * @param string $discountType 'percentage' hoặc 'fixed'
 * @param float $discountValue Giá trị giảm
 * @return float Số tiền giảm
 */
function calculateDiscount($total, $discountType, $discountValue)
{
    if ($discountType === 'percentage') {
        return round($total * ($discountValue / 100), 0);
    } else {
        return min($discountValue, $total); // Không giảm quá tổng tiền
    }
}

// ============================================================================
// REDIRECT & URL HELPERS
// ============================================================================

/**
 * Redirect đến URL
 * 
 * @param string $path
 * @return void
 */
function redirect($path)
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    header('Location: ' . $base_url . $path);
    exit;
}

/**
 * Redirect back
 * 
 * @return void
 */
function redirectBack()
{
    $referer = $_SERVER['HTTP_REFERER'] ?? (defined('BASE_URL') ? BASE_URL : '/');
    header('Location: ' . $referer);
    exit;
}

/**
 * Get current URL path
 * 
 * @return string
 */
function getCurrentPath()
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    $path = str_replace($base_url, '', $_SERVER['REQUEST_URI']);
    return strtok($path, '?');
}

/**
 * Build URL with query params
 * 
 * @param string $path
 * @param array $params
 * @return string
 */
function buildUrl($path, $params = [])
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    $url = $base_url . $path;

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}

// ============================================================================
// STRING & ARRAY HELPERS
// ============================================================================

/**
 * Truncate string
 * 
 * @param string $str
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($str, $length = 100, $suffix = '...')
{
    if (mb_strlen($str) <= $length) {
        return $str;
    }
    return mb_substr($str, 0, $length) . $suffix;
}

/**
 * Create slug from string
 * 
 * @param string $str
 * @return string
 */
function createSlug($str)
{
    // Convert Vietnamese to ASCII
    $vietnamese = [
        'à',
        'á',
        'ả',
        'ã',
        'ạ',
        'ă',
        'ằ',
        'ắ',
        'ẳ',
        'ẵ',
        'ặ',
        'â',
        'ầ',
        'ấ',
        'ẩ',
        'ẫ',
        'ậ',
        'đ',
        'è',
        'é',
        'ẻ',
        'ẽ',
        'ẹ',
        'ê',
        'ề',
        'ế',
        'ể',
        'ễ',
        'ệ',
        'ì',
        'í',
        'ỉ',
        'ĩ',
        'ị',
        'ò',
        'ó',
        'ỏ',
        'õ',
        'ọ',
        'ô',
        'ồ',
        'ố',
        'ổ',
        'ỗ',
        'ộ',
        'ơ',
        'ờ',
        'ớ',
        'ở',
        'ỡ',
        'ợ',
        'ù',
        'ú',
        'ủ',
        'ũ',
        'ụ',
        'ư',
        'ừ',
        'ứ',
        'ử',
        'ữ',
        'ự',
        'ỳ',
        'ý',
        'ỷ',
        'ỹ',
        'ỵ',
    ];
    $ascii = [
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'd',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        'i',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'y',
        'y',
        'y',
        'y',
        'y',
    ];

    $str = mb_strtolower($str);
    $str = str_replace($vietnamese, $ascii, $str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    $str = trim($str, '-');

    return $str;
}

// ============================================================================
// DEBUG HELPERS
// ============================================================================

/**
 * Debug print (chỉ trong development)
 * 
 * @param mixed $data
 * @param string $label
 * @return void
 */
function dd($data, $label = 'DEBUG')
{
    if ((defined('APP_ENV') && APP_ENV === 'development')) {
        echo "<pre style='background: #f0f0f0; padding: 10px; border-left: 3px solid #333; margin: 10px 0;'>";
        echo "<strong>{$label}:</strong>\n";
        print_r($data);
        echo "</pre>";
    }
}

/**
 * Log to file
 * 
 * @param string $message
 * @param string $filename
 * @return void
 */
function logError($message, $filename = 'app.log')
{
    $log_dir = dirname(__FILE__, 2) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $log_message = "[" . date('Y-m-d H:i:s') . "] " . $message . "\n";
    file_put_contents($log_dir . '/' . $filename, $log_message, FILE_APPEND);
}

// ============================================================================
// JSON RESPONSE (cho AJAX)
// ============================================================================

/**
 * Return JSON response
 * 
 * @param bool $success
 * @param string $message
 * @param mixed $data
 * @return void
 */
function jsonResponse($success, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// ============================================================================
// END OF COMMON FUNCTIONS
// ============================================================================
