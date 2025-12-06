<?php
/**
 * ==============================================================================
 * PRICING CALCULATOR COMPONENT
 * ==============================================================================
 * 
 * Component để tính và hiển thị breakdown giá tour theo công thức mới
 * 
 * Variables:
 * - $tour_id (optional): ID của tour (nếu đang edit)
 * - $breakdown (optional): Array breakdown data từ PricingHelper
 * - $fixed_costs (required): Array ['guide' => X, 'management' => Y, 'marketing' => Z, 'other' => W]
 * - $min_participants (required): Số người tối thiểu
 * - $service_cost_total (optional): Tổng chi phí dịch vụ (nếu không có breakdown)
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

// Default values
$fixed_costs = $fixed_costs ?? ['guide' => 0, 'management' => 0, 'marketing' => 0, 'other' => 0];
$min_participants = $min_participants ?? 15;
$breakdown = $breakdown ?? null;

// Calculate if breakdown not provided
if (!$breakdown) {
    $service_cost_total = $service_cost_total ?? 0;
    $total_fixed_cost = array_sum($fixed_costs);
    $fixed_cost_per_person = ($min_participants > 0) ? ($total_fixed_cost / $min_participants) : 0;
    $total_cost_per_person = $service_cost_total + $fixed_cost_per_person;

    $breakdown = [
        'service_cost_total' => $service_cost_total,
        'fixed_cost_total' => $total_fixed_cost,
        'fixed_cost_per_person' => $fixed_cost_per_person,
        'total_cost_per_person' => $total_cost_per_person,
        'suggested_price_per_person' => $total_cost_per_person,
        'min_participants' => $min_participants
    ];
}
?>

<div class="pricing-calculator bg-white border border-gray-200 rounded-lg p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">💰 Phân tích giá tour</h3>

    <!-- Service Cost Breakdown (by Day) -->
    <?php if (!empty($breakdown['service_cost_by_day'])): ?>
        <div class="mb-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Chi phí dịch vụ/người:</h4>
            <div class="space-y-1 text-sm">
                <?php foreach ($breakdown['service_cost_by_day'] as $day => $cost): ?>
                    <div class="flex justify-between text-gray-600">
                        <span>• Ngày <?= $day ?>:</span>
                        <span class="font-medium"><?= number_format($cost, 0, ',', '.') ?>đ</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-2 pt-2 border-t border-gray-200 flex justify-between font-semibold">
                <span>Tổng:</span>
                <span
                    class="text-blue-600"><?= number_format($breakdown['service_cost_total'], 0, ',', '.') ?>đ/người</span>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Chi phí dịch vụ/người:</h4>
            <div class="text-sm text-gray-600">
                <span
                    class="font-medium text-blue-600"><?= number_format($breakdown['service_cost_total'], 0, ',', '.') ?>đ/người</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Fixed Cost Breakdown -->
    <div class="mb-4 bg-gray-50 p-3 rounded">
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Chi phí cố định (tính theo
            <?= $breakdown['min_participants'] ?> người):</h4>
        <div class="space-y-1 text-sm text-gray-600">
            <?php if ($fixed_costs['guide'] > 0): ?>
                <div class="flex justify-between">
                    <span>• Lương HDV:</span>
                    <span><?= number_format($fixed_costs['guide'], 0, ',', '.') ?>đ</span>
                </div>
            <?php endif; ?>
            <?php if ($fixed_costs['management'] > 0): ?>
                <div class="flex justify-between">
                    <span>• Chi phí quản lý:</span>
                    <span><?= number_format($fixed_costs['management'], 0, ',', '.') ?>đ</span>
                </div>
            <?php endif; ?>
            <?php if ($fixed_costs['marketing'] > 0): ?>
                <div class="flex justify-between">
                    <span>• Chi phí marketing:</span>
                    <span><?= number_format($fixed_costs['marketing'], 0, ',', '.') ?>đ</span>
                </div>
            <?php endif; ?>
            <?php if ($fixed_costs['other'] > 0): ?>
                <div class="flex justify-between">
                    <span>• Chi phí khác:</span>
                    <span><?= number_format($fixed_costs['other'], 0, ',', '.') ?>đ</span>
                </div>
            <?php endif; ?>
        </div>
        <div class="mt-2 pt-2 border-t border-gray-300 flex justify-between font-semibold">
            <span>Tổng chi phí cố định:</span>
            <span><?= number_format($breakdown['fixed_cost_total'], 0, ',', '.') ?>đ</span>
        </div>
        <div class="mt-1 text-xs text-gray-500">
            Chi phí cố định/người: <span
                class="font-medium"><?= number_format($breakdown['fixed_cost_per_person'], 0, ',', '.') ?>đ</span>
        </div>
    </div>

    <!-- Total Cost -->
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
        <div class="flex justify-between items-center">
            <span class="text-sm font-semibold text-blue-900">Tổng chi phí/người:</span>
            <span
                class="text-xl font-bold text-blue-700"><?= number_format($breakdown['total_cost_per_person'], 0, ',', '.') ?>đ</span>
        </div>
        <div class="mt-2 text-xs text-blue-700">
            = Chi phí dịch vụ/người + Chi phí cố định/người
        </div>
    </div>

    <!-- Suggested Price -->
    <div class="p-4 bg-green-50 border border-green-200 rounded">
        <div class="flex justify-between items-center">
            <span class="text-sm font-semibold text-green-900">💰 Giá đề xuất/người:</span>
            <span id="suggested-price"
                class="text-xl font-bold text-green-700"><?= number_format($breakdown['suggested_price_per_person'], 0, ',', '.') ?>đ</span>
        </div>
        <div class="mt-2 text-xs text-green-700">
            (Giá = Chi phí thực tế, đã bao gồm đầy đủ: dịch vụ + nhân sự + marketing + quản lý)
        </div>
    </div>

    <!-- Info Note -->
    <div class="mt-4 text-xs text-gray-500 italic">
        <p>💡 Giá tour được tính dựa trên số người tối thiểu (<?= $breakdown['min_participants'] ?> người).
            Nếu booking có nhiều người hơn, lợi nhuận tự động tăng (giá không đổi).</p>
    </div>
</div>

<script>
    // Auto-update suggested price when costs change
    function updatePricingCalculator() {
        const serviceCost = parseFloat(document.getElementById('service-cost-total')?.value || 0);
        const fixedGuide = parseFloat(document.getElementById('fixed-cost-guide')?.value || 0);
        const fixedManagement = parseFloat(document.getElementById('fixed-cost-management')?.value || 0);
        const fixedMarketing = parseFloat(document.getElementById('fixed-cost-marketing')?.value || 0);
        const fixedOther = parseFloat(document.getElementById('fixed-cost-other')?.value || 0);
        const minParticipants = parseFloat(document.getElementById('min-participants')?.value || 15);

        const totalFixedCost = fixedGuide + fixedManagement + fixedMarketing + fixedOther;
        const fixedCostPerPerson = (minParticipants > 0) ? (totalFixedCost / minParticipants) : 0;
        const totalCostPerPerson = serviceCost + fixedCostPerPerson;

        const suggestedPriceEl = document.getElementById('suggested-price');
        if (suggestedPriceEl) {
            suggestedPriceEl.textContent = formatCurrency(totalCostPerPerson);
        }

        // Update hidden field for form submission
        const suggestedPriceInput = document.getElementById('suggested-adult-price');
        if (suggestedPriceInput) {
            suggestedPriceInput.value = Math.round(totalCostPerPerson);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + 'đ';
    }

    // Auto-calculate when inputs change
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = ['fixed-cost-guide', 'fixed-cost-management', 'fixed-cost-marketing', 'fixed-cost-other', 'min-participants'];
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', updatePricingCalculator);
            }
        });
    });
</script>