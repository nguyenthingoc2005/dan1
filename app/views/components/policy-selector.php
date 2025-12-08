<?php
/**
 * ==============================================================================
 * POLICY SELECTOR COMPONENT
 * ==============================================================================
 * 
 * Component để chọn policies cho tour
 * 
 * Variables:
 * - $policies (required): Array các policies có sẵn
 * - $selected_policy_ids (optional): Array các policy IDs đã được chọn
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

$policies = $policies ?? [];
$selected_policy_ids = $selected_policy_ids ?? [];

// Group policies by type
$policies_by_type = [];
foreach ($policies as $policy) {
    $type = $policy['policy_type'] ?? 'other';
    if (!isset($policies_by_type[$type])) {
        $policies_by_type[$type] = [];
    }
    $policies_by_type[$type][] = $policy;
}

$policy_type_names = [
    'cancellation' => 'Chính sách hủy tour',
    'change' => 'Chính sách đổi tour',
    'refund' => 'Chính sách hoàn tiền',
    'booking' => 'Chính sách đặt tour',
    'other' => 'Chính sách khác'
];
?>

<div class="policy-selector">
    <div class="mb-4 lg:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <h4 class="text-base lg:text-lg font-bold text-primary-700">Chọn chính sách</h4>
        <button type="button" onclick="openCreatePolicyModal()"
            class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base flex items-center justify-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Thêm chính sách mới
        </button>
    </div>

    <!-- Selected Policies -->
    <div id="selected-policies-list" class="mb-4 lg:mb-6">
        <?php if (empty($selected_policy_ids)): ?>
            <div class="text-primary-500 text-center py-6 lg:py-8 bg-primary-50 rounded-2xl border-2 border-dashed border-primary-200">
                <div class="flex justify-center mb-2 lg:mb-3">
                    <i data-lucide="file-text" class="w-8 h-8 lg:w-12 lg:h-12 text-primary-300"></i>
                </div>
                <p class="text-xs lg:text-sm text-primary-600">Chưa chọn chính sách nào</p>
            </div>
        <?php else: ?>
            <div class="space-y-2 lg:space-y-3">
                <?php foreach ($selected_policy_ids as $policy_id): ?>
                    <?php
                    $policy = null;
                    foreach ($policies as $p) {
                        if ($p['id'] == $policy_id) {
                            $policy = $p;
                            break;
                        }
                    }
                    if ($policy):
                        ?>
                        <div
                            class="selected-policy-item bg-success-bg border border-success rounded-xl p-3 lg:p-4 flex items-center justify-between" data-policy-id="<?= $policy_id ?>">
                            <div>
                                <span class="font-semibold text-success-dark text-sm lg:text-base"><?= htmlspecialchars($policy['name']) ?></span>
                                <span class="text-xs lg:text-sm text-success-text ml-2">
                                    (<?= $policy_type_names[$policy['policy_type'] ?? 'other'] ?? 'Khác' ?>) 
                                </span>
                            </div>
                            <button type="button" onclick="removePolicy(<?= $policy_id ?>)" class="text-danger hover:text-danger-dark flex items-center justify-center w-6 h-6 lg:w-8 lg:h-8 rounded-lg hover:bg-danger-bg transition-colors">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                            <input type="hidden" name="policy_ids[]" value="<?= $policy_id ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Available Policies by Type -->
    <div class="space-y-4 lg:space-y-6">
        <?php foreach ($policies_by_type as $type => $type_policies): ?>
            <div class="policy-type-group">
                <h5 class="font-bold text-primary-700 mb-3 text-sm lg:text-base">
                    <?= $policy_type_names[$type] ?? 'Chính sách khác' ?>
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:gap-4">
                    <?php foreach ($type_policies as $policy): ?>
                        <?php $is_selected = in_array($policy['id'], $selected_policy_ids); ?>
                        <div
                            class="policy-card border rounded-xl p-3 lg:p-4 <?= $is_selected ? 'bg-accent-100 border-accent shadow-sm' : 'bg-panel border-primary-100 hover:border-accent' ?> transition-all">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="policy-<?= $policy['id'] ?>" value="<?= $policy['id'] ?>"
                                    <?= $is_selected ? 'checked' : '' ?>
                                    onchange="togglePolicy(this, <?= $policy['id'] ?>, '<?= htmlspecialchars($policy['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($policy_type_names[$type] ?? 'Khác', ENT_QUOTES) ?>')"
                                    class="mt-1 w-4 h-4 lg:w-5 lg:h-5 text-accent rounded focus:ring-2 focus:ring-accent">
                                <div class="flex-1">
                                    <label for="policy-<?= $policy['id'] ?>" class="cursor-pointer">
                                        <div class="font-semibold text-primary-700 text-sm lg:text-base"><?= htmlspecialchars($policy['name']) ?></div>
                                        <?php if (!empty($policy['description'])): ?>
                                            <div class="text-xs lg:text-sm text-primary-600 mt-1"><?= htmlspecialchars($policy['description']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </label>

                                    <button type="button" onclick="previewPolicy(<?= $policy['id'] ?>)"
                                        class="mt-2 text-xs lg:text-sm text-accent hover:text-accent-dark font-semibold flex items-center gap-1">
                                        <i data-lucide="eye" class="w-3 h-3 lg:w-4 lg:h-4"></i>
                                        Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Policy Modal -->
<div id="create-policy-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-primary-100">
        <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50 rounded-t-2xl">
            <h3 class="text-base lg:text-lg font-bold text-primary-700">Thêm chính sách mới</h3>
        </div>

        <form id="create-policy-form" class="p-4 lg:p-6" onsubmit="saveNewPolicy(event); return false;">
            <div class="space-y-3 lg:space-y-4">
                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Tên chính sách <span
                            class="text-danger">*</span></label>
                    <input type="text" id="new-policy-name"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="VD: Chính sách hủy tour 7 ngày">
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Loại chính sách</label>
                    <select id="new-policy-type" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base">
                        <option value="">-- Chọn loại --</option>
                        <option value="cancellation">Chính sách hủy tour</option>
                        <option value="change">Chính sách đổi tour</option>
                        <option value="refund">Chính sách hoàn tiền</option>
                        <option value="booking">Chính sách đặt tour</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Mô tả</label>
                    <textarea id="new-policy-description" rows="2"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Mô tả ngắn..."></textarea>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-semibold text-primary-700 mb-1 lg:mb-2">Nội dung chi tiết <span
                            class="text-danger">*</span></label>
                    <textarea id="new-policy-content" rows="8"
                        class="w-full px-3 lg:px-4 py-2 lg:py-2.5 bg-primary-50 border border-primary-100 rounded-xl focus:ring-2 focus:ring-accent focus:border-accent outline-none transition-all text-primary-700 text-sm lg:text-base"
                        placeholder="Nhập nội dung chi tiết của chính sách..."></textarea>
                    <p class="text-xs text-primary-500 mt-1">Bạn có thể sử dụng rich text editor để format nội dung</p>
                </div>
            </div>

            <div class="mt-4 lg:mt-6 flex flex-col sm:flex-row justify-end gap-2 lg:gap-3">
                <button type="button" onclick="closeCreatePolicyModal()"
                    class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-primary-50 text-primary-700 border border-primary-100 rounded-xl hover:bg-primary-100 font-semibold transition-colors text-sm lg:text-base">
                    Hủy
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                    Tạo chính sách
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Policy Modal -->
<div id="preview-policy-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-panel rounded-2xl shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-primary-100">
        <div class="p-4 lg:p-6 border-b border-primary-100 bg-primary-50 rounded-t-2xl">
            <h3 id="preview-policy-title" class="text-base lg:text-lg font-bold text-primary-700"></h3>
        </div>
        <div class="p-4 lg:p-6">
            <div id="preview-policy-content" class="prose max-w-none text-primary-700 text-sm lg:text-base"></div>
        </div>
        <div class="p-4 lg:p-6 border-t border-primary-100 flex justify-end">
            <button type="button" onclick="closePreviewPolicyModal()"
                class="px-4 lg:px-6 py-2 lg:py-2.5 bg-gradient-to-r from-accent-gradient-from to-accent-gradient-to hover:opacity-90 text-white rounded-xl font-semibold shadow-sm transition-all text-sm lg:text-base">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize selectedPolicies from PHP data
    // Sử dụng window để có thể truy cập từ mọi nơi
    window.selectedPolicies = <?= json_encode(array_flip($selected_policy_ids)) ?>;
    let selectedPolicies = window.selectedPolicies; // Alias for backward compatibility

    // Restore policy IDs from session if available (from create.php)
    // Note: This will be handled by restorePolicyIdsFromSession() in create.php
    // We just need to make sure selectedPolicyIds is available globally
    
    console.log('📋 Initialized selectedPolicies:', window.selectedPolicies);

    function togglePolicy(checkbox, policyId, policyName, policyType) {
        // Đảm bảo sử dụng window.selectedPolicies
        if (!window.selectedPolicies) {
            window.selectedPolicies = {};
        }
        
        if (checkbox.checked) {
            if (!window.selectedPolicies[policyId]) {
                window.selectedPolicies[policyId] = true;
                selectedPolicies = window.selectedPolicies; // Sync alias
                addPolicyToList(policyId, policyName, policyType);
            }
        } else {
            delete window.selectedPolicies[policyId];
            selectedPolicies = window.selectedPolicies; // Sync alias
            removePolicyFromList(policyId);
        }

        // Lưu vào session khi thay đổi
        savePolicyIdsToSession();
    }

    function addPolicyToList(policyId, policyName, policyType) {
        const container = document.getElementById('selected-policies-list');

        if (container.innerHTML.includes('Chưa chọn chính sách')) {
            container.innerHTML = '';
        }

        const policyHtml = `
        <div class="selected-policy-item bg-success-bg border border-success rounded-xl p-3 lg:p-4 flex items-center justify-between" data-policy-id="${policyId}">
            <div>
                <span class="font-semibold text-success-dark text-sm lg:text-base">${escapeHtml(policyName)}</span>
                <span class="text-xs lg:text-sm text-success-text ml-2">(${escapeHtml(policyType)})</span>
            </div>
            <button type="button" 
                    onclick="removePolicy(${policyId})"
                    class="text-danger hover:text-danger-dark flex items-center justify-center w-6 h-6 lg:w-8 lg:h-8 rounded-lg hover:bg-danger-bg transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
            <input type="hidden" name="policy_ids[]" value="${policyId}">
        </div>
    `;

        container.insertAdjacentHTML('beforeend', policyHtml);
    }

    function removePolicy(policyId) {
        console.log('🗑️ Removing policy:', policyId);
        
        // Đảm bảo sử dụng window.selectedPolicies
        if (!window.selectedPolicies) {
            window.selectedPolicies = {};
        }
        
        // Xóa khỏi selectedPolicies object
        delete window.selectedPolicies[policyId];
        selectedPolicies = window.selectedPolicies; // Sync alias

        // Uncheck checkbox
        const checkbox = document.getElementById(`policy-${policyId}`);
        if (checkbox) {
            checkbox.checked = false;
            console.log('✅ Checkbox unchecked for policy:', policyId);
        } else {
            console.warn('⚠️ Checkbox not found for policy:', policyId);
        }

        // Remove from list (sẽ xóa cả hidden input)
        removePolicyFromList(policyId);

        // Lưu vào session khi thay đổi
        savePolicyIdsToSession();
        
        console.log('✅ Policy removed and session saved');
    }

    function removePolicyFromList(policyId) {
        // Tìm phần tử bằng data-policy-id
        const item = document.querySelector(`.selected-policy-item[data-policy-id="${policyId}"]`);
        if (item) {
            // Xóa phần tử (sẽ xóa cả hidden input bên trong)
            item.remove();
            console.log('✅ Policy item removed from DOM:', policyId);
        } else {
            console.warn('⚠️ Policy item not found in DOM:', policyId);
            // Fallback: Tìm bằng hidden input
            const hiddenInputs = document.querySelectorAll(`[name="policy_ids[]"]`);
            hiddenInputs.forEach(input => {
                if (parseInt(input.value) === parseInt(policyId)) {
                    input.closest('.selected-policy-item')?.remove();
                    console.log('✅ Policy item removed via fallback');
                }
            });
        }

        // Show empty message if no policies left
        const container = document.getElementById('selected-policies-list');
        if (container && container.children.length === 0) {
            container.innerHTML = `
            <div class="text-primary-500 text-center py-6 lg:py-8 bg-primary-50 rounded-2xl border-2 border-dashed border-primary-200">
                <div class="flex justify-center mb-2 lg:mb-3">
                    <i data-lucide="file-text" class="w-8 h-8 lg:w-12 lg:h-12 text-primary-300"></i>
                </div>
                <p class="text-xs lg:text-sm text-primary-600">Chưa chọn chính sách nào</p>
            </div>
        `;
        }
    }

    function openCreatePolicyModal() {
        document.getElementById('create-policy-modal').classList.remove('hidden');
    }

    function closeCreatePolicyModal() {
        document.getElementById('create-policy-modal').classList.add('hidden');
        document.getElementById('create-policy-form').reset();
    }

    function saveNewPolicy(event) {
        event.preventDefault();

        // Validate required fields manually (vì đã bỏ required attribute)
        const name = document.getElementById('new-policy-name').value.trim();
        const content = document.getElementById('new-policy-content').value.trim();

        if (!name) {
            if (typeof showToast === 'function') {
                showToast('Vui lòng nhập tên chính sách', 'warning');
            } else {
                alert('Vui lòng nhập tên chính sách');
            }
            document.getElementById('new-policy-name').focus();
            return false;
        }

        if (!content) {
            if (typeof showToast === 'function') {
                showToast('Vui lòng nhập nội dung chi tiết', 'warning');
            } else {
                alert('Vui lòng nhập nội dung chi tiết');
            }
            document.getElementById('new-policy-content').focus();
            return false;
        }

        const formData = {
            name: name,
            policy_type: document.getElementById('new-policy-type').value,
            description: document.getElementById('new-policy-description').value,
            content: content
        };

        // AJAX create policy
        fetch('?act=admin&module=tours&action=createPolicy', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(formData)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show new policy
                    location.reload();
                } else {
                    if (typeof showToast === 'function') {
                        showToast('Có lỗi: ' + (data.message || 'Không thể tạo chính sách'), 'error');
                    } else {
                        alert('Có lỗi: ' + (data.message || 'Không thể tạo chính sách'));
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof showToast === 'function') {
                    showToast('Có lỗi xảy ra khi tạo chính sách', 'error');
                } else {
                    alert('Có lỗi xảy ra khi tạo chính sách');
                }
            });
    }

    function previewPolicy(policyId) {
        // Load policy content via AJAX
        fetch(`?act=admin&module=tours&action=getPolicy&id=${policyId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preview-policy-title').textContent = data.data.name;
                    document.getElementById('preview-policy-content').innerHTML = data.data.content;
                    document.getElementById('preview-policy-modal').classList.remove('hidden');
                }
            });
    }

    function closePreviewPolicyModal() {
        document.getElementById('preview-policy-modal').classList.add('hidden');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Lưu policy IDs vào session
    function savePolicyIdsToSession() {
        // Collect policy IDs từ checkboxes và hidden inputs
        const policyIds = [];

        // Lấy từ checkboxes (ưu tiên)
        document.querySelectorAll('[id^="policy-"]:checked').forEach(cb => {
            const id = parseInt(cb.value);
            if (id && !policyIds.includes(id)) {
                policyIds.push(id);
            }
        });

        // Lấy từ hidden inputs (backup - nếu checkbox chưa được check nhưng đã có trong list)
        document.querySelectorAll('[name="policy_ids[]"]').forEach(input => {
            const id = parseInt(input.value);
            if (id && !policyIds.includes(id)) {
                policyIds.push(id);
            }
        });

        // Cập nhật selectedPolicies object để đồng bộ
        window.selectedPolicies = {};
        policyIds.forEach(id => {
            window.selectedPolicies[id] = true;
        });

        // Gọi saveFormDataToSession nếu function tồn tại (từ create.php)
        if (typeof saveFormDataToSession === 'function') {
            saveFormDataToSession({
                policy_ids: policyIds
            });
            console.log('✅ Policy IDs saved to session:', policyIds);
        } else {
            console.warn('⚠️ saveFormDataToSession function not found');
        }
    }
</script>