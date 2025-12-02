<?php
/**
 * ADMIN - FORM TẠO SUPPLIER
 * Variables: $supplier_code (auto-generated)
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-8xl mx-auto">
    <h1 class="text-2xl font-bold text-primary mb-6">Thêm nhà cung cấp mới</h1>

    <form method="POST" action="?act=admin&module=suppliers&action=store" class="bg-white p-6 rounded">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin cơ bản</h3>

                <!-- Company Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên công ty <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="VD: Công ty TNHH Du lịch ABC">
                </div>

                <!-- Supplier Code (readonly) -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã nhà cung cấp</label>
                    <input type="text" value="<?= $supplier_code ?>" disabled
                        class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 font-mono">
                    <small class="text-gray-500">Mã tự động sinh khi tạo</small>
                </div>

                <!-- Contact Person -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Người liên hệ</label>
                    <input type="text" name="contact_person"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="VD: Nguyễn Văn A">
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="email@company.com">
                    <small class="text-gray-500">Email phải hợp lệ và duy nhất</small>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="0901234567">
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <textarea name="address" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="Địa chỉ công ty"></textarea>
                </div>
            </div>

            <!-- COLUMN 2: THÔNG TIN TÀI CHÍNH & HỢP ĐỒNG -->
            <div>
                <h3 class="text-lg font-semibold text-primary mb-4">Thông tin tài chính & hợp đồng</h3>

                <!-- Tax Code -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã số thuế</label>
                    <input type="text" name="tax_code" pattern="[0-9]{10}"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="0123456789">
                    <small class="text-gray-500">Mã số thuế phải là 10 chữ số và duy nhất</small>
                </div>

                <!-- Bank Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngân hàng</label>
                    <input type="text" name="bank_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="VD: Vietcombank">
                </div>

                <!-- Bank Account -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tài khoản</label>
                    <input type="text" name="bank_account"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="0123456789">
                </div>

                <!-- Bank Holder -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chủ tài khoản</label>
                    <input type="text" name="bank_holder"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="Tên chủ tài khoản">
                </div>

                <!-- Contract Start Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu hợp đồng</label>
                    <input type="date" name="contract_start"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                </div>

                <!-- Contract End Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc hợp đồng</label>
                    <input type="date" name="contract_end"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <small class="text-gray-500">Ngày kết thúc phải sau ngày bắt đầu</small>
                </div>

                <!-- Payment Terms -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Điều khoản thanh toán</label>
                    <textarea name="payment_terms" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                        placeholder="VD: Thanh toán trong 30 ngày"></textarea>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH: NOTES & STATUS -->
        <div class="mt-6 border-t pt-6">
            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                    placeholder="Ghi chú thêm về nhà cung cấp"></textarea>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Vô hiệu</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                Tạo nhà cung cấp
            </button>
            <a href="?act=admin&module=suppliers" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Hủy
            </a>
        </div>
    </form>
</div>