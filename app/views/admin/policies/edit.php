<?php
/**
 * ADMIN - FORM SỬA POLICY
 * Variables: $policy
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Sửa chính sách</h1>
        <a href="?act=admin&module=policies" class="text-gray-500 hover:text-gray-700">← Quay lại</a>
    </div>

    <form method="POST" action="?act=admin&module=policies&action=update" id="policy-form"
        class="bg-white rounded-lg shadow-sm p-6 space-y-6" onsubmit="return saveTinyMCEContent()">
        <input type="hidden" name="id" value="<?= $policy['id'] ?>">

        <!-- Tên chính sách -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên chính sách <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required value="<?= htmlspecialchars($policy['name']) ?>"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
        </div>

        <!-- Loại chính sách -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại chính sách</label>
            <select name="policy_type"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
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
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
            <textarea name="description" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="Mô tả ngắn gọn về chính sách này"><?= htmlspecialchars($policy['description'] ?? '') ?></textarea>
        </div>

        <!-- Nội dung -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nội dung chính sách <span class="text-red-500">*</span>
            </label>
            <textarea name="content" id="policy-content" rows="12"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent tinymce-editor"><?= htmlspecialchars($policy['content']) ?></textarea>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" <?= ($policy['status'] ?? 'active') == 'active' ? 'selected' : '' ?>>Hoạt động
                </option>
                <option value="inactive" <?= ($policy['status'] ?? '') == 'inactive' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                ✓ Cập nhật chính sách
            </button>
            <a href="?act=admin&module=policies" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
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