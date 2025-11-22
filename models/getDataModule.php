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
            WHERE tour.trang_thai_xoa = 0";
    
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getTourById($tour_id){
    $sql = "SELECT * FROM tour WHERE tour_id = :tour_id AND trang_thai_xoa = 0";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function getAggregatedTourDetail($tour_id) {
    $sql = "SELECT 
        -- 1. Thông tin Tour Cơ bản (t) & Danh Mục (dmt)
        t.tour_id, t.ten AS ten_tour, t.mo_ta, t.mo_ta_ngan,
        t.gia_co_ban, 
        t.thoi_luong_mac_dinh AS thoi_gian, -- Đặt bí danh là 'thoi_gian' cho khớp View cũ
        dmt.ten AS loai_tour_ten, -- Tên Danh mục làm Loai Tour
        
        -- 2. Thông tin Chính sách (cs, tcs)
        cs.chinh_sach_id, cs.ten AS ten_chinh_sach, cs.loai AS loai_chinh_sach, tcs.ghi_chu AS cs_ghi_chu,
        
        -- 3. Thông tin Địa điểm (dd, qg, ddt)
        dd.dia_diem_id, dd.ten AS ten_diadiem, qg.ten AS quoc_gia_diadiem, dd.mo_ta AS dd_mo_ta, ddt.ghi_chu AS dd_ghi_chu,
        
        -- 4. Thông tin Lịch trình (lt)
        lt.lich_trinh_id, lt.ngay_thu, lt.tieu_de AS tieu_de_lt, lt.noi_dung AS noi_dung_lt
        
    FROM Tour t
    LEFT JOIN DanhMucTour dmt ON t.danh_muc_id = dmt.danh_muc_id
    
    LEFT JOIN TourChinhSach tcs ON t.tour_id = tcs.tour_id
    LEFT JOIN ChinhSach cs ON tcs.chinh_sach_id = cs.chinh_sach_id
    
    LEFT JOIN DiaDiemTour ddt ON t.tour_id = ddt.tour_id
    LEFT JOIN DiaDiem dd ON ddt.dia_diem_id = dd.dia_diem_id
    LEFT JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id
    
    LEFT JOIN LichTrinh lt ON t.tour_id = lt.tour_id
    
    WHERE t.tour_id = :tour_id
    ORDER BY lt.ngay_thu ASC, cs.chinh_sach_id ASC, dd.dia_diem_id ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
    $stmt->execute();
    $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rawData)) {
        return null;
    }

    $tourDetail = [];
    $cs_ids = []; 
    $dd_ids = []; 
    $lt_ids = []; 

    foreach ($rawData as $row) {
        // 1. Gán thông tin Tour Cơ bản (Chỉ cần làm 1 lần)
        if (empty($tourDetail)) {
            $tourDetail = [
                'tour_id' => $row['tour_id'],
                'ten' => $row['ten_tour'],
                'mo_ta' => $row['mo_ta'],
                // Ánh xạ tên cột mới sang tên cũ cho khớp View
                'gia' => $row['gia_co_ban'], 
                'thoi_gian' => $row['thoi_gian'], 
                'loai_tour' => $row['loai_tour_ten'], // Lấy từ DanhMucTour
                'phuong_tien' => 'N/A', // Không có cột này, đặt là N/A hoặc cần JOIN thêm DV_tour
                
                'chinh_sach' => [],
                'dia_diem' => [],
                'lich_trinh' => [],
            ];
        }

        // 2. Gán Chính sách
        if ($row['chinh_sach_id'] !== null && !in_array($row['chinh_sach_id'], $cs_ids)) {
            $tourDetail['chinh_sach'][] = [
                'ten' => $row['ten_chinh_sach'],
                'loai' => $row['loai_chinh_sach'],
                'ghi_chu' => $row['cs_ghi_chu']
            ];
            $cs_ids[] = $row['chinh_sach_id'];
        }

        // 3. Gán Địa điểm
        if ($row['dia_diem_id'] !== null && !in_array($row['dia_diem_id'], $dd_ids)) {
            $tourDetail['dia_diem'][] = [
                'ten_diadiem' => $row['ten_diadiem'],
                'quoc_gia' => $row['quoc_gia_diadiem'],
                'mo_ta' => $row['dd_mo_ta'],
                'ghi_chu' => $row['dd_ghi_chu']
            ];
            $dd_ids[] = $row['dia_diem_id'];
        }

        // 4. Gán Lịch trình
        if ($row['lich_trinh_id'] !== null && !in_array($row['lich_trinh_id'], $lt_ids)) {
            $tourDetail['lich_trinh'][] = [
                'ngay_thu' => $row['ngay_thu'],
                'tieu_de' => $row['tieu_de_lt'],
                'noi_dung' => $row['noi_dung_lt']
            ];
            $lt_ids[] = $row['lich_trinh_id'];
        }
    }

    // Sắp xếp Lịch trình theo Ngày Thứ (đảm bảo)
    usort($tourDetail['lich_trinh'], function($a, $b) {
        return $a['ngay_thu'] <=> $b['ngay_thu'];
    });

    return $tourDetail;
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
        $sql = "SELECT * FROM nhacungcap WHERE isdelete=0 ORDER BY ncc_id DESC ";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNCCById($id){
        $sql = "SELECT * FROM nhacungcap WHERE ncc_id = :id AND isdelete=0";
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
    public function getAllDatTour(){
    $sql = "SELECT
    -- Chọn các cột cần thiết từ DatTour
    DT.dat_tour_id,
    DT.so_nguoi,
    DT.trang_thai AS trang_thai_dat_tour,
    DT.ngay_tao AS ngay_dat_tour,

    -- Thông tin khách hàng
    KH.khach_hang_id,
    ND.ho_ten AS ten_khach_hang,
    KH.cccd,

    -- Thông tin lịch khởi hành (LEFT JOIN vì có thể chưa gán lịch)
    LKH.lich_id,
    LKH.ngay_bat_dau,
    LKH.ngay_ket_thuc,

    -- Thông tin Tour (LEFT JOIN vì Tour được nối qua LKH, có thể NULL)
    T.tour_id,
    T.ten AS ten_tour,
    T.mo_ta_ngan

FROM
    DatTour DT
INNER JOIN
    KhachHang KH ON DT.khach_hang_id = KH.khach_hang_id
INNER JOIN
    NguoiDung ND ON KH.nguoi_dung_id = ND.nguoi_dung_id -- Cần NguoiDung để lấy ho_ten khách hàng

LEFT JOIN 
    LichKhoiHanh LKH ON DT.lich_id = LKH.lich_id

LEFT JOIN 
    Tour T ON LKH.tour_id = T.tour_id

WHERE
    DT.isdelete = 0"; // Điều kiện lọc: Chỉ lấy đơn đặt tour KHÔNG bị xóa mềm
    
    try {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Xử lý lỗi (ví dụ: ghi log)
        error_log("Lỗi khi lấy danh sách đơn đặt tour: " . $e->getMessage());
        return [];
    }
}
    public function getAllKhachHang() {
    $sql = "SELECT
        KH.khach_hang_id,
        KH.cccd,
        KH.dia_chi,
        KH.ngay_tao AS ngay_tao_khach_hang,

        -- Thông tin chi tiết từ bảng NguoiDung
        ND.nguoi_dung_id,
        ND.email,
        ND.ho_ten,
        ND.so_dien_thoai,
        ND.trang_thai AS trang_thai_tai_khoan
        
    FROM
        KhachHang KH
    INNER JOIN
        NguoiDung ND ON KH.nguoi_dung_id = ND.nguoi_dung_id
    
    -- Lọc theo trạng thái xóa mềm: chỉ lấy người dùng KHÔNG bị xóa (isdelete = 0)
    WHERE 
        ND.isdelete = 0
        
    ORDER BY
        KH.khach_hang_id DESC";

    try {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Xử lý lỗi (ghi log hoặc ném exception)
        error_log("Lỗi khi lấy danh sách khách hàng: " . $e->getMessage());
        return [];
    }
}

    public function getDatTourById($dat_tour_id){
        $sql = "SELECT * FROM `dattour` WHERE dat_tour_id = :dat_tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dat_tour_id', $dat_tour_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // public function getDatTourDetail(int $dat_tour_id) {
    //     // Truy vấn chính sử dụng LEFT JOIN cho tất cả các bảng 1-1 hoặc 1-nhiều (SUM)
    //     $sql = "SELECT
    //         -- 1. Thông tin Đặt Tour chính
    //         DT.dat_tour_id, DT.lich_id, DT.so_nguoi, DT.tong_tien, DT.tien_te, 
    //         DT.trang_thai AS trang_thai_dat_tour, DT.nguon, DT.ngay_tao, DT.ghi_chu AS ghi_chu_dat_tour, DT.loai,
            
    //         -- 2. Thông tin Khách hàng đặt tour (LEFT JOIN)
    //         ND.ho_ten AS ten_khach_hang, ND.so_dien_thoai, ND.email, 
    //         KH.cccd, KH.dia_chi,
            
    //         -- 3. Thông tin Tour & Lịch Khởi Hành (LEFT JOIN)
    //         T.tour_id, T.ten AS ten_tour, T.mo_ta_ngan,
    //         LKH.ngay_bat_dau, LKH.ngay_ket_thuc, LKH.gia_mac_dinh, LKH.tien_te AS tien_te_lich,
            
    //         -- 4. Thông tin Đặt Cọc (LEFT JOIN)
    //         SUM(DC.so_tien) AS tong_dat_coc
            
    //     FROM
    //         DatTour DT
        
    //     -- LEFT JOIN cho KhachHang và NguoiDung
    //     LEFT JOIN
    //         KhachHang KH ON DT.khach_hang_id = KH.khach_hang_id
    //     LEFT JOIN
    //         NguoiDung ND ON KH.nguoi_dung_id = ND.nguoi_dung_id
        
    //     -- LEFT JOIN cho Lịch Khởi Hành và Tour
    //     LEFT JOIN 
    //         LichKhoiHanh LKH ON DT.lich_id = LKH.lich_id
    //     LEFT JOIN 
    //         Tour T ON LKH.tour_id = T.tour_id

    //     -- LEFT JOIN cho Đặt Cọc
    //     LEFT JOIN
    //         DatCoc DC ON DT.dat_tour_id = DC.dat_tour_id AND DC.trang_thai = 'confirmed' 

    //     WHERE
    //         DT.dat_tour_id = :id 
    //         -- AND DT.isdelete = 0 -- Giả sử trường này cần được dùng nếu có trong DB
    //     GROUP BY 
    //         DT.dat_tour_id";

        
    //     // --- Lấy dữ liệu Dạng Mảng (Hành Khách, Dịch Vụ Thêm) ---
    //     $sql_hanh_khach = "SELECT ho_ten, cccd, ngay_sinh, so_ghe, ghi_chu FROM HanhKhachlist WHERE dat_tour_id = :id";
    //     $sql_dv_them = "SELECT DVT.ten, DVT.gia, DVT.don_vi, DVT.mo_ta, DVTD.so_luong, DVTD.tong_tien AS tong_tien_dv 
    //                     FROM DichVuThemDat DVTD
    //                     INNER JOIN DichVuThem DVT ON DVTD.dv_them_id = DVT.dv_them_id
    //                     WHERE DVTD.dat_tour_id = :id";
        
    //     try {
    //         // Thực hiện truy vấn chính (dữ liệu 1-1)
    //         $stmt_main = $this->conn->prepare($sql);
    //         $stmt_main->bindParam(':id', $dat_tour_id, PDO::PARAM_INT);
    //         $stmt_main->execute();
    //         $detail = $stmt_main->fetch(PDO::FETCH_ASSOC);

    //         if ($detail) {
    //             // Thực hiện truy vấn phụ (dữ liệu 1-nhiều)
                
    //             // 1. Hành Khách
    //             $stmt_hk = $this->conn->prepare($sql_hanh_khach);
    //             $stmt_hk->bindParam(':id', $dat_tour_id, PDO::PARAM_INT);
    //             $stmt_hk->execute();
    //             $detail['hanh_khach_list'] = $stmt_hk->fetchAll(PDO::FETCH_ASSOC);
                
    //             // 2. Dịch Vụ Thêm
    //             $stmt_dv = $this->conn->prepare($sql_dv_them);
    //             $stmt_dv->bindParam(':id', $dat_tour_id, PDO::PARAM_INT);
    //             $stmt_dv->execute();
    //             $detail['dich_vu_them'] = $stmt_dv->fetchAll(PDO::FETCH_ASSOC);
    //         }
            
    //         return $detail;

    //     } catch (PDOException $e) {
    //         error_log("Lỗi khi lấy chi tiết Đơn Đặt Tour: " . $e->getMessage());
    //         return false;
    //     }
    // }


    }

?>