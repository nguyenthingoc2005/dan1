<?php
class AdminController
{
    public $modelGet;
    public $modelCreate;
    public $modelDelete;
    public $modelUpdate;
    public $modelRole;

    public function __construct()
    {
        // Giả định các class Data Module đã được định nghĩa và kết nối DB
        $this->modelGet = new getDataModule();
        $this->modelCreate = new creatDataModule();
        $this->modelDelete = new deleteDataModule();
        $this->modelUpdate = new uppDateDataModuleDataModule();
        $this->modelRole = new getDataModule();
    }

    // ===================================================================
    // I. TOUR
    // ===================================================================

 public function index()
    {
        // 1. LẤY DỮ LIỆU TỪ MODEL
        // Lưu ý: Đảm bảo modelGet đã được khởi tạo trong __construct
        $allBookings  = $this->modelGet->getAllDatTour();   // Lấy list đơn đặt
        $allTours     = $this->modelGet->getAllTours();     // Lấy list tour
        $allCustomers = $this->modelGet->getAllKhachHang(); // Lấy list khách hàng

        // 2. KHỞI TẠO BIẾN THỐNG KÊ
        $stats = [
            'revenue'      => 0, // Doanh thu
            'bookings'     => count($allBookings), // Tổng đơn
            'active_tours' => 0, // Tour đang hoạt động
            'customers'    => count($allCustomers) // Tổng khách
        ];

        // 3. TÍNH TOÁN SỐ LIỆU
        
        // A. Đếm Tour đang hoạt động (hoat_dong = 1)
        foreach ($allTours as $tour) {
            if (isset($tour['hoat_dong']) && $tour['hoat_dong'] == 1) {
                $stats['active_tours']++;
            }
        }

        // B. Tính Doanh Thu & Lọc Tour Sắp Khởi Hành
        $upcomingTours = [];
        $today = date('Y-m-d'); // Ngày hiện tại

        foreach ($allBookings as $bk) {
            // --- Tính Doanh Thu (Ước tính: Giá cơ bản * Số người) ---
            // Chỉ cộng tiền các đơn đã 'hoàn tất', 'thành công' hoặc 'đã đặt cọc'
            $status = strtolower($bk['trang_thai_dat_tour'] ?? '');
            if ($status == 'hoàn tất' || $status == 'completed' || $status == 'success' || $status == 'đã xác nhận') {
                // Lấy giá cơ bản từ bảng Tour (trong hàm getAllDatTour bạn đã join bảng Tour chưa?)
                // Giả định trường giá là 'gia_co_ban' và số người là 'so_nguoi'
                $price = $bk['gia_co_ban'] ?? 0; // Cần đảm bảo Model trả về trường này
                $pax   = $bk['so_nguoi'] ?? 0;
                $stats['revenue'] += ($price * $pax);
            }

            // --- Lọc Tour Sắp Khởi Hành (Lớn hơn ngày hiện tại) ---
            if (!empty($bk['ngay_bat_dau']) && $bk['ngay_bat_dau'] >= $today) {
                $upcomingTours[] = $bk;
            }
        }

        // 4. SẮP XẾP & CẮT DỮ LIỆU HIỂN THỊ
        
        // A. 5 Đơn hàng mới nhất (Sắp xếp theo ID giảm dần)
        usort($allBookings, function($a, $b) {
            return $b['dat_tour_id'] <=> $a['dat_tour_id'];
        });
        $recentBookings = array_slice($allBookings, 0, 5);

        // B. 5 Tour sắp khởi hành (Sắp xếp ngày tăng dần - gần nhất lên đầu)
        usort($upcomingTours, function($a, $b) {
            return strtotime($a['ngay_bat_dau']) - strtotime($b['ngay_bat_dau']);
        });
        $upcomingTours = array_slice($upcomingTours, 0, 5);

        // 5. GỌI VIEW
        require_once './views/admin/Dashboard.php';
    }
    public function showTours()
    {
        $data1 = $this->modelGet->getAllTours();
        require_once './views/admin/tourlist.php';
    }
    public function formaddtour()
    {
        $data = $this->modelGet->getAllDanh_muc_tour();
        require_once './views/admin/tourcreat.php';
    }
    public function getAggregatedTourData($id)
    {
        $tourDetail = $this->modelGet->getAggregatedTourDetail($id);
        include './views/admin/chitiettour.php';
    }
    public function createtour()
    {
        // 1. Thu thập dữ liệu từ $_POST vào mảng $data
        $data = [
            'ten' => $_POST['ten'],
            'danh_muc_id' => $_POST['danh_muc_id'],
            'mo_ta_ngan' => $_POST['mo_ta_ngan'],
            'mo_ta' => $_POST['mo_ta'],
            'gia_co_ban' => $_POST['gia_co_ban'],
            'thoi_luong_mac_dinh' => $_POST['thoi_luong_mac_dinh'],
            'diem_khoi_hanh' => $_POST['diem_khoi_hanh'],
            'hoat_dong' => $_POST['hoat_dong'],
            // BỔ SUNG: 'nguoi_tao_id' nếu cần
        ];

        // 2. Gọi Model và BẮT LẤY ID TRẢ VỀ
        $newTourId = $this->modelCreate->createTour($data);

        // 3. Kiểm tra kết quả và Chuyển hướng
        if ($newTourId) {
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $newTourId);
            exit();
        } else {
            echo "Lỗi: Không thể lưu thông tin tour vào cơ sở dữ liệu.";
            exit();
        }
    }
    public function deletetour($tour_id)
    {
        if ($tour_id !== null) {
            $this->modelDelete->deleteTour($tour_id);
        }
        header('Location: ' . BASEURL . '?act=tour_list');
    }
    public function uppdatetour($tour_id)
    {
        $data = $this->modelGet->getAllDanh_muc_tour();
        $tour = $this->modelGet->getTourById($tour_id);
        require_once './views/admin/tourupdate.php';

    }
    public function uppdatetour1($tour_id)
    {
        $data = [
            'ten' => $_POST['ten'] ?? '',
            'danh_muc_id' => $_POST['danh_muc_id'] ?? '',
            'mo_ta_ngan' => $_POST['mo_ta_ngan'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'gia_co_ban' => $_POST['gia_co_ban'] ?? 0,
            'thoi_luong_mac_dinh' => $_POST['thoi_luong_mac_dinh'] ?? 0,
            'diem_khoi_hanh' => $_POST['diem_khoi_hanh'] ?? '',
            'hoat_dong' => $_POST['hoat_dong'] ?? 0
        ];

        $this->modelUpdate->uppDateTour($tour_id, $data);
        header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id);
    }

    // ===================================================================
    // II. ĐỊA ĐIỂM
    // ===================================================================

    public function diadiem($tour_id)
    {
        $diadiemList = $this->modelGet->getDiaDiemByTourId($tour_id);
        require_once './views/admin/diadiemtour.php';
    }
  public function gan_diadiem($tour_id)
{
    $datatour = $this->modelGet->getTourById($tour_id);

    if (isset($datatour['danh_muc_id']) && $datatour['danh_muc_id'] == 1) {
        $data = $this->modelGet->getAllDiaDiemtn(); // Lấy data Trong nước
    } else {
        $data = $this->modelGet->getAllDiaDiemqt(); // Lấy data Quốc tế (hoặc các loại khác)
    }

    // 3. Lấy các địa điểm đã gán (dùng để hiển thị và loại trừ khỏi danh sách chọn)
    $diaDiemDaGan = $this->modelGet->getDiaDiemByTourId($tour_id);
    
    // 4. Chuẩn bị View
    require_once './views/admin/gan_diadiemtour.php';
}
    public function luu_gan_diadiem($tour_id)
    {
        $diaDiemIds = $_POST['dia_diem_id'] ?? [];
        $ghiChu = $_POST['ghi_chu_rieng'] ?? [];

        if (!is_array($diaDiemIds) || empty($diaDiemIds)) {
            header('Location: ' . BASEURL . '?act=loi_chua_chon');
            exit;
        }

        $allSuccess = true;
        foreach ($diaDiemIds as $ddId) {
            $ghiChuRieng = $ghiChu[$ddId] ?? '';
            $result = $this->modelCreate->ganDiaDiemChoTour($tour_id, $ddId, $ghiChuRieng);
            if (!$result) {
                $allSuccess = false;
            }
        }

        if ($allSuccess) {
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id . '&msg=success');
        } else {
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id . '&error=insert_failed');
        }
        exit;
    }
    public function xoa_dia_diem_tour($dia_diem_tour_id, $tour_id)
    {
        if ($dia_diem_tour_id !== null) {
            $this->modelDelete->xoaDiaDiemKhoiTour($dia_diem_tour_id);
        }
        header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . ($tour_id ?? 0));
    }
  
    public function sua_diadiem_tour($dia_diem_tour_id, $tour_id)
    {
        $info = $this->modelGet->getDiaDiemTourById($dia_diem_tour_id);
        $data = $this->modelGet->getAllDiaDiem();
        require_once './views/admin/suadiadiemtour.php';
    }
    public function capnhat_diadiem_tour($dia_diem_tour_id)
    {
        $ghi_chu = $_POST['ghi_chu'] ?? null;
        $dia_diem_id = $_POST['dia_diem_id'] ?? 0;
        $tour_id = $_POST['tour_id'] ?? 0;

        $this->modelUpdate->capNhatDiaDiemTour($dia_diem_tour_id, $dia_diem_id, $ghi_chu);
        header('Location: ' . BASEURL . '?act=diadiem&tour_id=' . $tour_id);
    }

    // ===================================================================
    // III. NHÀ CUNG CẤP (NCC)
    // ===================================================================

    public function formaddncc()
    {
        require_once './views/admin/ncc_add.php';
    }

    public function createncc()
    {
        $data = [
            'ten' => $_POST['ten'] ?? '',
            'lien_he' => $_POST['lien_he'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'ma_so_thue' => $_POST['ma_so_thue'] ?? ''
        ];

        $this->modelCreate->createNCC($data);
        header("Location: " . BASEURL . "?act=ncc_list");
    }

    public function deletencc($id)
    {
        $this->modelDelete->deleteNCC($id);
        header("Location: " . BASEURL . "?act=ncc_list");
    }

    public function formupdatencc($id)
    {
        $ncc = $this->modelGet->getNCCById($id);
        require_once './views/admin/ncc_update.php';
    }

    public function updatencc($id)
    {
        $data = [
            'ten' => $_POST['ten'] ?? '',
            'lien_he' => $_POST['lien_he'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'ma_so_thue' => $_POST['ma_so_thue'] ?? ''
        ];

        $this->modelUpdate->updateNCC($id, $data);
        header("Location: " . BASEURL . "?act=ncc_list");
    }
    public function showncc()
    {
        $data = $this->modelGet->getAllNCC();
        require_once './views/admin/ncc_list.php';
    }

    // ===================================================================
    // IV. LỊCH TRÌNH
    // ===================================================================

    public function listlichtrinhtour()
    {
        $tour_id = $_GET['tour_id'] ?? null;
        $data = $this->modelGet->getAllLichTrinhTour($tour_id);
        require './views/admin/listlichtrinhtour.php';
    }

    public function formAddLichTrinh($tour_id)
    {
        $data = $this->modelGet->getAllLichTrinhTour($tour_id);
        $tour = $this->modelGet->getTourById($tour_id);
        require './views/admin/lichtrinhtouradd.php';
    }

    public function createLichTrinh()
    {
        $tour_id = $_POST['tour_id'] ?? 0;
        if (empty($tour_id)) {
            die('Lỗi: tour_id không được để trống!');
        }

        $data = [
            'tour_id' => $tour_id,
            'ngay_thu' => $_POST['ngay_thu'] ?? 1,
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'noi_dung' => $_POST['noi_dung'] ?? ''
        ];

        $this->modelCreate->createLichTrinh($data);
        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $tour_id);
    }

    public function editLichTrinh($lich_trinh_id)
    {
        $info = $this->modelGet->getLichTrinhById($lich_trinh_id);
        require './views/admin/editlichtrinhtour.php';
    }

    public function capnhatLichTrinh($lich_trinh_id)
    {
        $tour_id = $_POST['tour_id'] ?? 0;
        $data = [
            'ngay_thu' => $_POST['ngay_thu'] ?? 1,
            'tieu_de' => $_POST['tieu_de'] ?? '',
            'noi_dung' => $_POST['noi_dung'] ?? ''
        ];

        $this->modelUpdate->updateLichTrinh($lich_trinh_id, $data);
        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $tour_id);
    }

    public function deleteLichTrinh($lich_trinh_id)
    {
        $this->modelDelete->deleteLichTrinh($lich_trinh_id);
        $tour_id = $_GET['tour_id'] ?? 0;
        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $tour_id);
    }

    // ===================================================================
    // V. HƯỚNG DẪN VIÊN (HDV)
    // ===================================================================

    public function hdv()
    {
        $hdvList = $this->modelGet->getAllHDV();
        require_once './views/admin/hdvlist.php';
    }
    public function addHDV()
    {
        require_once './views/admin/hdvcreate.php';
    }
    public function createHDV()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ho_ten'        => $_POST['ho_ten'] ?? '',
                'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
                'email'         => $_POST['email'] ?? '',
                'kinh_nghiem'   => $_POST['kinh_nghiem'] ?? '',
                'ngon_ngu'      => $_POST['ngon_ngu'] ?? '',
                'nguoi_dung_id' => 3 // Hardcode tạm thời
            ];

            $result = $this->modelCreate->addHDV($data);

            if ($result) {
                header("Location: " . BASEURL . "?act=hdv&msg=success");
                exit;
            } else {
                echo "<pre>Lỗi SQL: ";
                print_r($this->modelCreate->conn->errorInfo());
                echo "</pre>";
                exit;
            }
        }
    }
    public function deleteHDV($id)
    {
        if ($id > 0) {
            $result = $this->modelDelete->deleteHDV($id);
            if ($result) {
                header("Location: " . BASEURL . "?act=hdv&msg=deleted");
                exit;
            } else {
                echo "<pre>Lỗi SQL: ";
                print_r($this->modelDelete->conn->errorInfo());
                echo "</pre>";
                exit;
            }
        } else {
            header("Location: " . BASEURL . "?act=hdv&msg=invalid_id");
            exit;
        }
    }
    public function editHDV($id)
    {
        if ($id > 0) {
            $hdv = $this->modelGet->getHDVById($id);
            if ($hdv) {
                require_once './views/admin/hdvedit.php';
            } else {
                header("Location: " . BASEURL . "?act=hdv&msg=not_found");
                exit;
            }
        } else {
            header("Location: " . BASEURL . "?act=hdv&msg=invalid_id");
            exit;
        }
    }
    public function updateHDV($id)
    {
        if ($id > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ho_ten'        => $_POST['ho_ten'] ?? '',
                'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
                'email'         => $_POST['email'] ?? '',
                'kinh_nghiem'   => $_POST['kinh_nghiem'] ?? '',
                'ngon_ngu'      => $_POST['ngon_ngu'] ?? ''
            ];

            $result = $this->modelUpdate->updateHDV($id, $data);

            if ($result) {
                header("Location: " . BASEURL . "?act=hdv&msg=updated");
                exit;
            } else {
                echo "<pre>Lỗi SQL: ";
                print_r($this->modelUpdate->conn->errorInfo());
                echo "</pre>";
                exit;
            }
        } else {
            header("Location: " . BASEURL . "?act=hdv&msg=invalid_request");
            exit;
        }
    }

    // ===================================================================
    // VI. CHÍNH SÁCH
    // ===================================================================

    public function chinhsach($tour_id)
    {
        $chinhSachList = $this->modelGet->getChinhSachByTourId($tour_id);
        $danhsachchinhsach = $this->modelGet->getDanhSachChinhSach();
        require_once './views/admin/chinhsachtour.php';
    }
public function luuChinhSachTour($tour_id)
{
    // Lấy mảng ID chính sách được chọn
    $chinhSachIds = $_POST['chinh_sach_ids'] ?? [];
    
    // Lấy mảng ghi chú (dạng key-value: [id_chinh_sach => 'nội dung ghi chú'])
    $ghi_chu_list = $_POST['ghi_chu'] ?? []; 
    
    $allSuccess = true;

    if (!empty($chinhSachIds) && is_array($chinhSachIds)) {
        foreach ($chinhSachIds as $csId) {
            // Lấy ghi chú tương ứng với ID chính sách. 
            // Nếu không có thì gán chuỗi rỗng.
            $noi_dung_ghi_chu = isset($ghi_chu_list[$csId]) ? trim($ghi_chu_list[$csId]) : ''; 
            
            // Gọi Model để lưu (truyền chuỗi ghi chú, không truyền mảng)
            $result = $this->modelCreate->luuChinhSachTour($tour_id, $csId, $noi_dung_ghi_chu);
            
            if (!$result) {
                $allSuccess = false;
            }
        }
    } else {
        // Trường hợp không chọn chính sách nào
        $allSuccess = false; 
    }

    if ($allSuccess) {
        header('Location: ' . BASEURL . '?act=chinhsach&tour_id=' . $tour_id . '&msg=success');
    } else {
        header('Location: ' . BASEURL . '?act=chinhsach&tour_id=' . $tour_id . '&msg=db_fail');
    }
    exit;
}
    public function xoaChinhSachTour($tour_chinh_sach_id)
    {
        if ($tour_chinh_sach_id !== null) {
            $this->modelDelete->xoaChinhSachKhoiTour($tour_chinh_sach_id);
        }
        header('Location: ' . BASEURL . '?act=chinhsach&tour_id=' . ($_GET['tour_id'] ?? 0));
    }

    // ===================================================================
    // VII. ĐẶT TOUR & HÀNH KHÁCH
    // ===================================================================

    public function dattourlist()
    {
        $data = $this->modelGet->getAllDatTour();
        require_once './views/admin/dattourlist.php';
    }
    public function dat_tour_add()
    {
        $dataTour = $this->modelGet->getAllTours();
        $data = $this->modelGet->getAllKhachHang();
        require_once './views/admin/dat_tour_add.php';
    }

    public function dat_tour_save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '?act=dat_tour_add');
            exit();
        }

        // Lấy dữ liệu POST
        $khachHangId = $_POST['khach_hang_id'] ?? null;
        $tourId = $_POST['tour_id'] ?? null;
        $soNguoi = (int)($_POST['so_nguoi'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? 'chờ xác nhận';
        $nguon = $_POST['nguon'] ?? 'web';
        $ghiChu = $_POST['ghi_chu'] ?? null;

        $errors = [];
        if (empty($khachHangId)) {
            $errors[] = "Vui lòng chọn Khách Hàng.";
        }
        if (empty($tourId)) {
            $errors[] = "Vui lòng chọn Tour.";
        }
        if ($soNguoi < 1) {
            $errors[] = "Số lượng người phải lớn hơn 0.";
        }

        if (!empty($errors)) {
            // Xử lý lỗi validation
            $_SESSION['error_message'] = "Lỗi nhập liệu: " . implode('; ', $errors);
            header('Location: ' . BASEURL . '?act=dat_tour_add');
            exit();
        }

        $loaiDatTour = ($soNguoi === 1) ? 'individual' : 'group';

        $data = [
            'khach_hang_id' => $khachHangId,
            'tour_id'       => $tourId,
            'so_nguoi'      => $soNguoi,
            'loai'          => $loaiDatTour,
            'trang_thai'    => $trangThai,
            'nguon'         => $nguon,
            'ghi_chu'       => $ghiChu
        ];

        $dat_tour_id = $this->modelCreate->createDatTour($data);

        if ($dat_tour_id) {
            $_SESSION['success_message'] = "Đơn đặt tour #{$dat_tour_id} đã được tạo thành công. Vui lòng nhập thông tin hành khách.";
            header('Location: ' . BASEURL . '?act=hanh_khach_add&dat_tour_id=' . $dat_tour_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Lỗi hệ thống: Không thể tạo đơn đặt tour. (Lỗi DB hoặc Model)";
            header('Location: ' . BASEURL . '?act=dat_tour_add');
            exit();
        }
    }
    public function dat_tour_delete($dat_tour_id)
    {
        if ($dat_tour_id !== null) {
            $this->modelDelete->deleteDatTour($dat_tour_id);
        }
        header('Location: ' . BASEURL . '?act=dattourlist');
    }

    public function hanh_khach_add($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/hanh_khach_add.php';
    }

  public function hanh_khach_save($dat_tour_id)
    {
        // Lấy ID từ POST nếu có, nếu không dùng tham số truyền vào
        $dat_tour_id = $_POST['dat_tour_id'] ?? $dat_tour_id;
        $hanh_khach_data = $_POST['hanh_khach'] ?? [];

        foreach ($hanh_khach_data as $hk) {
            // Chuẩn bị mảng dữ liệu khớp với tên cột trong Database
            $data = [
                'dat_tour_id'      => $dat_tour_id,
                'ho_ten'           => $hk['ho_ten'] ?? '',
                'gioi_tinh'        => $hk['gioi_tinh'] ?? null,
                'ngay_sinh'        => $hk['ngay_sinh'] ?? null,
                
                // Map: Input form -> Tên cột DB
                // Ưu tiên lấy 'so_giay_to', nếu không có thì lấy 'cccd' (phòng trường hợp form cũ)
                'so_giay_to'       => $hk['so_giay_to'] ?? ($hk['cccd'] ?? null),
                
                // Ưu tiên lấy 'lien_he', nếu không có thì lấy 'sdt'
                'lien_he'          => $hk['lien_he'] ?? ($hk['sdt'] ?? null),
                
                // Ưu tiên lấy 'yeu_cau_ca_nhan', nếu không có thì lấy 'ghi_chu'
                'yeu_cau_ca_nhan'  => $hk['yeu_cau_ca_nhan'] ?? ($hk['ghi_chu'] ?? null),
            ];

            // Gọi Model để insert
            $this->modelCreate->createHanhKhach($data);
        }

        // Sau khi lưu xong, chuyển hướng sang trang Tạo Đặt Cọc
        header('Location: ' . BASEURL . '?act=dat_coc&dat_tour_id=' . $dat_tour_id);
        exit();
    }

    public function dat_coc($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/dat_coc.php';
    }
    public function dat_coc_save($id)
    {
        $data = [
            'dat_tour_id' => $id,
            'so_tien' => $_POST['so_tien'] ?? 0,
            'tien_te' => $_POST['tien_te'] ?? 'VND',
            'ngay_dat_coc' => $_POST['ngay_dat_coc'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? '',
            'ngay_dat' => $_POST['ngay_dat'] ?? '',
            'hinh_thuc' => $_POST['hinh_thuc'] ?? '',
            'ghi_chu' => $_POST['ghi_chu'] ?? '',
        ];
        $this->modelCreate->createDatCoc($data);
        header('Location: ' . BASEURL . '?act=dattourlist');
    }
    public function dat_tour_edit($dat_tour_id)
    {
        $dataTour = $this->modelGet->getAllTours();
        $dataKhachHang = $this->modelGet->getAllKhachHang();
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/dat_tour_edit.php';
    }
    public function dat_tour_update($dat_tour_id)
    {
        $khachHangId = $_POST['khach_hang_id'] ?? null;
        $tourId = $_POST['tour_id'] ?? null;
        $soNguoi = (int)($_POST['so_nguoi'] ?? 0);
        $trangThai = $_POST['trang_thai'] ?? 'chờ xác nhận';
        $nguon = $_POST['nguon'] ?? 'web';
        $ghiChu = $_POST['ghi_chu'] ?? null;

        $loaiDatTour = ($soNguoi === 1) ? 'individual' : 'group';

        $data = [
            'khach_hang_id' => $khachHangId,
            'tour_id'       => $tourId,
            'so_nguoi'      => $soNguoi,
            'loai'          => $loaiDatTour,
            'trang_thai'    => $trangThai,
            'nguon'         => $nguon,
            'ghi_chu'       => $ghiChu
        ];

        $this->modelUpdate->updateDatTour($dat_tour_id, $data);
        header('Location: ' . BASEURL . '?act=hanh_khach_edit&dat_tour_id=' . $dat_tour_id);
    }
    public function hanh_khach_edit($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        $hanhKhachList = $this->modelGet->getHanhKhachByDatTourId($dat_tour_id);
        require_once './views/admin/hanh_khach_edit.php';
    }

  public function hanh_khach_update($dat_tour_id)
    {
        $hanh_khach_data = $_POST['hanh_khach'] ?? [];
        $dat_tour_id = $_POST['dat_tour_id'] ?? $dat_tour_id;
        
        // Nhận biết bước tiếp theo (nếu có)
        $next_step = $_POST['next_step'] ?? '';

        $success_count = 0;

        foreach ($hanh_khach_data as $hanh_khach_input) {
            // Chuẩn bị dữ liệu map với bảng `hanhkhachlist` trong DB
            $data = [
                'dat_tour_id'      => $dat_tour_id,
                'ho_ten'           => $hanh_khach_input['ho_ten'] ?? '',
                'gioi_tinh'        => $hanh_khach_input['gioi_tinh'] ?? null, // Mới thêm
                'ngay_sinh'        => $hanh_khach_input['ngay_sinh'] ?? null,
                
                // Map các trường input sang tên cột trong DB
                // Form gửi 'so_giay_to' hoặc 'cccd' -> Lưu vào 'so_giay_to'
                'so_giay_to'       => $hanh_khach_input['so_giay_to'] ?? ($hanh_khach_input['cccd'] ?? null),
                
                // Form gửi 'lien_he' hoặc 'sdt' -> Lưu vào 'lien_he'
                'lien_he'          => $hanh_khach_input['lien_he'] ?? ($hanh_khach_input['sdt'] ?? null),
                
                // Form gửi 'yeu_cau_ca_nhan' hoặc 'ghi_chu' -> Lưu vào 'yeu_cau_ca_nhan'
                'yeu_cau_ca_nhan'  => $hanh_khach_input['yeu_cau_ca_nhan'] ?? ($hanh_khach_input['ghi_chu'] ?? null),
            ];

            $hanh_khach_id = (int)($hanh_khach_input['hanh_khach_id'] ?? 0);

            if ($hanh_khach_id > 0) {
                // CẬP NHẬT bản ghi đã tồn tại
                $result = $this->modelUpdate->updateHanhKhach($hanh_khach_id, $data);
                if ($result !== false) {
                    $success_count++;
                }
            } else {
                // TẠO MỚI bản ghi
                $result = $this->modelCreate->createHanhKhach($data);
                if ($result !== false) {
                    $success_count++;
                }
            }
        }

        if ($success_count > 0) {
            $_SESSION['success'] = "Đã cập nhật $success_count hành khách thành công!";
        } else {
            $_SESSION['error'] = "Không có hành khách nào được cập nhật.";
        }

        // Xử lý chuyển hướng dựa trên nút bấm
        if ($next_step === 'deposit') {
            // Chuyển sang trang tạo đặt cọc
            header('Location: ' . BASEURL . '?act=dat_tour_detail&dat_tour_id=' . $dat_tour_id);
        } else {
            // Quay về trang chi tiết hoặc danh sách
            header('Location: ' . BASEURL . '?act=dat_tour_detail&dat_tour_id=' . $dat_tour_id);
        }
        exit();
    }
    public function dat_tour_detail($dat_tour_id)
    {
        $data= $this->modelGet->getDatTourDetail($dat_tour_id);
        include './views/admin/dat_tour_detail.php';
        
    }

    // ===================================================================
    // VIII. DỊCH VỤ & NHÀ CUNG CẤP
    // ===================================================================

    public function layDichVu()
    {
        // LƯU Ý: Hàm này sử dụng $this->modelGet->conn thay vì biến $db.
        // Cần đảm bảo hàm layTatCaDichVu trong Model nhận đúng đối tượng kết nối (như trong Model bạn gửi)
        $db = $this->modelGet->conn;
        $dichVuList = $this->modelGet->layTatCaDichVu($db);
        $nccList = $this->modelGet->layTatCaNhaCungCap($db);
        require_once './views/admin/dichvulist.php';
    }
    public function layDichVuNCC($ncc_id)
    {
        $db = $this->modelGet->conn;
        $dichVuList = $this->modelGet->layDichVuTheoNCC($ncc_id);
        $nccList = $this->modelGet->layTatCaNhaCungCap($db);
        require_once './views/admin/dichvutheoncc.php';
    }
    public function themDichVu()
    {
        $db = $this->modelGet->conn;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'loai_dich_vu' => $_POST['loai_dich_vu'] ?? '',
                'ma' => $_POST['ma'] ?? '',
                'ten_dich_vu' => $_POST['ten_dich_vu'] ?? '',
                'mo_ta' => $_POST['mo_ta'] ?? '',
                'gia_mac_dinh' => $_POST['gia_mac_dinh'] ?? 0,
                'don_vi' => $_POST['don_vi'] ?? '',
                'ncc_id' => $_POST['ncc_id'] ?? 0
            ];

            // Hàm Model tạo dịch vụ cần được sửa để nhận mảng $data
            $this->modelCreate->themDichVu($data);

            header('location:' . BASEURL . '?act=lay_dich_vu_ncc&ncc_id=' . $data['ncc_id']);
            $db = $this->modelGet->conn;
            $loai_dich_vu = $_POST['loai_dich_vu'];
            $ma = $_POST['ma'];
            $tendv = $_POST['ten_dich_vu'];
            $mo_ta = $_POST['mo_ta'];
            $gia_mac_dinh = $_POST['gia_mac_dinh'];
            $don_vi = $_POST['don_vi'];
            $ncc_id = $_POST['ncc_id'];

            $this->modelCreate->themDichVu($db, $loai_dich_vu, $ma, $tendv, $mo_ta, $gia_mac_dinh, $don_vi, $ncc_id);
            header('location:' . BASEURL . '?act=lay_dich_vu&ncc_id=' . $ncc_id);
            exit;
        } else {
            $nccList = $this->modelGet->layTatCaNhaCungCap($db);
            require_once './views/admin/dichvuadd.php';
        }
    }

    public function xoaDichVu($id)
    {
        if ($id !== null) {
            $this->modelDelete->xoaDichVu($id);
        }
        header("Location: " . BASEURL . "?act=lay_dich_vu");
        exit;
    }

    public function capNhatDichVu($id)
    {
        if ($id === null) {
            header("Location: " . BASEURL . "?act=lay_dich_vu&msg=invalid_id");
            exit;
        }

        $db = $this->modelGet->conn; // Kết nối DB

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'loai_dich_vu'  => $_POST['loai_dich_vu'],
                'ma'            => $_POST['ma'],
                'ten_dich_vu'   => $_POST['ten_dich_vu'],
                'mo_ta'         => $_POST['mo_ta'],
                'gia_mac_dinh'  => $_POST['gia_mac_dinh'],
                'don_vi'        => $_POST['don_vi'],
                'ncc_id'        => $_POST['ncc_id']
            ];

            $this->modelUpdate->capNhatDichVu($id, $data);

            header("Location: " . BASEURL . "?act=lay_dich_vu&msg=updated");
            exit;
        }

        $dichvu = $this->modelGet->layDichVuTheoId($db, $id);
        $nccList = $this->modelGet->layTatCaNhaCungCap($db);

        require_once 'views/admin/editdichvu.php';
    }

    // public function showHK() {
    //     $keyword = $_GET['keyword'] ?? "";
    //     $list = $this->modelGet->search($keyword);

    //     require_once "views/admin/hanhkhach_list.php";
    // }

    public function logout()
    {
        unset($_SESSION['user']);
        header("Location: " . BASEURL . "?act=login");
        exit;
    }



    public function ganDichVuTour($tour_id)
    {

        $data = $this->modelGet->layTatCaDichVu($this->modelGet->conn);
        $dichVuDaGan = $this->modelGet->getDichVuByTourId($tour_id);
        require_once './views/admin/gandichvutour.php';
    }
    // Lưu gán dịch vụ cho tour
public function luuGanDichVuTour($tour_id)
    {
        if (!$tour_id) {
            header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $tour_id . '&error=missing_tour_id');
            exit;
        }

        $dichVuIds = $_POST['dich_vu_id'] ?? [];
        // Giả sử bên HTML bạn đặt name="ghi_chu[ID_DICH_VU]" thì đây sẽ là mảng dạng [ID => 'Nội dung']
        $ghiChu = $_POST['ghi_chu'] ?? []; 

        if (!is_array($dichVuIds) || empty($dichVuIds)) {
            header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $tour_id . '&error=no_services_selected');
            exit;
        }

        $allSuccess = true;
        foreach ($dichVuIds as $dich_vu_id) {
            // --- SỬA Ở ĐÂY ---
            // Lấy ghi chú cụ thể theo ID dịch vụ đang lặp. 
            // Nếu không có thì để rỗng ''.
            $note_content = isset($ghiChu[$dich_vu_id]) ? trim($ghiChu[$dich_vu_id]) : ''; 

            // Truyền $note_content (chuỗi) thay vì $ghiChu (mảng)
            $result = $this->modelCreate->ganDichVuTour($tour_id, $dich_vu_id, $note_content);
            
            if (!$result) {
                $allSuccess = false;
            }
        }

        if ($allSuccess) {
            header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $tour_id . '&msg=success');
        } else {
            header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $tour_id . '&error=insert_failed');
        }
        exit;
    }

    public function xoaGanDichVuTour($gia_dv_id, $tour_id)
    {


        if ($gia_dv_id !== null && $tour_id !== null) {
            $this->modelDelete->xoaGanDichVuTour($gia_dv_id, $tour_id);
        }
        header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $_GET['tour_id']);
    }
    public function userList()
    {
        $users = $this->modelGet->getAllUsers();
        require './views/admin/user_list.php';
    }
     public function createUser()
    {
        $roles = $this->modelRole->getAllVaiTro();
        require './views/admin/user_create.php';
    }


// 1. Cập nhật storeUser
public function storeUser()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST;
        
        // Tạo user và lấy ID trả về
        $newUserId = $this->modelCreate->storeUser($data);

        if ($newUserId) {
            // Kiểm tra vai trò để chuyển hướng
            if ($data['vai_tro_id'] == 3) { 
                // Vai trò HDV -> Chuyển sang form chi tiết HDV kèm user_id
                header("Location: " . BASEURL . "?act=hdv_detail_add&user_id=" . $newUserId);
                exit;
            } else if ($data['vai_tro_id'] == 4) {
                // Vai trò Khách hàng -> Chuyển sang form chi tiết KH kèm user_id
                header("Location: " . BASEURL . "?act=khachhang_detail_add&user_id=" . $newUserId);
                exit;
            } else {
                // Các vai trò khác (Admin/Staff) -> Về danh sách user
                header("Location: " . BASEURL . "?act=user_list");
                exit;
            }
        } else {
            // Xử lý lỗi nếu không tạo được user
            echo "Lỗi tạo tài khoản!";
        }
    }
}
public function editUser()

    {
        $id = $_GET['id'];
        $user = $this->modelGet->find($id);
        $roles = $this->modelRole->getAllVaiTro();

        require './views/admin/user_edit.php';
    }

    public function updateUser()
    {
        $id = $_POST['id'];
        $data = $_POST;

        $this->modelUpdate->updateUser($id, $data);

        header("Location: " . BASEURL . "?act=user_list");
    }

    public function deleteUser()
    {
        $id = $_GET['id'];
        $this->modelDelete->softDeleteUser($id);

        header("Location: " . BASEURL . "?act=user_list");
    }


// 2. Các hàm cho Hướng Dẫn Viên (HDV)
public function formAddHDVDetail() {
    $user_id = $_GET['user_id'] ?? 0;
    // Lấy thông tin cơ bản để hiển thị tên (nếu cần)
    $user = $this->modelGet->find($user_id); 
    require './views/admin/hdv_detail_add.php';
}

public function storeHDVDetail() {
    // 1. Xử lý Upload Ảnh đại diện
    $filename = null;
    if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
        $uploadDir = './assets/uploads/hdv/'; // Đường dẫn thư mục lưu ảnh
        
        // Tạo thư mục nếu chưa tồn tại
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Tạo tên file duy nhất để tránh trùng lặp
        $fileExtension = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
        $filename = 'hdv_' . time() . '_' . rand(100, 999) . '.' . $fileExtension;
        
        // Di chuyển file từ thư mục tạm sang thư mục đích
        move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $uploadDir . $filename);
    }

    // 2. Chuẩn bị dữ liệu để insert vào bảng 'huongdanvien'
    // Lưu ý: Key của mảng phải TRÙNG KHỚP với tên cột trong Database
    $data = [
        'nguoi_dung_id'         => $_POST['nguoi_dung_id'],
        'ngay_sinh'             => $_POST['ngay_sinh'],
        'gioi_tinh'             => $_POST['gioi_tinh'], // Enum: 'Nam' hoặc 'Nữ'
        'dia_chi_lien_he'       => $_POST['dia_chi_lien_he'],
        'anh_dai_dien'          => $filename, // Lưu tên file ảnh
        'chung_chi_chuyen_mon'  => $_POST['chung_chi_chuyen_mon'],
        'ngon_ngu_su_dung'      => $_POST['ngon_ngu_su_dung'],
        'kinh_nghiem_lam_viec'  => $_POST['kinh_nghiem_lam_viec'],
        'tinh_trang_suc_khoe'   => $_POST['tinh_trang_suc_khoe'],
        'ngay_tao'              => date('Y-m-d H:i:s'), // Lấy thời gian hiện tại
        'tinh_trang_hoat_dong'  => 'Sẵn sàng' // Mặc định khi mới tạo
    ];
    
    // 3. Gọi Model để insert
    // Hàm addHDV trong model phải viết câu lệnh INSERT INTO huongdanvien ...
    $this->modelCreate->addHDV($data);
    
    // 4. Lưu xong -> Chuyển hướng
    // Có thể thêm thông báo thành công vào Session nếu muốn
    header("Location: " . BASEURL . "?act=hdvlist"); 
    exit();
}

// 3. Các hàm cho Khách Hàng
public function formAddKhachHangDetail() {
    $user_id = $_GET['user_id'] ?? 0;
    $user = $this->modelGet->find($user_id);
    require './views/admin/khachhang_detail_add.php';
}

public function storeKhachHangDetail() {
    $data = [
        'nguoi_dung_id' => $_POST['nguoi_dung_id'],
        'cccd'          => $_POST['cccd'],
        'dia_chi'       => $_POST['dia_chi']
    ];
    
    $this->modelCreate->createKhachHang($data);
    
    // Lưu xong -> Chuyển về danh sách Khách hàng
    header("Location: " . BASEURL . "?act=khach_hang_list");
}

public function listKhachHang() {
    $data = $this->modelGet->getAllKhachHang();
    require './views/admin/khachhang_list.php';
}

    
    
    

      public function formAddSchedule($tour_id)
    {
        $tour = $this->modelGet->getTourById($tour_id);
        require_once './views/admin/schedule_add.php';
    }
    public function createSchedule()
    {
        $tour_id = $_POST['tour_id'];

        $data = [
            'tour_id'       => $tour_id,
            'trang_thai'    => $_POST['trang_thai'],
            'ngay_bat_dau'  => $_POST['ngay_bat_dau'],
            'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
            'hieu_luc_tu'   => $_POST['hieu_luc_tu'],
            'hieu_luc_den'  => $_POST['hieu_luc_den'],
            'ngay_tao'     => date('Y-m-d H:i:s'),
            'ghi_chu'       => $_POST['ghi_chu'],
        ];

        // 🔥 1. Tạo lịch mới và lấy ID lịch vừa tạo
        $lich_id = $this->modelCreate->createSchedule($data);

        // 🔥 2. Cập nhật sang dattour
        $sql = "UPDATE dattour SET lich_id = :lich_id WHERE tour_id = :tour_id";
        $stmt = $this->modelCreate->conn->prepare($sql);
        $stmt->execute([
            ':lich_id' => $lich_id,
            ':tour_id' => $tour_id
        ]);

        // Chuyển trang
        header("Location: " . BASE_URL . "?act=dattourlist");
    }
    
    
    public function editSchedule($lich_id)
    {
        
        $lich= $this->modelGet->getLichKhoiHanhById($lich_id);
        require_once './views/admin/schedule_edit.php';
        var_dump($lich);
        var_dump($lich_id);
    }
    public function updateSchedule($lich_id)
    {
        $tour_id = $_POST['tour_id'];

        $data = [
            'trang_thai'    => $_POST['trang_thai'],
            'ngay_bat_dau'  => $_POST['ngay_bat_dau'],
            'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
            'hieu_luc_tu'   => $_POST['hieu_luc_tu'],
            'hieu_luc_den'  => $_POST['hieu_luc_den'],
            'ghi_chu'       => $_POST['ghi_chu'],
        ];

        $this->modelUpdate->updateSchedule($lich_id, $data);

        // Chuyển trang
        header("Location: " . BASE_URL . "?act=dattourlist");
    }
    public function deleteSchedule($lich_id)
    {
        if ($lich_id !== null) {
            $this->modelDelete->Scheduledelete($lich_id);
        }
        header("Location: " . BASEURL . "?act=dattourlist");
    }


}
