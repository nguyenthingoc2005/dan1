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
    public function chitiet_khach_hang()
    {
        // 1. Lấy ID khách hàng từ URL
        // (Ở view danh sách, link sẽ là: ?act=chitiet_khach_hang&id=...)
        $hanh_khach_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($hanh_khach_id > 0) {
            // 2. Gọi Model lấy thông tin chi tiết
            $khachhang = $this->modelGet->getPassengerDetail($hanh_khach_id);

            // 3. Kiểm tra dữ liệu
            if ($khachhang) {
                // Nếu có dữ liệu thì gọi View hiển thị
                require_once './views/hdv/xem_chitiet_khach_hang.php';
            } else {
                // Nếu ID không tồn tại trong DB
                echo "Không tìm thấy thông tin hành khách này.";
            }
        } else {
            // Nếu không truyền ID hoặc ID = 0
            header("Location: " . BASEURL . "?act=danh_sach_tour");
            exit;
        }
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
