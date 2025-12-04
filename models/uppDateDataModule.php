<?php
class uppDateDataModuleDataModule
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }
    public function uppDateTour($tour_id, $data)
    {
        $sql = "UPDATE tour SET 
                ten = :ten,
                danh_muc_id = :danh_muc_id,
                mo_ta_ngan = :mo_ta_ngan,
                mo_ta = :mo_ta,
                gia_co_ban = :gia_co_ban,
                thoi_luong_mac_dinh = :thoi_luong_mac_dinh,
                diem_khoi_hanh = :diem_khoi_hanh,
                hoat_dong = :hoat_dong
            WHERE tour_id = :tour_id AND trang_thai_xoa = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten', $data['ten']);
        $stmt->bindParam(':danh_muc_id', $data['danh_muc_id']);
        $stmt->bindParam(':mo_ta_ngan', $data['mo_ta_ngan']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':gia_co_ban', $data['gia_co_ban']);
        $stmt->bindParam(':thoi_luong_mac_dinh', $data['thoi_luong_mac_dinh']);
        $stmt->bindParam(':diem_khoi_hanh', $data['diem_khoi_hanh']);
        $stmt->bindParam(':hoat_dong', $data['hoat_dong']);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function capNhatDiaDiemTour($dia_diem_tour_id, $dia_diem_id, $ghi_chu = null)
    {
        $sql = "UPDATE DiaDiemTour
            SET dia_diem_id = :dia_diem_id,
                ghi_chu = :ghi_chu
            WHERE dia_diem_tour_id = :dia_diem_tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dia_diem_id', $dia_diem_id, PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $ghi_chu);
        $stmt->bindParam(':dia_diem_tour_id', $dia_diem_tour_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateNCC($id, $data)
    {
        $sql = "UPDATE nhacungcap 
                SET ten = :ten, lien_he = :lien_he, dia_chi = :dia_chi, ma_so_thue = :mst
                WHERE ncc_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten', $data['ten']);
        $stmt->bindParam(':lien_he', $data['lien_he']);
        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':mst', $data['ma_so_thue']);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    public function updateLichTrinh($lich_trinh_id, $data)
    {
        $sql = "UPDATE LichTrinh SET
                    ngay_thu = :ngay_thu,
                    tieu_de = :tieu_de,
                    noi_dung = :noi_dung
                WHERE lich_trinh_id = :lich_trinh_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ngay_thu', $data['ngay_thu'], PDO::PARAM_INT);
        $stmt->bindParam(':tieu_de', $data['tieu_de']);
        $stmt->bindParam(':noi_dung', $data['noi_dung']);
        $stmt->bindParam(':lich_trinh_id', $lich_trinh_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateDatTour($dat_tour_id, $data)
    {

        // SQL UPDATE: Thiết lập các cột cần cập nhật.
        $sql = "UPDATE `DatTour` SET
                `khach_hang_id` = :khach_hang_id, 
                `tour_id`       = :tour_id,
                `so_nguoi`      = :so_nguoi, 
                `loai`          = :loai, 
                `trang_thai`    = :trang_thai, 
                `nguon`         = :nguon, 
                `ghi_chu`       = :ghi_chu, 
                `ngay_cap_nhat` = NOW()
            WHERE `dat_tour_id` = :dat_tour_id";

        try {
            $stmt = $this->conn->prepare($sql);

            // --- Xử lý dữ liệu và Binding các tham số ---
            $id_to_update = (int)$dat_tour_id;
            $khach_hang_id = (int)($data['khach_hang_id'] ?? 0);
            $tour_id = (int)($data['tour_id'] ?? 0);
            $so_nguoi = (int)($data['so_nguoi'] ?? 0);

            // Binding ID WHERE
            $stmt->bindParam(':dat_tour_id', $id_to_update, PDO::PARAM_INT);

            // Binding dữ liệu cập nhật
            $stmt->bindParam(':khach_hang_id', $khach_hang_id, PDO::PARAM_INT);
            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->bindParam(':so_nguoi', $so_nguoi, PDO::PARAM_INT);
            $stmt->bindParam(':loai', $data['loai']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            $stmt->bindParam(':nguon', $data['nguon']);

            // Binding: Ghi chú (Cho phép NULL)
            $ghi_chu = $data['ghi_chu'] ?? null;
            $stmt->bindParam(':ghi_chu', $ghi_chu, $ghi_chu === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Lỗi UPDATE DatTour: " . $e->getMessage());
            return false;
        }
    }
    public function capNhatDichVu($dich_vu_id, $data)
    {
        $sql = "UPDATE `dichvuncc`
            SET `loai_dich_vu` = :loai_dich_vu,
                `ma` = :ma,
                `ten_dich_vu` = :ten_dich_vu,
                `mo_ta` = :mo_ta,
                `gia_mac_dinh` = :gia_mac_dinh,
                `don_vi` = :don_vi,
                `ncc_id` = :ncc_id
            WHERE `dich_vu_id` = :dich_vu_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':loai_dich_vu', $data['loai_dich_vu']);
        $stmt->bindParam(':ma', $data['ma']);
        $stmt->bindParam(':ten_dich_vu', $data['ten_dich_vu']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':gia_mac_dinh', $data['gia_mac_dinh']);
        $stmt->bindParam(':don_vi', $data['don_vi']);
        $stmt->bindParam(':ncc_id', $data['ncc_id'], PDO::PARAM_INT);
        $stmt->bindParam(':dich_vu_id', $dich_vu_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function xuLydichVuKhoiTour($gia_dv_id, $data)
    {
        $sql = "UPDATE TourDichVu
            SET gia = :gia,
                da_xac_nhan = :da_xac_nhan,
                ghi_chu = :ghi_chu
            WHERE gia_dv_id = :gia_dv_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':gia', $data['gia'], PDO::PARAM_INT);
        $stmt->bindParam(':da_xac_nhan', $data['da_xac_nhan'], PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':gia_dv_id', $gia_dv_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateGiaDichVu($id, $gia, $da_xac_nhan, $ghi_chu)
    {
        $sql = "UPDATE gia_dv_tour 
                SET gia = ?, da_xac_nhan = ?, ghi_chu = ?
                WHERE gia_dv_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$gia, $da_xac_nhan, $ghi_chu, $id]);
    }


    // Hàm updateHanhKhach đã được thêm vào từ HEAD
    public function updateHanhKhach($hanh_khach_id, $data)
    {
        $sql = "UPDATE `hanhkhachlist` SET 
                ho_ten = :ho_ten, 
                gioi_tinh = :gioi_tinh, 
                so_giay_to = :so_giay_to,   -- Tên cột trong DB là so_giay_to
                ngay_sinh = :ngay_sinh, 
                lien_he = :lien_he,         -- Tên cột trong DB là lien_he
                yeu_cau_ca_nhan = :yeu_cau_ca_nhan -- Tên cột trong DB là yeu_cau_ca_nhan
            WHERE hanh_khach_id = :hanh_khach_id";

        $stmt = $this->conn->prepare($sql);

        // Binding dữ liệu từ mảng $data (được map từ controller)
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':gioi_tinh', $data['gioi_tinh']);
        $stmt->bindParam(':so_giay_to', $data['so_giay_to']);
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':lien_he', $data['lien_he']);
        $stmt->bindParam(':yeu_cau_ca_nhan', $data['yeu_cau_ca_nhan']);
        $stmt->bindParam(':hanh_khach_id', $hanh_khach_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateUser($id, $data)
    {
        $sql = "UPDATE nguoidung
                SET email=?, ho_ten=?, so_dien_thoai=?, vai_tro_id=?, trang_thai=?
                WHERE nguoi_dung_id=?";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['email'],
            $data['ho_ten'],
            $data['so_dien_thoai'],
            $data['vai_tro_id'],
            $data['trang_thai'],
            $id
        ]);
    }
    // File: models/uppDateDataModule.php


    public function updateSchedule($lich_id, $data)
    {
        $sql = "UPDATE `lichkhoihanh` SET
                    `trang_thai` = :trang_thai,
                    `ngay_bat_dau` = :ngay_bat_dau,
                    `ngay_ket_thuc` = :ngay_ket_thuc,
                    `hieu_luc_tu` = :hieu_luc_tu,
                    `hieu_luc_den` = :hieu_luc_den,
                    `ghi_chu` = :ghi_chu
                WHERE `lich_id` = :lich_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trang_thai', $data['trang_thai']);
        $stmt->bindParam(':ngay_bat_dau', $data['ngay_bat_dau']);
        $stmt->bindParam(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindParam(':hieu_luc_tu', $data['hieu_luc_tu']);
        $stmt->bindParam(':hieu_luc_den', $data['hieu_luc_den']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':lich_id', $lich_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateThongTinCoBanUser($nguoi_dung_id, $ho_ten, $email, $so_dien_thoai)
    {
        $sql = "UPDATE nguoidung 
            SET ho_ten = :ho_ten, 
                email = :email, 
                so_dien_thoai = :so_dien_thoai 
            WHERE nguoi_dung_id = :nguoi_dung_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ho_ten', $ho_ten);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':so_dien_thoai', $so_dien_thoai);
        $stmt->bindParam(':nguoi_dung_id', $nguoi_dung_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    public function updateHDV($id, $data)
    {
        // Cập nhật các trường thông tin dựa trên cấu trúc bảng mới
        // Lưu ý: Đã đổi tên các cột kinh_nghiem_lam_viec, ngon_ngu_su_dung cho khớp DB
        $sql = "UPDATE `huongdanvien` 
            SET `ngay_sinh` = :ngay_sinh,
                `gioi_tinh` = :gioi_tinh,
                `dia_chi_lien_he` = :dia_chi_lien_he,
                `chung_chi_chuyen_mon` = :chung_chi_chuyen_mon,
                `ngon_ngu_su_dung` = :ngon_ngu_su_dung,
                `kinh_nghiem_lam_viec` = :kinh_nghiem_lam_viec,
                `tinh_trang_suc_khoe` = :tinh_trang_suc_khoe,
                `tinh_trang_hoat_dong` = :tinh_trang_hoat_dong,
                `ngay_cap_nhat` = NOW() 
            " . (!empty($data['anh_dai_dien']) ? ", `anh_dai_dien` = :anh_dai_dien" : "") . "
            WHERE `hdv_id` = :id";

        $stmt = $this->conn->prepare($sql);

        // Bind param
        // Lưu ý: Key của mảng $data nên khớp với tên cột hoặc form name của bạn
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':gioi_tinh', $data['gioi_tinh']);
        $stmt->bindParam(':dia_chi_lien_he', $data['dia_chi_lien_he']);
        $stmt->bindParam(':chung_chi_chuyen_mon', $data['chung_chi_chuyen_mon']);
        $stmt->bindParam(':ngon_ngu_su_dung', $data['ngon_ngu_su_dung']);
        $stmt->bindParam(':kinh_nghiem_lam_viec', $data['kinh_nghiem_lam_viec']);
        $stmt->bindParam(':tinh_trang_suc_khoe', $data['tinh_trang_suc_khoe']);
        $stmt->bindParam(':tinh_trang_hoat_dong', $data['tinh_trang_hoat_dong']);

        // ID dùng cho mệnh đề WHERE
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Chỉ bind ảnh đại diện nếu có dữ liệu (tránh lỗi "number of bound variables does not match")
        if (!empty($data['anh_dai_dien'])) {
            $stmt->bindParam(':anh_dai_dien', $data['anh_dai_dien']);
        }

        return $stmt->execute();
    }
    // File: models/uppDateDataModule.php

    public function updateKhachHangDetail($khach_hang_id, $data)
    {
        // Giả định bảng khachhang có các cột này. Bạn hãy kiểm tra lại DB của mình.
        $sql = "UPDATE khachhang SET 
            dia_chi = :dia_chi,
            cccd = :cccd
            WHERE khach_hang_id = :khach_hang_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':cccd', $data['cccd']);
        $stmt->bindParam(':khach_hang_id', $khach_hang_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function cap_nhat_yeu_cau($id, $noi_dung, $muc_do, $da_chuan_bi, $ghi_chu)
    {
        $sql = "UPDATE yeucaukhachhang 
                SET noi_dung = '$noi_dung', 
                    muc_do_uu_tien = '$muc_do', 
                    da_chuan_bi = '$da_chuan_bi', 
                    ghi_chu = '$ghi_chu' 
                WHERE id = $id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }
}
