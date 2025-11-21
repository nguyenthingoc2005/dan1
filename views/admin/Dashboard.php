<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Quản Trị Hệ Thống</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css">
    
    <link rel="stylesheet" href="./assets/css/sidebar.css">
    
    <style>
        /* CSS Cơ bản cho Dashboard */
        body { 
            background-color: #f8f9fa;
        }
        /* QUAN TRỌNG: Điều chỉnh margin-top cho nội dung chính */
        .main-content {
            padding: 20px;
            margin-top: 70px; /* Bù trừ chiều cao NAV cố định */
            margin-left: 0; 
            transition: margin-left .32s ease;
        }
        /* Giữ lại style thẻ thống kê */
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
        
        /* Cấu trúc NAV (giống như trang Danh sách Tour hoạt động) */
        nav {
            position: fixed; top: 0; left: 0; height: 70px; width: 100%;
            display: flex; align-items: center; background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,.08); z-index: 1002;
        }
        nav .logo { display: flex; align-items: center; margin: 0 20px; }
        .logo .menu-icon { color: #333; font-size: 24px; margin-right: 12px; cursor: pointer; }
        .logo .logo-name { color: #333; font-size: 20px; font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    
    <?php include './views/parts/sidebar.php'; ?> 

    <div class="overlay"></div>

    <div class="main-content">
        <div class="container-fluid">
            
            <h2 class="mb-4 text-dark fw-bold border-bottom pb-2">🚀 Tổng quan Hệ thống</h2>

            <div class="row g-4 mb-5">
                
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase text-muted small">Tổng Tour</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h2 mb-0 fw-bold text-primary">125</span>
                                <i class="bi bi-map h1 text-primary-emphasis opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase text-muted small">Doanh thu Tháng</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h2 mb-0 fw-bold text-success">2.5 Tỷ</span>
                                <i class="bi bi-currency-dollar h1 text-success-emphasis opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase text-muted small">Đơn hàng chờ xử lý</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h2 mb-0 fw-bold text-warning">18</span>
                                <i class="bi bi-bell h1 text-warning-emphasis opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card stat-card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-uppercase text-muted small">Khách hàng mới</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="h2 mb-0 fw-bold text-info">87</span>
                                <i class="bi bi-people h1 text-info-emphasis opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">Hoạt động gần đây</h5>
                </div>
                <div class="card-body">
                    <p>Đây là khu vực hiển thị dữ liệu chi tiết hoặc biểu đồ.</p>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebar.js"></script>
</body>
</html>