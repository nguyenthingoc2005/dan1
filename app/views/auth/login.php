<?php
/**
 * ==============================================================================
 * LOGIN VIEW - Login Form
 * ==============================================================================
 */
$base_url = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Quản lý Tour Du lịch</title>

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
                            200: '#C7D5FA',
                            300: '#A5BCF7',
                            400: '#618AF1',
                            500: '#1D58EB',
                            600: '#1B2559',
                            700: '#141B42',
                            800: '#0D122B',
                            900: '#070915'
                        },
                        accent: {
                            DEFAULT: '#7551FF',
                            light: '#B794FF',
                            hover: '#5D3FE6'
                        },
                        'accent-gradient-from': '#7551FF',
                        'accent-gradient-to': '#B794FF',
                        sidebar: '#FFFFFF',
                        panel: '#FFFFFF',
                        main: '#F4F7FE',
                        success: '#01B574',
                        'success-bg': '#E6F9F0',
                        'success-text': '#01B574',
                        warning: '#FFA70B',
                        'warning-bg': '#FFF4E5',
                        'warning-text': '#FFA70B',
                        danger: '#E31A1A',
                        'danger-bg': '#FEE5E5',
                        'danger-text': '#E31A1A',
                        info: '#4318FF',
                        'info-bg': '#EFEBFF',
                        'info-text': '#4318FF'
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'DM Sans', -apple-system, sans-serif;
        }

        .font-mono {
            font-family: 'Monaco', 'Courier New', monospace;
        }
    </style>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-6 lg:mb-8">
            <div class="inline-block px-5 lg:px-6 py-3 lg:py-4 bg-white rounded-2xl mb-4 lg:mb-6 shadow-sm">
                <i data-lucide="compass" class="w-8 h-8 lg:w-10 lg:h-10 text-accent"></i>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2">TourManager</h1>
            <p class="text-primary-200 text-sm lg:text-base">Quản lý Tour Du lịch Chuyên Nghiệp</p>
        </div>

        <!-- Login Form -->
        <div class="bg-panel rounded-2xl shadow-sm border border-primary-100 p-6 lg:p-8">
            <h2 class="text-xl lg:text-2xl font-bold text-primary-700 mb-6 lg:mb-8 text-center">Đăng Nhập</h2>

            <?php if ($error = get_error()): ?>
                <div class="mb-4 lg:mb-6 p-4 bg-danger-bg border border-danger rounded-xl">
                    <p class="text-danger-text text-sm flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                        <?php echo sanitize($error); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($success = get_success()): ?>
                <div class="mb-4 lg:mb-6 p-4 bg-success-bg border border-success rounded-xl">
                    <p class="text-success-text text-sm flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                        <?php echo sanitize($success); ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo $base_url; ?>/?act=login" class="space-y-4 lg:space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Email
                    </label>
                    <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required
                        class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 placeholder:text-primary-300 text-sm lg:text-base"
                        autofocus>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">
                        Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required
                        class="w-full px-3 lg:px-4 py-2.5 lg:py-3 bg-primary-50 border border-primary-100 rounded-xl focus:outline-none focus:border-accent focus:bg-white transition-all text-primary-700 placeholder:text-primary-300 text-sm lg:text-base">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 rounded-xl border-primary-200 text-accent focus:ring-accent">
                    <label for="remember" class="text-xs lg:text-sm text-primary-600">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white font-bold py-3 lg:py-3.5 px-4 rounded-xl shadow-sm transition-all text-sm lg:text-base">
                    ĐĂNG NHẬP
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-6 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-primary-100"></div>
                </div>
                <div class="relative flex justify-center text-xs lg:text-sm">
                    <span class="px-2 bg-panel text-primary-400">hoặc</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 lg:mt-8">
            <p class="text-primary-200 text-xs lg:text-sm">
                © 2025 Công ty Du lịch Commit2Bug
            </p>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        // Auto-fill demo account khi click
        document.querySelectorAll('.bg-primary-light').forEach(card => {
            card.addEventListener('click', function () {
                const email = this.querySelector('.text-text-secondary').textContent.trim();
                document.getElementById('email').value = email;
                document.getElementById('password').value = '123456';
                document.getElementById('email').focus();
            });
        });
    </script>
</body>

</html>