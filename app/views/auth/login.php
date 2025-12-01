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
    <title>Đ ăng nhập - Quản lý Tour Du lịch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e293b',
                        accent: '#3b82f6',
                        main: '#f3f4f6',
                        panel: '#ffffff'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-primary to-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block px-6 py-3 bg-white rounded-full mb-6">
                <span class="text-3xl">✈️</span>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Tour Management</h1>
            <p class="text-slate-300">Quản lý Tour Du lịch Chuyên Nghiệp</p>
        </div>

        <!-- Login Form -->
        <div class="bg-panel rounded-lg p-8 shadow-2xl">
            <h2 class="text-2xl font-bold text-primary mb-8 text-center">Đăng Nhập</h2>

            <?php if ($error = get_error()): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-700 text-sm flex items-center gap-2">
                        <span>⚠️</span>
                        <?php echo sanitize($error); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($success = get_success()): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm flex items-center gap-2">
                        <span>✅</span>
                        <?php echo sanitize($success); ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo $base_url; ?>/?act=login" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                        autofocus>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                        Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 rounded border-slate-300 text-accent focus:ring-accent">
                    <label for="remember" class="text-sm text-slate-600">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-accent hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-all transform hover:scale-105 active:scale-95">
                    ĐĂNG NHẬP
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-6 relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-panel text-slate-500">hoặc</span>
                </div>
            </div>

            <!-- Demo Accounts -->
            <div class="mt-6 space-y-2">
                <p class="text-xs text-slate-500 text-center font-medium">TÀI KHOẢN DEMO</p>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-center p-2 bg-slate-50 rounded text-xs">
                        <div class="font-medium text-slate-700">Admin</div>
                        <div class="text-slate-500 text-xs">admin@company.com</div>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded text-xs">
                        <div class="font-medium text-slate-700">Staff</div>
                        <div class="text-slate-500 text-xs">staff@company.com</div>
                    </div>
                    <div class="text-center p-2 bg-slate-50 rounded text-xs">
                        <div class="font-medium text-slate-700">Guide</div>
                        <div class="text-slate-500 text-xs">guide@company.com</div>
                    </div>
                </div>
                <p class="text-xs text-slate-500 text-center">Mật khẩu: <code
                        class="bg-slate-100 px-2 py-1 rounded">123456</code></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-slate-400 text-sm">
                © 2024 Công ty Du lịch ABC
            </p>
        </div>
    </div>

    <script>
        // Auto-fill demo account khi click
        document.querySelectorAll('.bg-slate-50').forEach(card => {
            card.addEventListener('click', function () {
                const email = this.querySelector('.text-slate-500').textContent.trim();
                document.getElementById('email').value = email;
                document.getElementById('password').value = '123456';
                document.getElementById('email').focus();
            });     });
    </script>
</body>

</html>