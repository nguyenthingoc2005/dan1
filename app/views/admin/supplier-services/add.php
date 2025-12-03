<?php
/**
 * ADMIN - ADD SERVICE TO SUPPLIER
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-2">Thêm dịch vụ</h1>
    <p class="text-gray-600 mb-6">
        Nhà cung cấp: <strong><?= htmlspecialchars($supplier['company_name']) ?></strong>
    </p>

    <form method="POST" action="?act=admin&module=supplier-services&action=store-service"
        class="bg-white p-6 rounded shadow">
        <input type="hidden" name="supplier_id" value="<?= $supplier['id'] ?>">

        <!-- Service Type -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Loại dịch vụ <span class="text-red-500">*</span>
            </label>
            <select name="service_type_id" required
                class="w-full px-3 py-2 border rounded focus:outline-none focus:border-accent">
                <option value="">-- Chọn loại dịch vụ --</option>
                <?php foreach ($service_types as $type): ?>
                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Service Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên dịch vụ <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required
                class="w-full px-3 py-2 border rounded focus:outline-none focus:border-accent"
                placeholder="VD: Phòng Deluxe">
        </div>

        <!-- Unit -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính</label>
            <input type="text" name="unit"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:border-accent"
                placeholder="VD: phòng/đêm, suất, xe/ngày">
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá dự kiến (VNĐ)</label>
            <input type="number" name="estimated_price" min="0" step="1000"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:border-accent" placeholder="0">
        </div>

        <!-- Notes -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
            <textarea name="notes" rows="3"
                class="w-full px-3 py-2 border rounded focus:outline-none focus:border-accent"
                placeholder="Ghi chú về dịch vụ"></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Thêm dịch vụ
            </button>
            <a href="?act=admin&module=supplier-services"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>