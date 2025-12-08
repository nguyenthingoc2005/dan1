<?php
/**
 * ==============================================================================
 * MENU HELPER - Sidebar Menu Generation
 * ==============================================================================
 * 
 * Chức năng:
 * - Generate sidebar menu theo role (admin/staff/guide)
 * - Generate user dropdown menu
 * - Check active route highlighting
 * 
 * Theo Vibe Coding: Simple is Best
 * 
 * @version 1.0
 * @date 2024-12-01
 * ==============================================================================
 */

/**
 * Lấy menu items theo role
 * 
 * @param string $role 'admin', 'staff', 'guide'
 * @return array Menu structure
 */
function get_menu_items($role)
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';

    $menus = [
        'admin' => [
            // ============================================================
            // 1. DASHBOARD
            // ============================================================
            [
                'type' => 'item',
                'icon' => 'layout-dashboard',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=admin',
                'active_pattern' => 'admin'
            ],

            // ============================================================
            // 2. QUẢN LÝ TOUR (Group with submenu)
            // ============================================================
            [
                'type' => 'group',
                'label' => 'Quản lý Tour',
                'icon' => 'map-pin',
                'submenu' => [
                    [
                        'icon' => 'map-pin',
                        'label' => 'Quản lý Tour',
                        'url' => $base_url . '/?act=admin&module=tours',
                        'active_pattern' => 'admin:tours'
                    ],
                    [
                        'icon' => 'calendar',
                        'label' => 'Lịch Khởi Hành',
                        'url' => $base_url . '/?act=admin&module=schedules',
                        'active_pattern' => 'admin:schedules'
                    ],
                    [
                        'icon' => 'clipboard-check',
                        'label' => 'Tour Đã Chốt',
                        'url' => $base_url . '/?act=admin&module=tour-operations',
                        'active_pattern' => 'admin:tour-operations'
                    ],
                ]
            ],

            // ============================================================
            // 3. QUẢN LÝ BOOKING (Group with submenu)
            // ============================================================
            [
                'type' => 'group',
                'label' => 'Quản lý Booking',
                'icon' => 'calendar-check',
                'submenu' => [
                    [
                        'icon' => 'calendar-check',
                        'label' => 'Quản lý Đặt Tour',
                        'url' => $base_url . '/?act=admin&module=bookings',
                        'active_pattern' => 'admin:bookings'
                    ],
                    [
                        'icon' => 'file-x',
                        'label' => 'Chính sách Hủy',
                        'url' => $base_url . '/?act=admin&module=cancellation-policies',
                        'active_pattern' => 'admin:cancellation-policies'
                    ],
                    [
                        'icon' => 'x-circle',
                        'label' => 'Hủy Booking',
                        'url' => $base_url . '/?act=admin&module=cancellations',
                        'active_pattern' => 'admin:cancellations'
                    ],
                ]
            ],

            // ============================================================
            // 4. QUẢN LÝ KHÁCH HÀNG
            // ============================================================
            [
                'type' => 'item',
                'icon' => 'users',
                'label' => 'Khách hàng',
                'url' => $base_url . '/?act=admin&module=customers',
                'active_pattern' => 'admin:customers'
            ],

            // ============================================================
            // 5. QUẢN LÝ TÀI CHÍNH (Group with submenu)
            // ============================================================
            [
                'type' => 'group',
                'label' => 'Quản lý Tài chính',
                'icon' => 'credit-card',
                'submenu' => [
                    [
                        'icon' => 'credit-card',
                        'label' => 'Thanh toán',
                        'url' => $base_url . '/?act=admin&module=payments',
                        'active_pattern' => 'admin:payments'
                    ],
                    [
                        'icon' => 'tag',
                        'label' => 'Mã giảm giá',
                        'url' => $base_url . '/?act=admin&module=discount-codes',
                        'active_pattern' => 'admin:discount-codes'
                    ],
                    [
                        'icon' => 'dollar-sign',
                        'label' => 'Chi phí phát sinh',
                        'url' => $base_url . '/?act=admin&module=expenses',
                        'active_pattern' => 'admin:expenses'
                    ],
                ]
            ],

            // ============================================================
            // 6. VẬN HÀNH TOUR
            // ============================================================
            [
                'type' => 'item',
                'icon' => 'book-open',
                'label' => 'Nhật ký Tour',
                'url' => $base_url . '/?act=admin&module=journals',
                'active_pattern' => 'admin:journals'
            ],

            // ============================================================
            // 7. BÁO CÁO
            // ============================================================
            [
                'type' => 'item',
                'icon' => 'bar-chart-3',
                'label' => 'Báo cáo',
                'url' => $base_url . '/?act=admin&module=reports',
                'active_pattern' => 'admin:reports'
            ],

            // ============================================================
            // 8. QUẢN LÝ NHÂN SỰ
            // ============================================================
            [
                'type' => 'item',
                'icon' => 'user-check',
                'label' => 'Nhân viên',
                'url' => $base_url . '/?act=admin&module=users',
                'active_pattern' => 'admin:users'
            ],

            // ============================================================
            // 9. CẤU HÌNH HỆ THỐNG (Group with submenu)
            // ============================================================
            [
                'type' => 'group',
                'label' => 'Cấu hình Hệ thống',
                'icon' => 'settings',
                'submenu' => [
                    [
                        'icon' => 'map',
                        'label' => 'Địa điểm & Dịch vụ',
                        'url' => $base_url . '/?act=admin&module=location-services',
                        'active_pattern' => 'admin:location-services'
                    ],
                    [
                        'icon' => 'bell',
                        'label' => 'Loại dịch vụ',
                        'url' => $base_url . '/?act=admin&module=service-types',
                        'active_pattern' => 'admin:service-types'
                    ],
                    [
                        'icon' => 'clipboard-list',
                        'label' => 'Chính sách',
                        'url' => $base_url . '/?act=admin&module=policies',
                        'active_pattern' => 'admin:policies'
                    ],
                    [
                        'icon' => 'truck',
                        'label' => 'Quản lý Xe',
                        'url' => $base_url . '/?act=admin&module=vehicles',
                        'active_pattern' => 'admin:vehicles'
                    ],
                    [
                        'icon' => 'user-cog',
                        'label' => 'Quản lý Tài xế',
                        'url' => $base_url . '/?act=admin&module=drivers',
                        'active_pattern' => 'admin:drivers'
                    ],
                ]
            ],
        ],

        'staff' => [
            [
                'icon' => 'layout-dashboard',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=staff-dashboard',
                'active_pattern' => 'staff-dashboard'
            ],
            [
                'icon' => 'map-pin',
                'label' => 'Tours',
                'url' => $base_url . '/?act=staff-tours',
                'active_pattern' => 'staff-tours',
                'submenu' => [
                    ['label' => 'Tours của tôi', 'url' => $base_url . '/?act=staff-tours'],
                    ['label' => 'Tạo Tour mới', 'url' => $base_url . '/?act=staff-tours&action=create'],
                ]
            ],
            [
                'icon' => 'calendar-check',
                'label' => 'Bookings',
                'url' => $base_url . '/?act=staff-bookings',
                'active_pattern' => 'staff-bookings',
                'submenu' => [
                    ['label' => 'Bookings của tôi', 'url' => $base_url . '/?act=staff-bookings'],
                    ['label' => 'Tạo Booking mới', 'url' => $base_url . '/?act=staff-bookings&action=create'],
                ]
            ],
            [
                'icon' => 'calendar',
                'label' => 'Lịch Tour',
                'url' => $base_url . '/?act=staff-schedules',
                'active_pattern' => 'staff-schedules'
            ],
            [
                'icon' => 'users',
                'label' => 'Customers',
                'url' => $base_url . '/?act=staff-customers',
                'active_pattern' => 'staff-customers',
                'submenu' => [
                    ['label' => 'Danh sách khách', 'url' => $base_url . '/?act=staff-customers'],
                    ['label' => 'Thêm khách mới', 'url' => $base_url . '/?act=staff-customers&action=create'],
                ]
            ],
            [
                'icon' => 'credit-card',
                'label' => 'Payments',
                'url' => $base_url . '/?act=staff-payments',
                'active_pattern' => 'staff-payments'
            ],
            [
                'icon' => 'user',
                'label' => 'Thông tin cá nhân',
                'url' => $base_url . '/?act=profile',
                'active_pattern' => 'profile'
            ],
        ],

        'guide' => [
            [
                'icon' => 'layout-dashboard',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=guide-dashboard',
                'active_pattern' => 'guide-dashboard'
            ],
            [
                'icon' => 'calendar',
                'label' => 'Lịch Tour',
                'url' => $base_url . '/?act=guide-tours',
                'active_pattern' => 'guide-tours'
            ],
            [
                'icon' => 'user',
                'label' => 'Thông tin cá nhân',
                'url' => $base_url . '/?act=profile',
                'active_pattern' => 'profile'
            ],
        ],
    ];

    return $menus[$role] ?? [];
}

/**
 * Check if route is active
 * 
 * Supports both old-style (?act=admin-dashboard) and new module-based (?act=admin&module=users)
 * 
 * @param string $pattern Route pattern to match (e.g., 'admin-dashboard' or 'admin:users')
 * @return bool
 */
function is_active_route($pattern)
{
    $current_act = $_GET['act'] ?? '';
    $current_module = $_GET['module'] ?? '';

    // New pattern: "act:module" (e.g., "admin:users")
    if (strpos($pattern, ':') !== false) {
        list($act_pattern, $module_pattern) = explode(':', $pattern, 2);
        return $current_act === $act_pattern && $current_module === $module_pattern;
    }

    // Old pattern: exact match, but only if no module is present
    // This prevents Dashboard from being active when viewing modules
    if (!empty($current_module)) {
        return false; // If there's a module, old patterns shouldn't match
    }
    return $current_act === $pattern; // Exact match only
}

/**
 * Render sidebar menu
 * 
 * @return void
 */
function render_menu()
{
    $role = get_user_role();
    $menu_items = get_menu_items($role);
    $current_act = $_GET['act'] ?? '';
    $current_module = $_GET['module'] ?? '';

    foreach ($menu_items as $index => $item) {
        $item_type = $item['type'] ?? (isset($item['submenu']) ? 'group' : 'item'); // Auto-detect if no type

        // Check active state
        $is_active = false;
        $has_active_child = false;

        if (($item_type === 'group' || isset($item['submenu'])) && isset($item['submenu'])) {
            // Check if any submenu item is active
            foreach ($item['submenu'] as $sub) {
                // Support both old format (no active_pattern) and new format
                if (isset($sub['active_pattern'])) {
                    $sub_active = is_active_route($sub['active_pattern']);
                } else {
                    // Old format: extract from URL
                    $sub_url = parse_url($sub['url'], PHP_URL_QUERY);
                    $sub_act = str_replace('?act=', '', $sub_url);
                    $sub_active = ($current_act === $sub_act);
                }

                if ($sub_active) {
                    $is_active = true;
                    $has_active_child = true;
                    break;
                }
            }
        } else {
            // Single item
            $is_active = is_active_route($item['active_pattern'] ?? '');
        }

        // Horizon UI Style - Better contrast
        $active_class = $is_active
            ? 'bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to text-white font-semibold'
            : 'text-primary-700 hover:bg-primary-50 hover:text-primary-900 font-semibold';
        $submenu_id = 'submenu-' . $index;

        // Render based on type
        if (($item_type === 'group' || isset($item['submenu'])) && isset($item['submenu']) && !empty($item['submenu'])) {
            // Group menu item with submenu
            echo '<div class="mb-1">';
            echo '<button onclick="toggleSubmenu(\'' . $submenu_id . '\')" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-colors ' . $active_class . '">';
            echo '<div class="flex items-center gap-3">';
            if (isset($item['icon'])) {
                echo '<i data-lucide="' . htmlspecialchars($item['icon']) . '" class="w-5 h-5"></i>';
            }
            echo '<span>' . htmlspecialchars($item['label']) . '</span>';
            echo '</div>';
            echo '<i data-lucide="chevron-down" id="arrow-' . $submenu_id . '" class="w-4 h-4 transition-transform duration-200 ' . ($is_active ? 'rotate-180' : '') . '"></i>';
            echo '</button>';

            // Submenu - Horizon UI Style
            $submenu_display = $is_active ? 'block' : 'hidden';
            echo '<div id="' . $submenu_id . '" class="submenu ml-4 mt-1 space-y-1 pl-2 ' . $submenu_display . '">';
            foreach ($item['submenu'] as $subitem) {
                // Support both old and new format
                if (isset($subitem['active_pattern'])) {
                    $sub_is_active = is_active_route($subitem['active_pattern']);
                } else {
                    // Old format: extract from URL
                    $sub_url = parse_url($subitem['url'], PHP_URL_QUERY);
                    $sub_act = str_replace('?act=', '', $sub_url);
                    $sub_is_active = ($current_act === $sub_act);
                }

                $sub_active_class = $sub_is_active
                    ? 'text-accent font-semibold bg-primary-50'
                    : 'text-primary-500 hover:text-primary-700 hover:bg-primary-50 font-medium';

                echo '<a href="' . htmlspecialchars($subitem['url']) . '" class="flex items-center gap-3 px-4 py-2 rounded-xl transition-colors text-sm ' . $sub_active_class . '">';
                if (isset($subitem['icon'])) {
                    echo '<i data-lucide="' . htmlspecialchars($subitem['icon']) . '" class="w-4 h-4"></i>';
                }
                echo '<span>' . htmlspecialchars($subitem['label']) . '</span>';
                echo '</a>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            // Simple menu item - Horizon UI Style
            echo '<a href="' . htmlspecialchars($item['url']) . '" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors mb-1 ' . $active_class . '">';
            echo '<i data-lucide="' . htmlspecialchars($item['icon']) . '" class="w-5 h-5"></i>';
            echo '<span>' . htmlspecialchars($item['label']) . '</span>';
            echo '</a>';
        }
    }
}

/**
 * Render user dropdown menu
 * 
 * @return void
 */
function render_user_menu()
{
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    $user = get_auth_user();

    echo '<div class="relative user-menu">';
    echo '<button onclick="toggleUserMenu(this)" class="flex items-center gap-2 p-2 hover:bg-slate-100 rounded-lg transition-colors">';

    // Avatar
    if (!empty($user['avatar'])) {
        echo '<img src="' . $base_url . '/public/uploads/avatars/' . $user['avatar'] . '" class="w-8 h-8 rounded-full">';
    } else {
        echo '<div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center text-sm font-bold text-white">';
        echo strtoupper(substr($user['full_name'], 0, 1));
        echo '</div>';
    }

    echo '<svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
    echo '</svg>';
    echo '</button>';

    // Dropdown menu - Flat Design
    echo '<div class="dropdown-menu hidden absolute right-0 top-12 w-48 bg-panel border border-slate-200 rounded py-2 z-50">';
    echo '<div class="px-4 py-2 border-b border-slate-200">';
    echo '<p class="text-sm font-medium text-slate-900">' . sanitize($user['full_name']) . '</p>';
    echo '<p class="text-xs text-slate-500">' . sanitize($user['email']) . '</p>';
    echo '</div>';
    echo '<a href="' . $base_url . '/?act=profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Hồ sơ của tôi</a>';
    echo '<a href="' . $base_url . '/?act=settings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">Cài đặt</a>';
    echo '<div class="border-t border-slate-200 mt-2 pt-2">';
    echo '<a href="' . $base_url . '/?act=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</a>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// ============================================================================
// END OF MENU HELPER
// ============================================================================
