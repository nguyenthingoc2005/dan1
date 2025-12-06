<?php
/**
 * ==============================================================================
 * PRICING HELPER - Tính toán giá tour theo công thức mới
 * ==============================================================================
 * 
 * Công thức:
 * - Chi phí dịch vụ/người = Σ(unit_price × quantity) từ itinerary_day_services
 * - Chi phí cố định/người = (fixed_costs) ÷ min_participants
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
 * @param array $fixed_costs ['guide' => float, 'management' => float, 'marketing' => float, 'other' => float]
 * @param int $min_participants
 * @return float Tổng chi phí/người
 */
function calculateTotalCostPerPerson($pdo, $tour_id, $fixed_costs, $min_participants)
{
    // 1. Tính chi phí dịch vụ/người từ itinerary_day_services
    $service_cost = calculateServiceCostPerPerson($pdo, $tour_id);

    // 2. Tính chi phí cố định/người
    $fixed_cost = calculateFixedCostPerPerson($fixed_costs, $min_participants);

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
 * @param array $fixed_costs ['guide' => float, 'management' => float, 'marketing' => float, 'other' => float]
 * @param int $min_participants
 * @return float Chi phí cố định/người
 */
function calculateFixedCostPerPerson($fixed_costs, $min_participants)
{
    $total_fixed = 0;
    $total_fixed += (float) ($fixed_costs['guide'] ?? 0);
    $total_fixed += (float) ($fixed_costs['management'] ?? 0);
    $total_fixed += (float) ($fixed_costs['marketing'] ?? 0);
    $total_fixed += (float) ($fixed_costs['other'] ?? 0);

    if ($min_participants <= 0) {
        return 0;
    }

    return $total_fixed / $min_participants;
}

/**
 * Tính breakdown chi tiết cho tour (để hiển thị)
 * 
 * @param PDO $pdo
 * @param int $tour_id
 * @param array $fixed_costs
 * @param int $min_participants
 * @return array ['service_cost' => float, 'fixed_cost' => float, 'total' => float, 'breakdown' => array]
 */
function calculateTourPricingBreakdown($pdo, $tour_id, $fixed_costs, $min_participants)
{
    $service_cost = calculateServiceCostPerPerson($pdo, $tour_id);
    $fixed_cost = calculateFixedCostPerPerson($fixed_costs, $min_participants);
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
        'fixed_costs_detail' => [
            'guide' => (float) ($fixed_costs['guide'] ?? 0),
            'management' => (float) ($fixed_costs['management'] ?? 0),
            'marketing' => (float) ($fixed_costs['marketing'] ?? 0),
            'other' => (float) ($fixed_costs['other'] ?? 0),
            'total' => array_sum([
                (float) ($fixed_costs['guide'] ?? 0),
                (float) ($fixed_costs['management'] ?? 0),
                (float) ($fixed_costs['marketing'] ?? 0),
                (float) ($fixed_costs['other'] ?? 0)
            ])
        ]
    ];
}
