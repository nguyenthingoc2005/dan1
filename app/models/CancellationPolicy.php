<?php
/**
 * CANCELLATION POLICY MODEL
 */
if (!class_exists('CancellationPolicy')) {
class CancellationPolicy
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Lấy tất cả policies
     */
    public function getAll($filters = [])
    {
        // Query từ bảng cancellation_policies
        $sql = "SELECT * FROM cancellation_policies WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY days_before ASC, id ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Remove duplicates by ID (đảm bảo không có duplicate)
        $unique = [];
        foreach ($results as $row) {
            if (!isset($unique[$row['id']])) {
                $unique[$row['id']] = $row;
            }
        }
        
        return array_values($unique);
    }

    /**
     * Lấy policy theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM cancellation_policies WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo policy mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO cancellation_policies 
                (name, description, days_before, fee_percentage, status) 
                VALUES (:name, :description, :days_before, :fee_percentage, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'days_before' => (int) $data['days_before'],
            'fee_percentage' => (float) $data['fee_percentage'],
            'status' => $data['status'] ?? 'active'
        ]);
    }

    /**
     * Cập nhật policy
     */
    public function update($id, $data)
    {
        $sql = "UPDATE cancellation_policies SET 
                name = :name,
                description = :description,
                days_before = :days_before,
                fee_percentage = :fee_percentage,
                status = :status
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'days_before' => (int) $data['days_before'],
            'fee_percentage' => (float) $data['fee_percentage'],
            'status' => $data['status'] ?? 'active',
            'id' => $id
        ]);
    }

    /**
     * Xóa policy
     */
    public function delete($id)
    {
        // Check if policy is being used
        $checkSql = "SELECT COUNT(*) FROM bookings WHERE cancellation_policy_id = :id";
        $checkStmt = $this->pdo->prepare($checkSql);
        $checkStmt->execute(['id' => $id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            throw new Exception("Không thể xóa policy này vì đang được sử dụng bởi $count booking");
        }

        $sql = "DELETE FROM cancellation_policies WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $policy = $this->getById($id);
        if (!$policy) {
            throw new Exception("Policy không tồn tại");
        }

        $newStatus = $policy['status'] === 'active' ? 'inactive' : 'active';
        $sql = "UPDATE cancellation_policies SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'status' => $newStatus,
            'id' => $id
        ]);
    }
}
} // End class_exists check

