<?php
/**
 * GUIDE - TOUR DETAIL & PASSENGER MANIFEST
 * Variables: $schedule, $tour, $passengers
 */
?>

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Chi tiết Tour</h1>
            <p class="text-gray-500 text-sm mt-1">
                <?= htmlspecialchars($tour['tour_code']) ?> - <?= htmlspecialchars($tour['name']) ?>
            </p>
        </div>
        <a href="?act=guide-tours"
            class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
            ← Quay lại
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT: INFO -->
        <div class="space-y-6">
            <div class="bg-panel rounded p-6 border border-slate-200">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Thông tin chuyến đi</h2>
                <div class="space-y-3">
                    <div>
                        <span class="block text-sm text-gray-500">Ngày khởi hành</span>
                        <span
                            class="font-medium text-gray-900"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Ngày kết thúc</span>
                        <span
                            class="font-medium text-gray-900"><?= date('d/m/Y', strtotime($schedule['end_date'])) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Điểm khởi hành</span>
                        <span
                            class="font-medium text-gray-900"><?= htmlspecialchars($tour['departure_location']) ?></span>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500">Số lượng khách</span>
                        <span class="font-bold text-blue-600 text-lg"><?= count($passengers) ?></span>
                        <span class="text-gray-400 text-sm">/ <?= $schedule['quota'] ?> chỗ</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($schedule['guide_notes'])): ?>
                <div class="bg-yellow-50 rounded p-6 border border-yellow-200">
                    <h2 class="text-lg font-bold text-yellow-800 border-b border-yellow-200 pb-2 mb-4">Ghi chú từ Điều hành
                    </h2>
                    <p class="text-yellow-900"><?= nl2br(htmlspecialchars($schedule['guide_notes'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: PASSENGER LIST -->
        <div class="lg:col-span-2">
            <div class="bg-panel rounded overflow-hidden border border-slate-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800">Danh sách hành khách</h2>
                    <button onclick="window.print()" class="text-blue-600 hover:underline text-sm">
                        <i class="fas fa-print mr-1"></i> In danh sách
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="px-6 py-3 font-medium w-10">#</th>
                                <th class="px-6 py-3 font-medium">Họ tên</th>
                                <th class="px-6 py-3 font-medium">Năm sinh</th>
                                <th class="px-6 py-3 font-medium">Giới tính</th>
                                <th class="px-6 py-3 font-medium">SĐT</th>
                                <th class="px-6 py-3 font-medium">Booking</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($passengers)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                        Chưa có hành khách nào.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($passengers as $index => $p): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 text-gray-400"><?= $index + 1 ?></td>
                                        <td class="px-6 py-3 font-medium text-gray-900">
                                            <?= htmlspecialchars($p['full_name']) ?>
                                            <?php if ($p['is_primary']): ?>
                                                <span
                                                    class="ml-1 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">Trưởng
                                                    đoàn</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">
                                            <?= $p['date_of_birth'] ? date('Y', strtotime($p['date_of_birth'])) : '-' ?>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">
                                            <?php
                                            $gender_map = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                                            echo $gender_map[$p['gender']] ?? $p['gender'];
                                            ?>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600 font-mono text-sm">
                                            <?= htmlspecialchars($p['phone']) ?>
                                        </td>
                                        <td class="px-6 py-3 text-gray-500 text-xs">
                                            <?= htmlspecialchars($p['booking_code']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>