<?php
/**
 * ADMIN - QUẢN LÝ ĐỊA ĐIỂM & DỊCH VỤ THỐNG NHẤT
 */

if (!is_admin())
    redirect('?act=access-denied');
?>

<style>
    .tree-item {
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .tree-item:hover {
        background-color: #f3f4f6;
    }

    .tree-item.active {
        background-color: #3b82f6;
        color: white;
    }

    .tree-item.active:hover {
        background-color: #2563eb;
    }

    .tree-children {
        margin-left: 20px;
    }

    .tree-children.hidden {
        display: none;
    }

    .tree-item.expanded .tree-children {
        display: block;
    }

    .tree-toggle {
        float: right;
        font-size: 12px;
    }

    .tree-badge {
        float: right;
        background-color: #e5e7eb;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-right: 8px;
    }

    .tree-item.active .tree-badge {
        background-color: rgba(255, 255, 255, 0.3);
    }

    .service-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
        transition: box-shadow 0.2s;
    }

    .service-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .price-badge {
        display: inline-block;
        padding: 4px 8px;
        background-color: #f0f9ff;
        border: 1px solid #3b82f6;
        border-radius: 4px;
        font-size: 12px;
        margin: 4px;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }
</style>

<div class="max-w-full mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Quản lý Địa điểm & Dịch vụ</h1>
    </div>

    <!-- Main Container -->
    <div class="flex gap-4" style="min-height: 600px;">
        <!-- Sidebar - Tree View -->
        <div class="w-80 bg-white rounded shadow-sm p-4" style="max-height: 80vh; overflow-y: auto;">
            <div class="mb-4">
                <input type="text" id="searchBox" placeholder="🔍 Tìm kiếm..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-accent">
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
                
                // Get current country_id from controller variable
                $current_country_id = $current_country_id ?? (!empty($_GET['country_id']) ? (int) $_GET['country_id'] : null);
                ?>
                <?php foreach ($countries['data'] as $country): ?>
                    <?php 
                    $is_expanded = ($current_country_id == $country['id']);
                    ?>
                    <div class="tree-item country <?= $is_expanded ? 'expanded active' : '' ?>" 
                         data-country-id="<?= $country['id'] ?>">
                            <span class="tree-icon">🌍</span>
                            <span class="tree-label"><?= htmlspecialchars($country['name']) ?></span>
                            <span class="tree-badge"><?= $country['provinces_count'] ?? 0 ?></span>
                        <span class="tree-toggle"><?= $is_expanded ? '▼' : '▶' ?></span>
                    </div>
                    <div class="tree-children <?= $is_expanded ? '' : 'hidden' ?>" id="country-<?= $country['id'] ?>-children">
                        <?php if ($is_expanded && !empty($provinces)): ?>
                            <?php foreach ($provinces as $province): ?>
                                <?php 
                                $is_province_active = (!empty($current_province_id) && $current_province_id == $province['id']);
                                ?>
                                <div class="tree-item province <?= $is_province_active ? 'active' : '' ?>" 
                                     data-province-id="<?= $province['id'] ?>">
                                    <span class="tree-icon">📍</span>
                                    <span class="tree-label"><?= htmlspecialchars($province['name']) ?></span>
                                    <span class="tree-badge"><?= $province['providers_count'] ?? 0 ?></span>
                        </div>
                            <?php endforeach; ?>
                            <?php if (empty($provinces)): ?>
                                <div class="tree-item" style="padding-left: 20px; color: #6b7280;">Chưa có tỉnh thành</div>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 bg-white rounded shadow-sm p-6" id="mainContent">
            <?php if (!empty($current_service_provider_id) && !empty($current_provider)): ?>
                <!-- Services List for Provider - Kiểm tra service_provider_id TRƯỚC province_id -->
                <div class="mb-4">
                    <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>" 
                       class="text-blue-600 hover:underline mb-2 inline-block">← Quay lại danh sách nhà cung cấp</a>
                    <h2 class="text-xl font-bold mb-2">Dịch vụ của: <?= htmlspecialchars($current_provider['name'] ?? '') ?></h2>
                    <a href="?act=admin&module=location-services&action=create-service&service_provider_id=<?= $current_service_provider_id ?>"
                       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 inline-block">
                        + Thêm dịch vụ
                    </a>
                </div>

                <?php if (empty($services['data'])): ?>
                    <div class="text-center py-10 text-gray-500">Chưa có dịch vụ nào. Hãy thêm mới!</div>
                <?php else: ?>
                    <?php foreach ($services['data'] as $service): ?>
                        <div class="border-l-4 border-blue-500 pl-4 mb-4 bg-gray-50 rounded-r-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h5 class="font-semibold text-lg"><?= htmlspecialchars($service['service_type_name'] ?? '') ?> - <?= htmlspecialchars($service['name']) ?></h5>
                                    <?php if (!empty($service['description'])): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($service['description']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($service['unit'])): ?>
                                        <p class="text-xs text-gray-500 mt-1">Đơn vị: <?= htmlspecialchars($service['unit']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex gap-1 ml-4">
                                    <button class="px-3 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600" 
                                            onclick="openCreatePriceModal(<?= $service['id'] ?>)">
                                        + Thêm giá
                                    </button>
                                    <a href="?act=admin&module=location-services&action=edit-service&id=<?= $service['id'] ?>"
                                       class="px-3 py-1 bg-yellow-500 text-white rounded text-xs hover:bg-yellow-600 inline-block">
                                        Sửa
                                    </a>
                                    <button class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600" 
                                            onclick="deleteService(<?= $service['id'] ?>)">
                                        Xóa
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Danh sách giá -->
                            <div class="mt-4 border-t pt-3">
                                <h6 class="font-semibold text-sm mb-2 text-gray-700">
                                    💰 Bảng giá (<?= count($service['prices'] ?? []) ?>)
                                </h6>
                                <?php if (empty($service['prices'])): ?>
                                    <p class="text-xs text-gray-500 italic">Chưa có giá nào. Nhấn "+ Thêm giá" để thêm giá mới.</p>
                                <?php else: ?>
                                    <div class="space-y-2">
                                        <?php foreach ($service['prices'] as $price): ?>
                                            <div class="bg-white border border-gray-200 rounded p-3 flex justify-between items-center hover:shadow-sm transition">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3">
                                                        <span class="font-semibold text-blue-600">
                                                            <?= number_format($price['unit_price'], 0, ',', '.') ?> VND
                                                        </span>
                                                        <span class="px-2 py-0.5 text-xs rounded 
                                                            <?= $price['price_type'] == 'peak' ? 'bg-red-100 text-red-700' : 
                                                                ($price['price_type'] == 'low' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') ?>">
                                                        <?= $price['price_type'] == 'peak' ? 'Cao điểm' : 
                                                            ($price['price_type'] == 'low' ? 'Thấp điểm' : 'Tiêu chuẩn') ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-4 mt-1 text-xs text-gray-500">
                                                        <?php if (!empty($price['start_date']) || !empty($price['end_date'])): ?>
                                                            <span>
                                                                📅 
                                                                <?= !empty($price['start_date']) ? date('d/m/Y', strtotime($price['start_date'])) : '...' ?> 
                                                                - 
                                                                <?= !empty($price['end_date']) ? date('d/m/Y', strtotime($price['end_date'])) : '...' ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">📅 Vô thời hạn</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($price['notes'])): ?>
                                                            <span class="italic">💬 <?= htmlspecialchars(substr($price['notes'], 0, 50)) ?><?= strlen($price['notes']) > 50 ? '...' : '' ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="flex gap-1 ml-4">
                                                    <button class="px-2 py-1 bg-yellow-400 text-white rounded text-xs hover:bg-yellow-500" 
                                                            onclick="openEditPriceModal(<?= $price['id'] ?>)">
                                                        Sửa
                                                    </button>
                                                    <button class="px-2 py-1 bg-red-400 text-white rounded text-xs hover:bg-red-500" 
                                                            onclick="deletePrice(<?= $price['id'] ?>)">
                                                        Xóa
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
                <div class="mb-4 border-b">
                    <nav class="flex space-x-4">
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=providers"
                           class="px-4 py-2 <?= ($current_tab ?? 'providers') == 'providers' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-800' ?>">
                            Nhà cung cấp dịch vụ (<?= count($service_providers['data'] ?? []) ?>)
                        </a>
                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&tab=destinations"
                           class="px-4 py-2 <?= ($current_tab ?? 'providers') == 'destinations' ? 'border-b-2 border-blue-500 text-blue-600 font-medium' : 'text-gray-600 hover:text-gray-800' ?>">
                            Địa điểm du lịch (<?= count($destinations['data'] ?? []) ?>)
                        </a>
                    </nav>
                </div>

                <?php if (($current_tab ?? 'providers') == 'providers'): ?>
                    <!-- Service Providers Tab -->
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">📍 <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?></h2>
                        <a href="?act=admin&module=location-services&action=create-provider&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">
                            + Thêm nhà cung cấp
                        </a>
                    </div>

                    <?php if (empty($service_providers['data'])): ?>
                        <div class="text-center py-10 text-gray-500">Chưa có nhà cung cấp nào. Hãy thêm mới!</div>
                    <?php else: ?>
                        <?php foreach ($service_providers['data'] as $provider): ?>
                            <div class="service-card mb-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-bold text-lg">🏢 <?= htmlspecialchars($provider['name']) ?></h3>
                                        <?php if (!empty($provider['service_code'])): ?>
                                            <p class="text-sm text-gray-500">Mã: <?= htmlspecialchars($provider['service_code']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($provider['service_type_name'])): ?>
                                            <p class="text-sm text-gray-600">Loại: <?= htmlspecialchars($provider['service_type_name']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($provider['phone'])): ?>
                                            <p class="text-sm text-gray-600">📞 <?= htmlspecialchars($provider['phone']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($provider['email'])): ?>
                                            <p class="text-sm text-gray-600">✉️ <?= htmlspecialchars($provider['email']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="?act=admin&module=location-services&action=edit-provider&id=<?= $provider['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                           class="px-3 py-1 bg-blue-500 text-white rounded text-sm mr-2 inline-block">
                                            Sửa
                                        </a>
                                        <button class="px-3 py-1 bg-red-500 text-white rounded text-sm" 
                                                onclick="deleteServiceProvider(<?= $provider['id'] ?>)">
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-semibold">Dịch vụ (<?= $provider['services_count'] ?? 0 ?>)</h4>
                                        <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>&service_provider_id=<?= $provider['id'] ?>"
                                           class="px-3 py-1 bg-green-500 text-white rounded text-sm">
                                            Xem dịch vụ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php elseif (($current_tab ?? 'providers') == 'destinations'): ?>
                    <!-- Destinations Tab -->
                    <div class="mb-4">
                        <h2 class="text-xl font-bold mb-2">📍 <?= htmlspecialchars($current_province['name'] ?? 'Tỉnh thành') ?></h2>
                        <a href="?act=admin&module=location-services&action=create-destination&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-block">
                            + Thêm địa điểm du lịch
                        </a>
                    </div>

                    <?php if (empty($destinations['data'])): ?>
                        <div class="text-center py-10 text-gray-500">Chưa có địa điểm nào. Hãy thêm mới!</div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php foreach ($destinations['data'] as $destination): ?>
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($destination['name']) ?></h3>
                                    <?php if (!empty($destination['description'])): ?>
                                        <p class="text-sm text-gray-600 mb-2"><?= htmlspecialchars(substr($destination['description'], 0, 100)) ?>...</p>
                                    <?php endif; ?>
                                    <?php if (!empty($destination['thumbnail'])): ?>
                                        <img src="<?= htmlspecialchars($destination['thumbnail']) ?>" alt="<?= htmlspecialchars($destination['name']) ?>" class="w-full h-32 object-cover rounded mb-2">
                                    <?php endif; ?>
                                    <div class="flex gap-2 mt-3">
                                        <a href="?act=admin&module=location-services&action=edit-destination&id=<?= $destination['id'] ?>&country_id=<?= $current_country_id ?? '' ?>&province_id=<?= $current_province_id ?>"
                                           class="px-3 py-1 bg-blue-500 text-white rounded text-sm inline-block">
                                            Sửa
                                        </a>
                                        <button class="px-3 py-1 bg-red-500 text-white rounded text-sm" 
                                                onclick="deleteDestination(<?= $destination['id'] ?>)">
                                            Xóa
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <?php elseif (!empty($current_country_id) && !empty($current_country)): ?>
                <!-- Chỉ có country_id - Hiển thị danh sách provinces -->
                <div class="text-center py-10">
                    <h2 class="text-xl font-bold mb-4">📍 <?= htmlspecialchars($current_country['name']) ?></h2>
                    <p class="text-gray-600 mb-4">Vui lòng chọn một tỉnh thành từ danh sách bên trái hoặc từ danh sách dưới đây để xem nhà cung cấp và địa điểm du lịch.</p>
                    <?php if (!empty($provinces)): ?>
                        <div class="mt-6">
                            <p class="text-sm text-gray-500 mb-2">Danh sách tỉnh thành:</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                <?php foreach ($provinces as $province): ?>
                                    <a href="?act=admin&module=location-services&country_id=<?= $current_country_id ?>&province_id=<?= $province['id'] ?>&tab=providers"
                                       class="px-4 py-2 bg-blue-50 hover:bg-blue-100 rounded text-sm text-center border border-blue-200">
                                        <?= htmlspecialchars($province['name']) ?>
                                        <span class="text-xs text-gray-500">(<?= $province['providers_count'] ?? 0 ?>)</span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">Chưa có tỉnh thành nào trong quốc gia này.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Chưa chọn gì - Hiển thị hướng dẫn -->
            <div class="loading">
                    <p>Vui lòng chọn một quốc gia từ danh sách bên trái để bắt đầu</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include __DIR__ . '/modals.php'; ?>

<script>
$(document).ready(function() {
    let currentProvinceId = null;
    window.currentServiceProviderId = null;
    let currentServiceId = null;

    // Tree view functionality - Dùng URL parameters thay vì AJAX
    $('.tree-item.country').on('click', function(e) {
        e.stopPropagation();
        const $item = $(this);
        const countryId = $item.data('country-id');
        
        // Redirect với country_id parameter
        const url = new URL(window.location.href);
        url.searchParams.set('act', 'admin');
        url.searchParams.set('module', 'location-services');
        url.searchParams.set('country_id', countryId);
        // Xóa province_id và service_provider_id khi chọn country mới
        url.searchParams.delete('province_id');
        url.searchParams.delete('service_provider_id');
        url.searchParams.delete('tab');
        
        window.location.href = url.toString();
    });

    // Select province - Dùng URL parameters
    // Select province - Dùng URL parameters (nếu provinces được render từ server)
    $(document).on('click', '.tree-item.province', function(e) {
                        e.stopPropagation();
                        const provinceId = $(this).data('province-id');
        const $countryItem = $(this).closest('.tree-children').siblings('.tree-item.country');
        const countryId = $countryItem.data('country-id') || <?= json_encode($current_country_id ?? null) ?>;
        
        // Redirect với province_id parameter
        const url = new URL(window.location.href);
        url.searchParams.set('act', 'admin');
        url.searchParams.set('module', 'location-services');
        if (countryId) {
            url.searchParams.set('country_id', countryId);
        }
        url.searchParams.set('province_id', provinceId);
        url.searchParams.set('tab', 'providers'); // Default tab
        // Xóa service_provider_id khi chọn province mới
        url.searchParams.delete('service_provider_id');
        
        window.location.href = url.toString();
    });

    // Không cần AJAX nữa - data đã được load từ server

    // Format price (nếu cần dùng trong modals)
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price);
    }

    // Delete functions - Dùng URL redirect sau khi xóa
    window.deleteServiceProvider = function(id) {
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

    window.deleteService = function(id) {
        if (!confirm('Bạn có chắc muốn xóa dịch vụ này?')) return;
        
        const url = new URL(window.location.href);
        url.searchParams.set('act', 'admin');
        url.searchParams.set('module', 'location-services');
        url.searchParams.set('action', 'deleteService');
        url.searchParams.set('id', id);
        
        window.location.href = url.toString();
    };

    window.deletePrice = function(priceId) {
        if (!confirm('Bạn có chắc muốn xóa giá này?')) return;
        
        const url = new URL(window.location.href);
        url.searchParams.set('act', 'admin');
        url.searchParams.set('module', 'location-services');
        url.searchParams.set('action', 'deletePrice');
        url.searchParams.set('id', priceId);
        
        window.location.href = url.toString();
    };
    
    window.openEditPriceModal = function(priceId) {
        // Load price data và mở modal
        $.ajax({
            url: `?act=admin&module=location-services&action=getPrice&id=${priceId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
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
                        success: function(serviceResponse) {
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
            error: function() {
                showToast('Lỗi khi tải thông tin giá', 'error');
            }
        });
    };

    
    // Modal functions are defined in modals.php
});
</script>

