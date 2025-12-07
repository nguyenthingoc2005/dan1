<?php
/**
 * ==============================================================================
 * TINYMCE EDITOR COMPONENT
 * ==============================================================================
 * 
 * Component tái sử dụng cho TinyMCE editor
 * 
 * Parameters:
 * - $id (required): ID của textarea
 * - $name (required): Name attribute của textarea
 * - $content (optional): Nội dung ban đầu
 * - $height (optional): Chiều cao editor (default: 400)
 * - $config (optional): Array config tùy chỉnh
 * - $placeholder (optional): Placeholder text
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$id = $id ?? 'tinymce-' . uniqid();
$name = $name ?? 'content';
$content = $content ?? '';
$height = $height ?? 400;
$placeholder = $placeholder ?? '';
$config = $config ?? [];

// Default config
$defaultConfig = [
    'license_key' => 'gpl',
    'height' => $height,
    'menubar' => false,
    'plugins' => [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
    ],
    'toolbar' => 'undo redo | formatselect | ' .
        'bold italic underline strikethrough | forecolor backcolor | ' .
        'alignleft aligncenter alignright alignjustify | ' .
        'bullist numlist | outdent indent | ' .
        'removeformat | image link | code | fullscreen | help',
    'content_style' => 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
    'images_upload_url' => '?act=admin&module=tours&action=uploadImage',
    'automatic_uploads' => true,
    'file_picker_types' => 'image',
    'relative_urls' => false,
    'remove_script_host' => false,
    'convert_urls' => true,
    'branding' => false,
    'promotion' => false,
];

// Merge custom config
$finalConfig = array_merge($defaultConfig, $config);
?>

<textarea 
    id="<?= htmlspecialchars($id) ?>" 
    name="<?= htmlspecialchars($name) ?>"
    class="tinymce-editor"
    placeholder="<?= htmlspecialchars($placeholder) ?>"
><?= htmlspecialchars($content) ?></textarea>

<script>
(function() {
    const editorId = '<?= htmlspecialchars($id) ?>';
    const config = <?= json_encode($finalConfig) ?>;
    
    // Wait for TinyMCE to be loaded
    function initEditor() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initEditor, 100);
            return;
        }
        
        // Check if already initialized
        if (tinymce.get(editorId)) {
            return;
        }
        
        // Add setup function
        config.setup = function(editor) {
            editor.on('change', function() {
                editor.save();
            });
            editor.on('init', function() {
                console.log('✅ TinyMCE initialized for ' + editorId);
            });
        };
        
        // Initialize
        tinymce.init({
            ...config,
            selector: '#' + editorId
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEditor);
    } else {
        initEditor();
    }
})();
</script>

