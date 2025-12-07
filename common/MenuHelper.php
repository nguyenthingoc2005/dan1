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
            [
                'icon' => '📊',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=admin',
                'active_pattern' => 'admin'
            ],
            [
                'icon' => '🗺️',
                'label' => 'Quản lý Tour',
                'url' => $base_url . '/?act=admin&module=tours',
                'active_pattern' => 'admin:tours'
            ],
            [
                'icon' => '📅',
                'label' => 'Lịch Khởi Hành',
                'url' => $base_url . '/?act=admin&module=schedules',
                'active_pattern' => 'admin:schedules'
            ],
            [
                'icon' => '📝',
                'label' => 'Quản lý Đặt Tour',
                'url' => $base_url . '/?act=admin&module=bookings',
                'active_pattern' => 'admin:bookings'
            ],
            [
                'icon' => '👥',
                'label' => 'Khách hàng',
                'url' => $base_url . '/?act=admin&module=customers',
                'active_pattern' => 'admin:customers'
            ],
            [
                'icon' => '💰',
                'label' => 'Thanh toán',
                'url' => $base_url . '/?act=admin&module=payments',
                'active_pattern' => 'admin:payments'
            ],
            [
                'icon' => '📔',
                'label' => 'Nhật ký Tour',
                'url' => $base_url . '/?act=admin&module=journals',
                'active_pattern' => 'admin:journals'
            ],
            [
                'icon' => '📈',
                'label' => 'Báo cáo',
                'url' => $base_url . '/?act=admin&module=reports',
                'active_pattern' => 'admin:reports'
            ],
            [
                'icon' => '👔',
                'label' => 'Nhân viên',
                'url' => $base_url . '/?act=admin&module=users',
                'active_pattern' => 'admin:users'
            ],
            // Danh mục & Cấu hình khác
            [
                'icon' => '🌍',
                'label' => 'Địa điểm & Dịch vụ',
                'url' => $base_url . '/?act=admin&module=location-services',
                'active_pattern' => 'admin:location-services'
            ],
            [
                'icon' => '🛎️',
                'label' => 'Loại dịch vụ',
                'url' => $base_url . '/?act=admin&module=service-types',
                'active_pattern' => 'admin:service-types'
            ],
            [
                'icon' => '📋',
                'label' => 'Chính sách',
                'url' => $base_url . '/?act=admin&module=policies',
                'active_pattern' => 'admin:policies'
            ],
        ],

        'staff' => [
            [
                'icon' => '📊',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=staff-dashboard',
                'active_pattern' => 'staff-dashboard'
            ],
            [
                'icon' => '✈️',
                'label' => 'Tours',
                'url' => $base_url . '/?act=staff-tours',
                'active_pattern' => 'staff-tours',
                'submenu' => [
                    ['label' => 'Tours của tôi', 'url' => $base_url . '/?act=staff-tours'],
                    ['label' => 'Tạo Tour mới', 'url' => $base_url . '/?act=staff-tours&action=create'],
                ]
            ],
            [
                'icon' => '📝',
                'label' => 'Bookings',
                'url' => $base_url . '/?act=staff-bookings',
                'active_pattern' => 'staff-bookings',
                'submenu' => [
                    ['label' => 'Bookings của tôi', 'url' => $base_url . '/?act=staff-bookings'],
                    ['label' => 'Tạo Booking mới', 'url' => $base_url . '/?act=staff-bookings&action=create'],
                ]
            ],
            [
                'icon' => '📅',
                'label' => 'Lịch Tour',
                'url' => $base_url . '/?act=staff-schedules',
                'active_pattern' => 'staff-schedules'
            ],
            [
                'icon' => '👥',
                'label' => 'Customers',
                'url' => $base_url . '/?act=staff-customers',
                'active_pattern' => 'staff-customers',
                'submenu' => [
                    ['label' => 'Danh sách khách', 'url' => $base_url . '/?act=staff-customers'],
                    ['label' => 'Thêm khách mới', 'url' => $base_url . '/?act=staff-customers&action=create'],
                ]
            ],
            [
                'icon' => '💰',
                'label' => 'Payments',
                'url' => $base_url . '/?act=staff-payments',
                'active_pattern' => 'staff-payments'
            ],
            [
                'icon' => '👤',
                'label' => 'Thông tin cá nhân',
                'url' => $base_url . '/?act=profile',
                'active_pattern' => 'profile'
            ],
        ],

        'guide' => [
            [
                'icon' => '📊',
                'label' => 'Dashboard',
                'url' => $base_url . '/?act=guide-dashboard',
                'active_pattern' => 'guide-dashboard'
            ],
            [
                'icon' => '✈️',
                'label' => 'Lịch Tour',
                'url' => $base_url . '/?act=guide-tours',
                'active_pattern' => 'guide-tours'
            ],
            [
                'icon' => '✅',
                'label' => 'Check-in',
                'url' => $base_url . '/?act=guide-checkin',
                'active_pattern' => 'guide-checkin'
            ],
            [
                'icon' => '📔',
                'label' => 'Nhật ký Tour',
                'url' => $base_url . '/?act=guide-journals',
                'active_pattern' => 'guide-journals'
            ],
            [
                'icon' => '💰',
                'label' => 'Chi phí phát sinh',
                'url' => $base_url . '/?act=guide-expenses',
                'active_pattern' => 'guide-expenses'
            ],
            [
                'icon' => '👤',
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

    // Old pattern: exact match or starts with
    return strpos($current_act, $pattern) === 0;
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

    foreach ($menu_items as $index => $item) {
        // Check active state strictly for parent items to avoid partial matching issues
        // or if any child is active
        $is_active = false;
        if (isset($item['submenu'])) {
            foreach ($item['submenu'] as $sub) {
                if (is_active_route(str_replace('?act=', '', parse_url($sub['url'], PHP_URL_QUERY)))) {
                    $is_active = true;
                    break;
                }
            }
        } else {
            $is_active = is_active_route($item['active_pattern']);
        }

        // Flat Design - No border, no shadow
        $active_class = $is_active ? 'bg-slate-700 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white';
        $submenu_id = 'submenu-' . $index;

        // Check if has submenu
        if (isset($item['submenu']) && !empty($item['submenu'])) {
            // Parent menu item with submenu
            echo '<div class="mb-1">';
            echo '<button onclick="toggleSubmenu(\'' . $submenu_id . '\')" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded transition-colors ' . $active_class . '">';
            echo '<div class="flex items-center gap-3">';
            echo '<span class="text-lg">' . $item['icon'] . '</span>';
            echo '<span class="font-medium">' . $item['label'] . '</span>';
            echo '</div>';
            echo '<svg id="arrow-' . $submenu_id . '" class="w-4 h-4 transition-transform duration-200 ' . ($is_active ? 'rotate-180' : '') . '" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
            echo '</svg>';
            echo '</button>';

            // Submenu - Flat Design
            $submenu_display = $is_active ? 'block' : 'hidden';
            echo '<div id="' . $submenu_id . '" class="submenu ml-4 mt-1 space-y-1 pl-2 ' . $submenu_display . '">';
            foreach ($item['submenu'] as $subitem) {
                // Extract act param from URL for check
                $sub_act = str_replace('?act=', '', parse_url($subitem['url'], PHP_URL_QUERY));
                $sub_is_active = ($current_act === $sub_act);

                $sub_active_class = $sub_is_active ? 'text-accent font-medium bg-slate-700' : 'text-slate-400 hover:text-white hover:bg-slate-700';

                echo '<a href="' . $subitem['url'] . '" class="block px-4 py-2 rounded transition-colors text-sm ' . $sub_active_class . '">';
                echo $subitem['label'];
                echo '</a>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            // Simple menu item - Flat Design
            echo '<a href="' . $item['url'] . '" class="flex items-center gap-3 px-4 py-3 rounded transition-colors mb-1 ' . $active_class . '">';
            echo '<span class="text-lg">' . $item['icon'] . '</span>';
            echo '<span class="font-medium">' . $item['label'] . '</span>';
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
