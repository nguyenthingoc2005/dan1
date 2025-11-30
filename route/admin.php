<?php
// Đây là file index.php hoặc admin.php của bạn
$act = $_GET['act'] ?? '/';

// Đảm bảo class AdminController đã được include/autoload ở đây
// require_once 'AdminController.php'; 

match ($act) {
    // ================== TOUR & CHI TIẾT ==================
    'admin' => (new AdminController())->index(),
    'tour_list' => (new AdminController())->showTours(),
    'addtour' => (new AdminController())->formaddtour(),
    'createtour' => (new AdminController())->createtour(),
    'uppdatetour' => (new AdminController())->uppdatetour($_GET['tour_id'] ?? null),
    'uppdatetour1' => (new AdminController())->uppdatetour1($_GET['tour_id'] ?? null),
    'chitiettour' => (new AdminController())->getAggregatedTourData($_GET['tour_id'] ?? null),
    'deletetour' => (new AdminController())->deletetour($_GET['tour_id'] ?? null),


    // ================== ĐỊA ĐIỂM TOUR ==================
    'diadiem' => (new AdminController())->diadiem($_GET['tour_id'] ?? null),
    'gan_diadiem' => (new AdminController())->gan_diadiem($_GET['tour_id'] ?? null),
    'luu_gan_diadiem' => (new AdminController())->luu_gan_diadiem($_GET['tour_id'] ?? null),
    'xoa_dia_diem' => (new AdminController())->xoa_dia_diem_tour($_GET['dia_diem_tour_id'] ?? null, $_GET['tour_id'] ?? null),

    // Route trùng lặp
    'sua_diadiemtour' => (new AdminController())->sua_diadiem_tour($_GET['dia_diem_tour_id'] ?? null, $_GET['tour_id'] ?? null),
    'capnhat_diadiem_tour' => (new AdminController())->capnhat_diadiem_tour($_GET['dia_diem_tour_id'] ?? null),

    // ================== NHÀ CUNG CẤP (NCC) ==================
    'ncc_list' => (new AdminController())->showncc(),
    'ncc_add' => (new AdminController())->formaddncc(),
    'ncc_create' => (new AdminController())->createncc(),
    'ncc_update' => (new AdminController())->formupdatencc($_GET['id'] ?? null),
    'ncc_update_save' => (new AdminController())->updatencc($_GET['id'] ?? null),
    'ncc_delete' => (new AdminController())->deletencc($_GET['id'] ?? null),


    // ================== LỊCH TRÌNH ==================
    // Route này thường không cần tour_id, nhưng giữ lại nếu bạn dùng để lọc danh sách
    'listlichtrinhtour' => (new AdminController())->listlichtrinhtour(),
    'addlichtrinh' => (new AdminController())->formAddLichTrinh($_GET['tour_id'] ?? null),
    'createlichtrinh' => (new AdminController())->createLichTrinh(), // Không cần tour_id ở đây vì nó nằm trong POST
    'editlichtrinh' => (new AdminController())->editLichTrinh($_GET['lich_trinh_id'] ?? null),
    'updatelichtrinh' => (new AdminController())->capnhatLichTrinh($_GET['lich_trinh_id'] ?? null),
    'deletelichtrinh' => (new AdminController())->deleteLichTrinh($_GET['lich_trinh_id'] ?? null),

    // ================== HƯỚNG DẪN VIÊN (HDV) ==================
    'hdv' => (new AdminController())->hdv(),
    'addhdv' => (new AdminController())->addHDV(),
    'createhdv' => (new AdminController())->createHDV(),
    'deletehdv' => (new AdminController())->deleteHDV($_GET['id'] ?? null),
    'edithdv' => (new AdminController())->editHDV($_GET['id'] ?? null),
    'updatehdv' => (new AdminController())->updateHDV($_GET['id'] ?? null),

    // ================== CHÍNH SÁCH TOUR ==================
    'chinhsach' => (new AdminController())->chinhsach($_GET['tour_id'] ?? null),
    'luu_chinh_sach_tour' => (new AdminController())->luuChinhSachTour($_GET['tour_id'] ?? null),
    'xoa_chinh_sach_tour' => (new AdminController())->xoaChinhSachTour($_GET['id'] ?? null),

    // ================== ĐẶT TOUR & HÀNH KHÁCH ==================
    'dattourlist' => (new AdminController())->dattourlist(),
    'dat_tour_add' => (new AdminController())->dat_tour_add(),
    'dat_tour_save' => (new AdminController())->dat_tour_save(),
    'hanh_khach_add' => (new AdminController())->hanh_khach_add($_GET['dat_tour_id'] ?? null),
    'dat_tour_detail' => (new AdminController())->dat_tour_detail($_GET['dat_tour_id'] ?? null),
    'hanh_khach_save' => (new AdminController())->hanh_khach_save($_GET['dat_tour_id'] ?? null),
    'dat_coc' => (new AdminController())->dat_coc($_GET['dat_tour_id'] ?? null),
    'dat_coc_save' => (new AdminController())->dat_coc_save($_GET['dat_tour_id'] ?? null),
    'dat_tour_edit' => (new AdminController())->dat_tour_edit($_GET['dat_tour_id'] ?? null),
    'dat_tour_update' => (new AdminController())->dat_tour_update($_GET['dat_tour_id'] ?? null),
    'dat_tour_delete' => (new AdminController())->dat_tour_delete($_GET['dat_tour_id'] ?? null),

    // **ROUTES MỚI HỢP NHẤT TỪ CONTROLLER**
    'hanh_khach_edit' => (new AdminController())->hanh_khach_edit($_GET['dat_tour_id'] ?? null),
    'hanh_khach_update' => (new AdminController())->hanh_khach_update($_GET['dat_tour_id'] ?? null),

    // ================== DỊCH VỤ ==================
    'lay_dich_vu' => (new AdminController())->layDichVu(),
    'lay_dich_vu_ncc' => (new AdminController())->layDichVuNCC($_GET['ncc_id'] ?? null),
    'them_dich_vu' => (new AdminController())->themDichVu(),
    'xoa_dich_vu' => (new AdminController())->xoaDichVu($_GET['id'] ?? null),
    'capnhat_dich_vu' => (new AdminController())->capNhatDichVu($_GET['id'] ?? null),
    'gandichvu' => (new AdminController())->ganDichVuTour($_GET['tour_id'] ?? null),
    'luuGanDichVuTour' => (new AdminController())->luuGanDichVuTour($_GET['tour_id'] ?? null),
    'XoaGanDichVuTour' => (new AdminController())->xoaGanDichVuTour($_GET['dich_vu_id'] ?? null, $_GET['tour_id'] ?? null),
    'logout' => (new AdminController())->logout(),

    'user_list'     => (new AdminController())->userList(),
    'user_create'   => (new AdminController())->createUser(),
    'user_store'    => (new AdminController())->storeUser(),
    'user_edit'     => (new AdminController())->editUser(),
    'user_update'   => (new AdminController())->updateUser(),
    'user_delete'   => (new AdminController())->deleteUser(),

    // --- Route cho HDV (Bổ sung) ---
    'hdv_detail_add'   => (new AdminController())->formAddHDVDetail(),
    'store_hdv_detail' => (new AdminController())->storeHDVDetail(),
    // 'hdv' -> Đã có sẵn, hiển thị danh sách HDV

    // --- Route cho KHÁCH HÀNG (Bổ sung) ---
    'khachhang_detail_add'   => (new AdminController())->formAddKhachHangDetail(),
    'store_khachhang_detail' => (new AdminController())->storeKhachHangDetail(),
    'khach_hang_list'        => (new AdminController())->listKhachHang(), // Tạo view danh sách KH nếu chưa có

    default => (new AdminController())->index(),
};
