<?php
/**
 * MODALS FOR LOCATION SERVICES MANAGEMENT
 * Version 2.0 - Updated for new database schema
 */
?>

<!-- Modal: Create/Edit Service Provider -->
<div id="providerModal" class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-panel p-4 lg:p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border-l-4 border-accent shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg lg:text-xl font-bold text-primary-700 flex items-center gap-2" id="providerModalTitle">
                <i data-lucide="building" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                Thêm Nhà dịch vụ
            </h3>
            <button onclick="closeProviderModal()" class="text-primary-500 hover:text-primary-700 text-2xl lg:text-3xl transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Context Breadcrumb -->
        <div id="providerModalContext" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border-l-4 border-info rounded-xl text-xs lg:text-sm">
            <span class="font-bold text-info-dark flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                Địa điểm:
            </span>
            <span id="providerContextText" class="text-info-text"></span>
        </div>

        <form id="providerForm">
            <input type="hidden" id="providerId" name="id">
            <input type="hidden" id="providerProvinceId" name="province_id">
            <input type="hidden" id="providerCountryId" name="country_id">

            <!-- Section 1: Thông tin cơ bản -->
            <div class="mb-4 lg:mb-6">
                <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Thông tin cơ bản</h4>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên nhà dịch vụ <span class="text-danger">*</span></label>
                    <input type="text" id="providerName" name="name"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Khách sạn ABC, Nhà hàng XYZ" required>
                    <p class="text-xs text-primary-500 mt-1">Tên của nhà dịch vụ (khách sạn, nhà hàng, điểm tham quan...)
                    </p>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại dịch vụ (Service Type)</label>
                    <select id="providerServiceTypeId" name="service_type_id"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn loại dịch vụ (tùy chọn) --</option>
                    </select>
                    <p class="text-xs text-primary-500 mt-1">Loại dịch vụ chính của nhà cung cấp (Hotel, Restaurant, etc.)
                    </p>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mã nhà dịch vụ</label>
                    <input type="text" id="providerServiceCode" name="service_code"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base"
                        placeholder="Để trống để tự động tạo" readonly>
                    <p class="text-xs text-primary-500 mt-1">Mã tự động: SP-YYYYMMDD-XXX (tự động tạo khi lưu)</p>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                    <textarea id="providerDescription" name="description"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        rows="3" placeholder="Mô tả về nhà dịch vụ..."></textarea>
                </div>
            </div>

            <!-- Section 2: Địa điểm -->
            <div class="mb-4 lg:mb-6">
                <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Địa điểm</h4>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                    <input type="text" id="providerProvinceDisplay" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base"
                        readonly>
                    <p class="text-xs text-primary-500 mt-1">Tỉnh/Thành phố đã chọn từ cây danh mục</p>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Quốc gia <span class="text-danger">*</span></label>
                    <input type="text" id="providerCountryDisplay" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base"
                        readonly>
                    <p class="text-xs text-primary-500 mt-1">Quốc gia đã chọn từ cây danh mục</p>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Địa chỉ chi tiết</label>
                    <textarea id="providerAddress" name="address"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        rows="2" placeholder="Số nhà, đường, phường/xã..."></textarea>
                </div>
            </div>

            <!-- Section 3: Thông tin liên hệ -->
            <div class="mb-4 lg:mb-6">
                <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Thông tin liên hệ</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 lg:gap-4 mb-3 lg:mb-4">
                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Người liên hệ</label>
                        <input type="text" id="providerContactPerson" name="contact_person"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                            placeholder="Tên người liên hệ">
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Điện thoại</label>
                        <input type="text" id="providerPhone" name="phone"
                            class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                            placeholder="0901234567">
                    </div>
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Email</label>
                    <input type="email" id="providerEmail" name="email"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="contact@example.com">
                </div>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Website</label>
                    <input type="url" id="providerWebsite" name="website"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="https://example.com">
                </div>
            </div>

            <!-- Section 4: Trạng thái -->
            <div class="mb-4 lg:mb-6">
                <h4 class="text-base lg:text-lg font-bold text-primary-700 mb-3 lg:mb-4 pb-2 lg:pb-3 border-b border-primary-100">Trạng thái</h4>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
                    <select id="providerStatus" name="status"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        required>
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Ngừng hoạt động</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
                <button type="button" onclick="closeProviderModal()"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Destination -->
<div id="destinationModal"
    class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-panel p-4 lg:p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl border-l-4 border-accent shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg lg:text-xl font-bold text-primary-700 flex items-center gap-2" id="destinationModalTitle">
                <i data-lucide="map-pin" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
                Thêm Địa điểm du lịch
            </h3>
            <button onclick="closeDestinationModal()"
                class="text-primary-500 hover:text-primary-700 text-2xl lg:text-3xl transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <!-- Context Breadcrumb -->
        <div id="destinationModalContext" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border-l-4 border-info rounded-xl text-xs lg:text-sm">
            <span class="font-bold text-info-dark flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
                Địa điểm:
            </span>
            <span id="destinationContextText" class="text-info-text"></span>
        </div>

        <form id="destinationForm">
            <input type="hidden" id="destinationId" name="id">
            <input type="hidden" id="destinationProvinceId" name="province_id">
            <input type="hidden" id="destinationCountryId" name="country_id">

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên địa điểm <span class="text-danger">*</span></label>
                <input type="text" id="destinationName" name="name"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="VD: Hồ Xuân Hương, Chợ Đà Lạt" required>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                <input type="text" id="destinationProvinceDisplay" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base"
                    readonly>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Quốc gia <span class="text-danger">*</span></label>
                <input type="text" id="destinationCountryDisplay" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-100 border border-primary-200 rounded-xl text-primary-600 text-sm lg:text-base"
                    readonly>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                <textarea id="destinationDescription" name="description"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="4"
                    placeholder="Mô tả về địa điểm..."></textarea>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Vị trí cụ thể</label>
                <textarea id="destinationLocations" name="locations"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"
                    placeholder="Số nhà, đường, phường/xã..."></textarea>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Trạng thái <span class="text-danger">*</span></label>
                <select id="destinationStatus" name="status"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    required>
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngừng hoạt động</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
                <button type="button" onclick="closeDestinationModal()"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                    Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Service -->
<div id="serviceModal" class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-panel p-4 lg:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl border-l-4 border-accent shadow-lg">
        <h3 class="text-lg lg:text-xl font-bold text-primary-700 mb-2 lg:mb-3 flex items-center gap-2" id="serviceModalTitle">
            <i data-lucide="plus" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
            Thêm dịch vụ
        </h3>
        <!-- Context Breadcrumb -->
        <div id="serviceModalContext" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border-l-4 border-info rounded-xl text-xs lg:text-sm text-info-text">
            <span id="serviceContextText"></span>
        </div>
        <form id="serviceForm">
            <input type="hidden" id="serviceId" name="id">
            <input type="hidden" id="serviceProviderId" name="service_provider_id">

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại dịch vụ <span class="text-danger">*</span></label>
                <select id="serviceTypeId" name="service_type_id" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
                    <option value="">-- Chọn loại dịch vụ --</option>
                </select>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên dịch vụ <span class="text-danger">*</span></label>
                <input type="text" id="serviceName" name="name" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" required>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                <textarea id="serviceDescription" name="description" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    rows="3"></textarea>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đơn vị</label>
                <input type="text" id="serviceUnit" name="unit" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    placeholder="VD: phòng/đêm, suất, xe/ngày">
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá ước tính</label>
                <input type="number" id="serviceEstimatedPrice" name="estimated_price"
                    class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" min="0" step="1000">
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                <textarea id="serviceNotes" name="notes" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
                <button type="button" onclick="closeServiceModal()" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">Hủy</button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Create/Edit Price -->
<div id="priceModal" class="hidden fixed inset-0 bg-primary-900 bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-panel p-4 lg:p-6 w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl border-l-4 border-accent shadow-lg">
        <h3 class="text-lg lg:text-xl font-bold text-primary-700 mb-3 lg:mb-4 flex items-center gap-2" id="priceModalTitle">
            <i data-lucide="dollar-sign" class="w-5 h-5 lg:w-6 lg:h-6 text-accent"></i>
            Thêm giá dịch vụ
        </h3>
        
        <!-- Context Info -->
        <div id="priceModalContext" class="mb-4 lg:mb-6 p-3 lg:p-4 bg-info-bg border-l-4 border-info rounded-xl text-xs lg:text-sm">
            <div class="mb-2">
                <span class="font-bold text-info-dark flex items-center gap-2">
                    <i data-lucide="building" class="w-4 h-4"></i>
                    Nhà dịch vụ:
                </span>
                <span id="priceContextText" class="text-info-text"></span>
            </div>
            <div class="text-xs text-info-text mt-2">
                <strong>💡 Lưu ý:</strong> Giá này áp dụng cho dịch vụ của nhà cung cấp. Bạn có thể thêm nhiều giá khác nhau cho cùng dịch vụ (khác loại giá hoặc khác thời gian).
            </div>
        </div>
        
        <form id="priceForm">
            <input type="hidden" id="priceId" name="id">
            <input type="hidden" id="priceServiceId" name="service_id">

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Giá <span class="text-danger">*</span></label>
                <input type="number" id="priceUnitPrice" name="unit_price" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                    min="0" step="1000" required>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại giá</label>
                <select id="priceType" name="price_type" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                    <option value="standard">Tiêu chuẩn</option>
                    <option value="peak">Cao điểm (Mùa cao điểm, lễ tết)</option>
                    <option value="low">Thấp điểm (Mùa thấp điểm, off-season)</option>
                </select>
                <p class="text-xs text-primary-500 mt-1">
                    <strong>Lưu ý:</strong> Có thể thêm nhiều giá cùng loại nhưng khác thời gian (từ ngày - đến ngày). 
                    Ví dụ: Giá cao điểm từ 1/1-7/1 và giá cao điểm từ 15/1-20/1 là 2 giá riêng biệt.
                </p>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Từ ngày</label>
                <input type="date" id="priceValidFrom" name="start_date" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <p class="text-xs text-primary-500 mt-1">Để trống nếu giá áp dụng vô thời hạn</p>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Đến ngày</label>
                <input type="date" id="priceValidTo" name="end_date" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                <p class="text-xs text-primary-500 mt-1">Để trống nếu giá áp dụng vô thời hạn</p>
            </div>

            <div class="mb-3 lg:mb-4">
                <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Ghi chú</label>
                <textarea id="priceNotes" name="notes" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base" rows="2"></textarea>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-2 lg:gap-3 pt-4 border-t border-primary-100">
                <button type="button" onclick="closePriceModal()" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 rounded-xl font-semibold hover:bg-primary-100 transition-colors text-sm lg:text-base">Hủy</button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">Lưu</button>
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
        $('#priceType').val('standard');

        // Load service info để hiển thị context
        $.ajax({
            url: `?act=admin&module=location-services&action=getService&id=${serviceId}`,
            method: 'GET',
            dataType: 'json',
            success: function (serviceResponse) {
                if (serviceResponse.success && serviceResponse.data) {
                    const service = serviceResponse.data;
                    const providerName = service.service_provider_name || '';
                    const serviceName = service.name || '';

                    // Hiển thị context
                    let contextParts = [];
                    if (providerName) contextParts.push(providerName);
                    if (serviceName) contextParts.push(serviceName);
                    const contextText = contextParts.length > 0
                        ? contextParts.join(' > ')
                        : 'Đang thêm giá dịch vụ';
                    $('#priceContextText').text(contextText);
                } else {
                    showToast('Không thể tải thông tin dịch vụ', 'error');
                }
            },
            error: function () {
                showToast('Lỗi khi tải thông tin dịch vụ', 'error');
            }
        });

        $('#priceModal').removeClass('hidden');
    };

    function closePriceModal() {
        $('#priceModal').addClass('hidden');
    }

    $('#priceForm').on('submit', function (e) {
        e.preventDefault();
        
        // Validate: service_id phải có
        const serviceId = $('#priceServiceId').val();
        if (!serviceId) {
            showToast('Không thể xác định dịch vụ. Vui lòng thử lại.', 'error');
            return;
        }
        
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
        <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 border-l-4 border-white border-opacity-30 z-50 animate-slide-in font-medium">
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