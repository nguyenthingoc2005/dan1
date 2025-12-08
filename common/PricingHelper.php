<?php
/**
 * ==============================================================================
 * PRICING HELPER - Tính toán giá tour theo công thức mới
 * ==============================================================================
 * 
 * Công thức:
 * - Chi phí dịch vụ/người = Σ(unit_price × quantity) từ itinerary_day_services
 * - Chi phí cố định/người = fixed_cost_total ÷ min_participants
 * - Tổng chi phí/người = Chi phí dịch vụ/người + Chi phí cố định/người
 * - Giá bán/người = Tổng chi phí/người (KHÔNG markup)
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

/**
 * Tính tổng chi phí/người cho tour
 * 
 * @param PDO $pdo
 * @param int $tour_id
 * @param float $fixed_cost_total Tổng chi phí cố định (nhập trực tiếp)
 * @param int $min_participants
 * @return float Tổng chi phí/người
 */
function calculateTotalCostPerPerson($pdo, $tour_id, $fixed_cost_total, $min_participants)
{
    // 1. Tính chi phí dịch vụ/người từ itinerary_day_services
    $service_cost = calculateServiceCostPerPerson($pdo, $tour_id);

    // 2. Tính chi phí cố định/người
    $fixed_cost = calculateFixedCostPerPerson($fixed_cost_total, $min_participants);

    // 3. Tổng chi phí/người
    return $service_cost + $fixed_cost;
}

/**
 * Tính chi phí dịch vụ/người từ itinerary_day_services
 * 
 * @param PDO $pdo
 * @param int $tour_id
 * @return float Chi phí dịch vụ/người
 */
function calculateServiceCostPerPerson($pdo, $tour_id)
{
    try {
        // Query từ itinerary_day_services qua itineraries
        $sql = "
            SELECT SUM(ids.unit_price * ids.quantity) as total
            FROM itinerary_day_services ids
            JOIN itineraries i ON ids.itinerary_id = i.id
            WHERE i.tour_id = :tour_id 
            AND ids.is_included_in_price = 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);

    } catch (PDOException $e) {
        error_log("calculateServiceCostPerPerson() Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Tính chi phí cố định/người
 * 
 * @param float $fixed_cost_total Tổng chi phí cố định (nhập trực tiếp)
 * @param int $min_participants
 * @return float Chi phí cố định/người
 */
function calculateFixedCostPerPerson($fixed_cost_total, $min_participants)
{
    if ($min_participants <= 0) {
        return 0;
    }

    return (float) $fixed_cost_total / $min_participants;
}

/**
 * Tính breakdown chi tiết cho tour (để hiển thị)
 * 
 * @param PDO $pdo
 * @param int $tour_id
 * @param float $fixed_cost_total Tổng chi phí cố định (nhập trực tiếp)
 * @param int $min_participants
 * @return array ['service_cost' => float, 'fixed_cost' => float, 'total' => float, 'breakdown' => array]
 */
function calculateTourPricingBreakdown($pdo, $tour_id, $fixed_cost_total, $min_participants)
{
    $service_cost = calculateServiceCostPerPerson($pdo, $tour_id);
    $fixed_cost = calculateFixedCostPerPerson($fixed_cost_total, $min_participants);
    $total = $service_cost + $fixed_cost;

    // Breakdown theo ngày
    $day_breakdown = [];
    try {
        $sql = "
            SELECT 
                i.day_number,
                SUM(ids.unit_price * ids.quantity) as day_total
            FROM itinerary_day_services ids
            JOIN itineraries i ON ids.itinerary_id = i.id
            WHERE i.tour_id = :tour_id 
            AND ids.is_included_in_price = 1
            GROUP BY i.day_number
            ORDER BY i.day_number ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        $day_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("calculateTourPricingBreakdown() Error: " . $e->getMessage());
    }

    return [
        'service_cost_per_person' => $service_cost,
        'fixed_cost_per_person' => $fixed_cost,
        'total_cost_per_person' => $total,
        'day_breakdown' => $day_breakdown,
        'fixed_cost_total' => (float) $fixed_cost_total
    ];
}
