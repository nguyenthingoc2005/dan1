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
        $tourId = $_GET['id'];
        $tourDetails = $this->modelGet->getTourById($tourId);

        require_once './views/hdv/xem_chitiet_tour.php';
    }
    public function list_Khach_hang()
    {
        $khachhang = $this->modelGet->getAllKhachHang();

        require_once './views/hdv/list_khachhang.php';
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
