<?php
/**
 * ADMIN - QUẢN LÝ LỊCH KHỞI HÀNH
 */
?>
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Lịch Khởi Hành</h1>
        <a href="?act=admin&module=schedules&action=create"
            class="bg-accent text-white px-4 py-2 rounded hover:bg-blue-600">
            + Thêm Lịch Mới
        </a>
    </div>

    <!-- FILTER -->
    <div class="bg-white p-4 rounded shadow-sm mb-6">
        <form action="" method="GET" class="flex gap-4">
            <input type="hidden" name="act" value="admin">
            <input type="hidden" name="module" value="schedules">

            <select name="tour_id" class="border rounded px-3 py-2 w-1/3">
                <option value="">-- Tất cả Tour --</option>
                <?php foreach ($tours as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($filters['tour_id'] == $t['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['tour_code'] . ' - ' . $t['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="start_date" value="<?= $filters['start_date'] ?>" class="border rounded px-3 py-2">

            <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200">Lọc</button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 border-b">Mã Tour</th>
                    <th class="px-6 py-3 border-b">Tên Tour</th>
                    <th class="px-6 py-3 border-b">Khởi hành</th>
                    <th class="px-6 py-3 border-b">Kết thúc</th>
                    <th class="px-6 py-3 border-b text-center">Chỗ</th>
                    <th class="px-6 py-3 border-b text-center">Đã đặt</th>
                    <th class="px-6 py-3 border-b text-right">Giá (NL)</th>
                    <th class="px-6 py-3 border-b text-center">Trạng thái</th>
                    <th class="px-6 py-3 border-b text-right">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">Chưa có lịch khởi hành nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono text-sm text-blue-600"><?= htmlspecialchars($s['tour_code']) ?></td>
                            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($s['tour_name']) ?></td>
                            <td class="px-6 py-4 text-green-700 font-bold"><?= date('d/m/Y', strtotime($s['start_date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y', strtotime($s['end_date'])) ?></td>
                            <td class="px-6 py-4 text-center"><?= $s['quota'] ?></td>
                            <td class="px-6 py-4 text-center font-bold text-accent"><?= $s['booked'] ?></td>
                            <td class="px-6 py-4 text-right"><?= number_format($s['adult_price']) ?> đ</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs rounded-full 
                                    <?= $s['status'] == 'open' ? 'bg-green-100 text-green-800' :
                                        ($s['status'] == 'closed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') ?>">
                                    <?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="#" class="text-blue-600 hover:underline">Sửa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>