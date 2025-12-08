<?php
/**
 * ADMIN - FORM SỬA POLICY
 * Variables: $policy
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <!-- Header - Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700">Sửa chính sách</h1>
        <a href="?act=admin&module=policies" class="text-primary-500 hover:text-primary-700 font-semibold text-sm lg:text-base flex items-center gap-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Quay lại
        </a>
    </div>

    <form method="POST" action="?act=admin&module=policies&action=update" id="policy-form"
        class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-4 lg:p-6 space-y-4 lg:space-y-6" onsubmit="return saveTinyMCEContent()">
        <input type="hidden" name="id" value="<?= $policy['id'] ?>">

        <!-- Tên chính sách -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Tên chính sách <span class="text-danger">*</span>
            </label>
            <input type="text" name="name" required value="<?= htmlspecialchars($policy['name']) ?>"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
        </div>

        <!-- Loại chính sách -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại chính sách</label>
            <select name="policy_type"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="">-- Chọn loại --</option>
                <option value="cancellation" <?= ($policy['policy_type'] ?? '') == 'cancellation' ? 'selected' : '' ?>>Hủy
                    tour</option>
                <option value="refund" <?= ($policy['policy_type'] ?? '') == 'refund' ? 'selected' : '' ?>>Hoàn tiền
                </option>
                <option value="payment" <?= ($policy['policy_type'] ?? '') == 'payment' ? 'selected' : '' ?>>Thanh toán
                </option>
                <option value="other" <?= ($policy['policy_type'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
            </select>
        </div>

        <!-- Mô tả -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả ngắn</label>
            <textarea name="description" rows="2"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all placeholder:text-primary-300 text-primary-700 text-sm lg:text-base"
                placeholder="Mô tả ngắn gọn về chính sách này"><?= htmlspecialchars($policy['description'] ?? '') ?></textarea>
        </div>

        <!-- Nội dung -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                Nội dung chính sách <span class="text-danger">*</span>
            </label>
            <textarea name="content" id="policy-content" rows="12"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base tinymce-editor"><?= htmlspecialchars($policy['content']) ?></textarea>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái</label>
            <select name="status"
                class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 text-sm lg:text-base">
                <option value="active" <?= ($policy['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($policy['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-primary-100">
            <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Cập nhật chính sách
            </button>
            <a href="?act=admin&module=policies" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-panel border border-primary-100 text-primary-700 rounded-xl hover:bg-primary-50 font-semibold transition-all text-sm lg:text-base text-center">
                Hủy
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Wait for TinyMCE to load from admin_layout.php
        const initTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                setTimeout(initTinyMCE, 100);
                return;
            }

            tinymce.init({
                selector: '#policy-content',
                license_key: 'gpl',
                height: 400,
                menubar: false,
                plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'],
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | removeformat | link | code | fullscreen | help',
                content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                branding: false,
                promotion: false
            });
        };

        initTinyMCE();
    });

    // Save TinyMCE content before form submit
    function saveTinyMCEContent() {
        if (typeof tinymce !== 'undefined') {
            const editor = tinymce.get('policy-content');
            if (editor) {
                editor.save(); // Save content to textarea
            }
        }

        // Validate content
        const content = document.getElementById('policy-content').value;
        if (!content || content.trim() === '') {
            alert('Vui lòng nhập nội dung chính sách.');
            if (typeof tinymce !== 'undefined') {
                const editor = tinymce.get('policy-content');
                if (editor) {
                    editor.focus();
                }
            }
            return false;
        }

        return true;
    }
</script>