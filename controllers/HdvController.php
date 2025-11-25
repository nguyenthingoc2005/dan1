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
        include './views/hdv/home.php';
    }
    public function loginForm()
    {
        include './views/hdv/login.php';
    }


    public function loginProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['mat_khau'];
            // Kiểm tra đăng nhập
            $user = $this->modelGet->checkLogin($email, $password);

            if ($user) {
                // Chuyển hướng thẳng đến dashboard
                header("Location: " . BASEURL . "?act=dashboard");
                echo "<script>alert('Đăng nhập thành công');</script>";
                $_SESSION['user'] = $user;
                $_SESSION['user']['vai_tro_id'] = $user['vai_tro_id'];
                exit;
            } else {
                // Đăng nhập thất bại
                
                header('Location:' . BASEURL . '?act=login');
                echo "<script>alert('Đăng nhập thất bại, vui lòng kiểm tra lại email và mật khẩu');</script>";
                exit();
            }
        }
        ;
    }
    public function logout()
    {
        unset($_SESSION['user']);
        header("Location: " . BASEURL . "?act=login");
        exit;
    }

}
?>