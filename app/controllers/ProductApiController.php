<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../utils/JWTHandler.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class ProductApiController {
    private $productModel;
    private $jwtHandler;

    public function __construct() {
        $this->productModel = new ProductModel((new Database())->getConnection());
        $this->jwtHandler = new JWTHandler();
    }

    // Middleware xác thực JWT và kiểm tra quyền admin
    private function authenticateAdmin() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
            exit;
        }

        $token = $matches[1];
        $decoded = $this->jwtHandler->decode($token);
        
        if (!$decoded || ($decoded['data']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Truy cập bị từ chối']);
            exit;
        }
    }

    // Middleware xác thực JWT cơ bản
    private function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token không hợp lệ']);
            exit;
        }

        $token = $matches[1];
        $decoded = $this->jwtHandler->decode($token);
        
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn']);
            exit;
        }
    }

    // Lấy danh sách sản phẩm (public)
    public function index() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $category_id = $_GET['category_id'] ?? null;
            $search = $_GET['search'] ?? null;

            // Validate input
            $page = max(1, (int)$page);
            $limit = max(1, min(100, (int)$limit));

            $result = $this->productModel->getPaginatedProducts($page, $limit, $category_id, $search);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $result['products'],
                'pagination' => [
                    'total' => $result['total'],
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($result['total'] / $limit)
                ]
            ]);
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }

    // Lấy thông tin sản phẩm theo ID (public)
    public function show($id) {
        try {
            $product = $this->productModel->getProductById($id);
            
            if ($product) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $product
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }

    // Thêm sản phẩm mới (admin only)
    public function store() {
        $this->authenticateAdmin();
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dữ liệu JSON không hợp lệ']);
                return;
            }
            
            // Validate required fields
            $requiredFields = ['name', 'description', 'price', 'category_id'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Thiếu trường bắt buộc: $field"]);
                    return;
                }
            }
            
            // Process image if exists (base64 encoded)
            $imagePath = null;
            if (!empty($data['image'])) {
                $imagePath = $this->processBase64Image($data['image']);
            }
            
            $result = $this->productModel->addProduct([
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'category_id' => $data['category_id'],
                'image' => $imagePath
            ]);
            
            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Tạo sản phẩm thành công',
                    'data' => ['id' => $result]
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Không thể tạo sản phẩm']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }

    // Cập nhật sản phẩm (admin only)
    public function update($id) {
        $this->authenticateAdmin();
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Dữ liệu JSON không hợp lệ']);
                return;
            }
            
            // Get existing product
            $existingProduct = $this->productModel->getProductById($id);
            if (!$existingProduct) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                return;
            }
            
            // Process image if exists (base64 encoded)
            $imagePath = $existingProduct->image;
            if (!empty($data['image'])) {
                // Delete old image if exists
                if ($imagePath && file_exists(__DIR__ . '/../../public/' . $imagePath)) {
                    unlink(__DIR__ . '/../../public/' . $imagePath);
                }
                $imagePath = $this->processBase64Image($data['image']);
            }
            
            $result = $this->productModel->updateProduct($id, [
                'name' => $data['name'] ?? $existingProduct->name,
                'description' => $data['description'] ?? $existingProduct->description,
                'price' => $data['price'] ?? $existingProduct->price,
                'category_id' => $data['category_id'] ?? $existingProduct->category_id,
                'image' => $imagePath
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật sản phẩm thành công'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật sản phẩm']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }

    // Xóa sản phẩm (admin only)
    public function destroy($id) {
        $this->authenticateAdmin();
        
        try {
            // Get product first to delete image
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                return;
            }
            
            // Delete image if exists
            if ($product->image && file_exists(__DIR__ . '/../../public/' . $product->image)) {
                unlink(__DIR__ . '/../../public/' . $product->image);
            }
            
            $result = $this->productModel->deleteProduct($id);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa sản phẩm thành công'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm']);
            }
        } catch(Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
    }

    // Xử lý ảnh base64
    private function processBase64Image($base64Image) {
        try {
            $targetDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Extract image type and data
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                throw new Exception('Định dạng ảnh không hợp lệ');
            }
            
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $data = base64_decode($data);
            
            if ($data === false) {
                throw new Exception('Không thể giải mã base64');
            }
            
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $type = strtolower($type[1]);
            if (!in_array($type, $allowedTypes)) {
                throw new Exception('Loại ảnh không được hỗ trợ');
            }
            
            $filename = uniqid() . '.' . $type;
            $targetFile = $targetDir . $filename;
            
            if (!file_put_contents($targetFile, $data)) {
                throw new Exception('Không thể lưu ảnh');
            }
            
            return 'uploads/' . $filename;
        } catch (Exception $e) {
            error_log('Image processing error: ' . $e->getMessage());
            return null;
        }
    }
}