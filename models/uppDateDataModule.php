<?php 
    class uppDateDataModuleDataModule{
        public $conn;

        public function __construct(){
        $this->conn =   connectDB();
        }
public function uppDateTour($tour_id, $data){
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

    public function capNhatDiaDiemTour($dia_diem_tour_id, $dia_diem_id, $ghi_chu = null){
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
}



?>