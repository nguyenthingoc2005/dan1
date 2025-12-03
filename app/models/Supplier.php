<?php
/**
 * ==============================================================================
 * SUPPLIER MODEL
 * ==============================================================================
 * 
 * Quản lý nhà cung cấp với:
 * - Auto-generate supplier_code: SUP-YYYYMMDD-XXX
 * - Validation: email, phone, tax_code, contract dates
 * - FK constraint checking
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class Supplier
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả suppliers
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['search'])) {
                $where_conditions[] = "(company_name LIKE :search OR supplier_code LIKE :search OR email LIKE :search OR phone LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM suppliers {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT id, supplier_code, company_name, contact_person, email, phone, 
                       address, tax_code, status, created_at
                FROM suppliers
                {$where_clause}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll();

            return [
                'data' => $data,
                'total' => $total,
                'pages' => ceil($total / $per_page),
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            error_log("Supplier::getAll() Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
        }
    }

    /**
     * Tìm supplier theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM suppliers WHERE id = :id LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Supplier::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm supplier theo email
     */
    public function findByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, company_name, email FROM suppliers WHERE email = :email LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Supplier::findByEmail() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm supplier theo tax code
     */
    public function findByTaxCode($tax_code)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, company_name, tax_code FROM suppliers WHERE tax_code = :tax_code LIMIT 1
            ");
            $stmt->execute(['tax_code' => $tax_code]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("Supplier::findByTaxCode() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Auto-generate supplier code: SUP-YYYYMMDD-XXX
     */
    public function generateSupplierCode()
    {
        try {
            $date = date('Ymd'); // 20241202
            $prefix = "SUP-{$date}-";

            // Get latest code today
            $stmt = $this->pdo->prepare("
                SELECT supplier_code 
                FROM suppliers 
                WHERE supplier_code LIKE :pattern 
                ORDER BY supplier_code DESC 
                LIMIT 1
            ");
            $stmt->execute(['pattern' => $prefix . '%']);
            $latest = $stmt->fetch();

            if ($latest) {
                // Extract number: SUP-20241202-001 -> 001
                $num = (int) substr($latest['supplier_code'], -3);
                $num++;
            } else {
                $num = 1;
            }

            return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);

        } catch (PDOException $e) {
            error_log("Supplier::generateSupplierCode() Error: " . $e->getMessage());
            return 'SUP-' . date('Ymd') . '-001';
        }
    }

    /**
     * Tạo supplier mới
     */
    public function create($data)
    {
        try {
            // Auto-generate supplier code if not provided
            if (empty($data['supplier_code'])) {
                $data['supplier_code'] = $this->generateSupplierCode();
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO suppliers (
                    supplier_code, company_name, contact_person, email, phone,
                    address, tax_code, bank_name, bank_account, bank_holder,
                    contract_start, contract_end, payment_terms, notes, status, created_by
                ) VALUES (
                    :supplier_code, :company_name, :contact_person, :email, :phone,
                    :address, :tax_code, :bank_name, :bank_account, :bank_holder,
                    :contract_start, :contract_end, :payment_terms, :notes, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'supplier_code' => $data['supplier_code'],
                'company_name' => $data['company_name'],
                'contact_person' => $data['contact_person'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'tax_code' => $data['tax_code'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account' => $data['bank_account'] ?? null,
                'bank_holder' => $data['bank_holder'] ?? null,
                'contract_start' => $data['contract_start'] ?? null,
                'contract_end' => $data['contract_end'] ?? null,
                'payment_terms' => $data['payment_terms'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Supplier::create() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật supplier
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = [
                'company_name',
                'contact_person',
                'email',
                'phone',
                'address',
                'tax_code',
                'bank_name',
                'bank_account',
                'bank_holder',
                'contract_start',
                'contract_end',
                'payment_terms',
                'notes',
                'status'
            ];

            $set_parts = [];
            $params = ['id' => $id];

            foreach ($allowed_fields as $field) {
                if (isset($data[$field])) {
                    $set_parts[] = "{$field} = :{$field}";
                    $params[$field] = $data[$field];
                }
            }

            // Add updated_by
            $set_parts[] = "updated_by = :updated_by";
            $params['updated_by'] = get_user_id();

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE suppliers
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Supplier::update() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa supplier (soft delete với FK check)
     */
    public function delete($id)
    {
        try {
            // Check if being used by services
            $check_services = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM services WHERE supplier_id = :id
            ");
            $check_services->execute(['id' => $id]);
            $services_count = $check_services->fetch()['count'];

            // Check if being used in booking_services
            $check_booking = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM booking_services WHERE supplier_id = :id
            ");
            $check_booking->execute(['id' => $id]);
            $booking_count = $check_booking->fetch()['count'];

            if ($services_count > 0 || $booking_count > 0) {
                $message = "Không thể xóa nhà cung cấp";
                $parts = [];
                if ($services_count > 0) {
                    $parts[] = "đang cung cấp {$services_count} dịch vụ";
                }
                if ($booking_count > 0) {
                    $parts[] = "đang được sử dụng trong {$booking_count} booking";
                }
                $message .= " " . implode(" và ", $parts) . ".";
                throw new Exception($message);
            }

            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE suppliers
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (Exception $e) {
            error_log("Supplier::delete() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $supplier = $this->findById($id);
            if (!$supplier)
                return false;

            $new_status = ($supplier['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("Supplier::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy suppliers cho dropdown
     */
    public function getForDropdown()
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, company_name, supplier_code
                FROM suppliers
                WHERE status = 'active'
                ORDER BY company_name ASC
            ");

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Supplier::getForDropdown() Error: " . $e->getMessage());
            return [];
        }
    }
}
