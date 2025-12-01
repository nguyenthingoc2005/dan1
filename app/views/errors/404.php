<?php
/**
 * ==============================================================================
 * 404 NOT FOUND - Page Not Found
 * ==============================================================================
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e293b',
                        accent: '#3b82f6'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
        <div class="text-8xl mb-6">❌</div>
        <h1 class="text-6xl font-bold text-primary mb-4">404</h1>
        <h2 class="text-2xl font-medium text-slate-700 mb-4">Không tìm thấy trang</h2>
        <p class="text-slate-600 mb-8">
            Trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.
        </p>
        <div class="space-y-3">
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/"
                class="block w-full bg-accent hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                Về Trang Chủ
            </a>
            <button onclick="history.back()"
                class="block w-full bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium py-3 px-6 rounded-lg transition-colors">
                Quay Lại
            </button>
        </div>
    </div>
</body>

</html>