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
    <div class="mb-4 flex justify-between items-center">
        <h4 class="text-lg font-semibold text-gray-800">Chọn chính sách</h4>
        <button type="button" onclick="openCreatePolicyModal()"
            class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors text-sm">
            <i class="fas fa-plus mr-2"></i>Thêm chính sách mới
        </button>
    </div>

    <!-- Selected Policies -->
    <div id="selected-policies-list" class="mb-6">
        <?php if (empty($selected_policy_ids)): ?>
            <div class="text-gray-500 text-center py-4 bg-gray-50 rounded-lg border-2 border-dashed">
                <i class="fas fa-file-contract text-2xl mb-2"></i>
                <p class="text-sm">Chưa chọn chính sách nào</p>
            </div>
        <?php else: ?>
            <div class="space-y-2">
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
                            class="selected-policy-item bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-green-900"><?= htmlspecialchars($policy['name']) ?></span>
                                <span class="text-sm text-green-700 ml-2">
                                    (<?= $policy_type_names[$policy['policy_type'] ?? 'other'] ?? 'Khác' ?>)
                                </span>
                            </div>
                            <button type="button" onclick="removePolicy(<?= $policy_id ?>)" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="hidden" name="policy_ids[]" value="<?= $policy_id ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Available Policies by Type -->
    <div class="space-y-6">
        <?php foreach ($policies_by_type as $type => $type_policies): ?>
            <div class="policy-type-group">
                <h5 class="font-semibold text-gray-700 mb-3">
                    <?= $policy_type_names[$type] ?? 'Chính sách khác' ?>
                </h5>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($type_policies as $policy): ?>
                        <?php $is_selected = in_array($policy['id'], $selected_policy_ids); ?>
                        <div
                            class="policy-card border rounded-lg p-4 <?= $is_selected ? 'bg-blue-50 border-blue-300' : 'bg-white border-gray-200 hover:border-blue-300' ?> transition-colors">
                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="policy-<?= $policy['id'] ?>" value="<?= $policy['id'] ?>"
                                    <?= $is_selected ? 'checked' : '' ?>
                                    onchange="togglePolicy(this, <?= $policy['id'] ?>, '<?= htmlspecialchars($policy['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($policy_type_names[$type] ?? 'Khác', ENT_QUOTES) ?>')"
                                    class="mt-1 w-5 h-5 text-blue-600 rounded">
                                <div class="flex-1">
                                    <label for="policy-<?= $policy['id'] ?>" class="cursor-pointer">
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($policy['name']) ?></div>
                                        <?php if (!empty($policy['description'])): ?>
                                            <div class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($policy['description']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </label>

                                    <button type="button" onclick="previewPolicy(<?= $policy['id'] ?>)"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>Xem chi tiết
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
<div id="create-policy-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 class="text-lg font-bold text-gray-800">Thêm chính sách mới</h3>
        </div>

        <form id="create-policy-form" class="p-6" onsubmit="saveNewPolicy(event); return false;">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên chính sách <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="new-policy-name"
                        class="w-full px-3 py-2 border rounded focus:border-purple-500"
                        placeholder="VD: Chính sách hủy tour 7 ngày">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Loại chính sách</label>
                    <select id="new-policy-type" class="w-full px-3 py-2 border rounded focus:border-purple-500">
                        <option value="">-- Chọn loại --</option>
                        <option value="cancellation">Chính sách hủy tour</option>
                        <option value="change">Chính sách đổi tour</option>
                        <option value="refund">Chính sách hoàn tiền</option>
                        <option value="booking">Chính sách đặt tour</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea id="new-policy-description" rows="2"
                        class="w-full px-3 py-2 border rounded focus:border-purple-500"
                        placeholder="Mô tả ngắn..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung chi tiết <span
                            class="text-red-500">*</span></label>
                    <textarea id="new-policy-content" rows="8"
                        class="w-full px-3 py-2 border rounded focus:border-purple-500"
                        placeholder="Nhập nội dung chi tiết của chính sách..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Bạn có thể sử dụng rich text editor để format nội dung</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeCreatePolicyModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                    Tạo chính sách
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Policy Modal -->
<div id="preview-policy-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <h3 id="preview-policy-title" class="text-lg font-bold text-gray-800"></h3>
        </div>
        <div class="p-6">
            <div id="preview-policy-content" class="prose max-w-none"></div>
        </div>
        <div class="p-6 border-t flex justify-end">
            <button type="button" onclick="closePreviewPolicyModal()"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
    // Initialize selectedPolicies from PHP data
    let selectedPolicies = <?= json_encode(array_flip($selected_policy_ids)) ?>;

    // Restore policy IDs from session if available (from create.php)
    // Note: This will be handled by restorePolicyIdsFromSession() in create.php
    // We just need to make sure selectedPolicyIds is available globally

    function togglePolicy(checkbox, policyId, policyName, policyType) {
        if (checkbox.checked) {
            if (!selectedPolicies[policyId]) {
                selectedPolicies[policyId] = true;
                addPolicyToList(policyId, policyName, policyType);
            }
        } else {
            delete selectedPolicies[policyId];
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
        <div class="selected-policy-item bg-green-50 border border-green-200 rounded-lg p-3 flex items-center justify-between" data-policy-id="${policyId}">
            <div>
                <span class="font-medium text-green-900">${escapeHtml(policyName)}</span>
                <span class="text-sm text-green-700 ml-2">(${escapeHtml(policyType)})</span>
            </div>
            <button type="button" 
                    onclick="removePolicy(${policyId})"
                    class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
            <input type="hidden" name="policy_ids[]" value="${policyId}">
        </div>
    `;

        container.insertAdjacentHTML('beforeend', policyHtml);
    }

    function removePolicy(policyId) {
        delete selectedPolicies[policyId];

        // Uncheck checkbox
        const checkbox = document.getElementById(`policy-${policyId}`);
        if (checkbox) {
            checkbox.checked = false;
        }

        // Remove from list
        removePolicyFromList(policyId);

        // Lưu vào session khi thay đổi
        savePolicyIdsToSession();
    }

    function removePolicyFromList(policyId) {
        const item = document.querySelector(`.selected-policy-item[data-policy-id="${policyId}"]`);
        if (item) {
            item.remove();
        }

        // Show empty message if no policies left
        const container = document.getElementById('selected-policies-list');
        if (container.children.length === 0) {
            container.innerHTML = `
            <div class="text-gray-500 text-center py-4 bg-gray-50 rounded-lg border-2 border-dashed">
                <i class="fas fa-file-contract text-2xl mb-2"></i>
                <p class="text-sm">Chưa chọn chính sách nào</p>
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
            alert('Vui lòng nhập tên chính sách');
            document.getElementById('new-policy-name').focus();
            return false;
        }

        if (!content) {
            alert('Vui lòng nhập nội dung chi tiết');
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
                    alert('Có lỗi: ' + (data.message || 'Không thể tạo chính sách'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra khi tạo chính sách');
            });
    }

    function previewPolicy(policyId) {
        // Load policy content via AJAX
        fetch(`?act=admin&module=tours&action=getPolicy&id=${policyId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preview-policy-title').textContent = data.data.name;
                    document.getElementById('preview-policy-content').textContent = data.data.content;
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

        // Lấy từ checkboxes
        document.querySelectorAll('[id^="policy-"]:checked').forEach(cb => {
            const id = parseInt(cb.value);
            if (id && !policyIds.includes(id)) {
                policyIds.push(id);
            }
        });

        // Lấy từ hidden inputs (đã được thêm vào list)
        document.querySelectorAll('[name="policy_ids[]"]').forEach(input => {
            const id = parseInt(input.value);
            if (id && !policyIds.includes(id)) {
                policyIds.push(id);
            }
        });

        // Gọi saveFormDataToSession nếu function tồn tại (từ create.php)
        if (typeof saveFormDataToSession === 'function') {
            saveFormDataToSession({
                policy_ids: policyIds
            });
            console.log('✅ Policy IDs saved to session:', policyIds);
        }
    }
</script>