<?php
class AccountModel {
    private $conn;
    private $table = 'account';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAccountByUsername($username) {
        $query = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getAccountById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createAccount($username, $password, $fullname, $email = null, $phone = null, $address = null) {
        $query = "INSERT INTO {$this->table} 
                 (username, password, fullname, email, phone, address) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        return $stmt->execute([
            $username,
            $hashed_password,
            $fullname,
            $email,
            $phone,
            $address
        ]);
    }

    public function updateAccount($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET fullname = ?, email = ?, phone = ?, address = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['fullname'],
            $data['email'],
            $data['phone'],
            $data['address'],
            $id
        ]);
    }

    public function authenticate($username, $password) {
        $account = $this->getAccountByUsername($username);
        if ($account && password_verify($password, $account->password)) {
            return $account;
        }
        return false;
    }

    public function changePassword($id, $new_password) {
        $query = "UPDATE {$this->table} 
                 SET password = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        return $stmt->execute([$hashed_password, $id]);
    }
}