<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Phân công Hướng dẫn viên</h1>
            <p class="text-sm text-gray-500 mt-1">Quản lý lịch trình và phân công nhân sự cho các tour sắp tới</p>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h6 class="font-semibold text-slate-700">Lịch khởi hành sắp tới</h6>
            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border border-gray-200">
                <?= !empty($schedules) ? count($schedules) : 0 ?> lịch chờ
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-white border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                        <th class="px-6 py-4">Mã Lịch</th>
                        <th class="px-6 py-4 w-1/3">Thông tin Tour</th>
                        <th class="px-6 py-4">Khởi hành</th>
                        <th class="px-6 py-4">Số khách</th>
                        <th class="px-6 py-4">HDV Phân công</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                        SCH-<?= $schedule['id'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800 mb-1"><?= htmlspecialchars($schedule['tour_name']) ?>
                                    </div>
                                    <div class="text-xs text-gray-500">Mã tour:
                                        <?= htmlspecialchars($schedule['tour_code'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                        <?= date('d/m/Y', strtotime($schedule['start_date'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $percent = ($schedule['booked'] / $schedule['quota']) * 100;
                                    $color = $percent >= 100 ? 'bg-red-100 text-red-700' : ($percent >= 75 ? 'bg-amber-100 text-amber-700' : 'bg-blue-50 text-blue-700');
                                    ?>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color ?>">
                                        <?= $schedule['booked'] ?>/<?= $schedule['quota'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    // Get assigned guides
                                    $assignmentModel = new TourAssignment($this->db);
                                    $assigned = $assignmentModel->getBySchedule($schedule['id']);
                                    if (!empty($assigned)) {
                                        foreach ($assigned as $guide) {
                                            echo '<div class="flex items-center gap-2 mb-1">';
                                            echo '<div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold">' . strtoupper(substr($guide['full_name'], 0, 1)) . '</div>';
                                            echo '<span class="text-sm text-gray-700">' . htmlspecialchars($guide['full_name']) . '</span>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<span class="text-xs text-gray-400 italic">Chưa phân công</span>';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                        onclick="openAssignModal(<?= $schedule['id'] ?>, '<?= htmlspecialchars($schedule['tour_name']) ?>', '<?= date('d/m/Y', strtotime($schedule['start_date'])) ?>')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <i class="fas fa-user-plus mr-1.5"></i> Phân công
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                                    <p>Không có lịch khởi hành sắp tới nào cần phân công.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Modal (Custom Tailwind) -->
<div id="assignModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
            onclick="closeAssignModal()"></div>

        <!-- Modal panel -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <form action="?act=admin&module=assignments&action=assign" method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-user-plus text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Phân công Hướng dẫn viên
                            </h3>
                            <div class="mt-4 space-y-4">
                                <input type="hidden" name="schedule_id" id="modal_schedule_id">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tour</label>
                                    <div id="modal_tour_name"
                                        class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-800">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày khởi hành</label>
                                    <div
                                        class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-sm text-gray-800 flex items-center gap-2">
                                        <i class="far fa-calendar text-gray-400"></i>
                                        <span id="modal_start_date"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn Hướng dẫn viên
                                        <span class="text-red-500">*</span></label>
                                    <select name="guide_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm"
                                        required>
                                        <option value="">-- Chọn HDV --</option>
                                        <?php foreach ($guides['data'] ?? [] as $guide): ?>
                                            <option value="<?= $guide['id'] ?>">
                                                <?= htmlspecialchars($guide['full_name']) ?> - <?= $guide['phone'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Lưu phân công
                    </button>
                    <button type="button" onclick="closeAssignModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAssignModal(id, tourName, startDate) {
        document.getElementById('modal_schedule_id').value = id;
        document.getElementById('modal_tour_name').textContent = tourName;
        document.getElementById('modal_start_date').textContent = startDate;
        document.getElementById('assignModal').classList.remove('hidden');
    }

    function closeAssignModal() {
        document.getElementById('assignModal').classList.add('hidden');
    }
</script>