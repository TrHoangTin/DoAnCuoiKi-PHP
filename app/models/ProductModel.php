<?php
class ProductModel {
    private $conn;
    private $table_name = "product";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        try {
            $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                      FROM ". $this->table_name . " p
                      LEFT JOIN category c ON p.category_id = c.id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting products: " . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id) {
        try {
            $query = "SELECT * FROM ". $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            error_log("Error getting product by ID: " . $e->getMessage());
            return false;
        }
    }

    public function addProduct($name, $description, $price, $category_id, $image = null) {
        $errors = [];
        if (empty($name)) $errors['name'] = 'Product name cannot be empty';
        if (empty($description)) $errors['description'] = 'Description cannot be empty';
        if (!is_numeric($price) || $price < 0) $errors['price'] = 'Invalid price';
        
        if (!empty($errors)) return $errors;

        try {
            $query = "INSERT INTO ". $this->table_name . " 
                     (name, description, price, category_id, image) 
                     VALUES (:name, :description, :price, :category_id, :image)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
            $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)), PDO::PARAM_STR);
            $stmt->bindParam(':price', htmlspecialchars(strip_tags($price)), PDO::PARAM_STR);
            $stmt->bindParam(':category_id', htmlspecialchars(strip_tags($category_id)), PDO::PARAM_INT);
            $stmt->bindParam(':image', htmlspecialchars(strip_tags($image)), PDO::PARAM_STR);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image = null) {
        try {
            $query = "UPDATE ". $this->table_name . " 
                     SET name = :name, description = :description, 
                         price = :price, category_id = :category_id, 
                         image = COALESCE(:image, image)
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
            $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)), PDO::PARAM_STR);
            $stmt->bindParam(':price', htmlspecialchars(strip_tags($price)), PDO::PARAM_STR);
            $stmt->bindParam(':category_id', htmlspecialchars(strip_tags($category_id)), PDO::PARAM_INT);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);

            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $query = "DELETE FROM ". $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
}