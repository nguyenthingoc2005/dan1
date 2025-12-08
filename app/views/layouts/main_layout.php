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
    .main-content {
        flex: 1;
        overflow-y: auto;
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .submenu.show {
        max-height: 500px;
    }

    .dropdown-menu {
        display: none;
    }

    .dropdown-menu.show {
        display: block;
    }

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
    .main-content::-webkit-scrollbar {
        width: 6px;
    }

    .main-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .main-content::-webkit-scrollbar-thumb {
        background: #A3AED0;
        border-radius: 3px;
    }

    .main-content::-webkit-scrollbar-thumb:hover {
        background: #707EAE;
    }
</style>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-main">
    <!-- Sidebar Overlay (Mobile only) -->
    <div id="sidebar-overlay" class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity"
        onclick="toggleSidebar()"></div>

    <div class="flex h-screen bg-main">
        <!-- ================================================================ -->
        <!-- SIDEBAR (Fixed Left)                                             -->
        <!-- ================================================================ -->
        <div id="sidebar"
            class="sidebar fixed left-0 top-0 bottom-0 w-[280px] lg:w-[260px] bg-sidebar border-r border-primary-100 flex flex-col z-50 lg:z-20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 overflow-y-auto">
            <!-- Close Button (Mobile only) -->
            <button
                class="lg:hidden absolute top-4 right-4 p-2 text-primary-700 hover:bg-primary-50 rounded-xl transition-colors"
                onclick="toggleSidebar()">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <!-- Logo Section -->
            <div class="p-4 lg:p-6 border-b border-primary-100">
                <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2">
                    <i data-lucide="compass" class="w-6 h-6 lg:w-7 lg:h-7 text-accent"></i>
                    COMMIT2 BUG
                </h1>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 py-4 lg:py-6 overflow-y-auto px-3 lg:px-4 space-y-1">
                <?php render_menu(); ?>
            </nav>

            <!-- User Section (Bottom) -->
            <div class="p-3 lg:p-4 border-t border-primary-100">
                <a href="<?php echo BASE_URL; ?>/profile"
                    class="flex items-center gap-3 p-3 rounded-xl hover:bg-primary-50 transition-colors mb-3">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo $user['avatar']; ?>"
                            class="w-10 h-10 rounded-full">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-white font-bold">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-primary-700"><?php echo $user['full_name']; ?></div>
                        <div class="text-xs text-primary-500"><?php echo $user['role_display']; ?></div>
                    </div>
                </a>

                <a href="<?php echo BASE_URL; ?>/logout"
                    class="w-full px-4 py-2 bg-danger hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors text-center">
                    Đăng xuất
                </a>
            </div>
        </div>

        <!-- ================================================================ -->
        <!-- MAIN CONTENT (Right Side)                                       -->
        <!-- ================================================================ -->
        <div class="flex-1 flex flex-col ml-0 lg:ml-[260px]">
            <!-- HEADER (Sticky Top) -->
            <header class="bg-panel border-b border-primary-100 sticky top-0 z-30">
                <div class="px-4 lg:px-8 py-3 lg:py-4 flex items-center justify-between">
                    <!-- Left: Breadcrumb / Title -->
                    <div class="flex items-center gap-2 lg:gap-4">
                        <button class="lg:hidden p-2 text-primary-500 hover:bg-primary-50 rounded-xl transition-colors"
                            onclick="toggleSidebar()">
                            <i data-lucide="menu" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                        </button>
                        <h2 class="text-xl lg:text-2xl font-bold text-primary-700">
                            <?php echo $page_title ?? 'Dashboard'; ?>
                        </h2>
                    </div>

                    <!-- Right: Icons & User Menu -->
                    <div class="flex items-center gap-2 lg:gap-4">
                        <!-- Notifications -->
                        <button
                            class="relative p-2 text-primary-500 hover:text-primary-700 hover:bg-primary-50 rounded-xl transition-colors"
                            title="Thông báo">
                            <i data-lucide="bell" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                            <span
                                class="absolute top-1.5 right-1.5 lg:top-2 lg:right-2 w-2 h-2 bg-danger rounded-full"></span>
                        </button>

                        <!-- Messages (Hidden on mobile) -->
                        <button
                            class="hidden lg:block p-2 text-primary-500 hover:text-primary-700 hover:bg-primary-50 rounded-xl transition-colors"
                            title="Tin nhắn">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </button>

                        <!-- User Menu -->
                        <?php render_user_menu(); ?>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA (Scrollable) -->
            <main class="main-content">
                <div class="p-4 lg:p-8">
                    <?php
                    // Display alert messages
                    if ($error = get_error()):
                        ?>
                        <div class="mb-6 p-4 bg-danger-bg border-l-4 border-danger rounded-r flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-danger"></i>
                            <div>
                                <p class="text-danger-text font-semibold">Lỗi</p>
                                <p class="text-danger-text text-sm"><?php echo sanitize($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if ($success = get_success()):
                        ?>
                        <div class="mb-6 p-4 bg-success-bg border-l-4 border-success rounded-r flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-success"></i>
                            <div>
                                <p class="text-success-text font-semibold">Thành công</p>
                                <p class="text-success-text text-sm"><?php echo sanitize($success); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Page Content -->
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
        // Toggle submenu
        function toggleSubmenu(button) {
            const submenu = button.nextElementSibling;
            const icon = button.querySelector('svg');

            submenu.classList.toggle('show');
            icon.classList.toggle('rotate-180');
        }

        // Toggle user menu dropdown
        function toggleUserMenu(button) {
            const menu = button.closest('.user-menu').querySelector('.dropdown-menu');
            menu.classList.toggle('show');
        }

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

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.user-menu')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Initialize Lucide Icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>

</html>