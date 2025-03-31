<?php
class AccountModel {
    private $conn;
    private $table_name = "account";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy thông tin tài khoản bằng username
    public function getAccountByUsername($username) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Lỗi khi lấy thông tin tài khoản: " . $e->getMessage());
            return false;
        }
    }

    // Tạo tài khoản mới
    public function createAccount($username, $password, $fullname, $role = 'user') {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (username, password, fullname, role) 
                     VALUES (:username, :password, :fullname, :role)";
            $stmt = $this->conn->prepare($query);

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Lỗi khi tạo tài khoản: " . $e->getMessage());
            return false;
        }
    }

    // Xác thực đăng nhập
    public function authenticate($username, $password) {
        $account = $this->getAccountByUsername($username);
        if ($account && password_verify($password, $account->password)) {
            return $account;
        }
        return false;
    }
}