<?php

$act = $_GET['act'] ?? '/';

match ($act) {
    'login' => (new HdvController())->loginForm(),
    'login_process' => (new HdvController())->loginProcess(),
    'home'=>(new HdvController())->home(),
    'logout'=>(new HdvController())->logout(),
    'dashboard' => (new HdvController())->home(),
    default=>(new HdvController())->home()

};
if(!isset($_SESSION['user']) && !in_array($act, ['login','login_process'])) { 
    header("Location:" . BASEURL . "?act=login");
    exit;
}

?>