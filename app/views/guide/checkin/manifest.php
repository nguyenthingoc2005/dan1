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
<body class="bg-white">

<div class="max-w-4xl mx-auto p-8">
    <!-- Header -->
    <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">DANH SÁCH HÀNH KHÁCH</h1>
        <div class="text-lg text-gray-700">
            <div class="font-bold"><?= htmlspecialchars($tour['name']) ?></div>
            <div class="text-sm mt-1">
                Mã tour: <span class="font-mono font-bold"><?= htmlspecialchars($tour['tour_code']) ?></span>
                <span class="mx-2">•</span>
                Ngày khởi hành: <span class="font-bold"><?= date('d/m/Y', strtotime($schedule['start_date'])) ?></span>
            </div>
        </div>
    </div>

    <!-- Tour Info -->
    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
        <div>
            <strong>Điểm khởi hành:</strong> <?= htmlspecialchars($tour['departure_location']) ?>
        </div>
        <div>
            <strong>Thời gian:</strong> <?= $tour['duration_days'] ?> ngày <?= $tour['duration_nights'] ?> đêm
        </div>
        <div>
            <strong>Tổng số khách:</strong> <span class="font-bold"><?= count($passengers) ?></span>
        </div>
        <div>
            <strong>Ngày in:</strong> <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <!-- Check-in Stats -->
    <?php if (isset($stats) && $stats['checked_in'] > 0): ?>
        <div class="bg-gray-100 p-4 rounded mb-6 text-sm">
            <strong>Thống kê check-in:</strong>
            ✅ Có mặt: <?= $stats['present'] ?> | 
            ❌ Vắng mặt: <?= $stats['absent'] ?> | 
            ⏰ Đến muộn: <?= $stats['late'] ?> | 
            ⏳ Chưa check-in: <?= $stats['not_checked'] ?>
        </div>
    <?php endif; ?>

    <!-- Passenger List -->
    <table class="w-full border-collapse border border-gray-800 text-sm">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="border border-gray-800 px-3 py-2 text-left w-10">#</th>
                <th class="border border-gray-800 px-3 py-2 text-left">Họ và tên</th>
                <th class="border border-gray-800 px-3 py-2 text-left">Năm sinh</th>
                <th class="border border-gray-800 px-3 py-2 text-left">Giới tính</th>
                <th class="border border-gray-800 px-3 py-2 text-left">SĐT</th>
                <th class="border border-gray-800 px-3 py-2 text-left">Booking</th>
                <th class="border border-gray-800 px-3 py-2 text-center w-24">Check-in</th>
                <th class="border border-gray-800 px-3 py-2 text-left">Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($passengers)): ?>
                <tr>
                    <td colspan="8" class="border border-gray-800 px-3 py-4 text-center text-gray-500">
                        Chưa có hành khách nào.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($passengers as $index => $p): ?>
                    <tr>
                        <td class="border border-gray-800 px-3 py-2 text-center"><?= $index + 1 ?></td>
                        <td class="border border-gray-800 px-3 py-2 font-medium">
                            <?= htmlspecialchars($p['full_name']) ?>
                            <?php if ($p['is_primary']): ?>
                                <span class="text-xs bg-blue-100 px-1 rounded">(Trưởng đoàn)</span>
                            <?php endif; ?>
                        </td>
                        <td class="border border-gray-800 px-3 py-2">
                            <?= $p['date_of_birth'] ? date('Y', strtotime($p['date_of_birth'])) : '-' ?>
                        </td>
                        <td class="border border-gray-800 px-3 py-2">
                            <?php
                            $gender_map = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                            echo $gender_map[$p['gender']] ?? $p['gender'];
                            ?>
                        </td>
                        <td class="border border-gray-800 px-3 py-2 font-mono"><?= htmlspecialchars($p['phone']) ?></td>
                        <td class="border border-gray-800 px-3 py-2 font-mono text-xs"><?= htmlspecialchars($p['booking_code']) ?></td>
                        <td class="border border-gray-800 px-3 py-2 text-center">
                            <?php
                            $status_map = [
                                'present' => '✅',
                                'absent' => '❌',
                                'late' => '⏰'
                            ];
                            echo $status_map[$p['checkin_status']] ?? '⏳';
                            if ($p['checkin_time']) {
                                echo '<br><span class="text-xs">' . date('H:i', strtotime($p['checkin_time'])) . '</span>';
                            }
                            ?>
                        </td>
                        <td class="border border-gray-800 px-3 py-2 text-xs"><?= htmlspecialchars($p['checkin_notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="mt-8 grid grid-cols-2 gap-8 text-sm">
        <div>
            <div class="font-bold mb-2">Hướng dẫn viên</div>
            <div class="border-t-2 border-gray-800 pt-2" style="min-height: 50px;">
                <?= htmlspecialchars($schedule['guide_name'] ?? '') ?>
            </div>
        </div>
        <div>
            <div class="font-bold mb-2">Ký tên</div>
            <div class="border-t-2 border-gray-800 pt-2" style="min-height: 50px;"></div>
        </div>
    </div>

    <!-- Print Button (hidden when printing) -->
    <div class="no-print mt-8 text-center">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
            🖨️ In danh sách
        </button>
        <a href="?act=guide-checkin&action=show&schedule_id=<?= $schedule['id'] ?>" 
            class="ml-3 px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 font-medium">
            ← Quay lại
        </a>
    </div>
</div>

</body>
</html>

