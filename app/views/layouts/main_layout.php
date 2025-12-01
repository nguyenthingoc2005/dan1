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
    .sidebar {
        width: 280px;
    }

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

    /* Scrollbar styling */
    .main-content::-webkit-scrollbar {
        width: 8px;
    }

    .main-content::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .main-content::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .main-content::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
</head>

<body class="bg-main">
    <div class="flex h-screen bg-main">
        <!-- ================================================================ -->
        <!-- SIDEBAR (Fixed Left)                                             -->
        <!-- ================================================================ -->
        <div class="sidebar bg-primary text-white flex flex-col fixed left-0 top-0 bottom-0 overflow-y-auto shadow-lg">
            <!-- Logo Section -->
            <div class="p-6 border-b border-slate-700">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl">✈️</span>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">Tour</h1>
                        <h1 class="font-bold text-lg leading-tight">Manager</h1>
                    </div>
                </div>
                <p class="text-slate-400 text-xs">v1.0 Pro</p>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 p-6 overflow-y-auto">
                <?php render_menu(); ?>
            </nav>

            <!-- User Section (Bottom) -->
            <div class="p-6 border-t border-slate-700">
                <a href="<?php echo BASE_URL; ?>/profile"
                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-700 transition-colors mb-3">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo $user['avatar']; ?>"
                            class="w-10 h-10 rounded-full">
                    <?php else: ?>
                        <div class="w-10 h-10 rounded-full bg-accent flex items-center justify-center text-sm font-bold">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <div class="text-sm font-medium"><?php echo $user['full_name']; ?></div>
                        <div class="text-xs text-slate-400"><?php echo $user['role_display']; ?></div>
                    </div>
                </a>

                <a href="<?php echo BASE_URL; ?>/logout"
                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors text-center">
                    Đăng xuất
                </a>
            </div>
        </div>

        <!-- ================================================================ -->
        <!-- MAIN CONTENT (Right Side)                                       -->
        <!-- ================================================================ -->
        <div class="flex-1 flex flex-col ml-[280px]">
            <!-- HEADER (Sticky Top) -->
            <header class="bg-panel border-b border-slate-200 sticky top-0 z-40 shadow-sm">
                <div class="px-8 py-4 flex items-center justify-between">
                    <!-- Left: Breadcrumb / Title -->
                    <div class="flex items-center gap-3">
                        <button class="p-2 hover:bg-slate-100 rounded-lg transition-colors" onclick="toggleSidebar()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-xl font-bold text-primary">
                            <?php echo $page_title ?? 'Dashboard'; ?>
                        </h2>
                    </div>

                    <!-- Right: Icons & User Menu -->
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative p-2 hover:bg-slate-100 rounded-lg transition-colors" title="Thông báo">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- Messages -->
                        <button class="p-2 hover:bg-slate-100 rounded-lg transition-colors" title="Tin nhắn">
                            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </button>

                        <!-- User Menu -->
                        <?php render_user_menu(); ?>
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA (Scrollable) -->
            <main class="main-content">
                <div class="p-8">
                    <?php
                    // Display alert messages
                    if ($error = get_error()):
                        ?>
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                            <span class="text-2xl">⚠️</span>
                            <div>
                                <p class="text-red-700 font-medium">Lỗi</p>
                                <p class="text-red-600 text-sm"><?php echo sanitize($error); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    if ($success = get_success()):
                        ?>
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="text-green-700 font-medium">Thành công</p>
                                <p class="text-green-600 text-sm"><?php echo sanitize($success); ?></p>
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
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.user-menu')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    </script>
</body>

</html>