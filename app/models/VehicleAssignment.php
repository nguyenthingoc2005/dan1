<?php
/**
 * ==============================================================================
 * VEHICLE ASSIGNMENT MODEL
 * ==============================================================================
 * 
 * Quản lý phân công xe và tài xế cho tour schedule
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class VehicleAssignment
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Tạo phân công xe và tài xế
     */
    public function create($data)
    {
        try {
            $this->pdo->beginTransaction();

            // Tạo vehicle_assignment
            $sql = "
                INSERT INTO vehicle_assignments (
                    tour_schedule_id, vehicle_id, driver_id,
                    assignment_date, start_date, start_time, end_date, end_time,
                    pickup_location, return_location,
                    estimated_distance, estimated_fuel_cost,
                    driver_salary, total_cost,
                    status, assigned_by, notes
                ) VALUES (
                    :tour_schedule_id, :vehicle_id, :driver_id,
                    CURDATE(), :start_date, :start_time, :end_date, :end_time,
                    :pickup_location, :return_location,
                    :estimated_distance, :estimated_fuel_cost,
                    :driver_salary, :total_cost,
                    :status, :assigned_by, :notes
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'tour_schedule_id' => $data['tour_schedule_id'],
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_date' => $data['start_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_date' => $data['end_date'],
                'end_time' => $data['end_time'] ?? null,
                'pickup_location' => $data['pickup_location'] ?? null,
                'return_location' => $data['return_location'] ?? null,
                'estimated_distance' => $data['estimated_distance'] ?? 0,
                'estimated_fuel_cost' => $data['estimated_fuel_cost'] ?? 0,
                'driver_salary' => $data['driver_salary'] ?? 0,
                'total_cost' => ($data['driver_salary'] ?? 0) + ($data['estimated_fuel_cost'] ?? 0),
                'status' => $data['status'] ?? 'assigned',
                'assigned_by' => $data['assigned_by'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);

            $assignment_id = $this->pdo->lastInsertId();

            // Lưu lịch sử thay đổi (lần đầu gán)
            $this->logVehicleChange(
                $data['tour_schedule_id'],
                null, // old_vehicle_id
                $data['vehicle_id'], // new_vehicle_id
                null, // old_driver_id
                $data['driver_id'], // new_driver_id
                $data['assigned_by'] ?? null,
                null, // reason
                'Gán xe và tài xế lần đầu' // notes
            );

            // Tạo driver_schedule (tránh trùng lịch)
            $schedule_sql = "
                INSERT INTO driver_schedules (
                    driver_id, tour_schedule_id, vehicle_assignment_id,
                    schedule_date, start_time, end_time, status
                ) VALUES (
                    :driver_id, :tour_schedule_id, :vehicle_assignment_id,
                    :schedule_date, :start_time, :end_time, 'scheduled'
                )
            ";

            // Tạo schedule cho từng ngày
            $start = new DateTime($data['start_date']);
            $end = new DateTime($data['end_date']);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end->modify('+1 day'));

            $schedule_stmt = $this->pdo->prepare($schedule_sql);
            foreach ($period as $date) {
                $schedule_stmt->execute([
                    'driver_id' => $data['driver_id'],
                    'tour_schedule_id' => $data['tour_schedule_id'],
                    'vehicle_assignment_id' => $assignment_id,
                    'schedule_date' => $date->format('Y-m-d'),
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null
                ]);
            }

            $this->pdo->commit();
            return $assignment_id;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            logError("VehicleAssignment::create() Error: " . $e->getMessage());
            throw new Exception("Không thể tạo phân công: " . $e->getMessage());
        }
    }

    /**
     * Lấy phân công theo tour_schedule_id
     */
    public function getByScheduleId($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    va.*,
                    v.vehicle_code,
                    v.vehicle_type,
                    v.license_plate,
                    v.capacity,
                    d.driver_code,
                    d.full_name AS driver_name,
                    d.phone AS driver_phone,
                    d.license_type,
                    u.full_name AS assigned_by_name
                FROM vehicle_assignments va
                JOIN vehicles v ON va.vehicle_id = v.id
                JOIN drivers d ON va.driver_id = d.id
                LEFT JOIN users u ON va.assigned_by = u.id
                WHERE va.tour_schedule_id = :schedule_id
                ORDER BY va.created_at DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("VehicleAssignment::getByScheduleId() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tự động tính phụ cấp tài xế từ tour_allowance_rules
     */
    public function calculateDriverSalary($tour_schedule_id)
    {
        try {
            // Lấy thông tin tour schedule và tour
            $schedule_sql = "
                SELECT 
                    ts.start_date, ts.end_date,
                    t.tour_type, t.duration_days,
                    COUNT(DISTINCT b.id) AS booking_count,
                    SUM(b.adult_count + b.child_count + b.infant_count) AS participant_count
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
                    AND b.payment_status = 'paid'
                WHERE ts.id = :schedule_id
                GROUP BY ts.id
            ";

            $schedule_stmt = $this->pdo->prepare($schedule_sql);
            $schedule_stmt->execute(['schedule_id' => $tour_schedule_id]);
            $schedule = $schedule_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$schedule) {
                return 0;
            }

            // Tính số ngày
            $start = new DateTime($schedule['start_date']);
            $end = new DateTime($schedule['end_date']);
            $duration_days = $start->diff($end)->days + 1;

            // Tìm rule phù hợp (nếu bảng tồn tại)
            $driver_allowance = 0;
            try {
                // Kiểm tra xem bảng có tồn tại không
                $checkTable = $this->pdo->query("SHOW TABLES LIKE 'tour_allowance_rules'");
                if ($checkTable->rowCount() > 0) {
                    // Bảng tồn tại, tìm rule
                    $rule_sql = "
                        SELECT driver_allowance
                        FROM tour_allowance_rules
                        WHERE tour_type = :tour_type
                          AND (duration_days_min IS NULL OR :duration_days >= duration_days_min)
                          AND (duration_days_max IS NULL OR :duration_days <= duration_days_max)
                          AND (participant_min IS NULL OR :participant_count >= participant_min)
                          AND (participant_max IS NULL OR :participant_count <= participant_max)
                          AND status = 'active'
                        ORDER BY priority DESC
                        LIMIT 1
                    ";

                    $rule_stmt = $this->pdo->prepare($rule_sql);
                    $rule_stmt->execute([
                        'tour_type' => $schedule['tour_type'] ?? 'domestic',
                        'duration_days' => $duration_days,
                        'participant_count' => $schedule['participant_count'] ?? 0
                    ]);

                    $rule = $rule_stmt->fetch(PDO::FETCH_ASSOC);
                    $driver_allowance = $rule ? (float)$rule['driver_allowance'] : 0;
                }
            } catch (PDOException $e) {
                // Bảng không tồn tại, bỏ qua
                error_log("tour_allowance_rules table not found, using default calculation");
            }
            
            // Nếu không có rule, trả về 0 (hoặc có thể dùng giá trị mặc định)
            return $driver_allowance;

        } catch (PDOException $e) {
            logError("VehicleAssignment::calculateDriverSalary() Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cập nhật phân công
     */
    public function update($id, $data)
    {
        try {
            // Ghi log thay đổi
            $old_assignment = $this->findById($id);
            if ($old_assignment) {
                $this->logChange($id, 'updated', $old_assignment, $data);
                
                // Lưu lịch sử thay đổi xe/tài xế nếu có thay đổi
                $old_vehicle_id = $old_assignment['vehicle_id'] ?? null;
                $new_vehicle_id = $data['vehicle_id'] ?? null;
                $old_driver_id = $old_assignment['driver_id'] ?? null;
                $new_driver_id = $data['driver_id'] ?? null;
                
                if ($old_vehicle_id != $new_vehicle_id || $old_driver_id != $new_driver_id) {
                    $this->logVehicleChange(
                        $old_assignment['tour_schedule_id'],
                        $old_vehicle_id,
                        $new_vehicle_id,
                        $old_driver_id,
                        $new_driver_id,
                        $_SESSION['user_id'] ?? null,
                        $data['reason'] ?? null,
                        $data['notes'] ?? null
                    );
                }
            }

            $sql = "
                UPDATE vehicle_assignments SET
                    vehicle_id = :vehicle_id,
                    driver_id = :driver_id,
                    start_date = :start_date,
                    start_time = :start_time,
                    end_date = :end_date,
                    end_time = :end_time,
                    pickup_location = :pickup_location,
                    return_location = :return_location,
                    estimated_distance = :estimated_distance,
                    estimated_fuel_cost = :estimated_fuel_cost,
                    driver_salary = :driver_salary,
                    total_cost = :total_cost,
                    notes = :notes
                WHERE id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'],
                'start_date' => $data['start_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_date' => $data['end_date'],
                'end_time' => $data['end_time'] ?? null,
                'pickup_location' => $data['pickup_location'] ?? null,
                'return_location' => $data['return_location'] ?? null,
                'estimated_distance' => $data['estimated_distance'] ?? 0,
                'estimated_fuel_cost' => $data['estimated_fuel_cost'] ?? 0,
                'driver_salary' => $data['driver_salary'] ?? 0,
                'total_cost' => ($data['driver_salary'] ?? 0) + ($data['estimated_fuel_cost'] ?? 0),
                'notes' => $data['notes'] ?? null
            ]);

            return true;

        } catch (PDOException $e) {
            logError("VehicleAssignment::update() Error: " . $e->getMessage());
            throw new Exception("Không thể cập nhật phân công: " . $e->getMessage());
        }
    }

    /**
     * Tìm phân công theo ID
     */
    public function findById($id)
    {
        try {
            $sql = "
                SELECT va.*,
                    v.vehicle_code, v.vehicle_type, v.license_plate,
                    d.driver_code, d.full_name AS driver_name
                FROM vehicle_assignments va
                JOIN vehicles v ON va.vehicle_id = v.id
                JOIN drivers d ON va.driver_id = d.id
                WHERE va.id = :id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (PDOException $e) {
            logError("VehicleAssignment::findById() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ghi log thay đổi (cho vehicle_assignment_history - giữ lại để tương thích)
     */
    private function logChange($assignment_id, $action, $old_values, $new_values)
    {
        try {
            $sql = "
                INSERT INTO vehicle_assignment_history (
                    vehicle_assignment_id, action, old_values, new_values, changed_by, reason
                ) VALUES (
                    :assignment_id, :action, :old_values, :new_values, :changed_by, :reason
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'assignment_id' => $assignment_id,
                'action' => $action,
                'old_values' => json_encode($old_values),
                'new_values' => json_encode($new_values),
                'changed_by' => $_SESSION['user_id'] ?? null,
                'reason' => null
            ]);

        } catch (PDOException $e) {
            logError("VehicleAssignment::logChange() Error: " . $e->getMessage());
        }
    }

    /**
     * Lưu lịch sử thay đổi xe/tài xế vào schedule_vehicle_history
     */
    public function logVehicleChange($schedule_id, $old_vehicle_id, $new_vehicle_id, $old_driver_id, $new_driver_id, $changed_by, $reason = null, $notes = null)
    {
        try {
            // Xác định loại thay đổi
            $vehicle_changed = ($old_vehicle_id != $new_vehicle_id);
            $driver_changed = ($old_driver_id != $new_driver_id);
            
            if (!$vehicle_changed && !$driver_changed) {
                return; // Không có thay đổi
            }

            $change_type = 'both';
            if ($vehicle_changed && !$driver_changed) {
                $change_type = 'vehicle';
            } elseif (!$vehicle_changed && $driver_changed) {
                $change_type = 'driver';
            }

            // Lấy thông tin xe cũ
            $old_vehicle_code = null;
            $old_vehicle_plate = null;
            if ($old_vehicle_id) {
                $stmt = $this->pdo->prepare("SELECT vehicle_code, license_plate FROM vehicles WHERE id = :id");
                $stmt->execute(['id' => $old_vehicle_id]);
                $old_vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($old_vehicle) {
                    $old_vehicle_code = $old_vehicle['vehicle_code'];
                    $old_vehicle_plate = $old_vehicle['license_plate'];
                }
            }

            // Lấy thông tin xe mới
            $new_vehicle_code = null;
            $new_vehicle_plate = null;
            if ($new_vehicle_id) {
                $stmt = $this->pdo->prepare("SELECT vehicle_code, license_plate FROM vehicles WHERE id = :id");
                $stmt->execute(['id' => $new_vehicle_id]);
                $new_vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($new_vehicle) {
                    $new_vehicle_code = $new_vehicle['vehicle_code'];
                    $new_vehicle_plate = $new_vehicle['license_plate'];
                }
            }

            // Lấy thông tin tài xế cũ
            $old_driver_name = null;
            if ($old_driver_id) {
                $stmt = $this->pdo->prepare("SELECT full_name FROM drivers WHERE id = :id");
                $stmt->execute(['id' => $old_driver_id]);
                $old_driver = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($old_driver) {
                    $old_driver_name = $old_driver['full_name'];
                }
            }

            // Lấy thông tin tài xế mới
            $new_driver_name = null;
            if ($new_driver_id) {
                $stmt = $this->pdo->prepare("SELECT full_name FROM drivers WHERE id = :id");
                $stmt->execute(['id' => $new_driver_id]);
                $new_driver = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($new_driver) {
                    $new_driver_name = $new_driver['full_name'];
                }
            }

            // Kiểm tra xem bảng có tồn tại không
            $checkTable = $this->pdo->query("SHOW TABLES LIKE 'schedule_vehicle_history'");
            if ($checkTable->rowCount() == 0) {
                // Bảng chưa tồn tại, bỏ qua
                return;
            }

            $sql = "
                INSERT INTO schedule_vehicle_history (
                    schedule_id, 
                    old_vehicle_id, new_vehicle_id, 
                    old_vehicle_code, new_vehicle_code,
                    old_vehicle_plate, new_vehicle_plate,
                    old_driver_id, new_driver_id,
                    old_driver_name, new_driver_name,
                    change_type, changed_by, reason, notes
                ) VALUES (
                    :schedule_id,
                    :old_vehicle_id, :new_vehicle_id,
                    :old_vehicle_code, :new_vehicle_code,
                    :old_vehicle_plate, :new_vehicle_plate,
                    :old_driver_id, :new_driver_id,
                    :old_driver_name, :new_driver_name,
                    :change_type, :changed_by, :reason, :notes
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'schedule_id' => $schedule_id,
                'old_vehicle_id' => $old_vehicle_id,
                'new_vehicle_id' => $new_vehicle_id,
                'old_vehicle_code' => $old_vehicle_code,
                'new_vehicle_code' => $new_vehicle_code,
                'old_vehicle_plate' => $old_vehicle_plate,
                'new_vehicle_plate' => $new_vehicle_plate,
                'old_driver_id' => $old_driver_id,
                'new_driver_id' => $new_driver_id,
                'old_driver_name' => $old_driver_name,
                'new_driver_name' => $new_driver_name,
                'change_type' => $change_type,
                'changed_by' => $changed_by,
                'reason' => $reason,
                'notes' => $notes
            ]);

        } catch (PDOException $e) {
            logError("VehicleAssignment::logVehicleChange() Error: " . $e->getMessage());
        }
    }

    /**
     * Lấy lịch sử thay đổi xe/tài xế của schedule
     */
    public function getVehicleHistory($schedule_id)
    {
        try {
            // Kiểm tra xem bảng có tồn tại không
            $checkTable = $this->pdo->query("SHOW TABLES LIKE 'schedule_vehicle_history'");
            if ($checkTable->rowCount() == 0) {
                return [];
            }

            $sql = "
                SELECT 
                    svh.*,
                    u.full_name AS changed_by_name
                FROM schedule_vehicle_history svh
                LEFT JOIN users u ON svh.changed_by = u.id
                WHERE svh.schedule_id = :schedule_id
                ORDER BY svh.created_at DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("VehicleAssignment::getVehicleHistory() Error: " . $e->getMessage());
            return [];
        }
    }
}

