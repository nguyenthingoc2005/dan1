<?php
/**
 * ==============================================================================
 * VALIDATION HELPER
 * ==============================================================================
 * 
 * Tập hợp các hàm validation tái sử dụng
 * 
 * @version 1.0
 * @date 2024-12-03
 * ==============================================================================
 */

class ValidationHelper
{
    /**
     * Validate email format
     * 
     * @param string $email
     * @param bool $allow_empty
     * @return bool
     */
    public static function validateEmail($email, $allow_empty = false)
    {
        if ($allow_empty && empty($email)) {
            return true;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number (Vietnamese format: 10-11 digits)
     * 
     * @param string $phone
     * @param bool $allow_empty
     * @return bool
     */
    public static function validatePhone($phone, $allow_empty = false)
    {
        if ($allow_empty && empty($phone)) {
            return true;
        }
        // Remove spaces, dashes, parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        return preg_match('/^[0-9]{10,11}$/', $phone);
    }

    /**
     * Validate tax code (10 digits)
     * 
     * @param string $tax_code
     * @param bool $allow_empty
     * @return bool
     */
    public static function validateTaxCode($tax_code, $allow_empty = false)
    {
        if ($allow_empty && empty($tax_code)) {
            return true;
        }
        return preg_match('/^[0-9]{10}$/', $tax_code);
    }

    /**
     * Validate price (min/max range)
     * 
     * @param float|string $price
     * @param float $min
     * @param float $max
     * @return bool
     */
    public static function validatePrice($price, $min = 0, $max = 1000000000)
    {
        $price = (float) $price;
        return $price >= $min && $price <= $max;
    }

    /**
     * Validate service unit (whitelist)
     * 
     * @param string $unit
     * @param bool $allow_empty
     * @return bool
     */
    public static function validateServiceUnit($unit, $allow_empty = true)
    {
        if ($allow_empty && empty($unit)) {
            return true;
        }
        $allowed = ['phòng/đêm', 'suất', 'xe/ngày', 'vé', 'người', 'bữa', 'ngày', 'giờ', 'km'];
        return in_array($unit, $allowed);
    }

    /**
     * Validate bank account (9-14 digits)
     * 
     * @param string $account
     * @param bool $allow_empty
     * @return bool
     */
    public static function validateBankAccount($account, $allow_empty = false)
    {
        if ($allow_empty && empty($account)) {
            return true;
        }
        // Remove spaces and dashes
        $account = preg_replace('/[\s\-]/', '', $account);
        return preg_match('/^[0-9]{9,14}$/', $account);
    }

    /**
     * Validate date range (end >= start)
     * 
     * @param string $start_date
     * @param string $end_date
     * @return bool
     */
    public static function validateDateRange($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return true; // Allow empty dates
        }
        return strtotime($end_date) >= strtotime($start_date);
    }

    /**
     * Validate image file (MIME type + size)
     * 
     * @param string $file_path
     * @param int $max_size_bytes (default 5MB)
     * @param array $allowed_mimes
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateImageFile($file_path, $max_size_bytes = 5242880, $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'])
    {
        if (!file_exists($file_path)) {
            return ['valid' => false, 'error' => 'File không tồn tại'];
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_path);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_mimes)) {
            return ['valid' => false, 'error' => 'Định dạng file không hợp lệ. Chỉ chấp nhận: ' . implode(', ', $allowed_mimes)];
        }

        // Check file size
        $file_size = filesize($file_path);
        if ($file_size > $max_size_bytes) {
            return ['valid' => false, 'error' => 'File quá lớn. Tối đa: ' . round($max_size_bytes / 1024 / 1024, 1) . 'MB'];
        }

        // Check image dimensions (optional)
        $image_info = @getimagesize($file_path);
        if ($image_info === false) {
            return ['valid' => false, 'error' => 'File không phải là ảnh hợp lệ'];
        }

        // Optional: Check min dimensions
        if ($image_info[0] < 200 || $image_info[1] < 200) {
            return ['valid' => false, 'error' => 'Kích thước ảnh quá nhỏ. Tối thiểu: 200x200px'];
        }

        // Optional: Check max dimensions
        if ($image_info[0] > 5000 || $image_info[1] > 5000) {
            return ['valid' => false, 'error' => 'Kích thước ảnh quá lớn. Tối đa: 5000x5000px'];
        }

        return ['valid' => true, 'error' => null];
    }
}



