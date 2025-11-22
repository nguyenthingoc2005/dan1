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
            WHERE tour_id = :tour_id AND trang_thai_xoa = 1";

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
    public function updateHDV($id, $data)
    {
        $sql = "UPDATE `huongdanvien` 
                    SET `ho_ten` = :ho_ten, `so_dien_thoai` = :so_dien_thoai, `email` = :email, 
                        `kinh_nghiem` = :kinh_nghiem, `ngon_ngu` = :ngon_ngu 
                    WHERE `hdv_id` = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':kinh_nghiem', $data['kinh_nghiem']);
        $stmt->bindParam(':ngon_ngu', $data['ngon_ngu']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
    public function capNhatDichVu($db, $dich_vu_id, $data)
    {
        $sql = "UPDATE `dichvuncc`
            SET `loai_dich_vu` = :loai_dich_vu,
                `ma` = :ma,
                `mo_ta` = :mo_ta,
                `gia_mac_dinh` = :gia_mac_dinh,
                `don_vi` = :don_vi,
                `ncc_id` = :ncc_id
            WHERE `dich_vu_id` = :dich_vu_id";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':loai_dich_vu', $data['loai_dich_vu']);
        $stmt->bindParam(':ma', $data['ma']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':gia_mac_dinh', $data['gia_mac_dinh']);
        $stmt->bindParam(':don_vi', $data['don_vi']);
        $stmt->bindParam(':ncc_id', $data['ncc_id'], PDO::PARAM_INT);
        $stmt->bindParam(':dich_vu_id', $dich_vu_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    // Hàm updateHanhKhach đã được thêm vào từ HEAD
    public function updateHanhKhach($hanh_khach_id, $data) {
        $sql = "UPDATE `hanhkhachlist` SET 
                ho_ten = :ho_ten, ngay_sinh = :ngay_sinh, cccd = :cccd, 
                sdt = :sdt, ghi_chu = :ghi_chu 
            WHERE hanh_khach_id = :hanh_khach_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':cccd', $data['cccd']);
        $stmt->bindParam(':sdt', $data['sdt']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':hanh_khach_id', $hanh_khach_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>