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
    'luu_diem_danh' => (new HdvController())->luu_diem_danh(),
    'chitiet_khach_hang' => (new HdvController())->chitiet_khach_hang(),
    'sua_yeu_cau' => (new HdvController())->sua_yeu_cau(),
    'luu_yeu_cau' => (new HdvController())->luuyeucau(),
    'xoa_yeu_cau' => (new HdvController())->xoayeucau(),
    default => (new HdvController())->home()
};
