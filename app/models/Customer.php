<?php
class Customer
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($filters = [], $page = 1, $limit = 20)
    {
        $sql = "SELECT * FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        if (!empty($filters['search'])) {
            $countSql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
        }
        if (!empty($filters['created_by'])) {
            $countSql .= " AND created_by = :created_by";
        }

        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();

        // Pagination
        $sql .= " ORDER BY created_at DESC LIMIT :offset, :limit";
        $offset = ($page - 1) * $limit;

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ];
    }

    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['created_by'])) {
            $sql .= " AND created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM customers WHERE full_name LIKE :kw OR phone LIKE :kw OR email LIKE :kw LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['kw' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPhone($phone)
    {
        $sql = "SELECT * FROM customers WHERE phone = :phone";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO customers (full_name, phone, email, address, gender, date_of_birth, source, created_by) 
                VALUES (:full_name, :phone, :email, :address, :gender, :date_of_birth, :source, :created_by)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'source' => $data['source'] ?? 'walk_in',
            'created_by' => $data['created_by'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        return $this->getById($id);
    }

    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = "UPDATE customers SET " . implode(', ', $fields) . " WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        // Check if has bookings
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM bookings WHERE customer_id = :id");
        $stmt->execute(['id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            return false; // Cannot delete
        }

        $stmt = $this->pdo->prepare("DELETE FROM customers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
