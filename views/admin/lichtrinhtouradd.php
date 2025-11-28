<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lịch Trình Tour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* Nút Xem Chi tiết Tour (Thường là hành động chính cuối cùng) */
        .btn-view-detail {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
            font-weight: 600;
        }
        .btn-view-detail:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
            color: white;
        }

        /* Nút Quay lại/Hành động Phụ (Quay lại Tour List) */
        .btn-return-list {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #495057;
            font-weight: 600;
        }
        .btn-return-list:hover {
            background-color: #e9ecef;
            color: #212529;
            border-color: #adb5bd;
        }

        /* Nút Tiếp theo (Bước tiếp theo trong quy trình) */
        .btn-next-step {
            background-color: #198754; /* Xanh lá */
            border-color: #198754;
            color: white;
            font-weight: 600;
        }
        .btn-next-step:hover {
            background-color: #157347;
            border-color: #146c43;
        }
    </style>
</head>
<body class="bg-light">

    <?php include './views/parts/sidebar.php'; ?>
    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid py-4">

            <?php 
                // Giả định $tour chứa thông tin cơ bản của tour
                $tour_id = htmlspecialchars($tour['tour_id'] ?? 2); 
            ?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 text-primary">
                    <i class="bi bi-calendar-check me-2"></i> Quản Lý Lịch Trình Tour
                </h2>
                <div>
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id ?>" class="btn btn-view-detail me-2 shadow-sm">
                        <i class="bi bi-eye"></i> Xem Chi tiết Tour
                    </a>
                    <a href="<?= BASEURL ?>?act=tour_list" class="btn btn-return-list shadow-sm">
                        <i class="bi bi-arrow-left-circle"></i> Về Danh sách Tour
                    </a>
                </div>
            </div>
            
            <h4 class="text-muted mb-4">Tour: #<?= $tour_id ?> - **<?= htmlspecialchars($tour['ten'] ?? 'N/A') ?>**</h4>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📋 Danh Sách Lịch Trình Hiện Tại</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle bg-white shadow-sm">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 10%;">Ngày Thứ</th>
                                    <th style="width: 25%;">Tiêu Đề</th>
                                    <th style="width: 45%;">Nội Dung</th>
                                    <th style="width: 15%;">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Giả định $data là danh sách lịch trình đã được Model trả về
                                if (!empty($data)): 
                                ?>
                                    <?php foreach ($data as $index => $lichTrinh): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td class="text-center">Ngày **<?= htmlspecialchars($lichTrinh['ngay_thu']) ?>**</td>
                                            <td>**<?= htmlspecialchars($lichTrinh['tieu_de']) ?>**</td>
                                            <td><?= nl2br(htmlspecialchars($lichTrinh['noi_dung'])) ?></td>
                                            <td class="text-center">
                                                <div class="d-grid gap-2">
                                                    <a href="<?= BASEURL ?>?act=editlichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil-square"></i> Sửa
                                                    </a>
                                                    <a href="<?= BASEURL ?>?act=deletelichtrinh&lich_trinh_id=<?= $lichTrinh['lich_trinh_id'] ?>&tour_id=<?= $lichTrinh['tour_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xoá lịch trình này?')">
                                                        <i class="bi bi-trash"></i> Xoá
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Chưa có lịch trình tour nào. Hãy thêm mới bên dưới.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">➕ Thêm Lịch Trình Mới</h5>
                </div>
                <div class="card-body">
                    <form action="<?= BASEURL ?>?act=createlichtrinh&tour_id=<?= $tour_id ?>" method="POST">
                        
                        <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Ngày Thứ <span class="text-danger">*</span></label>
                                <input type="number" name="ngay_thu" class="form-control" placeholder="VD: 1, 2, 3..." required min="1">
                            </div>
                            <div class="col-md-9 mb-3">
                                <label class="form-label fw-bold">Tiêu Đề <span class="text-danger">*</span></label>
                                <input type="text" name="tieu_de" class="form-control" placeholder="Nhập tiêu đề lịch trình (VD: Tham quan Vịnh Hạ Long)" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội Dung <span class="text-danger">*</span></label>
                            <textarea name="noi_dung" class="form-control" rows="4" placeholder="Nhập chi tiết hoạt động trong ngày (VD: 8h sáng: Ăn sáng, 9h: Khởi hành đi...) " required></textarea>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-success btn-lg shadow-sm">
                                <i class="bi bi-save"></i> Lưu Lịch Trình
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                
                <a href="<?= BASEURL ?>?act=gan_diadiem&tour_id=<?= $tour_id ?>" 
                   class="btn btn-return-list btn-lg">
                    <i class="bi bi-chevron-left me-2"></i> Quay lại: Gán Địa điểm
                </a>
                
                <div class="d-flex gap-2">
                    <a href="<?= BASEURL ?>?act=chitiettour&tour_id=<?= $tour_id ?>" 
                       class="btn btn-view-detail btn-lg">
                        <i class="bi bi-eye me-2"></i> BỎ QUA & Về Xem Chi tiết Tour
                    </a>
                    
                    <a href="<?= BASEURL ?>?act=chinhsach&tour_id=<?= $tour_id ?>" 
                       class="btn btn-next-step btn-lg">
                        <i class="bi bi-chevron-right me-2"></i> Tiếp theo: Gắn Chính sách
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>