<?php
class AdminController
{
    public $modelGet;
    public $modelCreate;
    public $modelDelete;
    public $modelUpdate;
    public function __construct()
    {
        $this->modelGet = new getDataModule();
        $this->modelCreate = new creatDataModule();
        $this->modelDelete = new deleteDataModule();
        $this->modelUpdate = new uppDateDataModuleDataModule();
    }
    public function index()
    {
        $data1 = $this->modelGet->getAllTours();
        require_once  './views/admin/Dashboard.php';
    }
    public function showTours()
    {
        $data1 = $this->modelGet->getAllTours();
        require_once  './views/admin/tourlist.php';
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
            // LƯU Ý: Bạn CẦN BỔ SUNG 'nguoi_tao_id' vào đây để Model lưu vào DB
            // 'nguoi_tao_id' => $_SESSION['user_id'] 
        ];

        // 2. Gọi Model và BẮT LẤY ID TRẢ VỀ từ hàm createTour
        $newTourId = $this->modelCreate->createTour($data);

        // 3. Kiểm tra kết quả và Chuyển hướng
        if ($newTourId) {
            // Thành công: Sử dụng ID vừa nhận được để chuyển hướng sang bước tiếp theo
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $newTourId);
            exit();
        } else {
            // Thất bại: Xử lý lỗi
            // Ví dụ: header('Location: '.BASEURL.'?act=create_tour_form&error=db_fail');
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
        $ten = $_POST['ten'];
        $danh_muc_id = $_POST['danh_muc_id'];
        $mo_ta_ngan = $_POST['mo_ta_ngan'];
        $mo_ta = $_POST['mo_ta'];
        $gia = $_POST['gia_co_ban'];
        $thoi_luong = $_POST['thoi_luong_mac_dinh'];
        $diem_khoi_hanh = $_POST['diem_khoi_hanh'];
        $hoat_dong = $_POST['hoat_dong'];

        $data = [
            'ten' => $ten,
            'danh_muc_id' => $danh_muc_id,
            'mo_ta_ngan' => $mo_ta_ngan,
            'mo_ta' => $mo_ta,
            'gia_co_ban' => $gia,
            'thoi_luong_mac_dinh' => $thoi_luong,
            'diem_khoi_hanh' => $diem_khoi_hanh,
            'hoat_dong' => $hoat_dong
        ];

        $this->modelUpdate->uppDateTour($tour_id, $data);
        header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id);
    }
    public function diadiem($tour_id)
    {
        $diadiemList = $this->modelGet->getDiaDiemByTourId($tour_id);
        require_once './views/admin/diadiemtour.php';
    }
    public function gan_diadiem($tour_id)
    {
        $data = $this->modelGet->getAllDiaDiem();
        $diaDiemDaGan = $this->modelGet->getDiaDiemByTourId($tour_id);
        require_once './views/admin/gan_diadiemtour.php';
    }
    public function luu_gan_diadiem($tour_id)
    {
        // 1. Kiểm tra xem dia_diem_id có phải là MẢNG không
        $diaDiemIds = $_POST['dia_diem_id'] ?? [];
        $ghiChu = $_POST['ghi_chu'] ?? null; // Lưu ý: Ghi chú sẽ giống nhau cho mọi địa điểm nếu dùng POST này

        if (!is_array($diaDiemIds) || empty($diaDiemIds)) {
            // Xử lý lỗi: Không có địa điểm nào được chọn
            header('Location: ' . BASEURL . '?act=loi_chua_chon');
            exit;
        }

        $allSuccess = true;

        // 2. LẶP QUA MẢNG ID để gọi Model cho từng địa điểm
        foreach ($diaDiemIds as $ddId) {
            // Gọi Model để chèn TỪNG dòng một
            $result = $this->modelCreate->ganDiaDiemChoTour($tour_id, $ddId, $ghiChu);
            if (!$result) {
                $allSuccess = false;
            }
        }

        // 3. Chuyển hướng
        if ($allSuccess) {
            $diaDiemDaGan = $this->modelGet->getDiaDiemByTourId($tour_id);
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id . '&msg=success');
        } else {
            // Xử lý lỗi nếu có ít nhất một lần INSERT thất bại
            header('Location: ' . BASEURL . '?act=gan_diadiem&tour_id=' . $tour_id . '&error=insert_failed');
        }
        exit;
    }
    public function xoa_diadiem_tour($dia_diem_tour_id)
    {
        if ($dia_diem_tour_id !== null) {
            $this->modelDelete->xoaDiaDiemKhoiTour($dia_diem_tour_id);
        }
        header('Location: ' . BASEURL . '?act=diadiem&tour_id=' . $_GET['tour_id']);
    }
    public function xoa_dia_diem_tour($dia_diem_tour_id)
    {
        if ($dia_diem_tour_id !== null) {
            $this->modelDelete->xoaDiaDiemKhoiTour($dia_diem_tour_id);
        }
        header('Location: ' . BASEURL . '?act=xoa_dia_diem&tour_id=' . $_GET['tour_id']);
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
        $dia_diem_id = $_POST['dia_diem_id'];
        $this->modelUpdate->capNhatDiaDiemTour($dia_diem_tour_id, $dia_diem_id, $ghi_chu);
        header('Location: ' . BASEURL . '?act=diadiem&tour_id=' . $_POST['tour_id']);
    }
    public function formaddncc()
    {
        require_once './views/admin/ncc_add.php';
    }

    public function createncc()
    {
        $data = [
            'ten' => $_POST['ten'],
            'lien_he' => $_POST['lien_he'],
            'dia_chi' => $_POST['dia_chi'],
            'ma_so_thue' => $_POST['ma_so_thue']
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
            'ten' => $_POST['ten'],
            'lien_he' => $_POST['lien_he'],
            'dia_chi' => $_POST['dia_chi'],
            'ma_so_thue' => $_POST['ma_so_thue']
        ];

        $this->modelUpdate->updateNCC($id, $data);
        header("Location: " . BASEURL . "?act=ncc_list");
    }
    public function showncc()
    {
        $data = $this->modelGet->getAllNCC();
        require_once './views/admin/ncc_list.php';
    }
    public function listlichtrinhtour()
    {
        $tour_id = $_GET['tour_id'] ?? null;
        $data = $this->modelGet->getAllLichTrinhTour($tour_id);
        require './views/admin/listlichtrinhtour.php';
    }

    // Form thêm
    public function formAddLichTrinh($tour_id)
    {
        $data = $this->modelGet->getAllLichTrinhTour($tour_id);
        $tour = $this->modelGet->getTourById($tour_id);
        require './views/admin/lichtrinhtouradd.php';
    }

    // Thêm
    public function createLichTrinh()
    {
        if (empty($_POST['tour_id'])) {
            die('Lỗi: tour_id không được để trống!');
        }

        $data = [
            'tour_id' => $_POST['tour_id'],
            'ngay_thu' => $_POST['ngay_thu'],
            'tieu_de' => $_POST['tieu_de'],
            'noi_dung' => $_POST['noi_dung']
        ];

        $this->modelCreate->createLichTrinh($data);
        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $_POST['tour_id']);
    }

    // Form sửa
    public function editLichTrinh($lich_trinh_id)
    {
        $info = $this->modelGet->getLichTrinhById($lich_trinh_id);
        require './views/admin/editlichtrinhtour.php';
    }

    // Update
    public function capnhatLichTrinh($lich_trinh_id)
    {
        $data = [
            'ngay_thu' => $_POST['ngay_thu'],
            'tieu_de' => $_POST['tieu_de'],
            'noi_dung' => $_POST['noi_dung']
        ];

        // ĐÚNG HÀM UPDATE LỊCH TRÌNH
        $this->modelUpdate->updateLichTrinh($lich_trinh_id, $data);

        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $_POST['tour_id']);
    }


    // Xóa
    public function deleteLichTrinh($lich_trinh_id)
    {
        $this->modelDelete->deleteLichTrinh($lich_trinh_id);

        $tour_id = $_GET['tour_id'] ?? 0;
        header("Location: " . BASEURL . "?act=addlichtrinh&tour_id=" . $tour_id);
    }
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
                'nguoi_dung_id' => 3
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
    public function chinhsach($tour_id)
    {
        $chinhSachList = $this->modelGet->getChinhSachByTourId($tour_id);
        $danhsachchinhsach = $this->modelGet->getDanhSachChinhSach();
        require_once './views/admin/chinhsachtour.php';
    }
    public function luuChinhSachTour($tour_id)
    {
        // Lấy MẢNG ID chính sách (Tên field là 'chinh_sach_ids' từ View đã sửa)
        $chinhSachIds = $_POST['chinh_sach_ids'] ?? [];
        $ghi_chu = $_POST['ghi_chu'] ?? null;

        // Biến kiểm tra thành công
        $allSuccess = true;

        // Kiểm tra xem có ID nào được chọn không
        if (!empty($chinhSachIds) && is_array($chinhSachIds)) {

            // VÒNG LẶP FOREACH ĐỂ CHÈN TỪNG DÒNG
            foreach ($chinhSachIds as $csId) {
                // Gọi hàm Model (của bạn) cho từng ID chính sách
                $result = $this->modelCreate->luuChinhSachTour($tour_id, $csId, $ghi_chu);

                if (!$result) {
                    // Nếu một lệnh chèn thất bại, ghi nhận và có thể dừng lại
                    $allSuccess = false;
                    // Có thể break hoặc ghi log lỗi ở đây
                }
            }
        } else {
            // Trường hợp người dùng không chọn bất kỳ chính sách nào
            $allSuccess = false; // Coi như thất bại nếu không có dữ liệu để chèn
        }

        // Chuyển hướng
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
        header('Location: ' . BASEURL . '?act=chinhsach&tour_id=' . $_GET['tour_id']);
    }
    public function dattourlist()
    {
        $data = $this->modelGet->getAllDatTour();
        require_once  './views/admin/dattourlist.php';
    }
    public function dat_tour_add()
    {   $dataTour = $this->modelGet->getAllTours();
        $data = $this->modelGet->getAllKhachHang();
        require_once './views/admin/dat_tour_add.php';
    }

    // File: controllers/DatTourController.php

    public function dat_tour_save()
{
    // Đảm bảo session đã được khởi tạo trước khi sử dụng $_SESSION
    // if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // 1. Kiểm tra request POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASEURL . '?act=dat_tour_add');
        exit();
    }

    // 2. Lấy và kiểm tra các trường bắt buộc (Validation)
    
    $khachHangId = $_POST['khach_hang_id'] ?? null;
    $tourId = $_POST['tour_id'] ?? null;
    $soNguoi = (int)($_POST['so_nguoi'] ?? 0);
    $trangThai = $_POST['trang_thai'] ?? 'chờ xác nhận';
    $nguon = $_POST['nguon'] ?? 'web';
    $ghiChu = $_POST['ghi_chu'] ?? null;

    $errors = [];
    if (empty($khachHangId)) { $errors[] = "Vui lòng chọn Khách Hàng."; }
    if (empty($tourId)) { $errors[] = "Vui lòng chọn Tour."; }
    if ($soNguoi < 1) { $errors[] = "Số lượng người phải lớn hơn 0."; }
    
    if (!empty($errors)) {
        // Có lỗi validation, chuyển hướng và hiển thị lỗi
        $_SESSION['error_message'] = "Lỗi nhập liệu: " . implode('; ', $errors);
        // Có thể lưu lại $_POST để điền lại form
        header('Location: ' . BASEURL . '?act=dat_tour_add');
        exit();
    }
    
    // 3. Tính toán lại Loại (Đảm bảo logic backend là nguồn tin cậy duy nhất)
    $loaiDatTour = ($soNguoi === 1) ? 'individual' : 'group';

    // 4. Chuẩn bị dữ liệu để gửi đến Model
    $data = [
        'khach_hang_id' => $khachHangId,
        'tour_id'       => $tourId,
        'so_nguoi'      => $soNguoi, 
        'loai'          => $loaiDatTour, 
        'trang_thai'    => $trangThai,
        'nguon'         => $nguon, 
        'ghi_chu'       => $ghiChu
    ];

    // 5. Gọi Model để lưu dữ liệu
    $dat_tour_id = $this->modelCreate->createDatTour($data);

    if ($dat_tour_id) {
        // THÀNH CÔNG: Chuyển hướng sang bước tiếp theo
        $_SESSION['success_message'] = "Đơn đặt tour #{$dat_tour_id} đã được tạo thành công. Vui lòng nhập thông tin hành khách.";
        header('Location: ' . BASEURL . '?act=hanh_khach_add&dat_tour_id=' . $dat_tour_id);
        exit();
    } else {
        // THẤT BẠI: Lỗi từ database
        $_SESSION['error_message'] = "Lỗi hệ thống: Không thể tạo đơn đặt tour. (Lỗi DB hoặc Model)";
        header('Location: ' . BASEURL . '?act=dat_tour_add');
        exit();
    }
}

    public function hanh_khach_add($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/hanh_khach_add.php';
    }
    // public function dat_tour_detail($dat_tour_id)
    // {
    //     $data = $this->modelGet->getDatTourDetail($dat_tour_id);
    //     require_once './views/admin/dat_tour_detail.php';
    // }
    public function hanh_khach_save($id){
        $khach_hang_data=$_POST['hanh_khach']??[];
        foreach($khach_hang_data as $khach_hang){
            $data=[
                'dat_tour_id'=>$id,
                'ho_ten'=>$khach_hang['ho_ten']??'',
                'ngay_sinh'=>$khach_hang['ngay_sinh']??'',
                'gioi_tinh'=>$khach_hang['gioi_tinh']??'',
                'cccd'=>$khach_hang['cccd']??'',
                'sdt'=>$khach_hang['sdt']??'',
                'email'=>$khach_hang['email']??'',
            ];
            $this->modelCreate->createHanhKhach($data);
        }    
        header('Location: '.BASEURL.'?act=dat_coc&dat_tour_id='.$id);
   
    }
    public function dat_coc($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/dat_coc.php';
    }
    public function dat_coc_save($id){
        $data=[
            'dat_tour_id'=>$id,
            'so_tien'=>$_POST['so_tien']??0,
            'tien_te'=>$_POST['tien_te']??'VND',
            'ngay_dat_coc'=>$_POST['ngay_dat_coc']??'',
            'trang_thai'=>$_POST['trang_thai']??'',
            'ngay_dat'=>$_POST['ngay_dat']??'',
            'hinh_thuc'=>$_POST['hinh_thuc']??'',
            'ghi_chu'=>$_POST['ghi_chu']??'',
        ];
        $this->modelCreate->createDatCoc($data);


        var_dump($data);
         header('Location: '.BASEURL.'?act=dattourlist');
        // header('Location: '.BASEURL.'?act=dattourlist');
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
    }public function hanh_khach_update($dat_tour_id) {
    // 1. Lấy dữ liệu từ POST
    $hanh_khach_data = $_POST['hanh_khach'] ?? [];
    $dat_tour_id = $_POST['dat_tour_id'] ?? $dat_tour_id; // Đảm bảo lấy được ID

    $success_count = 0;
    
    // 2. Lặp qua danh sách hành khách gửi lên từ form
    foreach($hanh_khach_data as $hanh_khach_input) {
        
        // Chuẩn bị mảng dữ liệu để truyền vào Model
        $data = [
            'dat_tour_id' => $dat_tour_id,
            'ho_ten'      => $hanh_khach_input['ho_ten'] ?? '',
            'ngay_sinh'   => $hanh_khach_input['ngay_sinh'] ?? null, // Lưu ý: Nếu là chuỗi rỗng nên là null
            // 'gioi_tinh'   => $hanh_khach_input['gioi_tinh'] ?? '', // Nếu có trong form
            'cccd'        => $hanh_khach_input['cccd'] ?? null,
            'sdt'         => $hanh_khach_input['sdt'] ?? null,
            // 'email'       => $hanh_khach_input['email'] ?? null, // Nếu có trong form
            'ghi_chu'     => $hanh_khach_input['ghi_chu'] ?? null,
        ];
        
        // Lấy ID hành khách (0 nếu là bản ghi mới)
        $hanh_khach_id = (int)($hanh_khach_input['hanh_khach_id'] ?? 0);

        // 3. Logic Cập nhật hoặc Tạo mới
        if ($hanh_khach_id > 0) {
            // CÓ ID -> CẬP NHẬT bản ghi đã tồn tại
            
            // Bổ sung ID vào mảng data để truyền cho hàm update (nếu cần)
            $data['hanh_khach_id'] = $hanh_khach_id; 
            
            // Gọi hàm Update trong Model
            $result = $this->modelUpdate->updateHanhKhach($hanh_khach_id, $data);
            if ($result !== false) {
                $success_count++;
            }
            
        } else {
            // KHÔNG CÓ ID (hoặc ID = 0) -> TẠO MỚI bản ghi
            
            // Gọi hàm Create trong Model
            $result = $this->modelCreate->createHanhKhach($data);
            if ($result !== false) {
                $success_count++;
            }
        }
    } 
    
    // 4. Xử lý kết quả và chuyển hướng
    if ($success_count > 0) {
        // Thông báo thành công (tùy chọn)
        $_SESSION['success'] = "Đã cập nhật/thêm $success_count hành khách thành công!";
    } else {
        // Thông báo lỗi (tùy chọn)
        $_SESSION['error'] = "Không có hành khách nào được cập nhật.";
    }

    // Chuyển hướng về trang chi tiết đơn đặt tour
    header('Location: '.BASEURL.'?act=dat_tour_detail&dat_tour_id='.$dat_tour_id);
    exit();
}
}