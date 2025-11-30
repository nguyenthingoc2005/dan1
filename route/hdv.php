<?php

$act = $_GET['act'] ?? '/';

if (!isset($_SESSION['user']) && !in_array($act, ['login', 'login_process'])) {
    header("Location:" . BASEURL . "?act=login");
    exit;
}
match ($act) {
    'login' => (new HdvController())->loginForm(),
    'login_process' => (new HdvController())->loginProcess(),
    'home' => (new HdvController())->home(),
    'logout' => (new HdvController())->logout(),
    'dashboard_HDV' => (new HdvController())->home(),
    'xemtour' => (new HdvController())->xemtour(),
    'xem_chitiet_tour' => (new HdvController())->xem_chitiet_tour(),
    'list_Khach_hang' => (new HdvController())->list_Khach_hang(),
    default => (new HdvController())->home()
};

