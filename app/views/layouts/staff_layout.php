<?php
/**
 * STAFF LAYOUT - Layout cho Staff
 * Sử dụng: require VIEWS_PATH . '/layouts/staff_layout.php';
 */

require_once COMMON_PATH . '/MenuHelper.php';
$user = get_auth_user();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - Tour Management</title>
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
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="bg-main font-sans text-slate-800">
    <div class="flex h-screen bg-main overflow-hidden">
        <!-- SIDEBAR -->
        <div class="w-72 bg-primary text-white flex flex-col transition-all duration-300 shadow-xl z-20 relative">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-700 flex items-center justify-between bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <span class="text-3xl filter drop-shadow-md">✈️</span>
                    <div>
                        <h1 class="font-bold text-xl tracking-wide text-white">Tour Manager</h1>
                        <p class="text-slate-400 text-xs uppercase tracking-wider font-medium">Staff Panel</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 py-6 overflow-y-auto custom-scrollbar px-3">
                <?php render_menu(); ?>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-slate-700 bg-slate-800/50">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/20">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="text-sm font-medium truncate text-white"><?php echo $user['full_name']; ?></div>
                        <div class="text-xs text-slate-400 truncate"><?php echo $user['role_display']; ?></div>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=logout"
                    class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm text-center transition-all shadow-md hover:shadow-lg font-medium">
                    Đăng xuất
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50 relative">
            <!-- Header -->
            <header
                class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm z-10">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h2 class="text-2xl font-bold text-slate-800"><?php echo $page_title ?? 'Dashboard'; ?></h2>
                </div>

                <div class="flex items-center gap-4">
                    <button
                        class="p-2 text-slate-400 hover:text-slate-600 transition-colors relative rounded-full hover:bg-slate-100">
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </button>
                    <?php render_user_menu(); ?>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <?php if ($error = get_error()): ?>
                        <div
                            class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg flex items-center gap-3 shadow-sm animate-fade-in">
                            <span class="text-2xl">⚠️</span>
                            <p class="text-red-700 font-medium"><?php echo sanitize($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($success = get_success()): ?>
                        <div
                            class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg flex items-center gap-3 shadow-sm animate-fade-in">
                            <span class="text-2xl">✅</span>
                            <p class="text-green-700 font-medium"><?php echo sanitize($success); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Load content file
                    if (!empty($content_file) && file_exists($content_file)) {
                        require $content_file;
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id);

            if (submenu) {
                if (submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    submenu.classList.add('block');
                    if (arrow) arrow.classList.add('rotate-180');
                } else {
                    submenu.classList.add('hidden');
                    submenu.classList.remove('block');
                    if (arrow) arrow.classList.remove('rotate-180');
                }
            }
        }

        function toggleUserMenu(btn) {
            const menu = btn.nextElementSibling;
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function (e) {
            const userMenu = document.querySelector('.user-menu');
            if (userMenu && !userMenu.contains(e.target)) {
                const dropdown = userMenu.querySelector('.dropdown-menu');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>
</body>

</html>