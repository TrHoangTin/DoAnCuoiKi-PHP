<?php
class CategoryModel {
    private $conn;
    private $table = 'category';

    public function __construct($db) {
        $this->conn = $db;
    }

    

    public function getCategories() {
        $query = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoriesWithCount() {
        $query = "SELECT c.*, COUNT(p.id) as product_count 
                  FROM {$this->table} c
                  LEFT JOIN product p ON c.id = p.category_id
                  GROUP BY c.id
                  ORDER BY c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function addCategory($name, $description) {
        $query = "INSERT INTO {$this->table} (name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description]);
    }

    public function updateCategory($id, $name, $description) {
        $query = "UPDATE {$this->table} 
                 SET name = ?, description = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $id]);
    }

    public function deleteCategory($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}