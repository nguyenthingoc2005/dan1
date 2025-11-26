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
        $data1 = $this->modelGet->getAllTours();
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
        $data = $this->modelGet->getAllDiaDiem();
        $diaDiemDaGan = $this->modelGet->getDiaDiemByTourId($tour_id);
        require_once './views/admin/gan_diadiemtour.php';
    }
    public function luu_gan_diadiem($tour_id)
    {
        $diaDiemIds = $_POST['dia_diem_id'] ?? [];
        $ghiChu = $_POST['ghi_chu'] ?? null;

        if (!is_array($diaDiemIds) || empty($diaDiemIds)) {
            header('Location: ' . BASEURL . '?act=loi_chua_chon');
            exit;
        }

        $allSuccess = true;
        foreach ($diaDiemIds as $ddId) {
            $result = $this->modelCreate->ganDiaDiemChoTour($tour_id, $ddId, $ghiChu);
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
    public function xoa_diadiem_tour($dia_diem_tour_id)
    {
        if ($dia_diem_tour_id !== null) {
            $this->modelDelete->xoaDiaDiemKhoiTour($dia_diem_tour_id);
        }
        header('Location: ' . BASEURL . '?act=diadiem&tour_id=' . ($_GET['tour_id'] ?? 0));
    }
    public function xoa_dia_diem_tour($dia_diem_tour_id)
    {
        // Chức năng này giống hệt xoa_diadiem_tour, chỉ khác URL redirect (xóa cái này hoặc gộp lại)
        if ($dia_diem_tour_id !== null) {
            $this->modelDelete->xoaDiaDiemKhoiTour($dia_diem_tour_id);
        }
        header('Location: ' . BASEURL . '?act=xoa_dia_diem&tour_id=' . ($_GET['tour_id'] ?? 0));
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
        $chinhSachIds = $_POST['chinh_sach_ids'] ?? [];
        $ghi_chu = $_POST['ghi_chu'] ?? null;
        $allSuccess = true;

        if (!empty($chinhSachIds) && is_array($chinhSachIds)) {
            foreach ($chinhSachIds as $csId) {
                $result = $this->modelCreate->luuChinhSachTour($tour_id, $csId, $ghi_chu);
                if (!$result) {
                    $allSuccess = false;
                }
            }
        } else {
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

    public function hanh_khach_add($dat_tour_id)
    {
        $data = $this->modelGet->getDatTourById($dat_tour_id);
        require_once './views/admin/hanh_khach_add.php';
    }

    public function hanh_khach_save($id)
    {
        $khach_hang_data = $_POST['hanh_khach'] ?? [];
        foreach ($khach_hang_data as $khach_hang) {
            $data = [
                'dat_tour_id' => $id,
                'ho_ten' => $khach_hang['ho_ten'] ?? '',
                'ngay_sinh' => $khach_hang['ngay_sinh'] ?? '',
                'gioi_tinh' => $khach_hang['gioi_tinh'] ?? '',
                // Dòng này được hợp nhất từ 2 nhánh (sử dụng cccd và sdt như nhánh HEAD, nhưng giữ tên trường mới nhất là so_cmnd và so_dien_thoai để tránh mất mát dữ liệu nhập nếu form dùng tên đó)
                'cccd' => $khach_hang['cccd'] ?? $khach_hang['so_cmnd'] ?? '',
                'sdt' => $khach_hang['sdt'] ?? $khach_hang['so_dien_thoai'] ?? '',
                'email' => $khach_hang['email'] ?? '',
                'ghi_chu' => $khach_hang['ghi_chu'] ?? null,
                'quoc_tich' => $khach_hang['quoc_tich'] ?? '',
            ];
            // Loại bỏ các key không dùng trong Model (ví dụ: 'so_cmnd', 'so_dien_thoai') nếu Model chỉ nhận 'cccd', 'sdt'
            // Chèn vào DB
            $this->modelCreate->createHanhKhach($data);
        }
        header('Location: ' . BASEURL . '?act=dat_coc&dat_tour_id=' . $id);
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

        $success_count = 0;

        foreach ($hanh_khach_data as $hanh_khach_input) {
            $data = [
                'dat_tour_id' => $dat_tour_id,
                'ho_ten'      => $hanh_khach_input['ho_ten'] ?? '',
                'ngay_sinh'   => $hanh_khach_input['ngay_sinh'] ?? null,
                'cccd'        => $hanh_khach_input['cccd'] ?? null,
                'sdt'         => $hanh_khach_input['sdt'] ?? null,
                'ghi_chu'     => $hanh_khach_input['ghi_chu'] ?? null,
                // Bổ sung các trường thiếu nếu có (email, gioi_tinh, quoc_tich) để tránh lỗi DB
                'gioi_tinh'   => $hanh_khach_input['gioi_tinh'] ?? '',
                'email'       => $hanh_khach_input['email'] ?? null,
                'quoc_tich'   => $hanh_khach_input['quoc_tich'] ?? '',
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
            $_SESSION['success'] = "Đã cập nhật/thêm $success_count hành khách thành công!";
        } else {
            $_SESSION['error'] = "Không có hành khách nào được cập nhật.";
        }

        header('Location: ' . BASEURL . '?act=dattourlist'); // Chuyển hướng về danh sách hoặc chi tiết tour
        exit();
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

            header('location:' . BASEURL . '?act=lay_dich_vu');
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
        $ghiChu = $_POST['ghi_chu'] ?? null;

        if (!is_array($dichVuIds) || empty($dichVuIds)) {
            header('Location: ' . BASEURL . '?act=gandichvu&tour_id=' . $tour_id . '&error=no_services_selected');
            exit;
        }

        $allSuccess = true;
        foreach ($dichVuIds as $dich_vu_id) {
            $result = $this->modelCreate->ganDichVuTour($tour_id, $dich_vu_id, $ghiChu);
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

    public function storeUser()
    {
        $data = $_POST;
        $this->modelCreate->storeUser($data);

        header("Location: " . BASEURL . "?act=user_list");
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
}
