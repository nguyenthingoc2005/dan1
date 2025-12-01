<?php

class hdvModel
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getAllTours()
    {
        $sql = "SELECT * FROM tours";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function getTourById($tourId)
    {
        $sql = "SELECT * FROM tours WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $tourId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getAllKhachHang()
    {
        $sql = "SELECT * FROM khach_hang";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lưu thông tin điểm danh (Tự động Insert hoặc Update)
     */
}
