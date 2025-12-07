<?php
/**
 * ADMIN - FORM TẠO POLICY
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Thêm chính sách mới</h1>
        <a href="?act=admin&module=policies" class="text-gray-500 hover:text-gray-700">← Quay lại</a>
    </div>

    <form method="POST" action="?act=admin&module=policies&action=store" id="policy-form"
        class="bg-white rounded-lg shadow-sm p-6 space-y-6" onsubmit="return saveTinyMCEContent()">

        <!-- Tên chính sách -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên chính sách <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" required
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="VD: Chính sách hủy tour, Chính sách hoàn tiền">
        </div>

        <!-- Loại chính sách -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại chính sách</label>
            <select name="policy_type"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="">-- Chọn loại --</option>
                <option value="cancellation">Hủy tour</option>
                <option value="refund">Hoàn tiền</option>
                <option value="payment">Thanh toán</option>
                <option value="other">Khác</option>
            </select>
            <small class="text-gray-500">Phân loại để dễ quản lý</small>
        </div>

        <!-- Mô tả -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn</label>
            <textarea name="description" rows="2"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent"
                placeholder="Mô tả ngắn gọn về chính sách này"></textarea>
        </div>

        <!-- Nội dung -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nội dung chính sách <span class="text-red-500">*</span>
            </label>
            <textarea name="content" id="policy-content" rows="12"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent tinymce-editor"
                placeholder="Nhập nội dung chi tiết của chính sách..."></textarea>
            <small class="text-gray-500">Có thể sử dụng định dạng HTML</small>
        </div>

        <!-- Trạng thái -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-accent">
                <option value="active" selected>Hoạt động</option>
                <option value="inactive">Vô hiệu</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <button type="submit" class="px-6 py-2 bg-accent text-white rounded hover:bg-blue-600">
                ✓ Tạo chính sách
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