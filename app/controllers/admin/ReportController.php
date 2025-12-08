<?php
/**
 * ==============================================================================
 * REPORT CONTROLLER (ADMIN)
 * ==============================================================================
 * 
 * Quản lý báo cáo thống kê
 * Routing: ?act=admin&module=reports&action=index
 * 
 * @version 2.0
 * @date 2024-12-03
 * ==============================================================================
 */

class ReportController
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Dashboard báo cáo chung
     */
    public function index()
    {
        require_admin();

        // Redirect to revenue report by default
        redirect('?act=admin&module=reports&action=revenue');
    }

    /**
     * Báo cáo doanh thu - Nâng cấp với metrics chi tiết
     */
    public function revenue()
    {
        require_admin();

        // Handle Excel export
        if (isset($_GET['export']) && $_GET['export'] === 'excel') {
            $this->exportRevenueToExcel();
            return;
        }

        // Filters
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $tour_id = $_GET['tour_id'] ?? null;
        $customer_id = $_GET['customer_id'] ?? null;
        $payment_method = $_GET['payment_method'] ?? null;
        $payment_status = $_GET['payment_status'] ?? null;
        $source = $_GET['source'] ?? null;

        // Build WHERE clause for filters
        $where_conditions = ["p.status = 'completed'", "p.payment_type != 'refund'"];
        $params = ['start' => $start_date, 'end' => $end_date];

        $where_conditions[] = "DATE(p.payment_date) BETWEEN :start AND :end";

        if ($tour_id) {
            $where_conditions[] = "b.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        if ($customer_id) {
            $where_conditions[] = "b.customer_id = :customer_id";
            $params['customer_id'] = $customer_id;
        }

        if ($payment_method) {
            $where_conditions[] = "p.payment_method = :payment_method";
            $params['payment_method'] = $payment_method;
        }

        if ($payment_status) {
            $where_conditions[] = "b.payment_status = :payment_status";
            $params['payment_status'] = $payment_status;
        }

        if ($source) {
            $where_conditions[] = "b.source = :source";
            $params['source'] = $source;
        }

        $where_clause = "WHERE " . implode(" AND ", $where_conditions);

        // ========================================================================
        // STATISTICS QUERIES
        // ========================================================================

        // 1. Tổng doanh thu (từ payments completed)
        $sql_total_revenue = "SELECT COALESCE(SUM(p.amount), 0) as total
                             FROM payments p
                             INNER JOIN bookings b ON p.booking_id = b.id
                             {$where_clause}";
        $stmt = $this->db->prepare($sql_total_revenue);
        $stmt->execute($params);
        $total_revenue = (float) $stmt->fetchColumn();

        // 2. Doanh thu theo tour
        $sql_revenue_by_tour = "SELECT 
                                    t.id,
                                    t.tour_code,
                                    t.name as tour_name,
                                    COUNT(DISTINCT b.id) as booking_count,
                                    COUNT(DISTINCT p.id) as payment_count,
                                    COALESCE(SUM(p.amount), 0) as revenue
                                FROM tours t
                                LEFT JOIN bookings b ON t.id = b.tour_id
                                LEFT JOIN payments p ON b.id = p.booking_id AND p.status = 'completed' AND p.payment_type != 'refund'
                                    AND DATE(p.payment_date) BETWEEN :start AND :end
                                " . ($tour_id ? "WHERE t.id = :tour_id" : "") . "
                                GROUP BY t.id, t.tour_code, t.name
                                HAVING revenue > 0
                                ORDER BY revenue DESC
                                LIMIT 20";
        $stmt = $this->db->prepare($sql_revenue_by_tour);
        $stmt->execute($params);
        $revenue_by_tour = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Doanh thu theo phương thức thanh toán
        $sql_revenue_by_payment_method = "SELECT 
                                            p.payment_method,
                                            COUNT(DISTINCT p.id) as payment_count,
                                            COALESCE(SUM(p.amount), 0) as revenue
                                          FROM payments p
                                          INNER JOIN bookings b ON p.booking_id = b.id
                                          {$where_clause}
                                          GROUP BY p.payment_method
                                          ORDER BY revenue DESC";
        $stmt = $this->db->prepare($sql_revenue_by_payment_method);
        $stmt->execute($params);
        $revenue_by_payment_method = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Doanh thu theo tháng (cho biểu đồ)
        $sql_revenue_by_month = "SELECT 
                                    DATE_FORMAT(p.payment_date, '%Y-%m') as month,
                                    COALESCE(SUM(p.amount), 0) as revenue
                                 FROM payments p
                                 INNER JOIN bookings b ON p.booking_id = b.id
                                 {$where_clause}
                                 GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
                                 ORDER BY month ASC";
        $stmt = $this->db->prepare($sql_revenue_by_month);
        $stmt->execute($params);
        $revenue_by_month = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Doanh thu theo ngày (cho biểu đồ chi tiết)
        $sql_revenue_by_day = "SELECT 
                                  DATE(p.payment_date) as date,
                                  COALESCE(SUM(p.amount), 0) as revenue
                               FROM payments p
                               INNER JOIN bookings b ON p.booking_id = b.id
                               {$where_clause}
                               GROUP BY DATE(p.payment_date)
                               ORDER BY date ASC";
        $stmt = $this->db->prepare($sql_revenue_by_day);
        $stmt->execute($params);
        $revenue_by_day = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. Tổng số booking
        $sql_total_bookings = "SELECT COUNT(DISTINCT b.id) as total
                               FROM bookings b
                               INNER JOIN payments p ON b.id = p.booking_id
                               {$where_clause}";
        $stmt = $this->db->prepare($sql_total_bookings);
        $stmt->execute($params);
        $total_bookings = (int) $stmt->fetchColumn();

        // 7. Tổng số khách hàng
        $sql_total_customers = "SELECT COUNT(DISTINCT b.customer_id) as total
                               FROM bookings b
                               INNER JOIN payments p ON b.id = p.booking_id
                               {$where_clause}";
        $stmt = $this->db->prepare($sql_total_customers);
        $stmt->execute($params);
        $total_customers = (int) $stmt->fetchColumn();

        // 8. Chi phí - Tiền trả cho nhà cung cấp dịch vụ (service_provider_payments)
        $cost_where = ["status = 'completed'", "DATE(payment_date) BETWEEN ? AND ?"];
        $cost_params = [$start_date, $end_date];

        // If tour_id filter is applied, only count costs for that tour's bookings
        if ($tour_id) {
            // Get booking IDs for this tour
            $sql_booking_ids = "SELECT DISTINCT b.id 
                               FROM bookings b 
                               WHERE b.tour_id = ?";
            $stmt = $this->db->prepare($sql_booking_ids);
            $stmt->execute([$tour_id]);
            $booking_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($booking_ids)) {
                $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
                $cost_where[] = "booking_id IN ($placeholders)";
                $cost_params = array_merge($cost_params, array_values($booking_ids));
            } else {
                $cost_where[] = "1=0"; // No bookings, no costs
            }
        }

        $cost_where_clause = "WHERE " . implode(" AND ", $cost_where);
        $sql_total_service_costs = "SELECT COALESCE(SUM(amount), 0) as total
                           FROM service_provider_payments
                           {$cost_where_clause}";
        $stmt = $this->db->prepare($sql_total_service_costs);
        $stmt->execute($cost_params);
        $total_service_costs = (float) $stmt->fetchColumn();

        // 8b. Chi phí - Hoàn tiền (Refunds)
        // Tính từ payments với payment_type = 'refund'
        $refund_where_conditions = ["p.status = 'completed'", "p.payment_type = 'refund'", "DATE(p.payment_date) BETWEEN :start AND :end"];
        $refund_params = ['start' => $start_date, 'end' => $end_date];

        if ($tour_id) {
            $refund_where_conditions[] = "b.tour_id = :tour_id";
            $refund_params['tour_id'] = $tour_id;
        }

        $refund_where_clause = "WHERE " . implode(" AND ", $refund_where_conditions);
        $sql_total_refunds = "SELECT COALESCE(SUM(p.amount), 0) as total
                             FROM payments p
                             INNER JOIN bookings b ON p.booking_id = b.id
                             {$refund_where_clause}";
        $stmt = $this->db->prepare($sql_total_refunds);
        $stmt->execute($refund_params);
        $total_refunds = (float) $stmt->fetchColumn();

        // 8c. Chi phí - Lương HDV (tour_assignments với salary_status = 'paid')
        // Lưu ý: Lương được tính theo ngày trả lương (paid_date) hoặc ngày kết thúc tour (end_date) nếu chưa có paid_date
        $salary_where_conditions = ["ta.salary_status = 'paid'"];
        $salary_params = [];

        if ($tour_id) {
            $salary_where_conditions[] = "ts.tour_id = ?";
            $salary_params[] = $tour_id;
        }

        // Filter by paid_date if exists, otherwise by tour end_date
        $salary_where_conditions[] = "(ta.paid_date BETWEEN ? AND ? OR (ta.paid_date IS NULL AND DATE(ts.end_date) BETWEEN ? AND ?))";
        $salary_params[] = $start_date;
        $salary_params[] = $end_date;
        $salary_params[] = $start_date;
        $salary_params[] = $end_date;

        $salary_where_clause = "WHERE " . implode(" AND ", $salary_where_conditions);
        $sql_total_guide_salary = "SELECT COALESCE(SUM(ta.salary_amount), 0) as total
                                  FROM tour_assignments ta
                                  INNER JOIN tour_schedules ts ON ta.tour_schedule_id = ts.id
                                  {$salary_where_clause}";
        $stmt = $this->db->prepare($sql_total_guide_salary);
        $stmt->execute($salary_params);
        $total_guide_salary = (float) $stmt->fetchColumn();

        // 8d. Chi phí - Lương tài xế (vehicle_assignments)
        $driver_salary_where = ["va.status = 'completed'"];
        $driver_salary_params = [];

        if ($tour_id) {
            $driver_salary_where[] = "ts.tour_id = ?";
            $driver_salary_params[] = $tour_id;
        }

        $driver_salary_where[] = "DATE(ts.end_date) BETWEEN ? AND ?";
        $driver_salary_params[] = $start_date;
        $driver_salary_params[] = $end_date;

        $driver_salary_where_clause = "WHERE " . implode(" AND ", $driver_salary_where);
        $sql_total_driver_salary = "SELECT COALESCE(SUM(va.driver_salary), 0) as total
                                   FROM vehicle_assignments va
                                   INNER JOIN tour_schedules ts ON va.tour_schedule_id = ts.id
                                   {$driver_salary_where_clause}";
        $stmt = $this->db->prepare($sql_total_driver_salary);
        $stmt->execute($driver_salary_params);
        $total_driver_salary = (float) $stmt->fetchColumn();

        // Tổng chi phí (tất cả các khoản chi)
        $total_costs = $total_service_costs + $total_refunds + $total_guide_salary + $total_driver_salary;

        // 9. Lợi nhuận (Doanh thu - Tổng chi phí)
        $profit = $total_revenue - $total_costs;

        // 9b. Chi tiết hoàn tiền
        $sql_refund_details = "SELECT 
                                 p.id,
                                 p.payment_date,
                                 p.amount,
                                 p.payment_method,
                                 b.booking_code,
                                 c.full_name as customer_name,
                                 t.name as tour_name
                               FROM payments p
                               INNER JOIN bookings b ON p.booking_id = b.id
                               INNER JOIN customers c ON b.customer_id = c.id
                               INNER JOIN tours t ON b.tour_id = t.id
                               {$refund_where_clause}
                               ORDER BY p.payment_date DESC";
        $stmt = $this->db->prepare($sql_refund_details);
        $stmt->execute($refund_params);
        $refund_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9c. Chi tiết tiền trả dịch vụ theo nhà cung cấp
        $sql_service_cost_by_provider = "SELECT 
                                           sp.id,
                                           sp.name as provider_name,
                                           COUNT(DISTINCT spp.id) as payment_count,
                                           COALESCE(SUM(spp.amount), 0) as total_amount
                                         FROM service_providers sp
                                         LEFT JOIN service_provider_payments spp ON sp.id = spp.service_provider_id
                                           AND spp.status = 'completed'
                                           AND DATE(spp.payment_date) BETWEEN ? AND ?
                                         GROUP BY sp.id, sp.name
                                         HAVING total_amount > 0
                                         ORDER BY total_amount DESC
                                         LIMIT 20";
        $stmt = $this->db->prepare($sql_service_cost_by_provider);
        $stmt->execute([$start_date, $end_date]);
        $service_cost_by_provider = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 9d. Chi tiết lương HDV
        $sql_guide_salary_details = "SELECT 
                                        ta.id,
                                        ta.salary_amount,
                                        ta.paid_date,
                                        ta.assignment_date,
                                        u.full_name as guide_name,
                                        ts.start_date,
                                        ts.end_date,
                                        t.name as tour_name,
                                        t.tour_code
                                     FROM tour_assignments ta
                                     INNER JOIN users u ON ta.guide_id = u.id
                                     INNER JOIN tour_schedules ts ON ta.tour_schedule_id = ts.id
                                     INNER JOIN tours t ON ts.tour_id = t.id
                                     {$salary_where_clause}
                                     ORDER BY COALESCE(ta.paid_date, ts.end_date) DESC
                                     LIMIT 50";
        $stmt = $this->db->prepare($sql_guide_salary_details);
        $stmt->execute($salary_params);
        $guide_salary_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 10. Doanh thu theo khách hàng (Top 10)
        $sql_revenue_by_customer = "SELECT 
                                       c.id,
                                       c.customer_code,
                                       c.full_name,
                                       COUNT(DISTINCT b.id) as booking_count,
                                       COALESCE(SUM(p.amount), 0) as revenue
                                    FROM customers c
                                    INNER JOIN bookings b ON c.id = b.customer_id
                                    INNER JOIN payments p ON b.id = p.booking_id
                                    {$where_clause}
                                    GROUP BY c.id, c.customer_code, c.full_name
                                    ORDER BY revenue DESC
                                    LIMIT 10";
        $stmt = $this->db->prepare($sql_revenue_by_customer);
        $stmt->execute($params);
        $revenue_by_customer = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 11. Doanh thu theo nguồn booking
        $sql_revenue_by_source = "SELECT 
                                     COALESCE(b.source, 'other') as source,
                                     COUNT(DISTINCT b.id) as booking_count,
                                     COALESCE(SUM(p.amount), 0) as revenue
                                  FROM bookings b
                                  INNER JOIN payments p ON b.id = p.booking_id
                                  {$where_clause}
                                  GROUP BY b.source
                                  ORDER BY revenue DESC";
        $stmt = $this->db->prepare($sql_revenue_by_source);
        $stmt->execute($params);
        $revenue_by_source = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load dropdown data for filters
        $tours = $this->getToursForDropdown();
        $customers = $this->getCustomersForDropdown();

        // Pass data to view
        $page_title = 'Báo cáo Doanh thu';
        $content_file = VIEWS_PATH . '/admin/reports/revenue.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xuất báo cáo doanh thu ra Excel
     */
    private function exportRevenueToExcel()
    {
        require_admin();

        // Get filters from GET params
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $tour_id = $_GET['tour_id'] ?? null;
        $customer_id = $_GET['customer_id'] ?? null;
        $payment_method = $_GET['payment_method'] ?? null;
        $payment_status = $_GET['payment_status'] ?? null;
        $source = $_GET['source'] ?? null;

        // Build WHERE clause (same as revenue method)
        $where_conditions = ["p.status = 'completed'", "p.payment_type != 'refund'"];
        $params = ['start' => $start_date, 'end' => $end_date];

        $where_conditions[] = "DATE(p.payment_date) BETWEEN :start AND :end";

        if ($tour_id) {
            $where_conditions[] = "b.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        if ($customer_id) {
            $where_conditions[] = "b.customer_id = :customer_id";
            $params['customer_id'] = $customer_id;
        }

        if ($payment_method) {
            $where_conditions[] = "p.payment_method = :payment_method";
            $params['payment_method'] = $payment_method;
        }

        if ($payment_status) {
            $where_conditions[] = "b.payment_status = :payment_status";
            $params['payment_status'] = $payment_status;
        }

        if ($source) {
            $where_conditions[] = "b.source = :source";
            $params['source'] = $source;
        }

        $where_clause = "WHERE " . implode(" AND ", $where_conditions);

        // Get detailed payment data
        $sql = "SELECT 
                   p.id,
                   p.payment_date,
                   p.amount,
                   p.payment_method,
                   p.payment_type,
                   p.transaction_id,
                   p.receipt_number,
                   b.booking_code,
                   b.payment_status as booking_payment_status,
                   t.tour_code,
                   t.name as tour_name,
                   c.customer_code,
                   c.full_name as customer_name,
                   c.phone as customer_phone
                FROM payments p
                INNER JOIN bookings b ON p.booking_id = b.id
                INNER JOIN tours t ON b.tour_id = t.id
                INNER JOIN customers c ON b.customer_id = c.id
                {$where_clause}
                ORDER BY p.payment_date DESC, p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set headers for Excel/CSV download
        $filename = 'BaoCaoDoanhThu_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Add BOM for UTF-8 Excel compatibility
        echo "\xEF\xBB\xBF";

        // Open output stream
        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'STT',
            'Ngày thanh toán',
            'Mã booking',
            'Tour',
            'Khách hàng',
            'SĐT',
            'Số tiền',
            'Phương thức',
            'Loại thanh toán',
            'Mã giao dịch',
            'Số hóa đơn',
            'Trạng thái booking'
        ], ',');

        // Data rows
        $stt = 1;
        foreach ($payments as $payment) {
            // Format payment method
            $payment_method_names = [
                'cash' => 'Tiền mặt',
                'bank_transfer' => 'Chuyển khoản',
                'credit_card' => 'Thẻ tín dụng',
                'other' => 'Khác'
            ];

            // Format payment type
            $payment_type_names = [
                'deposit' => 'Đặt cọc',
                'installment' => 'Trả góp',
                'full' => 'Thanh toán đủ',
                'refund' => 'Hoàn tiền'
            ];

            // Format payment status
            $payment_status_names = [
                'unpaid' => 'Chưa thanh toán',
                'partial' => 'Thanh toán một phần',
                'paid' => 'Đã thanh toán',
                'rejected' => 'Từ chối',
                'cancelled' => 'Hủy',
                'refunded' => 'Đã hoàn tiền'
            ];

            fputcsv($output, [
                $stt++,
                date('d/m/Y', strtotime($payment['payment_date'])),
                $payment['booking_code'],
                $payment['tour_name'],
                $payment['customer_name'],
                $payment['customer_phone'],
                number_format($payment['amount'], 0, ',', '.'),
                $payment_method_names[$payment['payment_method']] ?? $payment['payment_method'],
                $payment_type_names[$payment['payment_type']] ?? $payment['payment_type'],
                $payment['transaction_id'] ?? '',
                $payment['receipt_number'] ?? '',
                $payment_status_names[$payment['booking_payment_status']] ?? $payment['booking_payment_status']
            ], ',');
        }

        // Summary rows
        $sql_total = "SELECT COALESCE(SUM(p.amount), 0) as total
                     FROM payments p
                     INNER JOIN bookings b ON p.booking_id = b.id
                     {$where_clause}";
        $stmt = $this->db->prepare($sql_total);
        $stmt->execute($params);
        $total = (float) $stmt->fetchColumn();

        fputcsv($output, [], ',');
        fputcsv($output, ['TỔNG DOANH THU:', number_format($total, 0, ',', '.') . ' đ'], ',');

        fclose($output);
        exit;
    }

    /**
     * Get tours for dropdown filter
     */
    private function getToursForDropdown()
    {
        $sql = "SELECT id, tour_code, name FROM tours WHERE status = 'active' ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get customers for dropdown filter
     */
    private function getCustomersForDropdown()
    {
        $sql = "SELECT id, customer_code, full_name FROM customers WHERE status = 'active' ORDER BY full_name ASC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Báo cáo booking - Thống kê đặt tour
     */
    public function bookings()
    {
        require_admin();

        // Handle Excel export
        if (isset($_GET['export']) && $_GET['export'] === 'excel') {
            $this->exportBookingsToExcel();
            return;
        }

        // Filters
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $tour_id = $_GET['tour_id'] ?? null;
        $payment_status = $_GET['payment_status'] ?? null;
        $source = $_GET['source'] ?? null;

        // Build WHERE clause for filters
        $where_conditions = ["DATE(b.created_at) BETWEEN :start AND :end"];
        $params = ['start' => $start_date, 'end' => $end_date];

        if ($tour_id) {
            $where_conditions[] = "b.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        if ($payment_status) {
            $where_conditions[] = "b.payment_status = :payment_status";
            $params['payment_status'] = $payment_status;
        }

        if ($source) {
            $where_conditions[] = "b.source = :source";
            $params['source'] = $source;
        }

        $where_clause = "WHERE " . implode(" AND ", $where_conditions);

        // ========================================================================
        // STATISTICS QUERIES
        // ========================================================================

        // 1. Tổng số booking
        $sql_total_bookings = "SELECT COUNT(*) as total
                              FROM bookings b
                              {$where_clause}";
        $stmt = $this->db->prepare($sql_total_bookings);
        $stmt->execute($params);
        $total_bookings = (int) $stmt->fetchColumn();

        // 2. Booking theo trạng thái thanh toán
        $sql_bookings_by_status = "SELECT 
                                     b.payment_status,
                                     COUNT(*) as count,
                                     SUM(b.final_amount) as total_amount
                                   FROM bookings b
                                   {$where_clause}
                                   GROUP BY b.payment_status
                                   ORDER BY count DESC";
        $stmt = $this->db->prepare($sql_bookings_by_status);
        $stmt->execute($params);
        $bookings_by_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Booking theo tour
        $sql_bookings_by_tour = "SELECT 
                                    t.id,
                                    t.tour_code,
                                    t.name as tour_name,
                                    COUNT(b.id) as booking_count,
                                    SUM(b.adult_count + b.child_count + b.infant_count) as total_passengers,
                                    SUM(b.final_amount) as total_amount
                                 FROM tours t
                                 LEFT JOIN bookings b ON t.id = b.tour_id
                                   AND DATE(b.created_at) BETWEEN :start AND :end
                                 " . ($tour_id ? "WHERE t.id = :tour_id" : "") . "
                                 GROUP BY t.id, t.tour_code, t.name
                                 HAVING booking_count > 0
                                 ORDER BY booking_count DESC
                                 LIMIT 20";
        $stmt = $this->db->prepare($sql_bookings_by_tour);
        $stmt->execute($params);
        $bookings_by_tour = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Booking theo tháng (cho biểu đồ)
        $sql_bookings_by_month = "SELECT 
                                    DATE_FORMAT(b.created_at, '%Y-%m') as month,
                                    COUNT(*) as count
                                 FROM bookings b
                                 {$where_clause}
                                 GROUP BY DATE_FORMAT(b.created_at, '%Y-%m')
                                 ORDER BY month ASC";
        $stmt = $this->db->prepare($sql_bookings_by_month);
        $stmt->execute($params);
        $bookings_by_month = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Booking theo nguồn (source)
        $sql_bookings_by_source = "SELECT 
                                     COALESCE(b.source, 'other') as source,
                                     COUNT(*) as count,
                                     SUM(b.final_amount) as total_amount
                                  FROM bookings b
                                  {$where_clause}
                                  GROUP BY b.source
                                  ORDER BY count DESC";
        $stmt = $this->db->prepare($sql_bookings_by_source);
        $stmt->execute($params);
        $bookings_by_source = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. Tổng số khách (passengers)
        $sql_total_passengers = "SELECT 
                                   SUM(b.adult_count) as total_adults,
                                   SUM(b.child_count) as total_children,
                                   SUM(b.infant_count) as total_infants,
                                   SUM(b.adult_count + b.child_count + b.infant_count) as total_passengers
                                FROM bookings b
                                {$where_clause}";
        $stmt = $this->db->prepare($sql_total_passengers);
        $stmt->execute($params);
        $passengers_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // 7. Tỷ lệ hủy (cancelled + refunded)
        $sql_cancelled_count = "SELECT COUNT(*) as total
                               FROM bookings b
                               {$where_clause}
                               AND b.payment_status IN ('cancelled', 'refunded')";
        $stmt = $this->db->prepare($sql_cancelled_count);
        $stmt->execute($params);
        $cancelled_count = (int) $stmt->fetchColumn();

        $cancellation_rate = $total_bookings > 0 ? ($cancelled_count / $total_bookings) * 100 : 0;

        // 8. Tổng doanh thu từ bookings (theo final_amount, không phải payments)
        $sql_total_revenue_bookings = "SELECT 
                                         SUM(b.final_amount) as total_revenue,
                                         SUM(b.paid_amount) as total_paid,
                                         SUM(b.remaining_amount) as total_remaining
                                      FROM bookings b
                                      {$where_clause}
                                      AND b.payment_status NOT IN ('cancelled', 'rejected')";
        $stmt = $this->db->prepare($sql_total_revenue_bookings);
        $stmt->execute($params);
        $revenue_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // 9. Booking theo ngày trong tuần (thứ 2, 3, 4...)
        $sql_bookings_by_day_of_week = "SELECT 
                                           DAYNAME(b.created_at) as day_name,
                                           DAYOFWEEK(b.created_at) as day_number,
                                           COUNT(*) as count
                                        FROM bookings b
                                        {$where_clause}
                                        GROUP BY DAYNAME(b.created_at), DAYOFWEEK(b.created_at)
                                        ORDER BY day_number";
        $stmt = $this->db->prepare($sql_bookings_by_day_of_week);
        $stmt->execute($params);
        $bookings_by_day_of_week = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 10. Top khách hàng đặt nhiều nhất
        $sql_top_customers = "SELECT 
                                c.id,
                                c.customer_code,
                                c.full_name,
                                COUNT(b.id) as booking_count,
                                SUM(b.final_amount) as total_spent
                             FROM customers c
                             INNER JOIN bookings b ON c.id = b.customer_id
                             {$where_clause}
                             GROUP BY c.id, c.customer_code, c.full_name
                             ORDER BY booking_count DESC
                             LIMIT 10";
        $stmt = $this->db->prepare($sql_top_customers);
        $stmt->execute($params);
        $top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load dropdown data for filters
        $tours = $this->getToursForDropdown();

        // Pass data to view
        $page_title = 'Báo cáo Đặt tour';
        $content_file = VIEWS_PATH . '/admin/reports/bookings.php';
        require VIEWS_PATH . '/layouts/admin_layout.php';
    }

    /**
     * Xuất báo cáo booking ra Excel
     */
    private function exportBookingsToExcel()
    {
        require_admin();

        // Get filters
        $start_date = $_GET['start_date'] ?? date('Y-m-01');
        $end_date = $_GET['end_date'] ?? date('Y-m-t');
        $tour_id = $_GET['tour_id'] ?? null;
        $payment_status = $_GET['payment_status'] ?? null;
        $source = $_GET['source'] ?? null;

        // Build WHERE clause
        $where_conditions = ["DATE(b.created_at) BETWEEN :start AND :end"];
        $params = ['start' => $start_date, 'end' => $end_date];

        if ($tour_id) {
            $where_conditions[] = "b.tour_id = :tour_id";
            $params['tour_id'] = $tour_id;
        }

        if ($payment_status) {
            $where_conditions[] = "b.payment_status = :payment_status";
            $params['payment_status'] = $payment_status;
        }

        if ($source) {
            $where_conditions[] = "b.source = :source";
            $params['source'] = $source;
        }

        $where_clause = "WHERE " . implode(" AND ", $where_conditions);

        // Get booking data
        $sql = "SELECT 
                   b.id,
                   b.booking_code,
                   b.created_at,
                   b.start_date,
                   b.end_date,
                   b.adult_count,
                   b.child_count,
                   b.infant_count,
                   b.total_amount,
                   b.discount_amount,
                   b.final_amount,
                   b.paid_amount,
                   b.remaining_amount,
                   b.payment_status,
                   b.source,
                   t.tour_code,
                   t.name as tour_name,
                   c.customer_code,
                   c.full_name as customer_name,
                   c.phone as customer_phone
                FROM bookings b
                INNER JOIN tours t ON b.tour_id = t.id
                INNER JOIN customers c ON b.customer_id = c.id
                {$where_clause}
                ORDER BY b.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Set headers
        $filename = 'BaoCaoDatTour_' . date('Y-m-d_His') . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF"; // BOM for UTF-8

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'STT',
            'Mã booking',
            'Ngày đặt',
            'Tour',
            'Khách hàng',
            'SĐT',
            'Ngày khởi hành',
            'Ngày kết thúc',
            'Người lớn',
            'Trẻ em',
            'Em bé',
            'Tổng tiền',
            'Giảm giá',
            'Thành tiền',
            'Đã thanh toán',
            'Còn lại',
            'Trạng thái',
            'Nguồn'
        ], ',');

        // Data rows
        $stt = 1;
        $status_names = [
            'unpaid' => 'Chưa thanh toán',
            'partial' => 'Thanh toán một phần',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            'refunded' => 'Đã hoàn tiền',
            'rejected' => 'Từ chối'
        ];

        $source_names = [
            'phone' => 'Điện thoại',
            'email' => 'Email',
            'facebook' => 'Facebook',
            'zalo' => 'Zalo',
            'walk_in' => 'Tại quầy',
            'other' => 'Khác'
        ];

        foreach ($bookings as $booking) {
            fputcsv($output, [
                $stt++,
                $booking['booking_code'],
                date('d/m/Y H:i', strtotime($booking['created_at'])),
                $booking['tour_name'],
                $booking['customer_name'],
                $booking['customer_phone'],
                date('d/m/Y', strtotime($booking['start_date'])),
                date('d/m/Y', strtotime($booking['end_date'])),
                $booking['adult_count'],
                $booking['child_count'],
                $booking['infant_count'],
                number_format($booking['total_amount'], 0, ',', '.'),
                number_format($booking['discount_amount'], 0, ',', '.'),
                number_format($booking['final_amount'], 0, ',', '.'),
                number_format($booking['paid_amount'], 0, ',', '.'),
                number_format($booking['remaining_amount'], 0, ',', '.'),
                $status_names[$booking['payment_status']] ?? $booking['payment_status'],
                $source_names[$booking['source']] ?? $booking['source']
            ], ',');
        }

        fclose($output);
        exit;
    }
}
