<?php 
    class creatDataModule{
        public $conn;

        public function __construct(){
        $this->conn =   connectDB();
        }
       public function createTour($data){
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
    return $stmt->execute();
}
    public function ganDiaDiemChoTour($tour_id, $dia_diem_id, $ghi_chu = null){
    $sql = "INSERT INTO DiaDiemTour (tour_id, dia_diem_id, ghi_chu)
            VALUES (:tour_id, :dia_diem_id, :ghi_chu)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':dia_diem_id', $dia_diem_id, PDO::PARAM_INT);
    $stmt->bindParam(':ghi_chu', $ghi_chu);
    
    return $stmt->execute();
}


        
    }




?>