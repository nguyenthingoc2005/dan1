<?php
/**
 * ADMIN - FORM SỬA SUPPLIER
 * Variables: $supplier
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Sửa nhà cung cấp</h1>

    <form method="POST" action="?act=admin&module=suppliers&action=update" class="bg-white p-6 rounded">
        <input type="hidden" name="id" value="<?= $supplier['id'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin cơ bản</h3>

                <!-- Company Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên công ty <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($supplier['company_name']) ?>"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Supplier Code (readonly) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhà cung cấp</label>
                    <input type="text" value="<?= htmlspecialchars($supplier['supplier_code']) ?>" disabled
                        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 font-mono">
                    <small class="text-gray-500">Mã không thể thay đổi</small>
                </div>

                <!-- Contact Person -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Người liên hệ</label>
                    <input type="text" name="contact_person"
                        value="<?= htmlspecialchars($supplier['contact_person'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($supplier['email'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <small class="text-gray-500">Email phải hợp lệ và duy nhất</small>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($supplier['phone'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <textarea name="address" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($supplier['address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- COLUMN 2: THÔNG TIN TÀI CHÍNH & HỢP ĐỒNG -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin tài chính & hợp đồng</h3>

                <!-- Tax Code -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế</label>
                    <input type="text" name="tax_code" value="<?= htmlspecialchars($supplier['tax_code'] ?? '') ?>"
                        pattern="[0-9]{10}"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <small class="text-gray-500">Mã số thuế phải là 10 chữ số và duy nhất</small>
                </div>

                <!-- Bank Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngân hàng</label>
                    <input type="text" name="bank_name" value="<?= htmlspecialchars($supplier['bank_name'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Bank Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                    <input type="text" name="bank_account"
                        value="<?= htmlspecialchars($supplier['bank_account'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Bank Holder -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chủ tài khoản</label>
                    <input type="text" name="bank_holder"
                        value="<?= htmlspecialchars($supplier['bank_holder'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Contract Start Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu hợp đồng</label>
                    <input type="date" name="contract_start" value="<?= $supplier['contract_start'] ?? '' ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Contract End Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc hợp đồng</label>
                    <input type="date" name="contract_end" value="<?= $supplier['contract_end'] ?? '' ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <small class="text-gray-500">Ngày kết thúc phải sau ngày bắt đầu</small>
                </div>

                <!-- Payment Terms -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Điều khoản thanh toán</label>
                    <textarea name="payment_terms" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($supplier['payment_terms'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH: NOTES & STATUS -->
        <div class="mt-6 border-t pt-6">
            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"><?= htmlspecialchars($supplier['notes'] ?? '') ?></textarea>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="active" <?= ($supplier['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                    </option>
                    <option value="inactive" <?= ($supplier['status'] ?? 'active') == 'inactive' ? 'selected' : '' ?>>Vô
                        hiệu</option>
                </select>
            </div>

            <!-- Info -->
            <div class="p-3 bg-gray-50 border border-gray-200 rounded">
                <p class="text-sm text-gray-600">
                    <strong>Tạo lúc:</strong> <?= date('d/m/Y H:i', strtotime($supplier['created_at'])) ?> |
                    <strong>Cập nhật:</strong>
                    <?= $supplier['updated_at'] ? date('d/m/Y H:i', strtotime($supplier['updated_at'])) : '-' ?>
                </p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Cập nhật
            </button>
            <a href="?act=admin&module=suppliers" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>