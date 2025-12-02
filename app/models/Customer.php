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
}
