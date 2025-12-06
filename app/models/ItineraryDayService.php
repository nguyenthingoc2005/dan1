<?php
/**
 * ==============================================================================
 * ITINERARY DAY SERVICE MODEL
 * ==============================================================================
 * 
 * Quản lý dịch vụ theo từng ngày của tour (để tính chi phí)
 * Bảng: itinerary_day_services
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

class ItineraryDayService
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả dịch vụ của một itinerary (một ngày)
     */
    public function getByItineraryId($itinerary_id)
    {
        $sql = "SELECT ids.*, 
                       s.name as service_name_original,
                       sp.name as service_provider_name
                FROM itinerary_day_services ids
                LEFT JOIN services s ON ids.service_id = s.id
                LEFT JOIN service_providers sp ON ids.service_provider_id = sp.id
                WHERE ids.itinerary_id = :itinerary_id
                ORDER BY ids.created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['itinerary_id' => $itinerary_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả dịch vụ của một tour (tất cả các ngày)
     */
    public function getByTourId($tour_id)
    {
        $sql = "SELECT ids.*, 
                       i.day_number,
                       i.tour_id,
                       s.name as service_name_original,
                       sp.name as service_provider_name
                FROM itinerary_day_services ids
                JOIN itineraries i ON ids.itinerary_id = i.id
                LEFT JOIN services s ON ids.service_id = s.id
                LEFT JOIN service_providers sp ON ids.service_provider_id = sp.id
                WHERE i.tour_id = :tour_id
                ORDER BY i.day_number ASC, ids.created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tính tổng chi phí dịch vụ/người của tour (chỉ tính các dịch vụ is_included_in_price = 1)
     */
    public function calculateTotalCostPerPerson($tour_id)
    {
        $sql = "SELECT SUM(ids.unit_price * ids.quantity) as total_cost
                FROM itinerary_day_services ids
                JOIN itineraries i ON ids.itinerary_id = i.id
                WHERE i.tour_id = :tour_id
                  AND ids.is_included_in_price = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (float) $result['total_cost'] : 0.00;
    }

    /**
     * Tính tổng chi phí dịch vụ/người theo ngày
     */
    public function calculateCostPerPersonByDay($itinerary_id)
    {
        $sql = "SELECT SUM(unit_price * quantity) as total_cost
                FROM itinerary_day_services
                WHERE itinerary_id = :itinerary_id
                  AND is_included_in_price = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['itinerary_id' => $itinerary_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? (float) $result['total_cost'] : 0.00;
    }

    /**
     * Tạo dịch vụ mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO itinerary_day_services (
            itinerary_id, service_id, service_provider_id, service_name,
            unit_price, quantity, unit, is_included_in_price, notes
        ) VALUES (
            :itinerary_id, :service_id, :service_provider_id, :service_name,
            :unit_price, :quantity, :unit, :is_included_in_price, :notes
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'itinerary_id' => $data['itinerary_id'],
            'service_id' => $data['service_id'],
            'service_provider_id' => $data['service_provider_id'] ?? null,
            'service_name' => $data['service_name'] ?? null,
            'unit_price' => $data['unit_price'],
            'quantity' => $data['quantity'] ?? 1.00,
            'unit' => $data['unit'] ?? null,
            'is_included_in_price' => $data['is_included_in_price'] ?? 1,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Cập nhật dịch vụ
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE itinerary_day_services SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa dịch vụ
     */
    public function delete($id)
    {
        $sql = "DELETE FROM itinerary_day_services WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả dịch vụ của một itinerary
     */
    public function deleteByItineraryId($itinerary_id)
    {
        $sql = "DELETE FROM itinerary_day_services WHERE itinerary_id = :itinerary_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['itinerary_id' => $itinerary_id]);
    }

    /**
     * Xóa tất cả dịch vụ của một tour
     */
    public function deleteByTourId($tour_id)
    {
        $sql = "DELETE ids FROM itinerary_day_services ids
                JOIN itineraries i ON ids.itinerary_id = i.id
                WHERE i.tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id]);
    }

    /**
     * Lấy dịch vụ theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM itinerary_day_services WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

