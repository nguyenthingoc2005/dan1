<?php
/**
 * ==============================================================================
 * ACCESS DENIED - Unauthorized Access Page
 * ==============================================================================
 */
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối</title>
    
    <!-- DM Sans Font - Horizon UI -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Horizon UI - Navy/Purple Palette
                        primary: {
                            DEFAULT: '#1B2559',
                            50: '#F4F7FE',
                            100: '#E9EEFD',
                            600: '#1B2559',
                            700: '#141B42'
                        },
                        accent: {
                            DEFAULT: '#7551FF',
                            hover: '#5D3FE6'
                        },
                        'accent-gradient-from': '#7551FF',
                        'accent-gradient-to': '#B794FF',
                        panel: '#FFFFFF',
                        main: '#F4F7FE',
                        danger: '#E31A1A',
                        'danger-bg': '#FEE5E5',
                        'danger-text': '#E31A1A'
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'DM Sans', -apple-system, sans-serif;
        }
    </style>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gradient-to-br from-primary-50 to-primary-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
        <div class="mb-6 lg:mb-8">
            <div class="inline-block p-4 lg:p-5 bg-danger-bg rounded-2xl mb-4">
                <i data-lucide="shield-x" class="w-16 h-16 lg:w-20 lg:h-20 text-danger"></i>
            </div>
        </div>
        <h1 class="text-5xl lg:text-6xl font-bold text-danger mb-3 lg:mb-4">403</h1>
        <h2 class="text-xl lg:text-2xl font-semibold text-primary-700 mb-3 lg:mb-4">Truy cập bị từ chối</h2>
        <p class="text-sm lg:text-base text-primary-600 mb-6 lg:mb-8">
            Bạn không có quyền truy cập trang này. Vui lòng liên hệ quản trị viên nếu bạn nghĩ đây là lỗi.
        </p>
        <div class="space-y-3">
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/"
                class="block w-full bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-semibold py-3 lg:py-3.5 px-6 rounded-xl shadow-sm transition-all text-sm lg:text-base">
                <i data-lucide="home" class="w-4 h-4 inline mr-2"></i>
                Về Trang Chủ
            </a>
            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>/logout"
                class="block w-full bg-panel border border-primary-100 hover:bg-primary-50 text-primary-700 font-semibold py-3 lg:py-3.5 px-6 rounded-xl transition-all text-sm lg:text-base">
                <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>
                Đăng xuất
            </a>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>

</html>
