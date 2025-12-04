<?php


class HdvController
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

    public function home()
    {
        require_once './views/hdv/home.php';
    }

    public function loginForm()
    {
        require_once './views/hdv/login.php';
    }


    public function xemtour()
    {
        $tours = $this->modelGet->getAllTours();

        require_once './views/hdv/xemtour.php';
    }
    public function xem_chitiet_tour()
    {
        $tourId = $_GET['tour_id'];
        $tourDetails = $this->modelGet->getTourById($tourId);
        $listKhachHang = $this->modelGet->getListKhachHangByLichId($tourId);


        require_once './views/hdv/xem_chitiet_tour.php';
    }
    public function luu_diem_danh()
    {
        // 1. Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASEURL . "?act=danh_sach_tour");
            exit;
        }

        // 2. Lấy dữ liệu từ Form
        $lich_trinh_id = isset($_POST['lich_trinh_id']) ? intval($_POST['lich_trinh_id']) : 0;
        $tour_id = isset($_POST['tour_id']) ? intval($_POST['tour_id']) : 0;

        // 3. Lấy ID HDV (Bạn đang dùng logic gán trực tiếp user_id, dựa trên debug id=3 là hợp lý)
        $hdv_id = $_SESSION['user']['nguoi_dung_id'] ?? 0;

        // Lấy dữ liệu checkbox và ghi chú
        $statusArr = $_POST['status'] ?? [];
        $noteArr = $_POST['note'] ?? [];

        // 4. Tiến hành Lưu
        if ($lich_trinh_id > 0 && $hdv_id > 0) {
            try {
                // Lấy danh sách khách để duyệt (để xử lý cả trường hợp bỏ tick - vắng mặt)
                $allPassengers = $this->modelGet->getPassengersBySchedule($lich_trinh_id);

                foreach ($allPassengers as $kh) {
                    $kh_id = $kh['hanh_khach_id'];

                    // Logic: Có trong mảng status là 1 (Có mặt), không có là 0 (Vắng)
                    $da_den = isset($statusArr[$kh_id]) ? 1 : 0;
                    $ghi_chu = isset($noteArr[$kh_id]) ? trim($noteArr[$kh_id]) : '';

                    // Gọi Model lưu
                    $this->modelGet->saveDiemDanh($lich_trinh_id, $kh_id, $hdv_id, $da_den, $ghi_chu);
                }

                // 5. CHUYỂN HƯỚNG (REDIRECT)
                // Sửa lại act khớp với index.php của bạn: 'xem_chitiet_tour'
                if ($tour_id > 0) {
                    header("Location: " . BASEURL . "?act=xem_chitiet_tour&tour_id=" . $tour_id . "&msg=success");
                } else {
                    // Dự phòng nếu mất tour_id thì về danh sách
                    header("Location: " . BASEURL . "?act=xemtour");
                }
                exit;
            } catch (Exception $e) {
                // Nếu lỗi database thì hiện ra xem
                echo "Lỗi Database: " . $e->getMessage();
                die();
            }
        } else {
            echo "Lỗi: Thiếu thông tin Lịch trình hoặc HDV.";
            die();
        }
    }
    // --- 1. HIỂN THỊ CHI TIẾT KHÁCH HÀNG ---
    public function chitiet_khach_hang()
    {
        $hanh_khach_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($hanh_khach_id > 0) {
            // Lấy thông tin khách
            $khachhang = $this->modelGet->getPassengerDetail($hanh_khach_id);

            // [MỚI] Lấy danh sách yêu cầu phục vụ của khách này
            $listYeuCau = $this->modelGet->getServiceRequestsByCustomer($hanh_khach_id);

            if ($khachhang) {
                require_once 'views/hdv/xem_chitiet_khach_hang.php';
            } else {
                echo "Không tìm thấy khách hàng.";
            }
        } else {
            header("Location: " . BASEURL . "?act=danh_sach_tour");
        }
    }

    // --- 2. XỬ LÝ LƯU YÊU CẦU ---
    public function luuyeucau()
    {
        // 1. Kiểm tra phương thức gửi
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASEURL);
            exit;
        }

        // 2. Lấy dữ liệu
        $yeu_cau_id = isset($_POST['yeu_cau_id']) ? intval($_POST['yeu_cau_id']) : 0;
        $hanh_khach_id = isset($_POST['hanh_khach_id']) ? intval($_POST['hanh_khach_id']) : 0;

        // Chuẩn bị dữ liệu để lưu
        $data = [
            ':noi_dung' => $_POST['noi_dung'] ?? '',
            ':muc_do' => $_POST['muc_do_uu_tien'] ?? 'trung_binh',
            ':da_chuan_bi' => isset($_POST['da_chuan_bi']) ? 1 : 0,
            ':ghi_chu' => $_POST['ghi_chu'] ?? ''
        ];

        // Nếu là Thêm mới (INSERT) thì cần thêm khóa ngoại
        if ($yeu_cau_id == 0) {
            $data[':dat_tour_id'] = $_POST['dat_tour_id'] ?? 0;
            $data[':hanh_khach_id'] = $hanh_khach_id;
        }

        // 3. Gọi Model lưu dữ liệu
        $this->modelGet->luu_yeu_cau($yeu_cau_id, $data);

        // 4. Chuyển hướng quay lại trang chi tiết khách hàng
        if ($hanh_khach_id > 0) {
            header("Location: " . BASEURL . "?act=chitiet_khach_hang&id=" . $hanh_khach_id . "&msg=saved");
        } else {
            // Dự phòng nếu mất ID khách
            header("Location: " . BASEURL . "?act=danh_sach_tour");
        }
        exit;
    }

    // --- XỬ LÝ XÓA YÊU CẦU ---
    public function xoayeucau()
    {
        // 1. Lấy ID yêu cầu cần xóa (req_id) và ID khách hàng (hk_id) để quay lại
        $yc_id = isset($_GET['req_id']) ? intval($_GET['yc_id']) : 0;
        $hk_id = isset($_GET['hk_id']) ? intval($_GET['hk_id']) : 0;

        // 2. Gọi Model xóa
        if ($yc_id > 0) {
            $this->modelGet->xoa_yeu_cau($yc_id);
        }

        // 3. Quay lại trang chi tiết khách hàng
        if ($hk_id > 0) {
            header("Location: " . BASEURL . "?act=xem_chitiet_khach_hang&id=" . $hk_id . "&msg=deleted");
        } else {
            // Dự phòng nếu mất ID khách thì về danh sách tour
            header("Location: " . BASEURL . "?act=danh_sach_tour");
        }
        exit;
    }




    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['mat_khau'];
            // Kiểm tra đăng nhập
            $user = $this->modelGet->checkLogin($email, $password);

            if ($user) {
                $_SESSION['user'] = $user;
                $_SESSION['user']['vai_tro_id'] = $user['vai_tro_id'];
                // print_r($_SESSION['user']);
                // die();
                // Chuyển hướng thẳng đến dashboard
                header("Location: " . BASEURL . "?act=dashboard");
                echo "<script>alert('Đăng nhập thành công');</script>";

                exit;
            } else {
                // Đăng nhập thất bại

                header('Location:' . BASEURL . '?act=login');
                echo "<script>alert('Đăng nhập thất bại, vui lòng kiểm tra lại email và mật khẩu');</script>";

                exit();
            }
        };
    }
    public function logout()
    {
        unset($_SESSION['user']);
        header("Location: " . BASEURL . "?act=login");
        exit;
    }
}
