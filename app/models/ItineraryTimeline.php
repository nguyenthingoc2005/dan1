<?php
/**
 * ==============================================================================
 * ITINERARY TIMELINE MODEL
 * ==============================================================================
 * 
 * Quản lý timeline chi tiết cho từng ngày của tour
 * Bảng: itinerary_timelines
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

class ItineraryTimeline
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả timeline items của một itinerary (một ngày)
     */
    public function getByItineraryId($itinerary_id)
    {
        $sql = "SELECT it.*, 
                       d.name as destination_name,
                       sp.name as service_provider_name,
                       s.name as service_name
                FROM itinerary_timelines it
                LEFT JOIN destinations d ON it.destination_id = d.id
                LEFT JOIN service_providers sp ON it.service_provider_id = sp.id
                LEFT JOIN services s ON it.service_id = s.id
                WHERE it.itinerary_id = :itinerary_id
                ORDER BY it.timeline_time ASC, it.display_order ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['itinerary_id' => $itinerary_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy timeline items của tất cả các ngày trong tour
     */
    public function getByTourId($tour_id)
    {
        $sql = "SELECT it.*, 
                       i.day_number,
                       i.tour_id,
                       d.name as destination_name,
                       sp.name as service_provider_name,
                       s.name as service_name
                FROM itinerary_timelines it
                JOIN itineraries i ON it.itinerary_id = i.id
                LEFT JOIN destinations d ON it.destination_id = d.id
                LEFT JOIN service_providers sp ON it.service_provider_id = sp.id
                LEFT JOIN services s ON it.service_id = s.id
                WHERE i.tour_id = :tour_id
                ORDER BY i.day_number ASC, it.timeline_time ASC, it.display_order ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo timeline item mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO itinerary_timelines (
            itinerary_id, timeline_time, activity_title, activity_description,
            location, destination_id, service_provider_id, service_id,
            timeline_type, display_order, notes
        ) VALUES (
            :itinerary_id, :timeline_time, :activity_title, :activity_description,
            :location, :destination_id, :service_provider_id, :service_id,
            :timeline_type, :display_order, :notes
        )";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'itinerary_id' => $data['itinerary_id'],
            'timeline_time' => $data['timeline_time'],
            'activity_title' => $data['activity_title'],
            'activity_description' => $data['activity_description'] ?? null,
            'location' => $data['location'] ?? null,
            'destination_id' => $data['destination_id'] ?? null,
            'service_provider_id' => $data['service_provider_id'] ?? null,
            'service_id' => $data['service_id'] ?? null,
            'timeline_type' => $data['timeline_type'] ?? 'activity',
            'display_order' => $data['display_order'] ?? 0,
            'notes' => $data['notes'] ?? null
        ]);
    }

    /**
     * Cập nhật timeline item
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

        $sql = "UPDATE itinerary_timelines SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa timeline item
     */
    public function delete($id)
    {
        $sql = "DELETE FROM itinerary_timelines WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Xóa tất cả timeline items của một itinerary
     */
    public function deleteByItineraryId($itinerary_id)
    {
        $sql = "DELETE FROM itinerary_timelines WHERE itinerary_id = :itinerary_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['itinerary_id' => $itinerary_id]);
    }

    /**
     * Xóa tất cả timeline items của một tour
     */
    public function deleteByTourId($tour_id)
    {
        $sql = "DELETE it FROM itinerary_timelines it
                JOIN itineraries i ON it.itinerary_id = i.id
                WHERE i.tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id]);
    }

    /**
     * Lấy timeline item theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM itinerary_timelines WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

