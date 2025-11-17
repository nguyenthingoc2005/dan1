<?php 
    class deleteDataModule{
        public $conn;

        public function __construct(){
        $this->conn =   connectDB();
        }
       public function deleteTour($tour_id){
    $sql = "UPDATE tour SET trang_thai_xoa = 0 WHERE tour_id = :tour_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function xoaDiaDiemKhoiTour( $dia_diem_tour_id){
    $sql = "DELETE FROM DiaDiemTour
            WHERE dia_diem_tour_id = :dia_diem_tour_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':dia_diem_tour_id', $dia_diem_tour_id, PDO::PARAM_INT);

    return $stmt->execute();
}

    }




?>