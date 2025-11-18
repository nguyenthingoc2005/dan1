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
  public function getAllNCC(){
        $sql = "SELECT * FROM nhacungcap ORDER BY ncc_id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNCCById($id){
        $sql = "SELECT * FROM nhacungcap WHERE ncc_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllLichTrinhTour($tour_id)
    {
        $sql = "SELECT 
                lt.lich_trinh_id,
                lt.tour_id,
                t.ten AS ten_tour,
                lt.ngay_thu,
                lt.tieu_de,
                lt.noi_dung
            FROM LichTrinh lt
            JOIN Tour t ON lt.tour_id = t.tour_id
            WHERE lt.tour_id = :tour_id
            ORDER BY lt.ngay_thu ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin chi tiết một lịch trình theo ID
    public function getLichTrinhById($lich_trinh_id)
    {
        $sql = "SELECT * FROM LichTrinh WHERE lich_trinh_id = :lich_trinh_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_trinh_id', $lich_trinh_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tour liên quan của một lịch trình
    public function getTourByLichTrinh($lich_trinh_id)
    {
        $sql = "SELECT t.* 
                FROM tour t
                JOIN LichTrinh lt ON t.tour_id = lt.tour_id
                WHERE lt.lich_trinh_id = :lich_trinh_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_trinh_id', $lich_trinh_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getHDVById($id){
            $sql = "SELECT * FROM `huongdanvien` WHERE `hdv_id` = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
          public function getAllHDV(){
            $sql = "SELECT * FROM `huongdanvien`";
            $stmt= $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function getChinhSachByTourId($tour_id){
    $sql = "SELECT 
                tcs.tour_chinh_sach_id,
                cs.chinh_sach_id,
                cs.ten,
                cs.noi_dung,
                cs.loai,
                cs.hoat_dong,
                cs.ngay_tao,
                tcs.ghi_chu
            FROM TourChinhSach tcs
            JOIN ChinhSach cs ON tcs.chinh_sach_id = cs.chinh_sach_id
            WHERE tcs.tour_id = :tour_id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getDanhSachChinhSach(){
    $sql = "SELECT 
                chinh_sach_id,
                ten,
                noi_dung,
                loai,
                hoat_dong,
                ngay_tao
            FROM ChinhSach
            ORDER BY ngay_tao DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


        
    }



?>