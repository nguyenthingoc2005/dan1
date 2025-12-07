<?php
/**
 * ==============================================================================
 * SERVICE PRICE MODEL
 * ==============================================================================
 * 
 * Quản lý Giá dịch vụ theo địa điểm và mùa (Service Prices)
 * 
 * Relationships:
 * - service_id → services (REQUIRED)
 * - destination_id → destinations (Optional)
 * - province_id → provinces (Optional)
 * 
 * Logic: Mỗi service có thể có nhiều giá cho các địa điểm khác nhau
 * và các khoảng thời gian (mùa) khác nhau
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class ServicePrice
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy giá cho service tại một ngày cụ thể
     * 
     * @param int $service_id
     * @param string|null $date (Y-m-d format, null = today)
     * @return array|null
     */
    public function getPriceForService($service_id, $date = null)
    {
        try {
            if ($date === null) {
                $date = date('Y-m-d');
            }

            $sql = "
                SELECT *
                FROM service_prices
                WHERE service_id = :service_id
                  AND status = 'active'
                  AND (
                    (start_date IS NULL OR start_date <= :date)
                    AND (end_date IS NULL OR end_date >= :date)
                  )
                ORDER BY created_at DESC
                LIMIT 1
            ";
            $params = [
                'service_id' => $service_id,
                'date' => $date
            ];

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("ServicePrice::getPriceForService() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tất cả giá của một service
     */
    public function getByService($service_id, $filters = [])
    {
        try {
            $where_conditions = ["service_id = :service_id"];
            $params = ['service_id' => $service_id];

            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            if (!empty($filters['date'])) {
                $where_conditions[] = "(
                    (start_date IS NULL OR start_date <= :date)
                    AND (end_date IS NULL OR end_date >= :date)
                )";
                $params['date'] = $filters['date'];
            }

            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

            $sql = "
                SELECT sp.*
                FROM service_prices sp
                {$where_clause}
                ORDER BY sp.start_date ASC, sp.created_at DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();

        } catch (PDOException $e) {
            error_log("ServicePrice::getByService() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm service price theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    sp.*,
                    s.name as service_name
                FROM service_prices sp
                LEFT JOIN services s ON sp.service_id = s.id
                WHERE sp.id = :id
                LIMIT 1
            ");

            $stmt->execute(['id' => $id]);
            return $stmt->fetch() ?: null;

        } catch (PDOException $e) {
            error_log("ServicePrice::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo service price mới
     */
    public function create($data)
    {
        try {
            // Validate: service_id là bắt buộc
            if (empty($data['service_id'])) {
                throw new Exception("Thiếu service_id.");
            }

            // Validate: end_date phải >= start_date
            $start_date = $data['start_date'] ?? $data['valid_from'] ?? null;
            $end_date = $data['end_date'] ?? $data['valid_to'] ?? null;

            if (!empty($start_date) && !empty($end_date)) {
                if (strtotime($end_date) < strtotime($start_date)) {
                    throw new Exception("Ngày kết thúc phải sau ngày bắt đầu.");
                }
            }

            // Check overlap với giá hiện có (nếu có thời gian cụ thể)
            // Cho phép nhiều giá cùng loại nhưng khác thời gian
            if ($start_date || $end_date) {
                $overlap = $this->checkPriceOverlap(
                    $data['service_id'],
                    $start_date,
                    $end_date
                );

                if ($overlap) {
                    throw new Exception("Đã có giá cho dịch vụ này trong khoảng thời gian này. Vui lòng kiểm tra lại.");
                }
            }

            $stmt = $this->pdo->prepare("
                INSERT INTO service_prices (
                    service_id, unit_price, start_date, end_date,
                    price_type, notes, status, created_by
                ) VALUES (
                    :service_id, :unit_price, :start_date, :end_date,
                    :price_type, :notes, :status, :created_by
                )
            ");

            $success = $stmt->execute([
                'service_id' => $data['service_id'],
                'unit_price' => $data['unit_price'],
                'start_date' => $data['valid_from'] ?? $data['start_date'] ?? null,
                'end_date' => $data['valid_to'] ?? $data['end_date'] ?? null,
                'price_type' => $data['price_type'] ?? 'standard',
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => get_user_id()
            ]);

            return $success ? $this->pdo->lastInsertId() : false;

        } catch (Exception $e) {
            error_log("ServicePrice::create() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kiểm tra giá có bị trùng khoảng thời gian không
     */
    private function checkPriceOverlap($service_id, $start_date, $end_date)
    {
        try {
            $sql = "
                SELECT COUNT(*) as count
                FROM service_prices
                WHERE service_id = :service_id
                  AND status = 'active'
            ";
            $params = ['service_id' => $service_id];

            // Check overlap
            if ($start_date && $end_date) {
                $sql .= " AND (
                    (start_date IS NULL OR start_date <= :end_date)
                    AND (end_date IS NULL OR end_date >= :start_date)
                )";
                $params['start_date'] = $start_date;
                $params['end_date'] = $end_date;
            } elseif ($start_date) {
                $sql .= " AND (end_date IS NULL OR end_date >= :start_date)";
                $params['start_date'] = $start_date;
            } elseif ($end_date) {
                $sql .= " AND (start_date IS NULL OR start_date <= :end_date)";
                $params['end_date'] = $end_date;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();

            return ($result['count'] ?? 0) > 0;

        } catch (PDOException $e) {
            error_log("ServicePrice::checkPriceOverlap() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật service price
     */
    public function update($id, $data)
    {
        try {
            $allowed_fields = [
                'unit_price',
                'start_date',
                'end_date',
                'price_type',
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

            // Support both valid_from/valid_to and start_date/end_date
            if (isset($data['valid_from']) && !isset($data['start_date'])) {
                $set_parts[] = "start_date = :start_date";
                $params['start_date'] = $data['valid_from'];
            }
            if (isset($data['valid_to']) && !isset($data['end_date'])) {
                $set_parts[] = "end_date = :end_date";
                $params['end_date'] = $data['valid_to'];
            }

            // Validate dates
            $start_date = $data['start_date'] ?? $data['valid_from'] ?? null;
            $end_date = $data['end_date'] ?? $data['valid_to'] ?? null;
            if ($start_date && $end_date) {
                if (strtotime($end_date) < strtotime($start_date)) {
                    throw new Exception("Ngày kết thúc phải sau ngày bắt đầu.");
                }
            }

            if (empty($set_parts))
                return false;

            $set_clause = implode(', ', $set_parts);

            $stmt = $this->pdo->prepare("
                UPDATE service_prices
                SET {$set_clause}, updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute($params);

        } catch (Exception $e) {
            error_log("ServicePrice::update() Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa service price
     */
    public function delete($id)
    {
        try {
            // Soft delete
            $stmt = $this->pdo->prepare("
                UPDATE service_prices
                SET status = 'inactive', updated_at = NOW()
                WHERE id = :id
            ");

            return $stmt->execute(['id' => $id]);

        } catch (PDOException $e) {
            error_log("ServicePrice::delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        try {
            $price = $this->findById($id);
            if (!$price)
                return false;

            $new_status = ($price['status'] == 'active') ? 'inactive' : 'active';

            return $this->update($id, ['status' => $new_status]);

        } catch (PDOException $e) {
            error_log("ServicePrice::toggleStatus() Error: " . $e->getMessage());
            return false;
        }
    }
}

