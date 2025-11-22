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

    public function deleteNCC($id){
        $sql = "UPDATE nhacungcap
SET isdelete = 1  -- Sửa giá trị cột isdelete thành 1
WHERE ncc_id = :id; -- Tại nhà cung cấp có ID tương ứng";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
     public function deleteLichTrinh($lich_trinh_id)
    {
        $sql = "DELETE FROM LichTrinh WHERE lich_trinh_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $lich_trinh_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
      public function deleteHDV($id){
            $sql = "DELETE FROM `huongdanvien` WHERE `hdv_id` = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
        public function xoaChinhSachKhoiTour($tour_chinh_sach_id){
    $sql = "DELETE FROM TourChinhSach WHERE tour_chinh_sach_id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $tour_chinh_sach_id, PDO::PARAM_INT);

    return $stmt->execute();
}

    }




?>