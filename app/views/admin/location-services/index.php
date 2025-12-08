<?php
/**
 * ADMIN - QUẢN LÝ ĐỊA ĐIỂM & DỊCH VỤ THỐNG NHẤT
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<style>
    /* Horizon UI Style - Location Services */
    /* Simple Tree Item */
    .tree-item {
        padding: 12px 16px;
        margin-bottom: 4px;
        background-color: var(--panel);
        border-left: 3px solid transparent;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .tree-item:hover {
        background-color: var(--primary-50);
        border-left-color: var(--accent);
    }

    .tree-item.active {
        background: linear-gradient(135deg, var(--primary-900) 0%, var(--primary-700) 100%);
        color: white;
        border-left-color: var(--accent);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .tree-item.active * {
        color: white;
    }

    .tree-item a.no-underline {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .tree-icon {
        margin-right: 10px;
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

    .tree-item.country .tree-icon {
        color: var(--accent);
    }

    .tree-item.active .tree-icon {
        color: white;
    }

    .tree-label {
        flex: 1;
        font-weight: 600;
        font-size: 14px;
    }

    .tree-badge {
        background-color: var(--primary-100);
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        color: var(--primary-700);
        margin-left: 8px;
        margin-right: 8px;
        border-radius: 12px;
    }

    .tree-item.active .tree-badge {
        background-color: rgba(255, 255, 255, 0.25);
        color: white;
    }

    .tree-actions {
        display: flex;
        gap: 4px;
        margin-left: 12px;
    }

    .tree-action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .tree-action-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Service Cards */
    .service-card {
        background-color: var(--panel);
        border-left: 4px solid var(--accent);
        padding: 20px;
        margin-bottom: 16px;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }

    .service-card:hover {
        background-color: var(--primary-50);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .service-card.inactive {
        opacity: 0.6;
        border-left-color: var(--primary-300);
    }

    /* Price Items */
    .price-item {
        background-color: var(--panel);
        border-left: 3px solid var(--primary-100);
        padding: 12px 16px;
        margin-bottom: 8px;
        border-radius: 12px;
        transition: all 0.2s;
    }

    .price-item:hover {
        background-color: var(--primary-50);
        border-left-color: var(--accent);
    }

    /* Main Content */
    #mainContent {
        min-height: 600px;
        background-color: transparent;
    }

    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--primary-500);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--primary-300);
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 14px;
        color: var(--primary-600);
    }

    /* Tabs */
    .tab-nav {
        border-bottom: 2px solid var(--primary-100);
        margin-bottom: 20px;
    }

    .tab-link {
        padding: 12px 20px;
        font-weight: 600;
        font-size: 14px;
        color: var(--primary-600);
        border-bottom: 3px solid transparent;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
    }

    .tab-link:hover {
        color: var(--accent);
        background-color: var(--primary-50);
    }

    .tab-link.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
        background-color: var(--primary-50);
    }

    /* Province Cards */
    .province-card {
        padding: 16px;
        background: var(--panel);
        border: 1px solid var(--primary-100);
        border-left: 4px solid var(--accent);
        border-radius: 16px;
        color: inherit;
        display: block;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .province-card:hover {
        background-color: var(--primary-50);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Destination Cards */
    .destination-card {
        background: var(--panel);
        border: 1px solid var(--primary-100);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.2s;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .destination-card:hover {
        background-color: var(--primary-50);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    /* Status Badges */
    .status-badge {
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 12px;
    }

    .status-badge.active {
        background-color: var(--success-bg);
        color: var(--success-dark);
    }

    .status-badge.inactive {
        background-color: var(--primary-100);
        color: var(--primary-600);
    }

    /* Search Box */
    #searchBox {
        border: 1px solid var(--primary-100);
        border-radius: 12px;
    }

    #searchBox:focus {
        border-color: var(--accent);
        outline: none;
        ring: 2px;
        ring-color: var(--accent);
    }

    /* Sidebar Container */
    .sidebar-container {
        background: var(--panel);
        border: 1px solid var(--primary-100);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    /* Empty state */
    .tree-view-empty {
        text-align: center;
        padding: 40px 20px;
        color: var(--primary-400);
    }

    .tree-view-empty i {
        font-size: 36px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .tree-view-empty p {
        font-size: 13px;
        color: var(--primary-500);
    }
</style>

<div class="max-w-full mx-auto">
    <!-- Header - Responsive -->
    <div class="mb-4 lg:mb-6 bg-panel rounded-2xl p-4 lg:p-6 border border-primary-100 shadow-sm">
        <h1 class="text-xl lg:text-2xl font-bold text-primary-700 flex items-center gap-2 lg:gap-3">
            <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
            Quản lý Địa điểm & Dịch vụ
        </h1>
    </div>

    <!-- Main Container - Responsive -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6" style="min-height: 600px;">
        <!-- Sidebar - Tree View -->
        <div class="w-full lg:w-96 sidebar-container p-4 lg:p-6"
            style="max-height: calc(100vh - 200px); overflow-y: auto; position: sticky; top: 24px;">
            <div class="mb-4 lg:mb-6">
                <a href="?act=admin&module=location-services&action=create-country"
                    class="w-full mb-3 lg:mb-4 px-4 lg:px-6 py-2.5 lg:py-3 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base text-center inline-block flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Thêm Quốc gia</span>
                </a>
                <div class="relative">
                    <i data-lucide="search"
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-primary-400 w-4 h-4"></i>
                    <input type="text" id="searchBox" placeholder="Tìm kiếm..."
                        class="w-full pl-10 pr-3 py-2.5 lg:py-3 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                </div>
            </div>

            <div id="treeView" class="tree-view">
                <?php
                // Ensure countries is properly initialized
                if (!isset($countries) || !is_array($countries)) {
                    $countries = ['data' => []];
                }
                if (!isset($countries['data'])) {
                    $countries['data'] = [];
                }

                // Check if empty
                if (empty($countries['data'])):
                    ?>
                    <div class="tree-view-empty">
                        <div class="flex justify-center mb-3">
                            <i data-lucide="globe" class="w-12 h-12 text-primary-300"></i>
                        </div>
                        <p class="font-semibold text-primary-600">Chưa có quốc gia nào</p>
                        <p class="text-xs text-primary-500 mt-1">Nhấn "Thêm Quốc gia" để bắt đầu</p>
                    </div>
                <?php else: ?>
                    <?php
                    // Get current country_id from controller variable (ưu tiên từ controller, sau đó từ URL)
                    // Đảm bảo luôn cast về int hoặc null để so sánh đúng
                    if (isset($current_country_id) && $current_country_id !== null && $current_country_id !== '') {
                        $current_country_id = (int) $current_country_id;
                    } elseif (!empty($_GET['country_id'])) {
                        $current_country_id = (int) $_GET['country_id'];
                    } else {
                        $current_country_id = null;
                    }

                    if (isset($current_province_id) && $current_province_id !== null && $current_province_id !== '') {
                        $current_province_id = (int) $current_province_id;
                    } elseif (!empty($_GET['province_id'])) {
                        $current_province_id = (int) $_GET['province_id'];
                    } else {
                        $current_province_id = null;
                    }

                    // Nếu có province_id nhưng chưa có country_id, lấy từ province
                    if ($current_province_id && !$current_country_id && !empty($provinces)) {
                        foreach ($provinces as $prov) {
                            if ((int) $prov['id'] === $current_province_id) {
                                $current_country_id = (int) $prov['country_id'];
                                break;
                            }
                        }
                    }

                    // Nếu có service_provider_id nhưng chưa có province_id/country_id, cố gắng lấy từ provider
                    if (empty($current_province_id) && !empty($current_service_provider_id) && !empty($current_provider)) {
                        if (!empty($current_provider['province_id'])) {
                            $current_province_id = (int) $current_provider['province_id'];
                        }
                        if (!empty($current_provider['country_id'])) {
                            $current_country_id = (int) $current_provider['country_id'];
                        }
                    }
                    ?>
                    <?php
                    // Loại bỏ trùng lặp trong view (double check - đảm bảo an toàn)
                    $displayed_country_ids = [];
                    foreach ($countries['data'] as $country):
                        // Kiểm tra kỹ: chỉ skip nếu thực sự không có id hợp lệ
                        // Không dùng empty() vì có thể skip country hợp lệ có giá trị 0 hoặc false
                        if (!isset($country['id']) || $country['id'] === null || $country['id'] === '') {
                            continue; // Bỏ qua nếu không có ID hợp lệ
                        }
                        $country_id = (int) $country['id'];
                        if (in_array($country_id, $displayed_country_ids)) {
                            continue; // Bỏ qua nếu đã hiển thị
                        }
                        $displayed_country_ids[] = $country_id;

                        // Check nếu country này đang active
                        // Đảm bảo so sánh đúng với type casting
                        $is_expanded = false;
                        if ($current_country_id !== null && $current_country_id !== '') {
                            $is_expanded = ((int) $current_country_id === (int) $country_id);
                        }
                        ?>
                        <div class="tree-item country <?= $is_expanded ? 'expanded active' : '' ?>"
                            data-country-id="<?= $country_id ?>"
                            data-search-text="<?= htmlspecialchars(strtolower($country['name'])) ?>">
                            <div class="flex items-center justify-between w-full">
                                <a href="?act=admin&module=location-services&country_id=<?= $country_id ?>"
                                    class="flex items-center flex-1 no-underline"
                                    style="cursor: pointer; text-decoration: none; color: inherit;">
                                    <i data-lucide="globe" class="tree-icon w-4 h-4 lg:w-5 lg:h-5"></i>
                                    <span class="tree-label"><?= htmlspecialchars($country['name']) ?></span>
                                    <span class="tree-badge"><?= $country['provinces_count'] ?? 0 ?></span>
                                </a>
                                <div class="tree-actions" onclick="event.stopPropagation();">
                                    <a href="?act=admin&module=location-services&action=edit-country&id=<?= $country['id'] ?>"
                                        class="tree-action-btn bg-accent text-white hover:opacity-90" title="Sửa"
                                        onclick="event.stopPropagation();">
                                        <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    </a>
                                    <button
                                        onclick="event.stopPropagation(); toggleCountryStatus(<?= $country['id'] ?>, '<?= $country['status'] ?>')"
                                        class="tree-action-btn <?= $country['status'] == 'active' ? 'bg-success hover:opacity-90' : 'bg-primary-400 hover:opacity-90' ?> text-white"
                                        title="<?= $country['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                        <i data-lucide="<?= $country['status'] == 'active' ? 'check' : 'x' ?>"
                                            class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    </button>
                                    <button onclick="event.stopPropagation(); deleteCountry(<?= $country['id'] ?>)"
                                        class="tree-action-btn bg-danger text-white hover:opacity-90" title="Xóa">
                                        <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-4 lg:p-6" id="mainContent" style="min-height: 600px;">
            <?php if (!empty($current_service_provider_id) && !empty($current_provider)): ?>
                <!-- Services List for Provider - Kiểm tra service_provider_id TRƯỚC province_id -->
                <div class="mb-4 lg:mb-6">
                    <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                        class="text-accent hover:text-accent-dark font-semibold mb-2 lg:mb-3 inline-block flex items-center gap-2 text-sm lg:text-base">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Quay lại danh sách nhà cung cấp
                    </a>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 lg:gap-4 mb-4">
                        <h2 class="text-lg lg:text-xl font-bold text-primary-700">Dịch vụ của:
                            <?= htmlspecialchars($current_provider['name'] ?? '') ?></h2>
                        <a href="?act=admin&module=location-services&action=create-service&service_provider_id=<?= $current_service_provider_id ?>"
                            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-success to-success-dark hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base inline-flex items-center justify-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Thêm dịch vụ</span>
                        </a>
                    </div>
                </div>

                <?php if (empty($services['data'])): ?>
                    <div class="empty-state bg-panel rounded-2xl p-8 lg:p-12 border border-primary-100">
                        <div class="flex justify-center mb-3 lg:mb-4">
                            <i data-lucide="inbox" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                        </div>
                        <p class="text-base lg:text-lg font-semibold text-primary-700 mb-2">Chưa có dịch vụ nào</p>
                        <p class="text-xs lg:text-sm text-primary-500">Hãy thêm dịch vụ mới để bắt đầu</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($services['data'] as $service): ?>
                        <?php
                        $is_inactive = ($service['status'] ?? 'active') === 'inactive';
                        ?>
                        <div class="service-card <?= $is_inactive ? 'inactive' : '' ?>">
                            <div class="flex flex-col sm:flex-row justify-between items-start gap-3 lg:gap-4 mb-4">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <h5
                                            class="font-bold text-base lg:text-lg <?= $is_inactive ? 'text-primary-500' : 'text-primary-700' ?>">
                                            <?= htmlspecialchars($service['service_type_name'] ?? '') ?> -
                                            <?= htmlspecialchars($service['name']) ?>
                                        </h5>
                                        <!-- Status Badge -->
                                        <span
                                            class="status-badge <?= $is_inactive ? 'inactive' : 'active' ?> flex items-center gap-1">
                                            <i data-lucide="<?= $is_inactive ? 'x-circle' : 'check-circle' ?>" class="w-3 h-3"></i>
                                            <?= $is_inactive ? 'Đã vô hiệu hóa' : 'Hoạt động' ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($service['description'])): ?>
                                        <p
                                            class="text-xs lg:text-sm <?= $is_inactive ? 'text-primary-400' : 'text-primary-600' ?> mt-2">
                                            <?= htmlspecialchars($service['description']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($service['unit'])): ?>
                                        <p class="text-xs <?= $is_inactive ? 'text-primary-400' : 'text-primary-500' ?> mt-2">Đơn vị:
                                            <?= htmlspecialchars($service['unit']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                    <button
                                        class="px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center gap-1.5 <?= $is_inactive ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        onclick="<?= $is_inactive ? 'return false;' : 'openCreatePriceModal(' . $service['id'] . ')' ?>"
                                        <?= $is_inactive ? 'disabled title="Dịch vụ đã bị vô hiệu hóa"' : '' ?>>
                                        <i data-lucide="plus" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <span>Thêm giá</span>
                                    </button>
                                    <a href="?act=admin&module=location-services&action=edit-service&id=<?= $service['id'] ?>"
                                        class="px-3 lg:px-4 py-1.5 lg:py-2 bg-warning hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center gap-1.5">
                                        <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <span>Sửa</span>
                                    </a>
                                    <button
                                        class="px-3 lg:px-4 py-1.5 lg:py-2 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center gap-1.5"
                                        onclick="deleteService(<?= $service['id'] ?>)">
                                        <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        <span>Xóa</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Danh sách giá -->
                            <div class="mt-4 lg:mt-5 pt-4 border-t border-primary-100">
                                <h6 class="font-bold text-xs lg:text-sm mb-3 text-primary-700">
                                    Bảng giá (<?= count($service['prices'] ?? []) ?>)
                                </h6>
                                <?php if (empty($service['prices'])): ?>
                                    <p class="text-xs text-primary-500">Chưa có giá nào. Nhấn "+ Thêm giá" để thêm giá mới.</p>
                                <?php else: ?>
                                    <div class="space-y-2 lg:space-y-3">
                                        <?php foreach ($service['prices'] as $price): ?>
                                            <div
                                                class="price-item flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3">
                                                <div class="flex-1 w-full sm:w-auto">
                                                    <div class="flex flex-wrap items-center gap-2 lg:gap-3 mb-1">
                                                        <span class="font-bold text-accent text-sm lg:text-base">
                                                            <?= number_format($price['unit_price'], 0, ',', '.') ?> VND
                                                        </span>
                                                        <span
                                                            class="px-2 lg:px-3 py-0.5 lg:py-1 text-xs font-bold rounded-full
                                                            <?= $price['price_type'] == 'peak' ? 'bg-danger-bg text-danger-dark' :
                                                                ($price['price_type'] == 'low' ? 'bg-success-bg text-success-dark' : 'bg-info-bg text-info-dark') ?>">
                                                            <?= $price['price_type'] == 'peak' ? 'Cao điểm' :
                                                                ($price['price_type'] == 'low' ? 'Thấp điểm' : 'Tiêu chuẩn') ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-2 lg:gap-4 text-xs text-primary-500">
                                                        <?php if (!empty($price['start_date']) || !empty($price['end_date'])): ?>
                                                            <span>
                                                                <?= !empty($price['start_date']) ? date('d/m/Y', strtotime($price['start_date'])) : '...' ?>
                                                                -
                                                                <?= !empty($price['end_date']) ? date('d/m/Y', strtotime($price['end_date'])) : '...' ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span>Vô thời hạn</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($price['notes'])): ?>
                                                            <span class="text-primary-400">
                                                                <?= htmlspecialchars(substr($price['notes'], 0, 50)) ?>
                                                                <?= strlen($price['notes']) > 50 ? '...' : '' ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex gap-2 w-full sm:w-auto">
                                                    <button
                                                        class="px-2.5 lg:px-3 py-1 lg:py-1.5 bg-warning hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs inline-flex items-center gap-1"
                                                        onclick="openEditPriceModal(<?= $price['id'] ?>)">
                                                        <i data-lucide="edit" class="w-3 h-3"></i>
                                                        <span>Sửa</span>
                                                    </button>
                                                    <button
                                                        class="px-2.5 lg:px-3 py-1 lg:py-1.5 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs inline-flex items-center gap-1"
                                                        onclick="deletePrice(<?= $price['id'] ?>)">
                                                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                                                        <span>Xóa</span>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif (!empty($current_province_id)): ?>
                <!-- Có province_id - Hiển thị tabs và nội dung -->
                <!-- Tab Navigation -->
                <div class="tab-nav bg-panel rounded-2xl p-2 border border-primary-100 mb-4 lg:mb-6">
                    <nav class="flex gap-2">
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=providers"
                            class="tab-link <?= ($current_tab ?? 'providers') == 'providers' ? 'active' : '' ?> flex items-center gap-2">
                            <i data-lucide="building" class="w-4 h-4"></i>
                            Nhà cung cấp dịch vụ
                            <span
                                class="px-2 py-0.5 bg-primary-100 rounded-full text-xs font-bold text-primary-700"><?= count($service_providers['data'] ?? []) ?></span>
                        </a>
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=destinations"
                            class="tab-link <?= ($current_tab ?? 'providers') == 'destinations' ? 'active' : '' ?> flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            Địa điểm du lịch
                            <span
                                class="px-2 py-0.5 bg-primary-100 rounded-full text-xs font-bold text-primary-700"><?= count($destinations['data'] ?? []) ?></span>
                        </a>
                    </nav>
                </div>

                <?php if (($current_tab ?? 'providers') == 'providers'): ?>
                    <!-- Service Providers Tab -->
                    <div class="mb-4 lg:mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 lg:gap-4 mb-4">
                            <h2 class="text-lg lg:text-xl font-bold text-primary-700 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                                <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?>
                            </h2>
                            <a href="?act=admin&module=location-services&action=create-provider&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base inline-flex items-center justify-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Thêm nhà cung cấp</span>
                            </a>
                        </div>
                    </div>

                    <?php if (empty($service_providers['data'])): ?>
                        <div class="empty-state bg-panel rounded-2xl p-8 lg:p-12 border border-primary-100">
                            <div class="flex justify-center mb-3 lg:mb-4">
                                <i data-lucide="building" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                            </div>
                            <p class="text-base lg:text-lg font-semibold text-primary-700 mb-2">Chưa có nhà cung cấp nào</p>
                            <p class="text-xs lg:text-sm text-primary-500">Hãy thêm nhà cung cấp mới để bắt đầu</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($service_providers['data'] as $provider): ?>
                            <?php
                            $is_inactive = ($provider['status'] ?? 'active') === 'inactive';
                            ?>
                            <div class="service-card mb-4 <?= $is_inactive ? 'inactive' : '' ?>">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-3 lg:gap-4 mb-4">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3
                                                class="font-bold text-base lg:text-lg <?= $is_inactive ? 'text-primary-500' : 'text-primary-700' ?>">
                                                <?= htmlspecialchars($provider['name']) ?>
                                            </h3>
                                            <!-- Status Badge -->
                                            <span
                                                class="status-badge <?= $is_inactive ? 'inactive' : 'active' ?> flex items-center gap-1">
                                                <i data-lucide="<?= $is_inactive ? 'x-circle' : 'check-circle' ?>" class="w-3 h-3"></i>
                                                <?= $is_inactive ? 'Đã vô hiệu hóa' : 'Hoạt động' ?>
                                            </span>
                                        </div>
                                        <div
                                            class="space-y-1 text-xs lg:text-sm <?= $is_inactive ? 'text-primary-400' : 'text-primary-600' ?>">
                                            <?php if (!empty($provider['service_code'])): ?>
                                                <p>Mã: <span class="font-semibold"><?= htmlspecialchars($provider['service_code']) ?></span>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($provider['phone'])): ?>
                                                <p class="flex items-center gap-1">
                                                    <i data-lucide="phone" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                    <?= htmlspecialchars($provider['phone']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($provider['email'])): ?>
                                                <p class="flex items-center gap-1">
                                                    <i data-lucide="mail" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                    <?= htmlspecialchars($provider['email']) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                                        <a href="?act=admin&module=location-services&action=edit-provider&id=<?= $provider['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                            class="px-3 lg:px-4 py-1.5 lg:py-2 bg-accent hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center gap-1.5">
                                            <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            <span>Sửa</span>
                                        </a>
                                        <button
                                            class="px-3 lg:px-4 py-1.5 lg:py-2 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center gap-1.5"
                                            onclick="deleteServiceProvider(<?= $provider['id'] ?>)">
                                            <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            <span>Xóa</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-primary-100">
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 lg:gap-3">
                                        <h4
                                            class="font-bold text-sm lg:text-base <?= $is_inactive ? 'text-primary-500' : 'text-primary-700' ?>">
                                            Dịch vụ (<?= $provider['services_count'] ?? 0 ?>)</h4>
                                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&service_provider_id=<?= $provider['id'] ?>"
                                            class="w-full sm:w-auto px-3 lg:px-4 py-1.5 lg:py-2 bg-gradient-to-r from-success to-success-dark hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center gap-1.5 <?= $is_inactive ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= $is_inactive ? 'onclick="return false;" title="Nhà cung cấp đã bị vô hiệu hóa"' : '' ?>>
                                            <i data-lucide="list" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            <span>Xem dịch vụ</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php elseif (($current_tab ?? 'providers') == 'destinations'): ?>
                    <!-- Destinations Tab -->
                    <div class="mb-4 lg:mb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 lg:gap-4 mb-4">
                            <h2 class="text-lg lg:text-xl font-bold text-primary-700 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                                <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?>
                            </h2>
                            <a href="?act=admin&module=location-services&action=create-destination&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base inline-flex items-center justify-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Thêm địa điểm du lịch</span>
                            </a>
                        </div>
                    </div>

                    <?php if (empty($destinations['data'])): ?>
                        <div class="empty-state bg-panel rounded-2xl p-8 lg:p-12 border border-primary-100">
                            <div class="flex justify-center mb-3 lg:mb-4">
                                <i data-lucide="map-pin" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                            </div>
                            <p class="text-base lg:text-lg font-semibold text-primary-700 mb-2">Chưa có địa điểm nào</p>
                            <p class="text-xs lg:text-sm text-primary-500">Hãy thêm địa điểm du lịch mới</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
                            <?php foreach ($destinations['data'] as $destination): ?>
                                <div class="destination-card">
                                    <!-- Image Section -->
                                    <div class="relative w-full h-48 bg-primary-100 overflow-hidden rounded-t-2xl">
                                        <?php if (!empty($destination['thumbnail'])): ?>
                                            <img src="<?= htmlspecialchars($destination['thumbnail']) ?>"
                                                alt="<?= htmlspecialchars($destination['name']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                                <div class="text-center">
                                                    <i data-lucide="image" class="w-12 h-12 text-primary-300 mb-2"></i>
                                                    <p class="text-xs text-primary-500">Chưa có ảnh</p>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            <span
                                                class="status-badge <?= ($destination['status'] ?? 'active') == 'active' ? 'active' : 'inactive' ?> flex items-center gap-1">
                                                <i data-lucide="<?= ($destination['status'] ?? 'active') == 'active' ? 'check-circle' : 'x-circle' ?>"
                                                    class="w-3 h-3"></i>
                                                <?= ($destination['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Ngừng hoạt động' ?>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Content Section -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-base lg:text-lg text-primary-700 mb-2 line-clamp-1">
                                            <?= htmlspecialchars($destination['name']) ?>
                                        </h3>
                                        <?php if (!empty($destination['description'])): ?>
                                            <p class="text-xs lg:text-sm text-primary-600 mb-3 line-clamp-2">
                                                <?= htmlspecialchars(substr($destination['description'], 0, 100)) ?>
                                                <?= mb_strlen($destination['description']) > 100 ? '...' : '' ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <div class="flex gap-2 pt-3 border-t border-primary-100">
                                            <a href="?act=admin&module=location-services&action=edit-destination&id=<?= $destination['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                                class="flex-1 px-3 py-1.5 lg:py-2 bg-accent hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center gap-1.5">
                                                <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                <span>Sửa</span>
                                            </a>
                                            <button
                                                class="flex-1 px-3 py-1.5 lg:py-2 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center gap-1.5"
                                                onclick="deleteDestination(<?= $destination['id'] ?>)">
                                                <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                                <span>Xóa</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <?php elseif (!empty($current_country_id) && !empty($current_country)): ?>
                <!-- Chỉ có country_id - Hiển thị danh sách provinces với action buttons -->
                <div>
                    <div
                        class="mb-4 lg:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 lg:gap-4">
                        <h2 class="text-lg lg:text-xl font-bold text-primary-700 flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                            <?= htmlspecialchars($current_country['name']) ?>
                        </h2>
                        <a href="?act=admin&module=location-services&action=create-province&country_id=<?= $current_country_id ?>"
                            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-success to-success-dark hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base inline-flex items-center justify-center gap-2">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>Thêm tỉnh thành</span>
                        </a>
                    </div>
                    <?php if (!empty($provinces)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php
                            $displayed_province_ids = [];
                            foreach ($provinces as $province):
                                $province_id = (int) $province['id'];
                                // Chỉ hiển thị provinces thuộc country hiện tại
                                if ((int) $province['country_id'] !== (int) $current_country_id) {
                                    continue;
                                }
                                if (in_array($province_id, $displayed_province_ids)) {
                                    continue;
                                }
                                $displayed_province_ids[] = $province_id;
                                $province_url = "?act=admin&module=location-services&country_id={$current_country_id}&province_id={$province_id}&tab=providers";
                                $is_province_active = (!empty($current_province_id) && $current_province_id == $province_id);
                                ?>
                                <div class="province-card <?= $is_province_active ? 'border-accent border-2' : '' ?>">
                                    <div class="flex items-center justify-between mb-3">
                                        <a href="<?= $province_url ?>" class="flex-1">
                                            <h3
                                                class="font-bold text-base lg:text-lg text-primary-700 hover:text-accent transition-colors">
                                                <?= htmlspecialchars($province['name']) ?></h3>
                                        </a>
                                        <span
                                            class="status-badge <?= ($province['status'] ?? 'active') == 'active' ? 'active' : 'inactive' ?> ml-2 flex items-center gap-1">
                                            <i data-lucide="<?= ($province['status'] ?? 'active') == 'active' ? 'check-circle' : 'x-circle' ?>"
                                                class="w-3 h-3"></i>
                                            <?= ($province['status'] ?? 'active') == 'active' ? 'Hoạt động' : 'Tạm dừng' ?>
                                        </span>
                                    </div>
                                    <div class="text-xs lg:text-sm text-primary-600 mb-3">
                                        <p>Nhà cung cấp: <span class="font-semibold"><?= $province['providers_count'] ?? 0 ?></span>
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 pt-3 border-t border-primary-100">
                                        <a href="<?= $province_url ?>"
                                            class="flex-1 px-3 py-2 bg-accent hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm text-center inline-flex items-center justify-center gap-1">
                                            <i data-lucide="eye" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                            <span>Xem</span>
                                        </a>
                                        <a href="?act=admin&module=location-services&action=edit-province&id=<?= $province['id'] ?>&country_id=<?= $current_country_id ?>"
                                            class="px-3 py-2 bg-warning hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center">
                                            <i data-lucide="edit" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        </a>
                                        <button
                                            onclick="toggleProvinceStatus(<?= $province['id'] ?>, '<?= $province['status'] ?? 'active' ?>')"
                                            class="px-3 py-2 <?= ($province['status'] ?? 'active') == 'active' ? 'bg-success hover:opacity-90' : 'bg-primary-400 hover:opacity-90' ?> text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center"
                                            title="<?= ($province['status'] ?? 'active') == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                            <i data-lucide="<?= ($province['status'] ?? 'active') == 'active' ? 'check' : 'x' ?>"
                                                class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        </button>
                                        <button onclick="deleteProvince(<?= $province['id'] ?>, <?= $current_country_id ?>)"
                                            class="px-3 py-2 bg-danger hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-xs lg:text-sm inline-flex items-center justify-center"
                                            title="Xóa">
                                            <i data-lucide="trash-2" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state bg-panel rounded-2xl p-8 lg:p-12 border border-primary-100">
                            <div class="flex justify-center mb-3 lg:mb-4">
                                <i data-lucide="map-pin" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                            </div>
                            <p class="text-base lg:text-lg font-semibold text-primary-700 mb-2">Chưa có tỉnh thành nào</p>
                            <p class="text-xs lg:text-sm text-primary-500">Hãy thêm tỉnh thành mới cho quốc gia này</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Chưa chọn gì - Hiển thị hướng dẫn -->
                <div class="empty-state bg-panel rounded-2xl p-8 lg:p-12 border border-primary-100">
                    <div class="flex justify-center mb-3 lg:mb-4">
                        <i data-lucide="hand-pointer" class="w-12 h-12 lg:w-16 lg:h-16 text-primary-300"></i>
                    </div>
                    <p class="text-base lg:text-lg font-semibold text-primary-700">Vui lòng chọn một quốc gia từ danh sách
                        bên trái để bắt đầu</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include __DIR__ . '/modals.php'; ?>

<script>
    $(document).ready(function () {
        let currentProvinceId = null;
        window.currentServiceProviderId = null;
        let currentServiceId = null;

        // Không cần JavaScript handlers cho tree-item click nữa vì đã dùng thẻ <a>
        // Các buttons (edit, delete, toggle status) vẫn dùng JavaScript với preventDefault

        // Không cần AJAX nữa - data đã được load từ server

        // Format price (nếu cần dùng trong modals)
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        // Search functionality - Tìm kiếm real-time (chỉ tìm countries)
        $('#searchBox').on('input', function () {
            const searchTerm = $(this).val().toLowerCase().trim();
            const $treeView = $('#treeView');

            if (searchTerm === '') {
                // Reset: hiển thị tất cả
                $treeView.find('.tree-item').show();
                $treeView.find('.search-no-results').remove();
                return;
            }

            let hasVisibleResults = false;

            // Search countries only
            $treeView.find('.tree-item.country').each(function () {
                const $countryItem = $(this);
                const countrySearchText = $countryItem.data('search-text') || '';

                if (countrySearchText.includes(searchTerm)) {
                    $countryItem.show();
                    hasVisibleResults = true;
                } else {
                    $countryItem.hide();
                }
            });

            // Hiển thị message nếu không có kết quả
            let $noResults = $treeView.find('.search-no-results');
            if (!hasVisibleResults && searchTerm !== '') {
                if ($noResults.length === 0) {
                    $noResults = $('<div class="search-no-results" style="padding: 20px; text-align: center; color: var(--gray-500);"><i class="fas fa-search" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i><p style="font-size: 14px;">Không tìm thấy kết quả</p></div>');
                    $treeView.append($noResults);
                }
            } else {
                $noResults.remove();
            }
        });

        // Clear search on Escape key
        $('#searchBox').on('keydown', function (e) {
            if (e.key === 'Escape') {
                $(this).val('').trigger('input');
            }
        });

        // Country functions
        window.deleteCountry = function (id) {
            if (!confirm('Bạn có chắc muốn xóa quốc gia này? Lưu ý: Không thể xóa nếu đang có tỉnh thành.')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'delete-country');
            url.searchParams.set('id', id);

            window.location.href = url.toString();
        };

        window.deleteProvince = function (id, countryId) {
            if (!confirm('Bạn có chắc muốn xóa tỉnh thành này? Lưu ý: Không thể xóa nếu đang có địa điểm hoặc nhà dịch vụ.')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'delete-province');
            url.searchParams.set('id', id);
            if (countryId) {
                url.searchParams.set('country_id', countryId);
            }

            window.location.href = url.toString();
        };

        window.toggleProvinceStatus = function (id, currentStatus) {
            $.ajax({
                url: `?act=admin&module=location-services&action=toggle-province-status&id=${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showToast(response.message || 'Cập nhật trạng thái thành công!', 'success');
                        // Reload page để cập nhật UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        showToast(response.message || 'Có lỗi xảy ra', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi cập nhật trạng thái', 'error');
                }
            });
        };

        window.toggleCountryStatus = function (id, currentStatus) {
            $.ajax({
                url: `?act=admin&module=location-services&action=toggle-country-status&id=${id}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showToast(response.message || 'Cập nhật trạng thái thành công!', 'success');
                        // Reload page để cập nhật UI
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        showToast(response.message || 'Có lỗi xảy ra', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi cập nhật trạng thái', 'error');
                }
            });
        };

        // Delete functions - Dùng URL redirect sau khi xóa
        window.deleteServiceProvider = function (id) {
            if (!confirm('Bạn có chắc muốn xóa nhà cung cấp này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deleteServiceProvider');
            url.searchParams.set('id', id);
            url.searchParams.set('country_id', url.searchParams.get('country_id') || '');
            url.searchParams.set('province_id', url.searchParams.get('province_id') || '');

            window.location.href = url.toString();
        };

        window.deleteService = function (id) {
            if (!confirm('Bạn có chắc muốn xóa dịch vụ này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deleteService');
            url.searchParams.set('id', id);

            window.location.href = url.toString();
        };

        window.deletePrice = function (priceId) {
            if (!confirm('Bạn có chắc muốn xóa giá này?')) return;

            const url = new URL(window.location.href);
            url.searchParams.set('act', 'admin');
            url.searchParams.set('module', 'location-services');
            url.searchParams.set('action', 'deletePrice');
            url.searchParams.set('id', priceId);

            window.location.href = url.toString();
        };

        window.openEditPriceModal = function (priceId) {
            // Load price data và mở modal
            $.ajax({
                url: `?act=admin&module=location-services&action=getPrice&id=${priceId}`,
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.data) {
                        const price = response.data;

                        // Set form data
                        $('#priceId').val(price.id);
                        $('#priceServiceId').val(price.service_id);
                        $('#priceUnitPrice').val(price.unit_price);
                        $('#priceType').val(price.price_type || 'standard');
                        $('#priceValidFrom').val(price.start_date || '');
                        $('#priceValidTo').val(price.end_date || '');
                        $('#priceNotes').val(price.notes || '');

                        // Update modal title
                        $('#priceModalTitle').text('Sửa giá dịch vụ');

                        // Load service info để hiển thị context
                        $.ajax({
                            url: `?act=admin&module=location-services&action=getService&id=${price.service_id}`,
                            method: 'GET',
                            dataType: 'json',
                            success: function (serviceResponse) {
                                if (serviceResponse.success && serviceResponse.data) {
                                    const service = serviceResponse.data;
                                    const providerName = service.service_provider_name || '';
                                    const serviceName = service.name || '';

                                    let contextParts = [];
                                    if (providerName) contextParts.push(providerName);
                                    if (serviceName) contextParts.push(serviceName);
                                    const contextText = contextParts.length > 0
                                        ? contextParts.join(' > ')
                                        : 'Đang sửa giá dịch vụ';
                                    $('#priceContextText').text(contextText);
                                }
                            }
                        });

                        $('#priceModal').removeClass('hidden');
                    } else {
                        showToast('Không thể tải thông tin giá', 'error');
                    }
                },
                error: function () {
                    showToast('Lỗi khi tải thông tin giá', 'error');
                }
            });
        };



        // Modal functions are defined in modals.php
    });
</script>