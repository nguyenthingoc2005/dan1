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
    if ($stmt->execute()) {
            // Nếu insert thành công, trả về ID vừa sinh ra
            return $this->conn->lastInsertId(); 
        } else {
            // Nếu thất bại
            return false; 
        }
}
 public function ganDiaDiemChoTour($tour_id, $dia_diem_id, $ghi_chu = null) {
    // Bổ sung 'thu_tu' (thứ tự) nếu bạn muốn lưu theo thứ tự chuyến đi
    $sql = "INSERT INTO DiaDiemTour (tour_id, dia_diem_id, ghi_chu)
             VALUES (:tour_id, :dia_diem_id, :ghi_chu)";
    
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':dia_diem_id', $dia_diem_id, PDO::PARAM_INT);
    $stmt->bindParam(':ghi_chu', $ghi_chu);
    
    return $stmt->execute();
}public function createNCC($data){
        $sql = "INSERT INTO nhacungcap (ten, lien_he, dia_chi, ma_so_thue, ngay_tao)
                VALUES (:ten, :lien_he, :dia_chi, :mst, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':ten', $data['ten']);
        $stmt->bindParam(':lien_he', $data['lien_he']);
        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':mst', $data['ma_so_thue']);
        return $stmt->execute();
    }
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
     public function addHDV($data){
            $sql = "INSERT INTO `huongdanvien`(`nguoi_dung_id`, `ho_ten`, `so_dien_thoai`, `email`, `kinh_nghiem`, `ngon_ngu`, `ngay_tao`) 
                    VALUES (:nguoi_dung_id, :ho_ten, :so_dien_thoai, :email, :kinh_nghiem, :ngon_ngu, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nguoi_dung_id', $data['nguoi_dung_id']);
            $stmt->bindParam(':ho_ten', $data['ho_ten']);
            $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':kinh_nghiem', $data['kinh_nghiem']);
            $stmt->bindParam(':ngon_ngu', $data['ngon_ngu']);
            return $stmt->execute();
        }
  // Hàm Model luuChinhSachTour của bạn (Chèn đơn lẻ)

public function luuChinhSachTour($tour_id, $chinh_sach_id, $ghi_chu = null){
    $sql = "INSERT INTO TourChinhSach (tour_id, chinh_sach_id, ghi_chu)
             VALUES (:tour_id, :chinh_sach_id, :ghi_chu)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->bindParam(':chinh_sach_id', $chinh_sach_id, PDO::PARAM_INT);
    $stmt->bindParam(':ghi_chu', $ghi_chu);

    return $stmt->execute();
}
public function createDatTour($data) {
        
        // SQL INSERT: Chỉ bao gồm các cột còn lại sau khi loại bỏ tong_tien và tien_te
        $sql = "INSERT INTO `DatTour` (
                    `khach_hang_id`, 
                    `so_nguoi`, 
                    `loai`, 
                    `trang_thai`, 
                    `nguon`, 
                    `ghi_chu`, 
                    `ngay_tao`
                ) VALUES (
                    :khach_hang_id, 
                    :so_nguoi, 
                    :loai, 
                    :trang_thai, 
                    :nguon, 
                    :ghi_chu, 
                    NOW()
                )";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            // Ép kiểu (dù đã làm trong Controller, nên làm lại để an toàn)
            $khach_hang_id = (int)$data['khach_hang_id'];
            $so_nguoi = (int)$data['so_nguoi'];

            // Binding các tham số
            $stmt->bindParam(':khach_hang_id', $khach_hang_id, PDO::PARAM_INT);
            $stmt->bindParam(':so_nguoi', $so_nguoi, PDO::PARAM_INT);
            $stmt->bindParam(':loai', $data['loai']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            $stmt->bindParam(':nguon', $data['nguon']);
            $stmt->bindParam(':ghi_chu', $data['ghi_chu']);

            if ($stmt->execute()) {
                // Nếu insert thành công, trả về ID vừa sinh ra
                return $this->conn->lastInsertId(); 
            } else {
                // Nếu thất bại
                return false; 
            }

        } catch (PDOException $e) {
            // Ghi log lỗi
            error_log("Lỗi CREATE DatTour: " . $e->getMessage());
            return false;
        }
    }
public function createHanhKhach($data){
        $sql = "INSERT INTO `hanhkhachlist`(`dat_tour_id`, `ho_ten`, `cccd`, `ngay_sinh`, `so_ghe`, `ghi_chu`) 
                VALUES (:dat_tour_id, :ho_ten, :cccd, :ngay_sinh, :so_ghe, :ghi_chu)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dat_tour_id', $data['dat_tour_id'], PDO::PARAM_INT);
        $stmt->bindParam(':ho_ten', $data['ho_ten']);
        $stmt->bindParam(':cccd', $data['cccd']);
        $stmt->bindParam(':ngay_sinh', $data['ngay_sinh']);
        $stmt->bindParam(':so_ghe', $data['so_ghe']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        return $stmt->execute();


        
    }
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

        // Bind các tham số (giả định $data['tien_te'] luôn có giá trị mặc định là 'VND')
        $stmt->bindParam(':dat_tour_id', $datTourId, $datTourId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindParam(':so_tien', $soTien); 
        $stmt->bindParam(':tien_te', $data['tien_te'], PDO::PARAM_STR);
        $stmt->bindParam(':hinh_thuc', $hinhThuc, $hinhThuc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':trang_thai', $trangThai, $trangThai === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':ngay_dat', $ngayDat, $ngayDat === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':ghi_chu', $ghiChu, $ghiChu === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $a=$stmt->execute();
        // var_dump());;
        die;
        // 1. Thực thi
        // if () {
        //      // 2. Trả về ID vừa được chèn (dat_coc_id)
        //     return $this->conn->lastInsertId(); 
        // } else {
            
        //     return false;
        // }
        
    } catch (PDOException $e) {
        // Ghi lại lỗi và trả về false
        error_log("Lỗi tạo Đặt Cọc: " . $e->getMessage());
        var_dump($e->getMessage());
        return false;
    }
}
    }

?>