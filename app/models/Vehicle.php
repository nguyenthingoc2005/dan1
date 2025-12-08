<?php
/**
 * ==============================================================================
 * VEHICLE MODEL
 * ==============================================================================
 * 
 * Quản lý xe công ty
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class Vehicle
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả vehicles (có phân trang & lọc)
     */
    public function getAll($filters = [], $page = 1, $per_page = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Filter by Status
            if (!empty($filters['status'])) {
                $where_conditions[] = "status = :status";
                $params['status'] = $filters['status'];
            }

            // Filter by Vehicle Type
            if (!empty($filters['vehicle_type'])) {
                $where_conditions[] = "vehicle_type = :vehicle_type";
                $params['vehicle_type'] = $filters['vehicle_type'];
            }

            // Search by Code, License Plate
            if (!empty($filters['search'])) {
                $where_conditions[] = "(vehicle_code LIKE :search OR license_plate LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }

            $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

            // Count total
            $count_sql = "SELECT COUNT(*) as total FROM vehicles {$where_clause}";
            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $per_page;
            $params['offset'] = $offset;
            $params['limit'] = $per_page;

            $data_sql = "
                SELECT * 
                FROM vehicles 
                {$where_clause}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ";

            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            $total_pages = ceil($total / $per_page);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => $total_pages,
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            logError("Vehicle::getAll() Error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1
            ];
        }
    }

    /**
     * Tìm vehicle theo ID
     */
    public function findById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vehicles WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            logError("Vehicle::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách xe có sẵn (không trùng lịch)
     * 
     * @param string $start_date Ngày bắt đầu
     * @param string $end_date Ngày kết thúc
     * @param int $capacity_min Số chỗ tối thiểu cần
     * @return array
     */
    public function getAvailable($start_date, $end_date, $capacity_min = 0)
    {
        try {
            // Kiểm tra xem bảng vehicle_maintenance có tồn tại không
            $hasMaintenanceTable = false;
            try {
                $checkMaintenance = $this->pdo->query("SHOW TABLES LIKE 'vehicle_maintenance'");
                $hasMaintenanceTable = $checkMaintenance->rowCount() > 0;
            } catch (PDOException $e) {
                // Bảng không tồn tại, bỏ qua
                $hasMaintenanceTable = false;
            }

            // Query chính - lấy xe không bị trùng lịch
            // Sử dụng LEFT JOIN thay vì NOT IN để tránh lỗi parameter binding
            // Phải dùng parameter name khác nhau cho mỗi lần sử dụng vì PDO không cho phép dùng lại
            $sql = "
                SELECT DISTINCT v.*
                FROM vehicles v
                LEFT JOIN vehicle_assignments va ON v.id = va.vehicle_id
                    AND va.status IN ('assigned', 'confirmed', 'in_use')
                    AND va.start_date <= :end_date_va 
                    AND va.end_date >= :start_date_va";

            // Chỉ thêm JOIN với maintenance nếu bảng tồn tại
            if ($hasMaintenanceTable) {
                $sql .= "
                LEFT JOIN vehicle_maintenance vm ON v.id = vm.vehicle_id
                    AND vm.status IN ('scheduled', 'in_progress')
                    AND vm.maintenance_date <= :end_date_vm 
                    AND (vm.next_maintenance_date IS NULL OR vm.next_maintenance_date >= :start_date_vm)";
            }

            $sql .= "
                WHERE v.status = 'active'
                  AND v.capacity >= :capacity_min
                  AND va.id IS NULL";

            if ($hasMaintenanceTable) {
                $sql .= " AND vm.id IS NULL";
            }

            $sql .= " ORDER BY v.capacity ASC";

            // Chuẩn bị parameters
            $params = [
                'start_date_va' => $start_date,
                'end_date_va' => $end_date,
                'capacity_min' => $capacity_min
            ];

            // Thêm parameters cho maintenance nếu có
            if ($hasMaintenanceTable) {
                $params['start_date_vm'] = $start_date;
                $params['end_date_vm'] = $end_date;
            }

            // Debug log
            error_log("Vehicle::getAvailable() - SQL: " . $sql);
            error_log("Vehicle::getAvailable() - Params: " . print_r($params, true));

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug log
            error_log("Vehicle::getAvailable() - Found " . count($result) . " vehicles");

            return $result;

        } catch (PDOException $e) {
            logError("Vehicle::getAvailable() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tạo vehicle mới
     */
    public function create($data)
    {
        try {
            $sql = "
                INSERT INTO vehicles (
                    vehicle_code, vehicle_type, license_plate, capacity, 
                    status, notes
                ) VALUES (
                    :vehicle_code, :vehicle_type, :license_plate, :capacity,
                    :status, :notes
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'vehicle_code' => $data['vehicle_code'] ?? null,
                'vehicle_type' => $data['vehicle_type'],
                'license_plate' => $data['license_plate'],
                'capacity' => $data['capacity'],
                'status' => $data['status'] ?? 'active',
                'notes' => $data['notes'] ?? null
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            logError("Vehicle::create() Error: " . $e->getMessage());
            throw new Exception("Không thể tạo xe mới: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật vehicle
     */
    public function update($id, $data)
    {
        try {
            $sql = "
                UPDATE vehicles SET
                    vehicle_code = :vehicle_code,
                    vehicle_type = :vehicle_type,
                    license_plate = :license_plate,
                    capacity = :capacity,
                    status = :status,
                    notes = :notes
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'vehicle_code' => $data['vehicle_code'] ?? null,
                'vehicle_type' => $data['vehicle_type'],
                'license_plate' => $data['license_plate'],
                'capacity' => $data['capacity'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null
            ]);

            return true;

        } catch (PDOException $e) {
            logError("Vehicle::update() Error: " . $e->getMessage());
            throw new Exception("Không thể cập nhật xe: " . $e->getMessage());
        }
    }

    /**
     * Xóa vehicle (soft delete - cập nhật status)
     */
    public function delete($id)
    {
        try {
            $sql = "UPDATE vehicles SET status = 'inactive' WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            logError("Vehicle::delete() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra vehicle_code đã tồn tại chưa
     */
    public function isCodeExists($code, $excludeId = null)
    {
        $sql = "SELECT id FROM vehicles WHERE vehicle_code = :code";
        $params = ['code' => $code];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    /**
     * Kiểm tra license_plate đã tồn tại chưa
     */
    public function isLicensePlateExists($license_plate, $excludeId = null)
    {
        $sql = "SELECT id FROM vehicles WHERE license_plate = :license_plate";
        $params = ['license_plate' => $license_plate];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
}

