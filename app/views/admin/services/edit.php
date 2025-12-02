<?php
/**
 * ADMIN - FORM SỬA SERVICE
 * Variables: $service, $service_types, $suppliers
 */

if (!is_admin()) redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa dịch vụ</h1>

    <form method="POST" action="?act=admin&module=services&action=update" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $service['id'] ?>">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin cơ bản</h3>
                
                <!-- Service Type -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Loại dịch vụ <span class="text-red-500">*</span>
                    </label>
                    <select name="service_type_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">-- Chọn loại dịch vụ --</option>
                        <?php foreach ($service_types as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= $service['service_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Supplier -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nhà cung cấp <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>" <?= $service['supplier_id'] == $sup['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sup['company_name']) ?> (<?= $sup['supplier_code'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên dịch vụ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- COLUMN 2: CHI TIẾT & GIÁ -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Chi tiết & Giá</h3>
                
                <!-- Unit Price -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Đơn giá (VNĐ)
                    </label>
                    <input type="number" name="unit_price" value="<?= $service['unit_price'] ?>" min="0" step="1000" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Capacity -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sức chứa (người/lượt)
                    </label>
                    <input type="number" name="capacity" value="<?= $service['capacity'] ?>" min="0" 
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <small class="text-gray-500">Để trống nếu không giới hạn</small>
                </div>

                <!-- Availability -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tình trạng sẵn có</label>
                    <select name="availability" 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="available" <?= $service['availability'] == 'available' ? 'selected' : '' ?>>Sẵn sàng</option>
                        <option value="unavailable" <?= $service['availability'] == 'unavailable' ? 'selected' : '' ?>>Hết chỗ / Tạm ngưng</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú nội bộ</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($service['notes'] ?? '') ?></textarea>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select name="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                        <option value="active" <?= $service['status'] == 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="inactive" <?= $service['status'] == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6 border-t pt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin&module=services" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>
