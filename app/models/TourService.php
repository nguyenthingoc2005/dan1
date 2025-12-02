<?php
/**
 * ==============================================================================
 * TOUR SERVICE MODEL
 * ==============================================================================
 * 
 * Quản lý các dịch vụ đi kèm của Tour (Costing)
 * Table: tour_services
 * 
 * @version 1.0
 * @date 2024-12-02
 * ==============================================================================
 */

class TourService
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Lấy danh sách dịch vụ của một Tour
     * 
     * @param int $tour_id
     * @return array
     */
    public function getByTourId($tour_id)
    {
        $sql = "SELECT ts.*, s.name as original_service_name, s.unit as original_unit, st.name as service_type_name, sup.company_name as supplier_name
                FROM tour_services ts
                JOIN services s ON ts.service_id = s.id
                LEFT JOIN service_types st ON s.service_type_id = st.id
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id
                WHERE ts.tour_id = :tour_id
                ORDER BY st.name, s.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm dịch vụ vào Tour
     * 
     * @param array $data
     * @return int Last Insert ID
     */
    public function create($data)
    {
        $sql = "INSERT INTO tour_services (
                    tour_id, service_id, service_name, calculation_type, 
                    fixed_quantity, group_size, unit_price, unit, notes, is_included_in_price
                ) VALUES (
                    :tour_id, :service_id, :service_name, :calculation_type, 
                    :fixed_quantity, :group_size, :unit_price, :unit, :notes, :is_included_in_price
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tour_id' => $data['tour_id'],
            'service_id' => $data['service_id'],
            'service_name' => $data['service_name'],
            'calculation_type' => $data['calculation_type'] ?? 'per_person',
            'fixed_quantity' => $data['fixed_quantity'] ?? 1,
            'group_size' => $data['group_size'] ?? null,
            'unit_price' => $data['unit_price'],
            'unit' => $data['unit'] ?? '',
            'notes' => $data['notes'] ?? '',
            'is_included_in_price' => isset($data['is_included_in_price']) ? $data['is_included_in_price'] : 1
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Xóa dịch vụ khỏi Tour
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM tour_services WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả dịch vụ của Tour (để cập nhật lại)
     * 
     * @param int $tour_id
     * @return bool
     */
    public function deleteAllByTourId($tour_id)
    {
        $sql = "DELETE FROM tour_services WHERE tour_id = :tour_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id]);
    }
}
