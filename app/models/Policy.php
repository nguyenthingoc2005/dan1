<?php
/**
 * ==============================================================================
 * POLICY MODEL
 * ==============================================================================
 * 
 * Quản lý các chính sách (policies) của tour
 * Bảng: policies, tour_policies
 * 
 * @version 1.0
 * @date 2024-12-06
 * ==============================================================================
 */

class Policy
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả policies (có thể filter theo status)
     */
    public function getAll($filters = [])
    {
        $where = ["1=1"];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['policy_type'])) {
            $where[] = "policy_type = :policy_type";
            $params['policy_type'] = $filters['policy_type'];
        }

        $sql = "SELECT * FROM policies WHERE " . implode(' AND ', $where) . " ORDER BY name ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy policy theo ID
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM policies WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Tạo policy mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO policies (name, description, policy_type, content, status)
                VALUES (:name, :description, :policy_type, :content, :status)";

        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'policy_type' => $data['policy_type'] ?? null,
            'content' => $data['content'],
            'status' => $data['status'] ?? 'active'
        ]);

        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật policy
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

        $sql = "UPDATE policies SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa policy
     */
    public function delete($id)
    {
        $sql = "DELETE FROM policies WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy tất cả policies của một tour
     */
    public function getByTourId($tour_id)
    {
        $sql = "SELECT p.*
                FROM policies p
                JOIN tour_policies tp ON p.id = tp.policy_id
                WHERE tp.tour_id = :tour_id
                ORDER BY p.policy_type ASC, p.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gán policy cho tour
     */
    public function assignToTour($tour_id, $policy_id)
    {
        // Kiểm tra xem đã gán chưa
        $check = $this->pdo->prepare("SELECT COUNT(*) FROM tour_policies WHERE tour_id = :tour_id AND policy_id = :policy_id");
        $check->execute(['tour_id' => $tour_id, 'policy_id' => $policy_id]);
        if ($check->fetchColumn() > 0) {
            return true; // Đã gán rồi
        }

        $sql = "INSERT INTO tour_policies (tour_id, policy_id) VALUES (:tour_id, :policy_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id, 'policy_id' => $policy_id]);
    }

    /**
     * Hủy gán policy cho tour
     */
    public function unassignFromTour($tour_id, $policy_id)
    {
        $sql = "DELETE FROM tour_policies WHERE tour_id = :tour_id AND policy_id = :policy_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id, 'policy_id' => $policy_id]);
    }

    /**
     * Xóa tất cả policies của một tour
     */
    public function deleteByTourId($tour_id)
    {
        $sql = "DELETE FROM tour_policies WHERE tour_id = :tour_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['tour_id' => $tour_id]);
    }

    /**
     * Gán nhiều policies cho tour (replace all)
     */
    public function assignMultipleToTour($tour_id, $policy_ids)
    {
        try {
            $this->pdo->beginTransaction();

            // Xóa tất cả policies cũ
            $this->deleteByTourId($tour_id);

            // Gán policies mới
            if (!empty($policy_ids)) {
                $sql = "INSERT INTO tour_policies (tour_id, policy_id) VALUES (:tour_id, :policy_id)";
                $stmt = $this->pdo->prepare($sql);

                foreach ($policy_ids as $policy_id) {
                    $stmt->execute(['tour_id' => $tour_id, 'policy_id' => $policy_id]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Policy::assignMultipleToTour() Error: " . $e->getMessage());
            return false;
        }
    }
}

