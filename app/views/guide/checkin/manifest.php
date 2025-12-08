<?php
/**
 * GUIDE - IN DANH SÁCH HÀNH KHÁCH (MANIFEST)
 * Variables: $schedule, $tour, $passengers, $stats
 * This page is optimized for printing
 */
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Hành khách - <?= htmlspecialchars($tour['tour_code']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 20px; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-panel">

<div class="max-w-4xl mx-auto p-4 lg:p-8">
    <!-- Header -->
    <div class="text-center mb-6 lg:mb-8 border-b-2 border-primary-700 pb-3 lg:pb-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-primary-700 mb-2">DANH SÁCH HÀNH KHÁCH</h1>
        <div class="text-base lg:text-lg text-primary-600">
            <div class="font-bold"><?= htmlspecialchars($tour['name']) ?></div>
            <div class="text-xs lg:text-sm mt-1">
                Mã tour: <span class="font-mono font-bold text-primary-700"><?= htmlspecialchars($tour['tour_code']) ?></span>
                <span class="mx-2">•</span>
                Ngày khởi hành: <span class="font-bold text-primary-700"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Tour Info -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 mb-4 lg:mb-6 text-xs lg:text-sm">
        <div>
            <strong class="text-primary-700">Điểm khởi hành:</strong> <span class="text-primary-600"><?= htmlspecialchars($tour['departure_location']) ?></span>
        </div>
        <div>
            <strong class="text-primary-700">Thời gian:</strong> <span class="text-primary-600"><?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm</span>
        </div>
        <div>
            <strong class="text-primary-700">Tổng số khách:</strong> <span class="font-bold text-primary-700"><?= count($passengers) ?></span>
        </div>
        <div>
            <strong class="text-primary-700">Ngày in:</strong> <span class="text-primary-600"><?= date('d/m/Y H:i') ?></span>
        </div>
    </div>

    <!-- Check-in Stats -->
    <?php if (isset($stats) && $stats['checked_in'] > 0): ?>
        <div class="bg-info-bg border border-info rounded-xl p-3 lg:p-4 mb-4 lg:mb-6 text-xs lg:text-sm">
            <strong class="text-info-dark">Thống kê check-in:</strong>
            <span class="text-info-text">
                ✅ Có mặt: <?= $stats['present'] ?> | 
                ❌ Vắng mặt: <?= $stats['absent'] ?> | 
                ⏰ Đến muộn: <?= $stats['late'] ?> | 
                ⏳ Chưa check-in: <?= $stats['not_checked'] ?>
            </span>
        </div>
    <?php endif; ?>

    <!-- Passenger List -->
    <table class="w-full border-collapse border border-primary-700 text-xs lg:text-sm">
        <thead>
            <tr class="bg-primary-700 text-white">
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left w-10">#</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">Họ và tên</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">Năm sinh</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">Giới tính</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">SĐT</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">Booking</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-center w-24">Check-in</th>
                <th class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-left">Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($passengers)): ?>
                <tr>
                    <td colspan="8" class="border border-primary-700 px-3 py-4 text-center text-primary-500">
                        Chưa có hành khách nào.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($passengers as $index => $p): ?>
                    <tr class="hover:bg-primary-50">
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-center text-primary-700"><?= $index + 1 ?></td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 font-semibold text-primary-700">
                            <?= htmlspecialchars($p['full_name']) ?>
                            <?php if ($p['is_primary']): ?>
                                <span class="text-xs bg-info-bg text-info-dark px-1.5 py-0.5 rounded-lg font-semibold">(Trưởng đoàn)</span>
                            <?php endif; ?>
                        </td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-primary-600">
                            <?= $p['date_of_birth'] ? date('Y', strtotime($p['date_of_birth'])) : '-' ?>
                        </td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-primary-600">
                            <?php
                            $gender_map = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                            echo $gender_map[$p['gender']] ?? $p['gender'];
                            ?>
                        </td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 font-mono text-primary-600"><?= htmlspecialchars($p['phone']) ?></td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 font-mono text-xs text-primary-600"><?= htmlspecialchars($p['booking_code']) ?></td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-center">
                            <?php
                            $status_map = [
                                'present' => '✅',
                                'absent' => '❌',
                                'late' => '⏰'
                            ];
                            echo $status_map[$p['checkin_status']] ?? '⏳';
                            if ($p['checkin_time']) {
                                echo '<br><span class="text-xs text-primary-500">' . date('H:i', strtotime($p['checkin_time'])) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="border border-primary-700 px-2 lg:px-3 py-1.5 lg:py-2 text-xs text-primary-600"><?= htmlspecialchars($p['checkin_notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="mt-6 lg:mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-8 text-xs lg:text-sm">
        <div>
            <div class="font-bold text-primary-700 mb-2">Hướng dẫn viên</div>
            <div class="border-t-2 border-primary-700 pt-2 text-primary-600" style="min-height: 50px;">
                <?= htmlspecialchars($schedule['guide_name'] ?? '') ?>
            </div>
        </div>
        <div>
            <div class="font-bold text-primary-700 mb-2">Ký tên</div>
            <div class="border-t-2 border-primary-700 pt-2" style="min-height: 50px;"></div>
        </div>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print mt-6 lg:mt-8 text-center">
        <button onclick="window.print()" class="px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center gap-2 mx-auto">
            <i data-lucide="printer" class="w-4 h-4"></i>
            In danh sách
        </button>
        <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>" 
            class="ml-3 px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-600 hover:opacity-90 text-white rounded-xl font-semibold transition-all text-sm lg:text-base inline-flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>
</div>

</body>
</html>

