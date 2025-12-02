<?php
class Customer
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll($limit = 100)
    {
        $sql = "SELECT * FROM customers ORDER BY created_at DESC LIMIT $limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM customers WHERE full_name LIKE :kw OR phone LIKE :kw OR email LIKE :kw LIMIT 20";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['kw' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
}
