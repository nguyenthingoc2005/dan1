<?php 
    class getDataModule{
        public $conn;

        public function __construct(){
        $this->conn =   connectDB();
        }
      public function getAllTours(){
    $sql = "SELECT 
                tour.*, 
                danhmuctour.ten AS ten_danh_muc 
            FROM tour 
            JOIN danhmuctour ON tour.danh_muc_id = danhmuctour.danh_muc_id 
            WHERE tour.trang_thai_xoa = 1";
    
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getTourById($tour_id){
    $sql = "SELECT * FROM tour WHERE tour_id = :tour_id AND trang_thai_xoa = 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

        public function getAllDanh_muc_tour(){
                $sql = "SELECT * FROM `danhmuctour`";
                $stmt= $this->conn->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

           public function getDiaDiemByTourId($tour_id){
    $sql = "SELECT 
                ddt.dia_diem_tour_id ,
                dd.dia_diem_id,
                dd.ten AS ten_diadiem,
                qg.ten AS quoc_gia,
                dd.mo_ta,
                had.url AS hinh_anh
            FROM DiaDiemTour ddt
            JOIN DiaDiem dd ON ddt.dia_diem_id = dd.dia_diem_id
            JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id
            LEFT JOIN (
                SELECT dia_diem_id, MIN(url) AS url
                FROM HinhAnhDiaDiem
                GROUP BY dia_diem_id
            ) had ON dd.dia_diem_id = had.dia_diem_id
            WHERE ddt.tour_id = :tour_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getAllDiaDiem(){
    $sql = "SELECT 
                dd.dia_diem_id,
                dd.ten,
                dd.mo_ta,
                qg.ten AS quoc_gia,
                dd.ngay_tao
            FROM DiaDiem dd
            JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getDiaDiemTourById($dia_diem_tour_id){
    $sql = "SELECT 
                ddt.dia_diem_tour_id,
                ddt.tour_id,
                ddt.dia_diem_id,
                ddt.ghi_chu,
                dd.ten AS ten_dia_diem,
                qg.ten AS quoc_gia
            FROM DiaDiemTour ddt
            JOIN DiaDiem dd ON ddt.dia_diem_id = dd.dia_diem_id
            JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id
            WHERE ddt.dia_diem_tour_id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $dia_diem_tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

        
    }



?>