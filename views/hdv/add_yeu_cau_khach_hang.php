<style>
    /* Gradient đẹp cho tiêu đề */
    #modalRequest .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0dcaf0);
        color: white;
        border-bottom: 0;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    #modalRequest .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    #modalRequest .modal-title {
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* Tinh chỉnh form input */
    #modalRequest .form-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 5px;
    }

    #modalRequest .form-control,
    #modalRequest .form-select {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    #modalRequest .form-control:focus,
    #modalRequest .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }

    /* Style cho Switch Toggle */
    #modalRequest .form-check-input {
        cursor: pointer;
        width: 3em;
        height: 1.5em;
    }

    #modalRequest .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    #modalRequest .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }
</style>

<div class="modal fade" id="modalRequest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form action="<?= BASEURL ?>?act=luu_yeu_cau" method="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bi bi-journal-plus me-2"></i>Thêm Yêu Cầu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="yeu_cau_id" id="inpId" value="0">
                    <input type="hidden" name="hanh_khach_id" value="<?= $khachhang['hanh_khach_id'] ?>">
                    <input type="hidden" name="dat_tour_id" value="<?= $khachhang['dat_tour_id'] ?>">

                    <div class="mb-4">
                        <label class="form-label fw-bold">Nội dung chi tiết <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="noi_dung" id="txtContent" rows="3" required placeholder="Nhập yêu cầu cụ thể (VD: Khách ăn chay, dị ứng tôm)..."></textarea>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold">Mức độ ưu tiên</label>
                            <select class="form-select" name="muc_do_uu_tien" id="selPriority">
                                <option value="thap"> Thấp</option>
                                <option value="trung_binh" selected> Trung bình</option>
                                <option value="cao"> Cao</option>
                                <option value="khan_cap">Khẩn cấp</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-bold">Trạng thái xử lý</label>
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" name="da_chuan_bi" id="chkPrepared" value="1">
                                <label class="form-check-label ms-2 mt-1" for="chkPrepared">Đã hoàn tất</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Ghi chú nội bộ (HDV)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-pencil-square text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" name="ghi_chu" id="inpNote" placeholder="Ghi chú thêm...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-save me-1"></i> Lưu lại
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>