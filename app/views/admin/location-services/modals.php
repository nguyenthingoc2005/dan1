<?php
/**
 * MODALS FOR LOCATION SERVICES MANAGEMENT
 * Version 2.0 - Updated for new database schema
 */
?>

<!-- Modal: Create/Edit Service Provider -->
<div id="providerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold" id="providerModalTitle">Thêm Nhà dịch vụ</h3>
            <button onclick="closeProviderModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Context Breadcrumb -->
        <div id="providerModalContext" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
            <span class="font-medium text-blue-800">📍 Địa điểm: </span>
            <span id="providerContextText" class="text-blue-700"></span>
        </div>

        <form id="providerForm">
            <input type="hidden" id="providerId" name="id">
            <input type="hidden" id="providerProvinceId" name="province_id">
            <input type="hidden" id="providerCountryId" name="country_id">

            <!-- Section 1: Thông tin cơ bản -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin cơ bản</h4>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tên nhà dịch vụ *</label>
                    <input type="text" id="providerName" name="name"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="VD: Khách sạn ABC, Nhà hàng XYZ" required>
                    <p class="text-xs text-gray-500 mt-1">Tên của nhà dịch vụ (khách sạn, nhà hàng, điểm tham quan...)
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Loại dịch vụ (Service Type)</label>
                    <select id="providerServiceTypeId" name="service_type_id"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Loại dịch vụ chính của nhà cung cấp (Hotel, Restaurant, etc.)
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Mã nhà dịch vụ</label>
                    <input type="text" id="providerServiceCode" name="service_code"
                        class="w-full px-3 py-2 border rounded bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Để trống để tự động tạo" readonly>
                    <p class="text-xs text-gray-500 mt-1">Mã tự động: SP-YYYYMMDD-XXX (tự động tạo khi lưu)</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Mô tả</label>
                    <textarea id="providerDescription" name="description"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="3" placeholder="Mô tả về nhà dịch vụ..."></textarea>
                </div>
            </div>

            <!-- Section 2: Địa điểm -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Địa điểm</h4>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Tỉnh/Thành phố *</label>
                    <input type="text" id="providerProvinceDisplay" class="w-full px-3 py-2 border rounded bg-gray-100"
                        readonly>
                    <p class="text-xs text-gray-500 mt-1">Tỉnh/Thành phố đã chọn từ cây danh mục</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Quốc gia *</label>
                    <input type="text" id="providerCountryDisplay" class="w-full px-3 py-2 border rounded bg-gray-100"
                        readonly>
                    <p class="text-xs text-gray-500 mt-1">Quốc gia đã chọn từ cây danh mục</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Địa chỉ chi tiết</label>
                    <textarea id="providerAddress" name="address"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="2" placeholder="Số nhà, đường, phường/xã..."></textarea>
                </div>
            </div>

            <!-- Section 3: Thông tin liên hệ -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Thông tin liên hệ</h4>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Người liên hệ</label>
                        <input type="text" id="providerContactPerson" name="contact_person"
                            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Tên người liên hệ">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Điện thoại</label>
                        <input type="text" id="providerPhone" name="phone"
                            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0901234567">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="providerEmail" name="email"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="contact@example.com">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Website</label>
                    <input type="url" id="providerWebsite" name="website"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="https://example.com">
                </div>
            </div>

            <!-- Section 4: Trạng thái -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-3 pb-2 border-b">Trạng thái</h4>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Trạng thái *</label>
                    <select id="providerStatus" name="status"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Ngừng hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeProviderModal()"
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Hủy
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Destination -->
<div id="destinationModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold" id="destinationModalTitle">Thêm Địa điểm du lịch</h3>
            <button onclick="closeDestinationModal()"
                class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Context Breadcrumb -->
        <div id="destinationModalContext" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm">
            <span class="font-medium text-blue-800">📍 Địa điểm: </span>
            <span id="destinationContextText" class="text-blue-700"></span>
        </div>

        <form id="destinationForm">
            <input type="hidden" id="destinationId" name="id">
            <input type="hidden" id="destinationProvinceId" name="province_id">
            <input type="hidden" id="destinationCountryId" name="country_id">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên địa điểm *</label>
                <input type="text" id="destinationName" name="name"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="VD: Hồ Xuân Hương, Chợ Đà Lạt" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tỉnh/Thành phố *</label>
                <input type="text" id="destinationProvinceDisplay" class="w-full px-3 py-2 border rounded bg-gray-100"
                    readonly>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Quốc gia *</label>
                <input type="text" id="destinationCountryDisplay" class="w-full px-3 py-2 border rounded bg-gray-100"
                    readonly>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea id="destinationDescription" name="description"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"
                    placeholder="Mô tả về địa điểm..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Vị trí cụ thể</label>
                <textarea id="destinationLocations" name="locations"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2"
                    placeholder="Số nhà, đường, phường/xã..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Trạng thái *</label>
                <select id="destinationStatus" name="status"
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngừng hoạt động</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeDestinationModal()"
                    class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Hủy
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Service -->
<div id="serviceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-2" id="serviceModalTitle">Thêm dịch vụ</h3>
        <!-- Context Breadcrumb -->
        <div id="serviceModalContext" class="mb-4 p-2 bg-gray-100 rounded text-sm text-gray-600">
            <span id="serviceContextText"></span>
        </div>
        <form id="serviceForm">
            <input type="hidden" id="serviceId" name="id">
            <input type="hidden" id="serviceProviderId" name="service_provider_id">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Loại dịch vụ *</label>
                <select id="serviceTypeId" name="service_type_id" class="w-full px-3 py-2 border rounded" required>
                    <option value="">-- Chọn loại dịch vụ --</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tên dịch vụ *</label>
                <input type="text" id="serviceName" name="name" class="w-full px-3 py-2 border rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Mô tả</label>
                <textarea id="serviceDescription" name="description" class="w-full px-3 py-2 border rounded"
                    rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Đơn vị</label>
                <input type="text" id="serviceUnit" name="unit" class="w-full px-3 py-2 border rounded"
                    placeholder="VD: phòng/đêm, suất, xe/ngày">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Giá ước tính</label>
                <input type="number" id="serviceEstimatedPrice" name="estimated_price"
                    class="w-full px-3 py-2 border rounded" min="0" step="1000">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ghi chú</label>
                <textarea id="serviceNotes" name="notes" class="w-full px-3 py-2 border rounded" rows="2"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeServiceModal()" class="px-4 py-2 bg-gray-300 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Price -->
<div id="priceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4" id="priceModalTitle">Thêm giá dịch vụ</h3>
        <form id="priceForm">
            <input type="hidden" id="priceId" name="id">
            <input type="hidden" id="priceServiceId" name="service_id">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Tỉnh thành</label>
                <select id="priceProvinceId" name="province_id" class="w-full px-3 py-2 border rounded">
                    <option value="">-- Chọn tỉnh thành --</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Địa điểm</label>
                <select id="priceDestinationId" name="destination_id" class="w-full px-3 py-2 border rounded">
                    <option value="">-- Chọn địa điểm (tùy chọn) --</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Giá *</label>
                <input type="number" id="priceUnitPrice" name="unit_price" class="w-full px-3 py-2 border rounded"
                    min="0" step="1000" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Loại tiền</label>
                <select id="priceCurrency" name="currency" class="w-full px-3 py-2 border rounded">
                    <option value="VND">VND</option>
                    <option value="USD">USD</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Loại giá</label>
                <select id="priceType" name="price_type" class="w-full px-3 py-2 border rounded">
                    <option value="standard">Tiêu chuẩn</option>
                    <option value="peak">Cao điểm</option>
                    <option value="low">Thấp điểm</option>
                    <option value="custom">Tùy chỉnh</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Từ ngày</label>
                <input type="date" id="priceValidFrom" name="valid_from" class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Đến ngày</label>
                <input type="date" id="priceValidTo" name="valid_to" class="w-full px-3 py-2 border rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ghi chú</label>
                <textarea id="priceNotes" name="notes" class="w-full px-3 py-2 border rounded" rows="2"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closePriceModal()" class="px-4 py-2 bg-gray-300 rounded">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Load dropdown data for service modal
    function loadDropdownDataForService() {
        $.ajax({
            url: '?act=admin&module=location-services&action=getDropdownData',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Populate service types
                    const $serviceTypeSelect = $('#serviceTypeId');
                    $serviceTypeSelect.empty().append('<option value="">-- Chọn loại dịch vụ --</option>');
                    if (response.data.service_types && Array.isArray(response.data.service_types)) {
                        response.data.service_types.forEach(function (type) {
                            const name = type.name || type;
                            const id = type.id || type;
                            $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    } else if (typeof response.data.service_types === 'object') {
                        for (const [id, name] of Object.entries(response.data.service_types)) {
                            $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                        }
                    }

                    // Store for later use
                    window.dropdownData = response.data;
                }
            }
        });
    }

    // Provider Modal
    window.openCreateProviderModal = function (provinceId, countryId, provinceName, countryName) {
        $('#providerModalTitle').text('Thêm Nhà dịch vụ');
        $('#providerForm')[0].reset();
        $('#providerId').val('');
        $('#providerProvinceId').val(provinceId);
        $('#providerCountryId').val(countryId);
        $('#providerProvinceDisplay').val(provinceName || '');
        $('#providerCountryDisplay').val(countryName || '');
        $('#providerStatus').val('active');
        $('#providerServiceCode').val(''); // Will be auto-generated

        // Hiển thị context
        const contextText = countryName && provinceName
            ? `${countryName} > ${provinceName}`
            : provinceName || 'Chưa chọn địa điểm';
        $('#providerContextText').text(contextText);

        // Load dropdown data
        $.ajax({
            url: `?act=admin&module=location-services&action=getDropdownData&province_id=${provinceId}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Populate service types
                    const $serviceTypeSelect = $('#providerServiceTypeId');
                    $serviceTypeSelect.empty().append('<option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>');
                    if (response.data.service_types && Array.isArray(response.data.service_types)) {
                        response.data.service_types.forEach(function (type) {
                            const name = type.name || type;
                            const id = type.id || type;
                            $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    } else if (typeof response.data.service_types === 'object') {
                        for (const [id, name] of Object.entries(response.data.service_types)) {
                            $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                        }
                    }

                    // Store for later use
                    window.dropdownData = response.data;
                }
            }
        });

        $('#providerModal').removeClass('hidden');
    };

    window.openEditProviderModal = function (id, provinceId, countryId, provinceName, countryName) {
        $('#providerModalTitle').text('Sửa Nhà dịch vụ');

        // Hiển thị context
        const contextText = countryName && provinceName
            ? `${countryName} > ${provinceName}`
            : provinceName || 'Chưa chọn địa điểm';
        $('#providerContextText').text(contextText);

        // Load provider data
        $.ajax({
            url: `?act=admin&module=location-services&action=getServiceProvider&id=${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (providerResponse) {
                if (providerResponse.success && providerResponse.data) {
                    const provider = providerResponse.data;

                    // Load dropdown data
                    $.ajax({
                        url: `?act=admin&module=location-services&action=getDropdownData&province_id=${provider.province_id}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (dropdownResponse) {
                            if (dropdownResponse.success) {
                                // Populate service types
                                const $serviceTypeSelect = $('#providerServiceTypeId');
                                $serviceTypeSelect.empty().append('<option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>');
                                if (dropdownResponse.data.service_types && Array.isArray(dropdownResponse.data.service_types)) {
                                    dropdownResponse.data.service_types.forEach(function (type) {
                                        const name = type.name || type;
                                        const id = type.id || type;
                                        $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                                    });
                                }

                                // Now populate form with provider data
                                $('#providerId').val(provider.id);
                                $('#providerProvinceId').val(provider.province_id);
                                $('#providerCountryId').val(provider.country_id);
                                $('#providerProvinceDisplay').val(provider.province_name || provinceName || '');
                                $('#providerCountryDisplay').val(provider.country_name || countryName || '');
                                $('#providerServiceCode').val(provider.service_code || '');
                                $('#providerName').val(provider.name);
                                $('#providerServiceTypeId').val(provider.service_type_id || '');
                                $('#providerDescription').val(provider.description || '');
                                $('#providerAddress').val(provider.address || '');
                                $('#providerContactPerson').val(provider.contact_person || '');
                                $('#providerEmail').val(provider.email || '');
                                $('#providerPhone').val(provider.phone || '');
                                $('#providerWebsite').val(provider.website || '');
                                $('#providerStatus').val(provider.status || 'active');

                                $('#providerModal').removeClass('hidden');
                            }
                        }
                    });
                } else {
                    showToast('Không thể tải dữ liệu nhà dịch vụ', 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi tải dữ liệu', 'error');
            }
        });
    };

    function closeProviderModal() {
        $('#providerModal').addClass('hidden');
    }

    $('#providerForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const isEdit = $('#providerId').val() ? true : false;
        const action = isEdit ? 'updateServiceProvider' : 'createServiceProvider';

        // Show loading
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: `?act=admin&module=location-services&action=${action}`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.success) {
                    showToast(response.message, 'success');
                    closeProviderModal();
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false).text(originalText);
                showToast('Lỗi khi lưu dữ liệu', 'error');
            }
        });
    });

    // Destination Modal
    window.openCreateDestinationModal = function (provinceId, countryId, provinceName, countryName) {
        $('#destinationModalTitle').text('Thêm Địa điểm du lịch');
        $('#destinationForm')[0].reset();
        $('#destinationId').val('');
        $('#destinationProvinceId').val(provinceId);
        $('#destinationCountryId').val(countryId);
        $('#destinationProvinceDisplay').val(provinceName || '');
        $('#destinationCountryDisplay').val(countryName || '');
        $('#destinationStatus').val('active');

        // Hiển thị context
        const contextText = countryName && provinceName
            ? `${countryName} > ${provinceName}`
            : provinceName || 'Chưa chọn địa điểm';
        $('#destinationContextText').text(contextText);

        $('#destinationModal').removeClass('hidden');
    };

    window.openEditDestinationModal = function (id, provinceId, countryId, provinceName, countryName) {
        $('#destinationModalTitle').text('Sửa Địa điểm du lịch');

        // Hiển thị context
        const contextText = countryName && provinceName
            ? `${countryName} > ${provinceName}`
            : provinceName || 'Chưa chọn địa điểm';
        $('#destinationContextText').text(contextText);

        // Load destination data
        $.ajax({
            url: `?act=admin&module=location-services&action=getDestination&id=${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success && response.data) {
                    const destination = response.data;
                    $('#destinationId').val(destination.id);
                    $('#destinationProvinceId').val(destination.province_id || provinceId);
                    $('#destinationCountryId').val(destination.country_id || countryId);
                    $('#destinationProvinceDisplay').val(destination.province_name || provinceName || '');
                    $('#destinationCountryDisplay').val(destination.country_name || countryName || '');
                    $('#destinationName').val(destination.name);
                    $('#destinationDescription').val(destination.description || '');
                    $('#destinationLocations').val(destination.locations || '');
                    $('#destinationStatus').val(destination.status || 'active');

                    $('#destinationModal').removeClass('hidden');
                } else {
                    showToast('Không thể tải dữ liệu địa điểm', 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi tải dữ liệu', 'error');
            }
        });
    };

    function closeDestinationModal() {
        $('#destinationModal').addClass('hidden');
    }

    $('#destinationForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const action = $('#destinationId').val() ? 'updateDestination' : 'createDestination';

        // Show loading
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: `?act=admin&module=location-services&action=${action}`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.success) {
                    showToast(response.message, 'success');
                    closeDestinationModal();
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false).text(originalText);
                showToast('Lỗi khi lưu dữ liệu', 'error');
            }
        });
    });

    // Service Modal
    window.openCreateServiceModal = function (serviceProviderId, providerName, provinceName, countryName) {
        loadDropdownDataForService();
        $('#serviceModalTitle').text('Thêm dịch vụ');
        $('#serviceForm')[0].reset();
        $('#serviceId').val('');
        $('#serviceProviderId').val(serviceProviderId);

        // Hiển thị context
        let contextParts = [];
        if (countryName) contextParts.push(countryName);
        if (provinceName) contextParts.push(provinceName);
        if (providerName) contextParts.push(providerName);
        const contextText = contextParts.length > 0
            ? `📍 ${contextParts.join(' > ')}`
            : 'Đang thêm dịch vụ mới';
        $('#serviceContextText').text(contextText);

        $('#serviceModal').removeClass('hidden');
    };

    window.openEditServiceModal = function (id, providerName, provinceName, countryName) {
        $('#serviceModalTitle').text('Sửa dịch vụ');

        // Hiển thị context
        let contextParts = [];
        if (countryName) contextParts.push(countryName);
        if (provinceName) contextParts.push(provinceName);
        if (providerName) contextParts.push(providerName);
        const contextText = contextParts.length > 0
            ? `📍 ${contextParts.join(' > ')}`
            : 'Đang sửa dịch vụ';
        $('#serviceContextText').text(contextText);

        // Load dropdown data first, then load service data
        $.ajax({
            url: '?act=admin&module=location-services&action=getDropdownData',
            method: 'GET',
            dataType: 'json',
            success: function (dropdownResponse) {
                if (dropdownResponse.success) {
                    // Populate service types
                    const $serviceTypeSelect = $('#serviceTypeId');
                    $serviceTypeSelect.empty().append('<option value="">-- Chọn loại dịch vụ --</option>');
                    if (dropdownResponse.data.service_types && Array.isArray(dropdownResponse.data.service_types)) {
                        dropdownResponse.data.service_types.forEach(function (type) {
                            const name = type.name || type;
                            const id = type.id || type;
                            $serviceTypeSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    }

                    // Store for later use
                    window.dropdownData = dropdownResponse.data;

                    // Now load service data
                    $.ajax({
                        url: `?act=admin&module=location-services&action=getService&id=${id}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success && response.data) {
                                const service = response.data;
                                $('#serviceId').val(service.id);
                                $('#serviceProviderId').val(service.service_provider_id);
                                $('#serviceTypeId').val(service.service_type_id);
                                $('#serviceName').val(service.name);
                                $('#serviceDescription').val(service.description || '');
                                $('#serviceUnit').val(service.unit || '');
                                $('#serviceEstimatedPrice').val(service.estimated_price || '');
                                $('#serviceNotes').val(service.notes || '');
                                $('#serviceModal').removeClass('hidden');
                            } else {
                                showToast('Không thể tải dữ liệu dịch vụ', 'error');
                            }
                        },
                        error: function () {
                            showToast('Lỗi khi tải dữ liệu', 'error');
                        }
                    });
                }
            }
        });
    };

    function closeServiceModal() {
        $('#serviceModal').addClass('hidden');
    }

    $('#serviceForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const action = $('#serviceId').val() ? 'updateService' : 'createService';

        // Show loading
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: `?act=admin&module=location-services&action=${action}`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.success) {
                    showToast(response.message, 'success');
                    closeServiceModal();
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false).text(originalText);
                showToast('Lỗi khi lưu dữ liệu', 'error');
            }
        });
    });

    // Price Modal
    window.openCreatePriceModal = function (serviceId) {
        $('#priceModalTitle').text('Thêm giá dịch vụ');
        $('#priceForm')[0].reset();
        $('#priceId').val('');
        $('#priceServiceId').val(serviceId);

        // Load dropdown data for price modal
        $.ajax({
            url: '?act=admin&module=location-services&action=getDropdownData',
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Populate provinces
                    const $provinceSelect = $('#priceProvinceId');
                    $provinceSelect.empty().append('<option value="">-- Chọn tỉnh thành --</option>');
                    if (response.data.provinces && Array.isArray(response.data.provinces)) {
                        response.data.provinces.forEach(function (province) {
                            const name = province.name || province;
                            const id = province.id || province;
                            $provinceSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    } else if (typeof response.data.provinces === 'object') {
                        for (const [id, name] of Object.entries(response.data.provinces)) {
                            $provinceSelect.append(`<option value="${id}">${name}</option>`);
                        }
                    }

                    // Populate destinations
                    const $destSelect = $('#priceDestinationId');
                    $destSelect.empty().append('<option value="">-- Chọn địa điểm (tùy chọn) --</option>');
                    if (response.data.destinations && Array.isArray(response.data.destinations)) {
                        response.data.destinations.forEach(function (dest) {
                            const name = dest.name || dest;
                            const id = dest.id || dest;
                            $destSelect.append(`<option value="${id}">${name}</option>`);
                        });
                    } else if (typeof response.data.destinations === 'object') {
                        for (const [id, name] of Object.entries(response.data.destinations)) {
                            $destSelect.append(`<option value="${id}">${name}</option>`);
                        }
                    }
                }
            }
        });

        $('#priceModal').removeClass('hidden');
    };

    function closePriceModal() {
        $('#priceModal').addClass('hidden');
    }

    $('#priceForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const action = $('#priceId').val() ? 'updatePrice' : 'createPrice';

        // Show loading
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Đang xử lý...');

        $.ajax({
            url: `?act=admin&module=location-services&action=${action}`,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                $submitBtn.prop('disabled', false).text(originalText);
                if (response.success) {
                    showToast(response.message, 'success');
                    closePriceModal();
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false).text(originalText);
                showToast('Lỗi khi lưu dữ liệu', 'error');
            }
        });
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        type = type || 'info';
        const bgColor = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type] || 'bg-blue-500';

        const toast = $(`
        <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 animate-slide-in">
            ${message}
        </div>
    `);

        $('body').append(toast);

        setTimeout(function () {
            toast.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    // Delete functions
    function deleteServiceProvider(id) {
        if (!confirm('Bạn có chắc muốn xóa nhà cung cấp này?')) {
            return;
        }

        $.ajax({
            url: `?act=admin&module=location-services&action=deleteServiceProvider&id=${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi xóa nhà cung cấp', 'error');
            }
        });
    }

    function deleteDestination(id) {
        if (!confirm('Bạn có chắc muốn xóa địa điểm này?')) {
            return;
        }

        $.ajax({
            url: `?act=admin&module=location-services&action=deleteDestination&id=${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi xóa địa điểm', 'error');
            }
        });
    }

    function deleteService(id) {
        if (!confirm('Bạn có chắc muốn xóa dịch vụ này?')) {
            return;
        }

        $.ajax({
            url: `?act=admin&module=location-services&action=deleteService&id=${id}`,
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    // Reload page với preserve URL params
                    const urlParams = new URLSearchParams(window.location.search);
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi xóa dịch vụ', 'error');
            }
        });
    }

    // Load dropdown data on page load
    $(document).ready(function () {
        loadDropdownDataForService();
    });
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>