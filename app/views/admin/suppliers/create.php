<?php
/**
 * ADMIN - FORM TẠO SUPPLIER
 * Variables: $supplier_code (auto-generated)
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-6xl mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="?act=admin&module=suppliers" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Quay lại
            </a>
        </div>
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Thêm nhà cung cấp mới</h1>
    </div>

    <form method="POST" action="?act=admin&module=suppliers&action=store" class="bg-panel p-4 lg:p-6 rounded-2xl shadow-sm border border-primary-100 space-y-4 lg:space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <!-- COLUMN 1: THÔNG TIN CƠ BẢN -->
            <div class="space-y-4 lg:space-y-6">
                <h3 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2">Thông tin cơ bản</h3>

                <!-- Company Name -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Tên công ty <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="company_name" required
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Công ty TNHH Du lịch ABC">
                </div>

                <!-- Supplier Code (readonly) -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã nhà cung cấp</label>
                    <input type="text" value="<?= $supplier_code ?>" disabled
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl text-primary-500 text-sm lg:text-base cursor-not-allowed font-mono">
                    <small class="text-xs text-primary-500 mt-1">Mã tự động sinh khi tạo</small>
                </div>

                <!-- Contact Person -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Người liên hệ</label>
                    <input type="text" name="contact_person"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Nguyễn Văn A">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="email@company.com">
                    <small class="text-xs text-primary-500 mt-1">Email phải hợp lệ và duy nhất</small>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số điện thoại</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="0901234567">
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ</label>
                    <textarea name="address" rows="3"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Địa chỉ công ty"></textarea>
                </div>
            </div>

            <!-- COLUMN 2: THÔNG TIN TÀI CHÍNH & HỢP ĐỒNG -->
            <div class="space-y-4 lg:space-y-6">
                <h3 class="text-base lg:text-lg font-bold text-primary-700 border-b border-primary-100 pb-2">Thông tin tài chính & hợp đồng</h3>

                <!-- Tax Code -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã số thuế</label>
                    <input type="text" name="tax_code" pattern="[0-9]{10}"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="0123456789">
                    <small class="text-xs text-primary-500 mt-1">Mã số thuế phải là 10 chữ số và duy nhất</small>
                </div>

                <!-- Bank Name -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngân hàng</label>
                    <input type="text" name="bank_name"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Vietcombank">
                </div>

                <!-- Bank Account -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Số tài khoản</label>
                    <input type="text" name="bank_account"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="0123456789">
                </div>

                <!-- Bank Holder -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Chủ tài khoản</label>
                    <input type="text" name="bank_holder"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="Tên chủ tài khoản">
                </div>

                <!-- Contract Start Date -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày bắt đầu hợp đồng</label>
                    <input type="date" name="contract_start"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                </div>

                <!-- Contract End Date -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ngày kết thúc hợp đồng</label>
                    <input type="date" name="contract_end"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <small class="text-xs text-primary-500 mt-1">Ngày kết thúc phải sau ngày bắt đầu</small>
                </div>

                <!-- Payment Terms -->
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điều khoản thanh toán</label>
                    <textarea name="payment_terms" rows="2"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Thanh toán trong 30 ngày"></textarea>
                </div>
            </div>
        </div>

        <!-- FULL WIDTH: NOTES & STATUS -->
        <div class="pt-4 lg:pt-6 border-t border-primary-100 space-y-4 lg:space-y-6">
            <!-- Notes -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                <textarea name="notes" rows="3"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                    placeholder="Ghi chú thêm về nhà cung cấp"></textarea>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
                <select name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Vô hiệu</option>
                </select>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <a href="?act=admin&module=suppliers" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Tạo nhà cung cấp
            </button>
        </div>
    </form>
</div>