<?php
/**
 * ==============================================================================
 * TOUR OPERATIONS MODEL
 * ==============================================================================
 * 
 * Quản lý tour đã chốt (sau deadline booking, có đủ booking đã thanh toán)
 * Logic: CURDATE() >= (start_date - booking_deadline_days)
 * 
 * @version 1.0
 * @date 2024-12-XX
 * ==============================================================================
 */

class TourOperations
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách tour đã chốt (có đủ booking đã thanh toán)
     * Điều kiện: Đã qua deadline booking + có đủ booking đã thanh toán
     */
    public function getReadyForOperations($filters = [], $page = 1, $limit = 20)
    {
        try {
            $where_conditions = [];
            $params = [];

            // Điều kiện hiển thị: Chỉ cần đủ số người tối thiểu
            // Không cần điều kiện về deadline hoặc status ở đây
            // Điều kiện đủ số người sẽ được kiểm tra ở HAVING clause

            // Filter by Tour
            if (!empty($filters['tour_id'])) {
                $where_conditions[] = "ts.tour_id = :tour_id";
                $params['tour_id'] = $filters['tour_id'];
            }

            // Filter by Start Date
            if (!empty($filters['start_date_from'])) {
                $where_conditions[] = "ts.start_date >= :start_date_from";
                $params['start_date_from'] = $filters['start_date_from'];
            }

            if (!empty($filters['start_date_to'])) {
                $where_conditions[] = "ts.start_date <= :start_date_to";
                $params['start_date_to'] = $filters['start_date_to'];
            }

            // Filter by Status
            if (!empty($filters['status'])) {
                $where_conditions[] = "ts.status = :status";
                $params['status'] = $filters['status'];
            }

            // Filter by Guide (đã gán HDV chưa)
            if (isset($filters['has_guide'])) {
                if ($filters['has_guide'] == 'yes') {
                    $where_conditions[] = "ts.guide_id IS NOT NULL";
                } elseif ($filters['has_guide'] == 'no') {
                    $where_conditions[] = "ts.guide_id IS NULL";
                }
            }

            // Filter by Vehicle (đã gán xe chưa)
            if (isset($filters['has_vehicle'])) {
                if ($filters['has_vehicle'] == 'yes') {
                    $where_conditions[] = "EXISTS (SELECT 1 FROM vehicle_assignments va WHERE va.tour_schedule_id = ts.id)";
                } elseif ($filters['has_vehicle'] == 'no') {
                    $where_conditions[] = "NOT EXISTS (SELECT 1 FROM vehicle_assignments va WHERE va.tour_schedule_id = ts.id)";
                }
            }

            // Nếu không có điều kiện nào, dùng WHERE 1=1 để luôn có WHERE clause
            if (empty($where_conditions)) {
                $where_clause = 'WHERE 1=1';
            } else {
                $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
            }

            // Count total (với HAVING để filter số người đã thanh toán)
            $count_sql = "
                SELECT COUNT(*) as total
                FROM (
                    SELECT ts.id
                    FROM tour_schedules ts
                    JOIN tours t ON ts.tour_id = t.id
                    LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
                        AND b.payment_status = 'paid'
                    {$where_clause}
                    GROUP BY ts.id, t.min_participants
                    HAVING COALESCE(SUM(b.adult_count + b.child_count + b.infant_count), 0) >= t.min_participants
                ) AS subquery
            ";

            $count_stmt = $this->pdo->prepare($count_sql);
            $count_stmt->execute($params);
            $total = $count_stmt->fetch()['total'];

            // Get data
            $offset = ($page - 1) * $limit;
            $params['offset'] = $offset;
            $params['limit'] = $limit;

            $data_sql = "
                SELECT 
                    ts.id,
                    ts.start_date,
                    ts.end_date,
                    t.tour_code,
                    t.name AS tour_name,
                    t.min_participants,
                    t.booking_deadline_days,
                    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
                    COUNT(DISTINCT b.id) AS total_paid_bookings,
                    COALESCE(SUM(b.adult_count + b.child_count + b.infant_count), 0) AS total_paid_participants,
                    COALESCE(SUM(b.final_amount), 0) AS total_revenue,
                    ts.status,
                    ts.guide_id,
                    u.full_name AS guide_name,
                    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count,
                    (SELECT COUNT(*) FROM room_assignments WHERE tour_schedule_id = ts.id) AS room_assignment_count,
                    CASE 
                        WHEN ts.guide_id IS NOT NULL 
                            AND EXISTS (SELECT 1 FROM vehicle_assignments WHERE tour_schedule_id = ts.id)
                            AND EXISTS (SELECT 1 FROM room_assignments WHERE tour_schedule_id = ts.id)
                        THEN 'ready'
                        WHEN ts.guide_id IS NULL 
                            AND NOT EXISTS (SELECT 1 FROM vehicle_assignments WHERE tour_schedule_id = ts.id)
                            AND NOT EXISTS (SELECT 1 FROM room_assignments WHERE tour_schedule_id = ts.id)
                        THEN 'not_started'
                        ELSE 'in_progress'
                    END AS operations_status
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
                    AND b.payment_status = 'paid'
                LEFT JOIN users u ON ts.guide_id = u.id
                {$where_clause}
                GROUP BY ts.id, t.min_participants, t.booking_deadline_days, t.tour_code, t.name, ts.start_date, ts.end_date, ts.status, ts.guide_id, u.full_name
                HAVING COALESCE(SUM(b.adult_count + b.child_count + b.infant_count), 0) >= t.min_participants
                ORDER BY ts.start_date ASC
                LIMIT :limit OFFSET :offset
            ";

            $data_stmt = $this->pdo->prepare($data_sql);
            $data_stmt->execute($params);
            $data = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: Log để kiểm tra
            error_log("TourOperations::getReadyForOperations() - Query executed");
            error_log("TourOperations::getReadyForOperations() - Total count: " . $total);
            error_log("TourOperations::getReadyForOperations() - Found tours: " . count($data));
            if (!empty($data)) {
                error_log("TourOperations::getReadyForOperations() - First tour ID: " . ($data[0]['id'] ?? 'N/A'));
            } else {
                error_log("TourOperations::getReadyForOperations() - No tours found. SQL: " . $data_sql);
                error_log("TourOperations::getReadyForOperations() - Params: " . json_encode($params));
            }

            $total_pages = ceil($total / $limit);

            return [
                'data' => $data,
                'total' => $total,
                'pages' => $total_pages,
                'current_page' => $page
            ];

        } catch (PDOException $e) {
            logError("TourOperations::getReadyForOperations() Error: " . $e->getMessage());
            return [
                'data' => [],
                'total' => 0,
                'pages' => 0,
                'current_page' => 1
            ];
        }
    }

    /**
     * Tổng hợp thông tin tour cho trang operations
     */
    public function getTourOperationsSummary($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    ts.id,
                    ts.start_date,
                    ts.end_date,
                    ts.status,
                    ts.guide_id,
                    t.tour_code,
                    t.name AS tour_name,
                    t.duration_days,
                    t.duration_nights,
                    t.min_participants,
                    t.max_participants,
                    t.booking_deadline_days,
                    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
                    COUNT(DISTINCT b.id) AS total_paid_bookings,
                    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
                    SUM(b.final_amount) AS total_revenue,
                    SUM(b.paid_amount) AS total_collected,
                    u.full_name AS guide_name,
                    u.phone AS guide_phone,
                    (SELECT COUNT(*) FROM vehicle_assignments WHERE tour_schedule_id = ts.id) AS vehicle_count,
                    (SELECT COUNT(*) FROM room_assignments WHERE tour_schedule_id = ts.id) AS room_assignment_count,
                    CASE 
                        WHEN SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants 
                        THEN 'SUFFICIENT' 
                        ELSE 'INSUFFICIENT' 
                    END AS participant_status
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
                    AND b.payment_status = 'paid'
                LEFT JOIN users u ON ts.guide_id = u.id
                WHERE ts.id = :schedule_id
                GROUP BY ts.id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (PDOException $e) {
            logError("TourOperations::getTourOperationsSummary() Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra tour đã sẵn sàng cho operations chưa (để thao tác)
     * Điều kiện: (Tour đã đóng HOẶC đã qua deadline) + có đủ booking đã thanh toán
     */
    public function checkReadyForOperations($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    ts.id,
                    ts.status,
                    t.booking_deadline_days,
                    DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY) AS booking_deadline_date,
                    CURDATE() AS today,
                    CASE 
                        WHEN ts.status = 'closed' THEN 'CLOSED'
                        WHEN CURDATE() >= DATE_SUB(ts.start_date, INTERVAL t.booking_deadline_days DAY)
                        THEN 'PASSED_DEADLINE'
                        ELSE 'NOT_READY'
                    END AS operations_status,
                    SUM(b.adult_count + b.child_count + b.infant_count) AS total_paid_participants,
                    t.min_participants,
                    CASE 
                        WHEN SUM(b.adult_count + b.child_count + b.infant_count) >= t.min_participants
                        THEN 'SUFFICIENT'
                        ELSE 'INSUFFICIENT'
                    END AS participant_status
                FROM tour_schedules ts
                JOIN tours t ON ts.tour_id = t.id
                LEFT JOIN bookings b ON ts.id = b.tour_schedule_id 
                    AND b.payment_status = 'paid'
                WHERE ts.id = :schedule_id
                GROUP BY ts.id
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return false;
            }

            // Phải (đã đóng HOẶC qua deadline) VÀ đủ số người
            return ($result['operations_status'] === 'CLOSED' || $result['operations_status'] === 'PASSED_DEADLINE')
                && $result['participant_status'] === 'SUFFICIENT';

        } catch (PDOException $e) {
            logError("TourOperations::checkReadyForOperations() Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách booking đã thanh toán
     */
    public function getPaidBookings($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    b.id,
                    b.booking_code,
                    c.full_name AS customer_name,
                    c.phone AS customer_phone,
                    b.adult_count,
                    b.child_count,
                    b.infant_count,
                    b.final_amount,
                    b.paid_amount,
                    b.payment_status,
                    b.created_at AS booking_date
                FROM bookings b
                JOIN customers c ON b.customer_id = c.id
                WHERE b.tour_schedule_id = :schedule_id
                  AND b.payment_status = 'paid'
                ORDER BY b.created_at ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("TourOperations::getPaidBookings() Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách khách hàng (từ booking_customers)
     */
    public function getPaidParticipants($schedule_id)
    {
        try {
            $sql = "
                SELECT 
                    bc.id,
                    c.full_name,
                    c.gender,
                    c.date_of_birth,
                    bc.age_type,
                    b.booking_code,
                    ra.room_number,
                    ra.id AS room_assignment_id
                FROM booking_customers bc
                JOIN customers c ON bc.customer_id = c.id
                JOIN bookings b ON bc.booking_id = b.id
                LEFT JOIN room_assignment_customers rac ON bc.id = rac.booking_customer_id
                LEFT JOIN room_assignments ra ON rac.room_assignment_id = ra.id
                WHERE b.tour_schedule_id = :schedule_id
                  AND b.payment_status = 'paid'
                ORDER BY b.booking_code, bc.age_type
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schedule_id' => $schedule_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            logError("TourOperations::getPaidParticipants() Error: " . $e->getMessage());
            return [];
        }
    }
}

