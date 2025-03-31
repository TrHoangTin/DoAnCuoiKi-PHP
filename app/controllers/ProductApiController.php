<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/utils/JWTHandler.php');
require_once('app/helpers/SessionHelper.php');

class ProductApiController {
    private $productModel;
    private $jwtHandler;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    // Middleware xác thực JWT
    private function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $decoded = $this->jwtHandler->decode($token);
            
            if ($decoded) {
                return true;
            }
        }
        
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Lấy danh sách sản phẩm
    public function index() {
        $this->authenticate();
        
        try {
            $products = $this->productModel->getProducts();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $products]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id) {
        $this->authenticate();
        
        try {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $product]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Product not found']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Thêm sản phẩm mới
    public function store() {
        $this->authenticate();
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;
            
            $result = $this->productModel->addProduct($name, $description, $price, $category_id);
            
            if (is_array($result)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $result]);
            } elseif ($result) {
                http_response_code(201);
                echo json_encode(['success' => true, 'message' => 'Product created', 'id' => $result]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to create product']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Cập nhật sản phẩm
    public function update($id) {
        $this->authenticate();
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $name = $data['name'] ?? '';
            $description = $data['description'] ?? '';
            $price = $data['price'] ?? '';
            $category_id = $data['category_id'] ?? null;
            
            $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product updated']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to update product']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Xóa sản phẩm
    public function destroy($id) {
        $this->authenticate();
        
        try {
            $result = $this->productModel->deleteProduct($id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Product deleted']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}