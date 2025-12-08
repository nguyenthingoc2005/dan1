<?php
/**
 * GUIDE LAYOUT - Layout cho Guide
 * Flat Design - Không shadow, không gradient, không border dày
 * Sử dụng: require VIEWS_PATH . '/layouts/guide_layout.php';
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

    <!-- DM Sans Font (Horizon UI) -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // === PRIMARY (Navy/Purple) ===
                        primary: {
                            50: "#F4F7FE", // Background chính
                            100: "#E9EDF7", // Hover background
                            300: "#A3AED0", // Text secondary/disabled
                            500: "#707EAE", // Text muted
                            700: "#2B3674", // Text primary/Headings
                            900: "#1B2559", // Sidebar dark
                        },

                        // === ACCENT (Purple Gradient) ===
                        accent: {
                            DEFAULT: "#4318FF", // Button primary
                            hover: "#3311DB", // Button hover
                            light: "#7551FF", // Lighter variant
                            gradient: {
                                from: "#868CFF", // Gradient start
                                to: "#4318FF", // Gradient end
                            },
                        },

                        // === CHART COLORS ===
                        chart: {
                            primary: "#4318FF",
                            secondary: "#6AD2FF",
                            tertiary: "#01B574",
                            quaternary: "#FFB547",
                        },

                        // === STATUS COLORS ===
                        success: {
                            DEFAULT: "#05CD99",
                            bg: "#E6FAF5",
                            text: "#01B574",
                        },
                        warning: {
                            DEFAULT: "#FFCE20",
                            bg: "#FFF9E6",
                            text: "#FFB547",
                        },
                        danger: {
                            DEFAULT: "#EE5D50",
                            bg: "#FDEEED",
                            text: "#E31A1A",
                        },
                        info: {
                            DEFAULT: "#4299E1",
                            bg: "#EBF8FF",
                            text: "#3182CE",
                        },

                        // === BACKGROUNDS ===
                        main: "#F4F7FE", // Background chính
                        panel: "#FFFFFF", // Card/Panel
                        sidebar: "#FFFFFF", // Sidebar background
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: "DM Sans", -apple-system, sans-serif;
            letter-spacing: -0.01em;
            /* Tighter spacing */
        }

        /* Headings - Font đậm hơn */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            color: #2b3674;
        }

        /* Body text */
        body {
            color: #707eae;
            font-size: 14px;
        }

        .font-mono {
            font-family: 'Monaco', 'Courier New', monospace;
            font-variant-numeric: tabular-nums;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #A3AED0;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #707EAE;
        }
    </style>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-main text-text-primary">
    <!-- Sidebar Overlay (Mobile only) -->
    <div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity"
        onclick="toggleSidebar()"></div>

    <div class="flex h-screen bg-main overflow-hidden">
        <!-- SIDEBAR -->
        <div id="sidebar"
            class="sidebar fixed left-0 top-0 bottom-0 w-[280px] lg:w-[260px] bg-sidebar border-r border-primary-100 flex flex-col z-50 lg:z-20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-y-auto">
            <!-- Close Button (Mobile only) -->
            <button
                class="lg:hidden absolute top-4 right-4 p-2 text-primary-700 hover:bg-primary-50 rounded-xl transition-colors"
                onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Logo -->
            <div class="p-4 lg:p-6 border-b border-primary-100">
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
                    <i data-lucide="compass" class="w-6 h-6 lg:w-7 lg:h-7 text-accent"></i>
                    COMMIT2 BUG
                </h1>
            </div>

            <!-- Menu -->
            <nav class="flex-1 py-4 lg:py-6 overflow-y-auto custom-scrollbar px-3 lg:px-4 space-y-1">
                <?php render_menu(); ?>
            </nav>

            <!-- User Info -->
            <div class="p-3 lg:p-4 border-t border-primary-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-white font-bold">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="text-sm font-semibold truncate text-primary-700"><?php echo $user['full_name']; ?>
                        </div>
                        <div class="text-xs text-primary-500 truncate"><?php echo $user['role_display']; ?></div>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/?act=logout"
                    class="block w-full px-4 py-2 bg-danger hover:bg-red-600 text-white rounded-xl text-sm text-center transition-colors font-semibold">
                    Đăng xuất
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden bg-main relative ml-0 lg:ml-[260px]">
            <!-- Header -->
            <header
                class="bg-panel border-b border-primary-100 px-4 lg:px-8 py-3 lg:py-4 flex justify-between items-center z-10 sticky top-0">
                <div class="flex items-center gap-2 lg:gap-4">
                    <button class="lg:hidden p-2 text-primary-500 hover:bg-primary-50 rounded-xl transition-colors"
                        onclick="toggleSidebar()">
                        <i data-lucide="menu" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                    </button>
                    <h2 class="text-xl lg:text-2xl font-bold text-primary-700"><?php echo $page_title ?? 'Dashboard'; ?>
                    </h2>
                </div>

                <div class="flex items-center gap-2 lg:gap-4">
                    <button
                        class="p-2 text-primary-500 hover:text-primary-700 transition-colors relative rounded-xl hover:bg-primary-50">
                        <i data-lucide="bell" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                        <span
                            class="absolute top-1.5 right-1.5 lg:top-2 lg:right-2 w-2 h-2 bg-danger rounded-full"></span>
                    </button>
                    <?php render_user_menu(); ?>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-8 custom-scrollbar">
                <div class="max-w-7xl mx-auto">
                    <?php if ($error = get_error()): ?>
                        <div class="mb-6 p-4 bg-danger-bg border-l-4 border-danger rounded-r flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-danger"></i>
                            <p class="text-danger-text font-semibold"><?php echo sanitize($error); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($success = get_success()): ?>
                        <div class="mb-6 p-4 bg-success-bg border-l-4 border-success rounded-r flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-success"></i>
                            <p class="text-success-text font-semibold"><?php echo sanitize($success); ?></p>
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

        // Toggle sidebar (for mobile)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (!sidebar || !overlay) {
                console.error('Sidebar or overlay not found');
                return;
            }

            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Close sidebar when clicking outside (mobile)
        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuButtons = document.querySelectorAll('[onclick="toggleSidebar()"]');

            if (window.innerWidth < 1024 && sidebar && overlay) {
                let isMenuButton = false;
                menuButtons.forEach(btn => {
                    if (btn.contains(e.target)) isMenuButton = true;
                });

                if (!sidebar.contains(e.target) && !isMenuButton) {
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            if (window.innerWidth >= 1024) {
                if (sidebar) sidebar.classList.remove('-translate-x-full');
                if (overlay) overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>

</html>