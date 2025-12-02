<?php
class getDataModule
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }
    public function getAllTours()
    {
        // 1. Lấy NGUOI_DUNG_ID từ session
        $nguoi_dung_id = $_SESSION['user']['nguoi_dung_id'];

        $sql = "
    SELECT
        t.tour_id,                  -- <== ĐÃ THÊM: Lấy tour_id tại đây
        lkh.lich_id,                -- (Mình thêm luôn cái này, phòng khi bạn cần link vào chi tiết lịch trình cụ thể)
        t.ten AS Ten_Tour,
        lkh.ngay_bat_dau AS Ngay_Khoi_Hanh,
        lkh.ngay_ket_thuc AS Ngay_Ket_Thuc,
        pchdv.trang_thai AS Trang_Thai_Phan_Cong,
        nd.ho_ten AS Ten_HDV
    FROM
        phanconghdv pchdv
    JOIN
        lichkhoihanh lkh ON pchdv.lich_id = lkh.lich_id
    JOIN
        tour t ON lkh.tour_id = t.tour_id
    JOIN
        huongdanvien hdv ON pchdv.hdv_id = hdv.hdv_id
    JOIN
        nguoidung nd ON hdv.nguoi_dung_id = nd.nguoi_dung_id
    WHERE
        hdv.nguoi_dung_id = :nguoi_dung_id 
    ORDER BY
        lkh.ngay_bat_dau;
    ";

        // 2. Sử dụng Prepared Statements
        $stmt = $this->conn->prepare($sql);

        // 3. Gán giá trị
        $stmt->bindParam(':nguoi_dung_id', $nguoi_dung_id, PDO::PARAM_INT);

        // 4. Thực thi
        $stmt->execute();

        // 5. Trả về kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Lấy danh sách hành khách theo ID lịch khởi hành
     * Hàm này JOIN giữa bảng khách hàng và bảng đặt tour để lọc theo lịch trình
     */
    public function getPassengersBySchedule($lich_id)
    {
        $sql = "
            SELECT 
                hk.hanh_khach_id,
                hk.ho_ten,
                hk.gioi_tinh,
                hk.ngay_sinh,
                hk.so_giay_to,
                hk.lien_he,
                hk.yeu_cau_ca_nhan,
                dt.dat_tour_id,
                dt.trang_thai as trang_thai_don
            FROM 
                hanhkhachlist hk
            JOIN 
                dattour dt ON hk.dat_tour_id = dt.dat_tour_id
            WHERE 
                dt.lich_id = :lich_id 
                AND dt.isdelete = 0
            ORDER BY 
                hk.ho_ten ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_id', $lich_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTourById($tour_id)
    {
        // BƯỚC 1: Lấy thông tin chi tiết Tour (Chỉ 1 dòng)
        $sql = "SELECT * FROM tour WHERE tour_id = :tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->execute();

        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu không tìm thấy tour, trả về false ngay
        if (!$tour) return false;

        // BƯỚC 2: Lấy danh sách lịch trình của Tour đó (Nhiều dòng)
        // Sắp xếp theo ngày thứ tự tăng dần
        $sql_lt = "SELECT * FROM lichtrinh WHERE tour_id = :tour_id ORDER BY ngay_thu ASC";
        $stmt_lt = $this->conn->prepare($sql_lt);
        $stmt_lt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt_lt->execute();

        $lichtrinh = $stmt_lt->fetchAll(PDO::FETCH_ASSOC);

        // BƯỚC 3: Gộp lịch trình vào mảng Tour
        // Lúc này biến $tour sẽ có thêm key 'lich_trinh' chứa danh sách các ngày
        $tour['lich_trinh'] = $lichtrinh;

        return $tour;
    }


    public function saveDiemDanh($lich_trinh_id, $hanh_khach_id, $hdv_id, $da_den, $ghi_chu)
    {
        // BƯỚC 1: Kiểm tra xem hành khách này đã được điểm danh trong ngày này chưa
        $sqlCheck = "SELECT diem_danh_id FROM diemdanhkhach 
                 WHERE lich_trinh_id = :lich_trinh_id 
                 AND hanh_khach_id = :hanh_khach_id";

        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ':lich_trinh_id' => $lich_trinh_id,
            ':hanh_khach_id' => $hanh_khach_id
        ]);

        $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // BƯỚC 2: Xử lý Insert hoặc Update
        if ($existing) {
            // --- TRƯỜNG HỢP ĐÃ CÓ -> UPDATE ---
            $sqlUpdate = "UPDATE diemdanhkhach 
                      SET da_den = :da_den, 
                          ghi_chu = :ghi_chu, 
                          hdv_id = :hdv_id, 
                          thoi_gian = NOW() 
                      WHERE diem_danh_id = :id";

            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            return $stmtUpdate->execute([
                ':da_den' => $da_den,
                ':ghi_chu' => $ghi_chu,
                ':hdv_id' => $hdv_id,
                ':id' => $existing['diem_danh_id']
            ]);
        } else {
            // --- TRƯỜNG HỢP CHƯA CÓ -> INSERT ---
            $sqlInsert = "INSERT INTO diemdanhkhach (hanh_khach_id, lich_trinh_id, hdv_id, da_den, thoi_gian, ghi_chu) 
                      VALUES (:hanh_khach_id, :lich_trinh_id, :hdv_id, :da_den, NOW(), :ghi_chu)";

            $stmtInsert = $this->conn->prepare($sqlInsert);
            return $stmtInsert->execute([
                ':hanh_khach_id' => $hanh_khach_id,
                ':lich_trinh_id' => $lich_trinh_id,
                ':hdv_id' => $hdv_id,
                ':da_den' => $da_den,
                ':ghi_chu' => $ghi_chu
            ]);
        }
    }
    // Thêm vào class getDataModule
    public function getPassengerDetail($hanh_khach_id)
    {
        // JOIN bảng hành khách với bảng đặt tour để lấy thêm thông tin chuyến đi (lich_id)
        $sql = "SELECT 
                hk.*, 
                dt.lich_id,
                dt.trang_thai as trang_thai_don
            FROM hanhkhachlist hk
            JOIN dattour dt ON hk.dat_tour_id = dt.dat_tour_id
            WHERE hk.hanh_khach_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $hanh_khach_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Trong Model
    public function getListKhachHangByLichId($lich_id)
    {
        $sql = "
        SELECT 
            hk.*, 
            dt.dat_tour_id,
            dt.trang_thai as trang_thai_don
        FROM 
            hanhkhachlist hk
        JOIN 
            dattour dt ON hk.dat_tour_id = dt.dat_tour_id
        WHERE 
            dt.lich_id = :lich_id 
            AND dt.isdelete = 0
        ORDER BY 
            hk.ho_ten ASC
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_id', $lich_id, PDO::PARAM_INT);
        $stmt->execute();

        // Dùng fetchAll vì kết quả trả về NHIỀU người (mảng danh sách)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAggregatedTourDetail($tour_id)
    {
        $sql = "SELECT 
        -- 1. Thông tin Tour Cơ bản (t) & Danh Mục (dmt)
        t.tour_id, t.ten AS ten_tour, t.mo_ta, t.mo_ta_ngan,
        t.gia_co_ban, 
        t.thoi_luong_mac_dinh AS thoi_gian,
        dmt.ten AS loai_tour_ten, 
        
        -- 2. Thông tin Chính sách (cs, tcs)
        cs.chinh_sach_id, cs.ten AS ten_chinh_sach, cs.loai AS loai_chinh_sach, tcs.ghi_chu AS cs_ghi_chu,
        
        -- 3. Thông tin Địa điểm (dd, qg, ddt)
        dd.dia_diem_id, dd.ten AS ten_diadiem, qg.ten AS quoc_gia_diadiem, dd.mo_ta AS dd_mo_ta, ddt.ghi_chu AS dd_ghi_chu,
        
        -- 4. Thông tin Lịch trình (lt)
        lt.lich_trinh_id, lt.ngay_thu, lt.tieu_de AS tieu_de_lt, lt.noi_dung AS noi_dung_lt,
        
        -- 5. Thông tin Hình ảnh Địa điểm (hadd)
        hadd.hinh_id, hadd.url AS hinh_url, hadd.alt_text AS hinh_alt_text,
        
        -- 6. Thông tin Dịch vụ (dv, dvt)
        dv.dich_vu_id, dv.ten_dich_vu, dv.gia_mac_dinh AS gia_dv, dvt.ghi_chu AS dv_ghi_chu
        
    FROM Tour t
    LEFT JOIN DanhMucTour dmt ON t.danh_muc_id = dmt.danh_muc_id
    
    LEFT JOIN TourChinhSach tcs ON t.tour_id = tcs.tour_id
    LEFT JOIN ChinhSach cs ON tcs.chinh_sach_id = cs.chinh_sach_id
    
    LEFT JOIN LichTrinh lt ON t.tour_id = lt.tour_id
    
    -- JOIN Địa điểm
    LEFT JOIN DiaDiemTour ddt ON t.tour_id = ddt.tour_id
    LEFT JOIN DiaDiem dd ON ddt.dia_diem_id = dd.dia_diem_id
    -- JOIN Quốc gia
    LEFT JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id
    -- JOIN Hình ảnh Địa điểm
    LEFT JOIN HinhAnhDiaDiem hadd ON dd.dia_diem_id = hadd.dia_diem_id
    
    -- JOIN Dịch vụ Tour (Đã sử dụng tên bảng bạn cung cấp: dv_tour & dichvuncc)
    LEFT JOIN dv_tour dvt ON t.tour_id = dvt.tour_id
    LEFT JOIN dichvuncc dv ON dvt.dich_vu_id = dv.dich_vu_id
    
    WHERE t.tour_id = :tour_id
    
    -- Thêm sắp xếp cho Hình ảnh và Dịch vụ để dễ xử lý
    ORDER BY lt.ngay_thu ASC, cs.chinh_sach_id ASC, dd.dia_diem_id ASC, hadd.thu_tu ASC, dv.dich_vu_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->execute();
        $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rawData)) {
            return null;
        }

        $tourDetail = [];
        $cs_ids = [];
        $dd_info = []; // Dùng để gộp Địa điểm và Hình ảnh
        $lt_ids = [];
        $dv_ids = []; // Dùng để gộp Dịch vụ

        foreach ($rawData as $row) {
            // 1. Gán thông tin Tour Cơ bản (Chỉ cần làm 1 lần)
            if (empty($tourDetail)) {
                $tourDetail = [
                    'tour_id' => $row['tour_id'],
                    'ten' => $row['ten_tour'],
                    'mo_ta' => $row['mo_ta'],
                    'gia' => $row['gia_co_ban'],
                    'thoi_gian' => $row['thoi_gian'],
                    'loai_tour' => $row['loai_tour_ten'],
                    'phuong_tien' => 'N/A', // Cần bổ sung logic nếu có bảng PhươngTienTour
                    'chinh_sach' => [],
                    'dia_diem' => [],
                    'lich_trinh' => [],
                    'dich_vu' => [],
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

            // 3. Gán Địa điểm & Hình ảnh
            if ($row['dia_diem_id'] !== null) {
                $dd_id = $row['dia_diem_id'];

                // Khởi tạo Địa điểm nếu chưa có
                if (!isset($dd_info[$dd_id])) {
                    $dd_info[$dd_id] = [
                        'ten_diadiem' => $row['ten_diadiem'],
                        'quoc_gia' => $row['quoc_gia_diadiem'],
                        'mo_ta' => $row['dd_mo_ta'],
                        'ghi_chu' => $row['dd_ghi_chu'],
                        'hinh_anh' => []
                    ];
                }

                // Thêm Hình ảnh vào Địa điểm tương ứng
                if ($row['hinh_id'] !== null && $row['hinh_url'] !== null) {
                    // Kiểm tra trùng lặp hình ảnh (nếu SELECT có nhiều dòng trùng ID hình)
                    $is_duplicate = false;
                    foreach ($dd_info[$dd_id]['hinh_anh'] as $hinh) {
                        if ($hinh['url'] === $row['hinh_url']) {
                            $is_duplicate = true;
                            break;
                        }
                    }

                    if (!$is_duplicate) {
                        $dd_info[$dd_id]['hinh_anh'][] = [
                            'url' => $row['hinh_url'],
                            'alt_text' => $row['hinh_alt_text']
                        ];
                    }
                }
            }

            // 4. Gán Lịch trình
            if ($row['lich_trinh_id'] !== null && !in_array($row['lich_trinh_id'], $lt_ids)) {
                $tourDetail['lich_trinh'][] = [
                    'lich_trinh_id' => $row['lich_trinh_id'],
                    'ngay_thu' => $row['ngay_thu'],
                    'tieu_de' => $row['tieu_de_lt'],
                    'noi_dung' => $row['noi_dung_lt']
                ];
                $lt_ids[] = $row['lich_trinh_id'];
            }

            // 5. Gán Dịch vụ
            if ($row['dich_vu_id'] !== null && !in_array($row['dich_vu_id'], $dv_ids)) {
                $tourDetail['dich_vu'][] = [
                    'ten' => $row['ten_dich_vu'],
                    'gia' => $row['gia_dv'],
                    'ghi_chu' => $row['dv_ghi_chu']
                ];
                $dv_ids[] = $row['dich_vu_id'];
            }
        }

        // Đẩy Địa điểm đã tổng hợp vào $tourDetail
        $tourDetail['dia_diem'] = array_values($dd_info);

        // Sắp xếp Lịch trình theo Ngày Thứ (đảm bảo)
        usort($tourDetail['lich_trinh'], function ($a, $b) {
            return $a['ngay_thu'] <=> $b['ngay_thu'];
        });

        return $tourDetail;
    }

    public function getAllDanh_muc_tour()
    {
        $sql = "SELECT * FROM `danhmuctour`";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiaDiemByTourId($tour_id)
    {
        $sql = "SELECT 
                ddt.dia_diem_tour_id ,
                dd.dia_diem_id,
                dd.ten AS ten_diadiem,
                qg.ten AS quoc_gia,
                dd.mo_ta,
                had.url AS hinh_anh,
                ddt.ghi_chu
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
    public function getAllDiaDiem()
    {
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
    public function getAllDiaDiemtn()
    {
        $sql = "SELECT 
                dd.dia_diem_id,
                dd.ten,
                dd.mo_ta,
                qg.ten AS quoc_gia,
                dd.ngay_tao
            FROM DiaDiem dd
            JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id WHERE qg.quoc_gia_id=1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllDiaDiemqt()
    {
        $sql = "SELECT 
                dd.dia_diem_id,
                dd.ten,
                dd.mo_ta,
                qg.ten AS quoc_gia,
                dd.ngay_tao
            FROM DiaDiem dd
            JOIN QuocGia qg ON dd.quoc_gia_id = qg.quoc_gia_id WHERE qg.quoc_gia_id!=1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDiaDiemTourById($dia_diem_tour_id)
    {
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
    public function getAllNCC()
    {
        $sql = "SELECT * FROM nhacungcap WHERE isdelete=0 ORDER BY ncc_id DESC ";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNCCById($id)
    {
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
        $sql = "SELECT t.* FROM tour t
                JOIN LichTrinh lt ON t.tour_id = lt.tour_id
                WHERE lt.lich_trinh_id = :lich_trinh_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':lich_trinh_id', $lich_trinh_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getHDVById($id)
    {
        $sql = "SELECT * FROM `huongdanvien` WHERE `hdv_id` = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllHDV()
    {
        $sql = "SELECT * FROM `huongdanvien`";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getChinhSachByTourId($tour_id)
    {
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
    public function getDanhSachChinhSach()
    {
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
    public function getAllDatTour()
    {
        $sql = "SELECT
    DT.dat_tour_id,
    DT.so_nguoi,
    DT.trang_thai AS trang_thai_dat_tour,
    DT.ngay_tao AS ngay_dat_tour,
    DT.tour_id AS tour_id_dat_tour_moi, 

    KH.khach_hang_id,
    ND.ho_ten AS ten_khach_hang,
    KH.cccd,

    LKH.lich_id,
    LKH.ngay_bat_dau,
    LKH.ngay_ket_thuc,

    T.tour_id AS tour_id_tu_tour,
    T.ten AS ten_tour,
    T.mo_ta_ngan

FROM
    DatTour DT
INNER JOIN
    KhachHang KH ON DT.khach_hang_id = KH.khach_hang_id
INNER JOIN
    NguoiDung ND ON KH.nguoi_dung_id = ND.nguoi_dung_id 

LEFT JOIN 
    LichKhoiHanh LKH ON DT.lich_id = LKH.lich_id
LEFT JOIN 
    Tour T ON DT.tour_id = T.tour_id

WHERE
    DT.isdelete = 0";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách đơn đặt tour: " . $e->getMessage());
            return [];
        }
    }
    public function getAllKhachHang()
    {
        $sql = "SELECT
        KH.khach_hang_id,
        KH.cccd,
        KH.dia_chi,
        KH.ngay_tao AS ngay_tao_khach_hang,

        ND.nguoi_dung_id,
        ND.email,
        ND.ho_ten,
        ND.so_dien_thoai,
        ND.trang_thai AS trang_thai_tai_khoan
        
    FROM
        KhachHang KH
    INNER JOIN
        NguoiDung ND ON KH.nguoi_dung_id = ND.nguoi_dung_id
    
    WHERE 
        ND.isdelete = 0
        
    ORDER BY
        KH.khach_hang_id DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách khách hàng: " . $e->getMessage());
            return [];
        }
    }

    public function getDatTourById($dat_tour_id)
    {
        $sql = "SELECT * FROM `dattour` WHERE dat_tour_id = :dat_tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dat_tour_id', $dat_tour_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getBookingDetail($booking_id)
    {
        $sql = "SELECT 
            -- 1. Thông tin Booking (b)
            b.booking_id, b.ngay_dat, b.tong_tien AS tong_tien_booking, b.trang_thai_booking, b.so_luong_khach,
            
            -- 2. Thông tin Lịch Khởi Hành (lkh)
            lkh.ngay_khoi_hanh, lkh.ngay_ket_thuc, lkh.so_cho_toi_da, lkh.trang_thai_lich,
            
            -- 3. Thông tin Tour Cơ bản (t)
            t.tour_id, t.ten AS ten_tour, t.gia_co_ban, t.thoi_luong_mac_dinh,
            
            -- 4. Thông tin Hướng Dẫn Viên được giao (thdv, hdv)
            hdv.hdv_id, hdv.ten AS ten_hdv, hdv.email AS email_hdv, hdv.sdt AS sdt_hdv,
            
            -- 5. Thông tin Khách đi kèm (kddv - KhachDatDichVu)
            kddv.kddv_id, kddv.ten AS ten_khach_di_kem, kddv.sdt AS sdt_khach_di_kem, kddv.email AS email_khach_di_kem,
            kddv.vai_tro AS vai_tro_khach, -- Vai trò của khách trong booking (Ví dụ: khách chính, khách đi kèm)
            
            -- 6. Thông tin Dịch Vụ Tour (dv, dvt)
            dv.dich_vu_id, dv.ten_dich_vu, dvt.gia AS gia_dv_tour, dvt.ghi_chu AS dv_ghi_chu,
            
            -- 7. Thông tin Thanh Toán (tt) - Lấy tổng số tiền đã thanh toán/đặt cọc
            (SELECT SUM(bt.so_tien) FROM bookingthanhtoan bt WHERE bt.booking_id = b.booking_id AND bt.trang_thai = 'SUCCESS') AS tong_tien_da_thanh_toan
            
        FROM booking b
        JOIN lichkhoihanh lkh ON b.lich_khoi_hanh_id = lkh.lich_khoi_hanh_id
        JOIN tour t ON lkh.tour_id = t.tour_id
        
        -- Lấy HDV được giao (Giả định HDV được gán cho Lịch Khởi Hành thông qua bảng tourhdv)
        LEFT JOIN tourhdv thdv ON lkh.lich_khoi_hanh_id = thdv.lich_khoi_hanh_id
        LEFT JOIN huongdanvien hdv ON thdv.hdv_id = hdv.hdv_id
        
        -- Lấy danh sách Khách hàng chi tiết đi kèm dịch vụ/tour (dùng kddv thay vì KhachHang)
        LEFT JOIN khachdatdichvu kddv ON b.booking_id = kddv.booking_id
        
        -- Lấy Dịch vụ của Tour (dịch vụ mặc định đi kèm tour/lịch trình)
        LEFT JOIN dv_tour dvt ON t.tour_id = dvt.tour_id
        LEFT JOIN dichvuncc dv ON dvt.dich_vu_id = dv.dich_vu_id
        
        WHERE b.booking_id = :booking_id
        
        -- Sắp xếp để xử lý Khách hàng và Dịch vụ dễ dàng
        ORDER BY kddv.kddv_id ASC, dv.dich_vu_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':booking_id', $booking_id, PDO::PARAM_INT);
        $stmt->execute();
        $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rawData)) {
            return null;
        }

        $bookingDetail = [];
        $khach_ids = [];
        $dv_ids = [];
        $hdv_ids = []; // Để xử lý trường hợp nhiều HDV được giao

        foreach ($rawData as $row) {
            // 1. Gán thông tin Booking/Tour/LKH (Chỉ cần làm 1 lần)
            if (empty($bookingDetail)) {
                $bookingDetail = [
                    'booking_id' => $row['booking_id'],
                    'ngay_dat' => $row['ngay_dat'],
                    'tong_tien_booking' => $row['tong_tien_booking'],
                    'so_khach' => $row['so_luong_khach'],
                    'trang_thai_booking' => $row['trang_thai_booking'],
                    'so_tien_da_dat_coc' => $row['tong_tien_da_thanh_toan'] ?? 0,
                    'tour_info' => [
                        'tour_id' => $row['tour_id'],
                        'ten_tour' => $row['ten_tour'],
                        'gia_co_ban' => $row['gia_co_ban'],
                        'thoi_gian' => $row['thoi_luong_mac_dinh'],
                    ],
                    'lich_khoi_hanh' => [
                        'ngay_khoi_hanh' => $row['ngay_khoi_hanh'],
                        'ngay_ket_thuc' => $row['ngay_ket_thuc'],
                        'so_cho_toi_da' => $row['so_cho_toi_da'],
                        'trang_thai_lich' => $row['trang_thai_lich'],
                    ],
                    'huong_dan_vien' => [],
                    'danh_sach_khach' => [],
                    'dich_vu_tour' => [],
                ];
            }

            // 2. Gán Danh sách Khách đi kèm (từ khachdatdichvu)
            if ($row['kddv_id'] !== null && !in_array($row['kddv_id'], $khach_ids)) {
                $bookingDetail['danh_sach_khach'][] = [
                    'kddv_id' => $row['kddv_id'],
                    'ten' => $row['ten_khach_di_kem'],
                    'email' => $row['email_khach_di_kem'],
                    'sdt' => $row['sdt_khach_di_kem'],
                    'vai_tro' => $row['vai_tro_khach']
                ];
                $khach_ids[] = $row['kddv_id'];
            }

            // 3. Gán Dịch vụ của Tour
            if ($row['dich_vu_id'] !== null && !in_array($row['dich_vu_id'], $dv_ids)) {
                $bookingDetail['dich_vu_tour'][] = [
                    'dich_vu_id' => $row['dich_vu_id'],
                    'ten_dich_vu' => $row['ten_dich_vu'],
                    'gia_them' => $row['gia_dv_tour'],
                    'ghi_chu' => $row['dv_ghi_chu']
                ];
                $dv_ids[] = $row['dich_vu_id'];
            }

            // 4. Gán Hướng dẫn viên
            if ($row['hdv_id'] !== null && !in_array($row['hdv_id'], $hdv_ids)) {
                $bookingDetail['huong_dan_vien'][] = [
                    'hdv_id' => $row['hdv_id'],
                    'ten' => $row['ten_hdv'],
                    'email' => $row['email_hdv'],
                    'sdt' => $row['sdt_hdv'],
                ];
                $hdv_ids[] = $row['hdv_id'];
            }
        }

        return $bookingDetail;
    } ///////


    public function getAll()
    {
        $sql = "SELECT * FROM nguoidung";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function checkLogin($email, $password)
    {
        $sql = "SELECT * FROM `nguoidung` WHERE email = :email AND mat_khau = :password";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email, 'password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }
    public function getHanhKhachByDatTourId($dat_tour_id)
    {
        $sql = "SELECT * FROM `hanhkhachlist` WHERE dat_tour_id = :dat_tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dat_tour_id', $dat_tour_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layTatCaDichVu($db)
    {
        $stmt = $this->conn->prepare("
        SELECT dichvuncc.*, nhacungcap.ten AS ten_ncc
        FROM dichvuncc
        JOIN nhacungcap ON dichvuncc.ncc_id = nhacungcap.ncc_id
        
        where dichvuncc.isdelete=0
        ORDER BY dichvuncc.dich_vu_id DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function layDichVuTheoNCC($ncc_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM dichvuncc WHERE ncc_id = ? AND isdelete=0");
        $stmt->execute([$ncc_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function layDichVuTheoId($db, $dich_vu_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM dichvuncc WHERE dich_vu_id = ?");
        $stmt->execute([$dich_vu_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function layTatCaNhaCungCap($db)
    {
        $stmt = $db->prepare("
        SELECT *
        FROM nhacungcap
        ORDER BY ncc_id DESC
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDichVuByTourId($tour_id)
    {
        $sql = "SELECT 
    gdv.gia_dv_id,
    gdv.lich_id,
    gdv.tour_id,
    gdv.dich_vu_id,
    gdv.ghi_chu,

    dv.loai_dich_vu,
    dv.ma,
    dv.ten_dich_vu,
    dv.mo_ta,
    dv.gia_mac_dinh,
    dv.don_vi,
    dv.ncc_id

FROM dv_tour AS gdv
JOIN dichvuncc AS dv 
    ON gdv.dich_vu_id = dv.dich_vu_id

WHERE gdv.tour_id = :tour_id
  AND dv.isdelete = 0;

";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ganDichVuTour($tour_id, $dich_vu_id, $ghi_chu = null)
    {
        $sql = "INSERT INTO dv_tour (tour_id, dich_vu_id, gia, ghi_chu, da_xac_nhan) 
            VALUES (:tour_id, :dich_vu_id, 0, :ghi_chu, 0)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt->bindParam(':dich_vu_id', $dich_vu_id, PDO::PARAM_INT);
        $stmt->bindParam(':ghi_chu', $ghi_chu, PDO::PARAM_STR);
        return $stmt->execute();
    }
    function xoaGanDichVuTour($gia_dv_id, $tour_id)
    {
        $sql = "DELETE FROM dv_tour WHERE gia_dv_id = :gia_dv_id AND tour_id = :tour_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':gia_dv_id', $gia_dv_id, PDO::PARAM_INT);
        $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function getAllUsers()
    {
        $sql = "SELECT nguoidung.*, vaitro.ten AS ten_vai_tro
                FROM nguoidung 
                LEFT JOIN vaitro ON nguoidung.vai_tro_id = vaitro.vai_tro_id
                WHERE nguoidung.isdelete = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy 1 user theo id
    public function find($id)
    {
        $sql = "SELECT * FROM nguoidung WHERE nguoi_dung_id = ? AND isdelete = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllVaiTro()
    {
        $sql = "SELECT * FROM vaitro";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDatTourDetail($dat_tour_id)
    {
        $sql = "SELECT 
            -- 1. Thông tin Đặt Tour
            dt.dat_tour_id, 
            dt.ngay_tao AS ngay_dat, 
            dt.trang_thai AS trang_thai_dat_tour, 
            dt.so_nguoi AS so_luong_khach,
            dt.ghi_chu AS ghi_chu_booking,
            dt.loai AS loai_tour,
            dt.nguon,
            
            -- Tính tổng tiền ước tính (Giá cơ bản * Số người)
            -- (Lưu ý: Giá dịch vụ sẽ được cộng thêm trong vòng lặp PHP)
            (t.gia_co_ban * dt.so_nguoi) AS tong_tien_co_ban,

            -- 2. Thông tin Lịch & Tour
            lkh.lich_id, 
            lkh.ngay_bat_dau, 
            lkh.ngay_ket_thuc, 
            lkh.trang_thai AS trang_thai_lich,
            t.tour_id, 
            t.ten AS ten_tour, 
            t.gia_co_ban, 
            t.thoi_luong_mac_dinh,
            
            -- 3. Thông tin Hướng Dẫn Viên (Lấy từ bảng NGUOIDUNG)
            hdv.hdv_id, 
            nd_hdv.ho_ten AS ten_hdv,        -- Bảng nguoidung
            nd_hdv.email AS email_hdv,       -- Bảng nguoidung
            nd_hdv.so_dien_thoai AS sdt_hdv, -- Bảng nguoidung
            
            -- 4. Thông tin Hành Khách (Bảng hanhkhachlist)
            hk.hanh_khach_id, 
            hk.ho_ten AS ten_hanh_khach, 
            hk.lien_he AS sdt_hanh_khach,      -- Cột 'lien_he'
            hk.so_giay_to AS cccd_hanh_khach,  -- Cột 'so_giay_to'
            hk.gioi_tinh,                      -- Cột 'gioi_tinh'
            hk.ngay_sinh,
            hk.yeu_cau_ca_nhan AS yeu_cau_rieng, -- Cột 'yeu_cau_ca_nhan'
            
            -- 5. Dịch Vụ Tour (Lấy giá từ dichvuncc vì dv_tour không có giá)
            dv.dich_vu_id, 
            dv.ten_dich_vu, 
            dv.gia_mac_dinh AS gia_dv_tour, -- Lấy giá mặc định
            dvt.ghi_chu AS dv_ghi_chu,
            
            -- 6. Đặt Cọc & Thanh Toán (Tổng hợp từ cả 2 bảng datcoc và thanhtoan)
            (
                IFNULL((SELECT SUM(dc.so_tien) FROM datcoc dc WHERE dc.dat_tour_id = dt.dat_tour_id AND dc.trang_thai = 'confirmed'), 0) +
                IFNULL((SELECT SUM(tt.so_tien) FROM thanhtoan tt WHERE tt.dat_tour_id = dt.dat_tour_id AND tt.trang_thai = 'success'), 0)
            ) AS tong_tien_da_thanh_toan
            
        FROM dattour dt
        LEFT JOIN lichkhoihanh lkh ON dt.lich_id = lkh.lich_id
        LEFT JOIN tour t ON dt.tour_id = t.tour_id
        
        -- Join HDV -> Người dùng
        LEFT JOIN phanconghdv pc ON lkh.lich_id = pc.lich_id
        LEFT JOIN huongdanvien hdv ON pc.hdv_id = hdv.hdv_id
        LEFT JOIN nguoidung nd_hdv ON hdv.nguoi_dung_id = nd_hdv.nguoi_dung_id 
        
        -- Join Hành khách
        LEFT JOIN hanhkhachlist hk ON dt.dat_tour_id = hk.dat_tour_id
        
        -- Join Dịch vụ
        LEFT JOIN dv_tour dvt ON t.tour_id = dvt.tour_id
        LEFT JOIN dichvuncc dv ON dvt.dich_vu_id = dv.dich_vu_id
        
        WHERE dt.dat_tour_id = :dat_tour_id
        
        ORDER BY hk.hanh_khach_id ASC, dv.dich_vu_id ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':dat_tour_id', $dat_tour_id, PDO::PARAM_INT);
        $stmt->execute();
        $rawData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rawData)) return null;

        $datTourDetail = [];
        $hanhkhach_ids = [];
        $dv_ids = [];
        $hdv_ids = [];
        $tong_gia_dich_vu_phu_tro = 0;

        foreach ($rawData as $row) {
            // 1. Thông tin chung (Chỉ lấy 1 lần)
            if (empty($datTourDetail)) {
                $datTourDetail = [
                    'dat_tour_id' => $row['dat_tour_id'],
                    'ngay_dat' => $row['ngay_dat'],
                    'so_khach' => $row['so_luong_khach'],
                    'trang_thai' => $row['trang_thai_dat_tour'],
                    'ghi_chu' => $row['ghi_chu_booking'],
                    'loai_tour' => $row['loai_tour'],
                    'nguon' => $row['nguon'],
                    'da_thanh_toan' => $row['tong_tien_da_thanh_toan'] ?? 0,
                    'tong_tien_co_ban' => $row['tong_tien_co_ban'] ?? 0,

                    'tour_info' => [
                        'tour_id' => $row['tour_id'],
                        'ten_tour' => $row['ten_tour'],
                        'gia_co_ban' => $row['gia_co_ban'],
                        'thoi_gian' => $row['thoi_luong_mac_dinh'],
                    ],
                    'lich_khoi_hanh' => [
                        'lich_id' => $row['lich_id'],
                        'ngay_bat_dau' => $row['ngay_bat_dau'],
                        'ngay_ket_thuc' => $row['ngay_ket_thuc'],
                        'trang_thai' => $row['trang_thai_lich'],
                    ],
                    'huong_dan_vien' => [],
                    'danh_sach_hanh_khach' => [],
                    'dich_vu_tour' => [],
                ];
            }

            // 2. Dịch vụ (Cộng dồn giá vào tổng tiền)
            if ($row['dich_vu_id'] !== null && !in_array($row['dich_vu_id'], $dv_ids)) {
                $gia_dv = $row['gia_dv_tour'] ?? 0;
                $datTourDetail['dich_vu_tour'][] = [
                    'id' => $row['dich_vu_id'],
                    'ten' => $row['ten_dich_vu'],
                    'gia' => $gia_dv,
                    'ghi_chu' => $row['dv_ghi_chu']
                ];
                // Cộng giá dịch vụ vào tổng tiền (giả sử tính theo đầu người cho mỗi dịch vụ)
                // Hoặc nếu dịch vụ tính theo đoàn thì bỏ * so_luong_khach đi. 
                // Ở đây tạm tính: Giá dịch vụ * Số khách
                $tong_gia_dich_vu_phu_tro += ($gia_dv * $row['so_luong_khach']);

                $dv_ids[] = $row['dich_vu_id'];
            }

            // 3. Hành Khách
            if ($row['hanh_khach_id'] !== null && !in_array($row['hanh_khach_id'], $hanhkhach_ids)) {
                $datTourDetail['danh_sach_hanh_khach'][] = [
                    'id' => $row['hanh_khach_id'],
                    'ho_ten' => $row['ten_hanh_khach'],
                    'sdt' => $row['sdt_hanh_khach'], // Lấy từ cột lien_he
                    'cccd' => $row['cccd_hanh_khach'], // Lấy từ cột so_giay_to
                    'gioi_tinh' => $row['gioi_tinh'],
                    'ngay_sinh' => $row['ngay_sinh'],
                    'yeu_cau_noi_dung' => $row['yeu_cau_rieng'] // Lấy từ cột yeu_cau_ca_nhan
                ];
                $hanhkhach_ids[] = $row['hanh_khach_id'];
            }

            // 4. HDV
            if ($row['hdv_id'] !== null && !in_array($row['hdv_id'], $hdv_ids)) {
                $datTourDetail['huong_dan_vien'][] = [
                    'id' => $row['hdv_id'],
                    'ho_ten' => $row['ten_hdv'],
                    'email' => $row['email_hdv'],
                    'sdt' => $row['sdt_hdv'],
                ];
                $hdv_ids[] = $row['hdv_id'];
            }
        }

        // Tính tổng tiền cuối cùng
        // Tổng = Tiền vé cơ bản (đã nhân số người) + Tổng tiền dịch vụ (đã nhân số người)
        $datTourDetail['tong_tien_uoc_tinh'] = $datTourDetail['tong_tien_co_ban'] + $tong_gia_dich_vu_phu_tro;

        return $datTourDetail;
    }
}
