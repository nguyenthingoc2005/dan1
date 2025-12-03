<?php
/**
 * ==============================================================================
 * CUSTOMER IMPORT MODEL
 * ==============================================================================
 * 
 * Xử lý import khách hàng từ Excel/CSV
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class CustomerImport
{
    private $pdo;
    private $customerModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once MODELS_PATH . '/Customer.php';
        $this->customerModel = new Customer($pdo);
    }

    /**
     * Đọc file CSV/Excel và trả về mảng dữ liệu
     * Format Excel/CSV mong đợi:
     * - Row 1: Header (Họ tên, SĐT, Email, Ngày sinh, Giới tính, Loại, Địa chỉ)
     * - Row 2+: Data
     * 
     * @param string $filePath Đường dẫn file (có thể là tmp file)
     * @param string $fileType csv, xlsx, xls
     */
    public function readFile($filePath, $fileType = null)
    {
        if ($fileType === null) {
            $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }

        // Hiện tại chỉ hỗ trợ CSV
        // Excel files (.xlsx, .xls) cần export sang CSV hoặc dùng thư viện PhpSpreadsheet
        return $this->readCSV($filePath);
    }

    /**
     * Đọc file CSV
     */
    private function readCSV($filePath)
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new Exception("Không thể đọc file");
        }

        // Detect delimiter tự động (comma hoặc semicolon)
        $delimiter = $this->detectDelimiter($filePath);
        error_log("Detected CSV delimiter: '$delimiter'");

        // Đọc header (row 1) với delimiter đã detect
        $headers = fgetcsv($handle, 0, $delimiter);
        if (!$headers) {
            fclose($handle);
            throw new Exception("File không có dữ liệu hoặc format không đúng");
        }
        
        // Normalize headers (loại bỏ BOM, trim, lowercase, loại bỏ dấu ; thừa)
        $headers = array_map(function ($h) {
            $h = preg_replace('/\x{FEFF}/u', '', $h); // Loại bỏ BOM
            $h = trim($h, " \t\n\r\0\x0B;,"); // Trim whitespace và dấu ; thừa
            return strtolower($h);
        }, $headers);

        // Map headers to field names
        $fieldMap = $this->getFieldMap();
        $columnMap = [];
        foreach ($headers as $index => $header) {
            $header = trim($header);
            foreach ($fieldMap as $field => $aliases) {
                foreach ($aliases as $alias) {
                    // So sánh chính xác hơn: loại bỏ dấu tiếng Việt để so sánh
                    $normalizedHeader = $this->normalizeVietnamese($header);
                    $normalizedAlias = $this->normalizeVietnamese($alias);
                    
                    if (stripos($normalizedHeader, $normalizedAlias) !== false || 
                        stripos($header, $alias) !== false) {
                        $columnMap[$index] = $field;
                        error_log("Mapped column $index (header: '$header') to field: '$field'");
                        break 2;
                    }
                }
            }
        }
        
        // Debug: Log column mapping
        error_log("Column mapping: " . print_r($columnMap, true));
        error_log("Headers: " . print_r($headers, true));

        // Đọc data rows với delimiter đã detect
        $rowNum = 1;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNum++;

            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Clean row: loại bỏ dấu ; thừa ở đầu/cuối mỗi field
            $row = array_map(function($field) {
                return trim($field, " \t\n\r\0\x0B;,");
            }, $row);

            $customerData = [];
            foreach ($columnMap as $colIndex => $field) {
                if (isset($row[$colIndex])) {
                    $value = trim($row[$colIndex], " \t\n\r\0\x0B;,");
                    
                    // Xử lý đặc biệt cho phone: Excel có thể format thành số
                    if ($field === 'phone' && !empty($value)) {
                        // Loại bỏ khoảng trắng, dấu gạch ngang
                        $value = preg_replace('/[\s\-\(\)]/', '', $value);
                        
                        // Nếu là số (Excel format), chuyển về string và thêm lại số 0 đầu nếu thiếu
                        if (is_numeric($value)) {
                            $value = (string)$value;
                            // Nếu bắt đầu bằng 84, chuyển thành 0
                            if (strpos($value, '84') === 0 && strlen($value) == 11) {
                                $value = '0' . substr($value, 2);
                            }
                            // Nếu thiếu số 0 đầu và có 9 chữ số, thêm 0
                            elseif (strlen($value) == 9 && substr($value, 0, 1) != '0') {
                                $value = '0' . $value;
                            }
                            // Nếu là scientific notation (ví dụ: 9.01E+09), chuyển về số thường
                            if (stripos($value, 'e') !== false) {
                                $value = number_format((float)$value, 0, '', '');
                                // Thêm lại số 0 đầu nếu thiếu
                                if (strlen($value) == 9) {
                                    $value = '0' . $value;
                                }
                            }
                        }
                    }
                    
                    $customerData[$field] = $value;
                }
            }

            // Chỉ thêm nếu có tên (bắt buộc)
            if (!empty($customerData['full_name'])) {
                $customerData['_row_number'] = $rowNum;
                $data[] = $customerData;
            }
        }

        fclose($handle);
        return $data;
    }

    /**
     * Detect CSV delimiter (comma hoặc semicolon)
     * @param string $filePath
     * @return string Delimiter character
     */
    private function detectDelimiter($filePath)
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return ','; // Default to comma
        }

        // Đọc dòng đầu tiên để detect
        $firstLine = fgets($handle);
        fclose($handle);

        if ($firstLine === false) {
            return ','; // Default to comma
        }

        // Đếm số lượng comma và semicolon
        $commaCount = substr_count($firstLine, ',');
        $semicolonCount = substr_count($firstLine, ';');
        $tabCount = substr_count($firstLine, "\t");

        // Chọn delimiter có số lượng nhiều nhất
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            return ';';
        } elseif ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            return "\t";
        } else {
            return ','; // Default to comma
        }
    }

    /**
     * Map headers Excel/CSV với fields database
     */
    private function getFieldMap()
    {
        return [
            'full_name' => ['họ tên', 'ho ten', 'ten', 'name', 'fullname', 'khách hàng', 'khach hang'],
            'phone' => ['sđt', 'sdt', 'phone', 'điện thoại', 'dien thoai', 'tel', 'mobile', 'so dien thoai', 'số điện thoại'],
            'email' => ['email', 'e-mail', 'mail'],
            'date_of_birth' => ['ngày sinh', 'ngay sinh', 'dob', 'birthday', 'sinh ngày'],
            'gender' => ['giới tính', 'gioi tinh', 'gender', 'sex', 'nam/nữ'],
            'age_type' => ['loại', 'loai', 'type', 'age_type', 'tuổi', 'tuoi', 'người lớn', 'trẻ em', 'em bé'],
            'address' => ['địa chỉ', 'dia chi', 'address', 'địa điểm'],
            'id_card' => ['cmnd', 'cccd', 'id card', 'chứng minh'],
            'passport' => ['passport', 'hộ chiếu', 'ho chieu']
        ];
    }
    
    /**
     * Normalize Vietnamese text để so sánh (loại bỏ dấu)
     */
    private function normalizeVietnamese($text)
    {
        $text = mb_strtolower($text, 'UTF-8');
        // Loại bỏ dấu tiếng Việt
        $text = preg_replace('/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/', 'a', $text);
        $text = preg_replace('/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/', 'e', $text);
        $text = preg_replace('/ì|í|ị|ỉ|ĩ/', 'i', $text);
        $text = preg_replace('/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/', 'o', $text);
        $text = preg_replace('/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/', 'u', $text);
        $text = preg_replace('/ỳ|ý|ỵ|ỷ|ỹ/', 'y', $text);
        $text = preg_replace('/đ/', 'd', $text);
        return $text;
    }

    /**
     * Import danh sách khách hàng từ file
     * @return array ['success' => int, 'errors' => array, 'log_id' => int]
     */
    public function importFromFile($filePath, $fileName, $userId)
    {
        try {
            $this->pdo->beginTransaction();

            // Đọc file
            $rows = $this->readFile($filePath);

            if (empty($rows)) {
                throw new Exception("File không có dữ liệu hợp lệ");
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Import từng row
            foreach ($rows as $rowData) {
                try {
                    // Prepare customer data
                    $customerData = [
                        'full_name' => $rowData['full_name'] ?? '',
                        'phone' => $rowData['phone'] ?? '',
                        'email' => !empty($rowData['email']) ? $rowData['email'] : null,
                        'address' => !empty($rowData['address']) ? $rowData['address'] : null,
                        'date_of_birth' => !empty($rowData['date_of_birth']) ? $this->parseDate($rowData['date_of_birth']) : null,
                        'gender' => $this->parseGender($rowData['gender'] ?? ''),
                        'id_card' => !empty($rowData['id_card']) ? $rowData['id_card'] : null,
                        'passport' => !empty($rowData['passport']) ? $rowData['passport'] : null,
                        'created_by' => $userId
                    ];

                    // Validate required fields
                    if (empty($customerData['full_name']) && empty($customerData['phone'])) {
                        throw new Exception("Thiếu tên hoặc SĐT");
                    }

                    // Check duplicate phone
                    if (!empty($customerData['phone'])) {
                        $existing = $this->customerModel->findByPhone($customerData['phone']);
                        if ($existing) {
                            // Skip duplicate, không tính là error
                            continue;
                        }
                    }

                    // Create customer
                    $customerId = $this->customerModel->create($customerData);

                    if ($customerId) {
                        $successCount++;
                    } else {
                        throw new Exception("Không thể tạo khách hàng");
                    }

                } catch (Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'row' => $rowData['_row_number'] ?? 'N/A',
                        'name' => $rowData['full_name'] ?? 'N/A',
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Lưu import log
            $logId = $this->saveImportLog($fileName, $filePath, $userId, count($rows), $successCount, $errorCount, $errors);

            $this->pdo->commit();

            return [
                'success' => $successCount,
                'errors' => $errors,
                'total' => count($rows),
                'log_id' => $logId
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Parse date từ nhiều format
     */
    private function parseDate($dateStr)
    {
        if (empty($dateStr)) {
            return null;
        }

        // Try common formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d', 'd.m.Y'];

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateStr);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        // Try strtotime
        $timestamp = strtotime($dateStr);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Parse gender
     */
    private function parseGender($genderStr)
    {
        if (empty($genderStr)) {
            return null;
        }

        $genderStr = strtolower(trim($genderStr));

        if (in_array($genderStr, ['nam', 'male', 'm', '1'])) {
            return 'male';
        } elseif (in_array($genderStr, ['nữ', 'nu', 'female', 'f', '2'])) {
            return 'female';
        }

        return 'other';
    }

    /**
     * Lưu import log
     */
    private function saveImportLog($fileName, $filePath, $userId, $totalRows, $successCount, $errorCount, $errors)
    {
        $sql = "INSERT INTO customer_import_logs 
                (file_name, file_path, imported_by, total_rows, success_count, error_count, error_details)
                VALUES (:file_name, :file_path, :imported_by, :total_rows, :success_count, :error_count, :error_details)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'imported_by' => $userId,
            'total_rows' => $totalRows,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'error_details' => json_encode($errors, JSON_UNESCAPED_UNICODE)
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Lấy danh sách import logs
     */
    public function getImportLogs($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT il.*, u.full_name as importer_name
                FROM customer_import_logs il
                LEFT JOIN users u ON il.imported_by = u.id
                ORDER BY il.created_at DESC
                LIMIT $limit OFFSET $offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

