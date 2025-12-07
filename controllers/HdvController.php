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

    // 1. Hiển thị chi tiết khách hàng
    public function chitiet_khach_hang()
    {
        // Lấy ID từ URL (Ví dụ: ?act=chitiet&id=1)
        // Lưu ý: id này chính là hanh_khach_id
        if (isset($_GET['id'])) {
            $hanh_khach_id = $_GET['id'];
        } else {
            $hanh_khach_id = 0;
        }

        // Gọi model lấy thông tin từ bảng hanhkhachlist
        $khachhang = $this->modelGet->thongtinkhachhang($hanh_khach_id);

        // Gọi model lấy các yêu cầu của khách này
        // echo "<pre>";
        // print_r($khachhang); // In dữ liệu ra màn hình xem có gì không
        // echo "</pre>";
        // die();
        $listYeuCau = $this->modelGet->layyeucaukhachhang($hanh_khach_id);
        // var_dump($listYeuCau);

        require_once 'views/hdv/xem_chitiet_khach_hang.php';
    }


    public function themyeucau()
    {
        require_once './views/hdv/add_yeu_cau.php';
    }

    // ============================
    // LƯU YÊU CẦU (INSERT)
    // ============================
    public function luuyeucau()
    {

        $this->modelCreate->insert($_POST);
        header("Location: " . BASEURL . "?act=dashboard_HDV");
        exit();
    }

    // ============================
    // FORM SỬA YÊU CẦU
    // ============================
    public function suayeucau()
    {
        $id  = $_GET['id'];
        $row = $this->modelGet->laychitietyeucaukhachhang($id);

        include './views/hdv/edit_yeu_cau.php';
    }

    public function capnhatyeucau()
    {

        $id = $_POST['yeu_cau_id'];
        $this->modelUpdate->update($id, $_POST);
        header("Location: " . BASEURL . "?act=dashboard_HDV");
        exit();
    }


    // ============================
    // XÓA YÊU CẦU
    // ============================
    public function xoayeucau()
    {

        $id = $_GET['id'];
        $this->modelGet->xoayeucaukhachhang($id);

        header("Location: " . BASEURL . "?act=dashboard_HDV");
        exit();
    }









    // 5. Xử lý đăng nhập
    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['mat_khau'];

            $user = $this->modelGet->checkLogin($email, $password);

            if ($user) {
                $_SESSION['user'] = $user;
                $_SESSION['user']['vai_tro_id'] = $user['vai_tro_id'];

                // Lưu ý: Alert sẽ không hiện khi dùng header location ngay sau đó
                header("Location: " . BASEURL . "?act=dashboard");
                exit;
            } else {
                echo "<script>alert('Đăng nhập thất bại, kiểm tra lại email/mật khẩu'); window.location.href='" . BASEURL . "?act=login';</script>";
                exit();
            }
        };
    }

    // 6. Đăng xuất
    public function logout()
    {
        unset($_SESSION['user']);
        header("Location: " . BASEURL . "?act=login");
        exit;
    }
}
