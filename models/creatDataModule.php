<?php
class creatDataModule
{
    public $conn;

    public function __construct()
    {
        // Giả định connectDB() trả về đối tượng PDO đã kết nối
        $this->conn = connectDB();
    }

    // ================== TOUR ==================
    public function createTour($data)
    {
        $sql = "INSERT INTO `tour`(`ten`, `danh_muc_id`, `mo_ta_ngan`, `mo_ta`, `gia_co_ban`, `thoi_luong_mac_dinh`, `diem_khoi_hanh`, `hoat_dong`, `ngay_tao`) 
                VALUES (:ten, :danh_muc_id, :mo_ta_ngan, :mo_ta, :gia_co_ban, :thoi_luong_mac_dinh, :diem_khoi_hanh, :hoat_dong, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten', $data['ten']);
        $stmt->bindParam(':danh_muc_id', $data['danh_muc_id']);
        $stmt->bindParam(':mo_ta_ngan', $data['mo_ta_ngan']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':gia_co_ban', $data['gia_co_ban']);
        $stmt->bindParam(':thoi_luong_mac_dinh', $data['thoi_luong_mac_dinh']);
        $stmt->bindParam(':diem_khoi_hanh', $data['diem_khoi_hanh']);
        $stmt->bindParam(':hoat_dong', $data['hoat_dong']);
        if ($stmt->execute()) {
            // Nếu insert thành công, trả về ID vừa sinh ra
            return $this->conn->lastInsertId();
        } else {
            // Nếu thất bại
            return false;
        }
    }

    // ================== ĐỊA ĐIỂM TOUR ==================
    public function ganDiaDiemChoTour($tour_id, $dia_diem_id, $ghi_chu = null)
    {
        $sql = "INSERT INTO DiaDiemTour (tour_id, dia_diem_id, ghi_chu)
             VALUES (:tour_id, :dia_diem_id, :ghi_chu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':dia_diem_id', $dia_diem_id, PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $ghi_chu);

        return $stmt->execute();
    }

    // ================== NHÀ CUNG CẤP (NCC) ==================
    public function createNCC($data)
    {
        $sql = "INSERT INTO nhacungcap (ten, lien_he, dia_chi, ma_so_thue, ngay_tao)
                 VALUES (:ten, :lien_he, :dia_chi, :mst, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten', $data['ten']);
        $stmt->bindParam(':lien_he', $data['lien_he']);
        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':mst', $data['ma_so_thue']);
        return $stmt->execute();
    }

    // ================== LỊCH TRÌNH ==================
    public function createLichTrinh($data)
    {
        $sql = "INSERT INTO LichTrinh 
                (tour_id, ngay_thu, tieu_de, noi_dung) 
                VALUES 
                (:tour_id, :ngay_thu, :tieu_de, :noi_dung)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $data['tour_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ngay_thu', $data['ngay_thu'], PDO::PARAM_INT);
        $stmt->bindParam(':tieu_de', $data['tieu_de']);
        $stmt->bindParam(':noi_dung', $data['noi_dung']);

        return $stmt->execute();
    }

    // ================== ĐỊA ĐIỂM LỊCH TRÌNH ==================
    public function ganDiaDiemChoLichTrinh($lich_trinh_id, $dia_diem_tour_id, $mo_ta = null, $thu_tu = 1)
    {
        $sql = "INSERT INTO DiaDiemLichTrinh 
                    (lich_trinh_id, dia_diem_tour_id, mo_ta, thu_tu)
                VALUES 
                    (:lich_trinh_id, :dia_diem_tour_id, :mo_ta, :thu_tu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_trinh_id', $lich_trinh_id, PDO::PARAM_INT);
        $stmt->bindParam(':dia_diem_tour_id', $dia_diem_tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':mo_ta', $mo_ta);
        $stmt->bindParam(':thu_tu', $thu_tu, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // ================== HƯỚNG DẪN VIÊN (HDV) ==================
    public function createhdvid($data)
    {
        $sql = "INSERT INTO huongdanvien(nguoi_dung_id)VALUE(:nguoi_dung_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":nguoi_dung_id", $data);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function addHDV($data)
    {
        // 1. Câu lệnh SQL khớp 100% với Database và biến truyền vào
        // Lưu ý: Đã loại bỏ ho_ten, email... và thêm các trường mới
        $sql = "INSERT INTO `huongdanvien` (
                `nguoi_dung_id`, 
                `ngay_sinh`, 
                `gioi_tinh`, 
                `dia_chi_lien_he`, 
                `anh_dai_dien`, 
                `chung_chi_chuyen_mon`, 
                `ngon_ngu_su_dung`, 
                `kinh_nghiem_lam_viec`, 
                `tinh_trang_suc_khoe`, 
                `tinh_trang_hoat_dong`, 
                `ngay_tao`
            ) VALUES (
                :nguoi_dung_id, 
                :ngay_sinh, 
                :gioi_tinh, 
                :dia_chi_lien_he, 
                :anh_dai_dien, 
                :chung_chi_chuyen_mon, 
                :ngon_ngu_su_dung, 
                :kinh_nghiem_lam_viec, 
                :tinh_trang_suc_khoe, 
                :tinh_trang_hoat_dong, 
                NOW()
            )";

        $stmt = $this->conn->prepare($sql);

        // 2. Bind dữ liệu (Phải khớp chính xác với các dấu : ở trên)
        $stmt->bindParam(':nguoi_dung_id', $data['nguoi_dung_id']);
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':gioi_tinh', $data['gioi_tinh']);
        $stmt->bindParam(':dia_chi_lien_he', $data['dia_chi_lien_he']);
        $stmt->bindParam(':anh_dai_dien', $data['anh_dai_dien']);
        $stmt->bindParam(':chung_chi_chuyen_mon', $data['chung_chi_chuyen_mon']);
        $stmt->bindParam(':ngon_ngu_su_dung', $data['ngon_ngu_su_dung']);
        $stmt->bindParam(':kinh_nghiem_lam_viec', $data['kinh_nghiem_lam_viec']);
        $stmt->bindParam(':tinh_trang_suc_khoe', $data['tinh_trang_suc_khoe']);
        $stmt->bindParam(':tinh_trang_hoat_dong', $data['tinh_trang_hoat_dong']);

        // Lưu ý: Không cần bind :ngay_tao vì đã dùng hàm NOW() trực tiếp trong SQL

        return $stmt->execute();
    }

    // ================== CHÍNH SÁCH TOUR ==================
    public function luuChinhSachTour($tour_id, $chinh_sach_id, $ghi_chu = null)
    {
        $sql = "INSERT INTO TourChinhSach (tour_id, chinh_sach_id, ghi_chu)
             VALUES (:tour_id, :chinh_sach_id, :ghi_chu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':chinh_sach_id', $chinh_sach_id, PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $ghi_chu);

        return $stmt->execute();
    }

    // ================== ĐẶT TOUR ==================
    public function createDatTour($data)
    {
        $sql = "INSERT INTO `DatTour` (
                `khach_hang_id`, 
                `tour_id`, 
                `so_nguoi`, 
                `loai`, 
                `trang_thai`, 
                `nguon`, 
                `ghi_chu`, 
                `ngay_tao`
            ) VALUES (
                :khach_hang_id, 
                :tour_id, 
                :so_nguoi, 
                :loai, 
                :trang_thai, 
                :nguon, 
                :ghi_chu, 
                NOW()
            )";

        try {
            $stmt = $this->conn->prepare($sql);

            // --- Xử lý dữ liệu và Binding các tham số ---

            $khach_hang_id = (int) ($data['khach_hang_id'] ?? 0);
            $tour_id = (int) ($data['tour_id'] ?? 0);
            $so_nguoi = (int) ($data['so_nguoi'] ?? 0);

            $stmt->bindParam(':khach_hang_id', $khach_hang_id, PDO::PARAM_INT);
            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->bindParam(':so_nguoi', $so_nguoi, PDO::PARAM_INT);
            $stmt->bindParam(':loai', $data['loai']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            $stmt->bindParam(':nguon', $data['nguon']);

            $ghi_chu = $data['ghi_chu'] ?? null;
            $stmt->bindParam(':ghi_chu', $ghi_chu, $ghi_chu === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Lỗi CREATE DatTour: " . $e->getMessage());
            return false;
        }
    }

    // ================== HÀNH KHÁCH (Đã hợp nhất) ==================
    public function createHanhKhach($data)
    {
        $sql = "INSERT INTO `hanhkhachlist` (
                    dat_tour_id, 
                    ho_ten, 
                    gioi_tinh, 
                    so_giay_to,      -- Thay cho cccd
                    ngay_sinh, 
                    lien_he,         -- Thay cho sdt
                    yeu_cau_ca_nhan  -- Thay cho ghi_chu
                ) VALUES (
                    :dat_tour_id, 
                    :ho_ten, 
                    :gioi_tinh, 
                    :so_giay_to, 
                    :ngay_sinh, 
                    :lien_he, 
                    :yeu_cau_ca_nhan
                )";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':dat_tour_id', $data['dat_tour_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':gioi_tinh', $data['gioi_tinh']);
        $stmt->bindParam(':so_giay_to', $data['so_giay_to']);
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':lien_he', $data['lien_he']);
        $stmt->bindParam(':yeu_cau_ca_nhan', $data['yeu_cau_ca_nhan']);

        return $stmt->execute();
    }

    // ================== ĐẶT CỌC (Đã hợp nhất) ==================
    public function createDatCoc(array $data)
    {
        $sql = "INSERT INTO DatCoc (
                dat_tour_id, so_tien, tien_te, hinh_thuc, trang_thai, ngay_dat, ghi_chu
            ) VALUES (
                :dat_tour_id, :so_tien, :tien_te, :hinh_thuc, :trang_thai, :ngay_dat, :ghi_chu
            )";

        try {
            $stmt = $this->conn->prepare($sql);

            // Xử lý giá trị NULL (các trường Có NULL trong DB)
            $datTourId = $data['dat_tour_id'] ?? null;
            $soTien = $data['so_tien'] ?? null;
            $hinhThuc = $data['hinh_thuc'] ?? null;
            $trangThai = $data['trang_thai'] ?? null;
            $ngayDat = $data['ngay_dat'] ?? null;
            $ghiChu = $data['ghi_chu'] ?? null;

            // Bind các tham số
            $stmt->bindParam(':dat_tour_id', $datTourId, $datTourId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':so_tien', $soTien);
            // Thay thế bindParam bằng bindValue cho dòng này
            $stmt->bindValue(':tien_te', $data['tien_te'] ?? 'VND', PDO::PARAM_STR);
            $stmt->bindParam(':hinh_thuc', $hinhThuc, $hinhThuc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':trang_thai', $trangThai, $trangThai === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':ngay_dat', $ngayDat, $ngayDat === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':ghi_chu', $ghiChu, $ghiChu === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Trả về ID vừa được chèn (dat_coc_id)
                return $this->conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Ghi lại lỗi và trả về false
            error_log("Lỗi tạo Đặt Cọc: " . $e->getMessage());
            // var_dump($e->getMessage()); // Dùng để debug, nên xóa trong môi trường production
            return false;
        }
    }

    // ================== DỊCH VỤ (Đã chuẩn hóa) ==================
    public function themDichVu($data)
    {
        // Chuẩn hóa để sử dụng $this->conn và nhận mảng $data
        $sql = "INSERT INTO dichvuncc (loai_dich_vu, ma,ten_dich_vu, mo_ta, gia_mac_dinh, don_vi, ncc_id) 
                VALUES (:loai_dich_vu, :ma, :ten_dich_vu, :mo_ta, :gia_mac_dinh, :don_vi, :ncc_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':loai_dich_vu', $data['loai_dich_vu']);
        $stmt->bindParam(':ma', $data['ma']);
        $stmt->bindParam(':ten_dich_vu', $data['ten_dich_vu']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':gia_mac_dinh', $data['gia_mac_dinh']);
        $stmt->bindParam(':don_vi', $data['don_vi']);
        $stmt->bindParam(':ncc_id', $data['ncc_id'], PDO::PARAM_INT);

        return $stmt->execute();
    }
    // public function themDichVu($db, $loai_dich_vu, $ma, $ten_dich_vu, $mo_ta, $gia_mac_dinh, $don_vi, $ncc_id)
    // {
    //     $stmt = $db->prepare("INSERT INTO dichvuncc (loai_dich_vu, ma, ten_dich_vu, mo_ta, gia_mac_dinh, don_vi, ncc_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    //     return $stmt->execute([$loai_dich_vu, $ma, $ten_dich_vu, $mo_ta, $gia_mac_dinh, $don_vi, $ncc_id]);
    // }
    public function ganDichVuTour($tour_id, $dich_vu_id, $ghi_chu = null)
    {
        $sql = "INSERT INTO dv_tour (tour_id, dich_vu_id, ghi_chu)
             VALUES (:tour_id, :dich_vu_id, :ghi_chu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':dich_vu_id', $dich_vu_id, PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $ghi_chu);

        return $stmt->execute();
    }
    // 1. Sửa hàm storeUser để trả về lastInsertId
    public function storeUser($data)
    {
        $sql = "INSERT INTO nguoidung (email, mat_khau, ho_ten, so_dien_thoai, vai_tro_id, trang_thai, ngay_tao)
            VALUES (:email, :mat_khau, :ho_ten, :so_dien_thoai, :vai_tro_id, :trang_thai, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':mat_khau', $data['mat_khau']); // Lưu ý: Nên mã hóa password bằng password_hash()
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':vai_tro_id', $data['vai_tro_id']);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // TRẢ VỀ ID VỪA TẠO
        }
        return false;
    }

    // 2. Thêm hàm createKhachHang (Hàm addHDV đã có sẵn trong file của bạn rồi)
    public function createkhachhangid($nguoi_dung_id)
    {
        $sql = "INSERT INTO KhachHang (nguoi_dung_id, ngay_tao) 
            VALUES (:nguoi_dung_id, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nguoi_dung_id', $nguoi_dung_id);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // TRẢ VỀ ID VỪA TẠO
        }
        return false;
    }
}
