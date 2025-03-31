<?php
class ProductModel {
    private $conn;
    private $table = 'product';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getFeaturedProducts($limit = 8) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.price > 5000000  -- Sản phẩm có giá > 5 triệu coi là nổi bật
                  ORDER BY RAND() 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getNewProducts($limit = 6) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.created_at DESC 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getProductsByCategory($category_id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.category_id = ?
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category_id]);
        return $stmt->fetchAll();
    }

    public function addProduct($data) {
        $query = "INSERT INTO {$this->table} 
                 (name, description, price, image, category_id) 
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['category_id']
        ]);
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE {$this->table} 
                 SET name = ?, description = ?, price = ?, 
                     image = ?, category_id = ?, updated_at = NOW()
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image'],
            $data['category_id'],
            $id
        ]);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function searchProducts($keyword) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM {$this->table} p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.name LIKE ? OR p.description LIKE ?
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    // Thêm vào ProductModel.php
public function getPaginatedProducts($page = 1, $limit = 10, $category_id = null, $search = null) {
    $offset = ($page - 1) * $limit;
    
    $query = "SELECT SQL_CALC_FOUND_ROWS p.*, c.name as category_name 
              FROM {$this->table} p
              LEFT JOIN category c ON p.category_id = c.id
              WHERE 1=1";
    
    $params = [];
    
    if ($category_id) {
        $query .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    if ($search) {
        $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get total count
    $stmt = $this->conn->prepare("SELECT FOUND_ROWS() as total");
    $stmt->execute();
    $total = $stmt->fetch()->total;
    
    return [
        'products' => $products,
        'total' => $total
    ];
}
}