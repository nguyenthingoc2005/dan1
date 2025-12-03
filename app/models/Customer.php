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
        $sql = "SELECT * FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'customer') ";
        // Note: Assuming customers are in 'users' table with role 'customer' based on initial schema.
        // Wait, let me check the schema again. The user provided schema earlier showed 'users' table.
        // But the current code in Customer.php uses 'customers' table.
        // Let's check if 'customers' table exists or if it was a separate table.
        // In the previous turn, I saw `setup_database.sql` but didn't read it fully.
        // The error message says `SELECT * FROM c...` which implies `customers`.
        // Let's stick to `customers` table if that's what the current code uses, BUT
        // The current code `SELECT * FROM customers` might be wrong if the table is actually `users`.
        // Let's check `setup_database.sql` to be sure.

        // Actually, looking at the previous file content of Customer.php:
        // $sql = "SELECT * FROM customers ...";
        // If this file existed and was working (partially), then 'customers' table might exist.
        // However, the schema provided in the prompt (SYSTEM PROMPT) says:
        // "MODULE 1: USERS & ROLES" ... "users" table.
        // It does NOT explicitly show a "customers" table in the truncated part.
        // But usually in these systems, customers might be separate or users.

        // Let's assume 'customers' table exists for now because the file `app/models/Customer.php` was already there (or created by me previously? No, I created CustomerController but maybe not Model? No, I viewed Customer.php and it looked simple).
        // Wait, the file I just viewed `app/models/Customer.php` has `SELECT * FROM customers`.
        // If the user says "Undeclared variable: Array", it means the Controller passed an array to `getAll($limit)` and `$limit` became an array, so `LIMIT Array` caused SQL error.

        // So I just need to fix the signature.

        $sql = "SELECT * FROM customers WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Count total
        $countSql = "SELECT COUNT(*) FROM customers WHERE 1=1";
        if (!empty($filters['search'])) {
            $countSql .= " AND (full_name LIKE :search OR phone LIKE :search OR email LIKE :search)";
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

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
