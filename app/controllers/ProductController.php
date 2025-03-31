<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class ProductController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    public function index() {
        $products = $this->productModel->getProducts();
        require_once __DIR__ . '/../../app/views/product/list.php';

    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            SessionHelper::setFlash('error_message', 'Sản phẩm không tồn tại');
            header('Location: /webbanhang/product');
            exit();
        }
        require_once __DIR__ . '/../app/views/product/show.php';
    }

    public function add() {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'image' => $this->handleImageUpload()
            ];

            $result = $this->productModel->addProduct($data);

            if ($result) {
                SessionHelper::setFlash('success_message', 'Thêm sản phẩm thành công');
                header('Location: /webbanhang/product');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Thêm sản phẩm không thành công');
            }
        }

        require_once __DIR__ . '/../views/product/add.php';

    }

    public function edit($id) {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            SessionHelper::setFlash('error_message', 'Sản phẩm không tồn tại');
            header('Location: /webbanhang/product');
            exit();
        }

        $categories = $this->categoryModel->getCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'image' => $this->handleImageUpload() ?? $product->image
            ];

            $result = $this->productModel->updateProduct($id, $data);

            if ($result) {
                SessionHelper::setFlash('success_message', 'Cập nhật sản phẩm thành công');
                header('Location: /webbanhang/product');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Cập nhật sản phẩm không thành công');
            }
        }

        require_once __DIR__ . '/../views/product/edit.php';

    }

    public function delete($id) {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            SessionHelper::setFlash('success_message', 'Xóa sản phẩm thành công');
        } else {
            SessionHelper::setFlash('error_message', 'Xóa sản phẩm không thành công');
        }
        header('Location: /webbanhang/product');
        exit();
    }

    private function handleImageUpload() {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;

            // Kiểm tra loại file
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                SessionHelper::setFlash('error_message', 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG hoặc GIF');
                return null;
            }

            // Kiểm tra kích thước file (tối đa 5MB)
            if ($_FILES['image']['size'] > 5000000) {
                SessionHelper::setFlash('error_message', 'File ảnh quá lớn (tối đa 5MB)');
                return null;
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                return 'uploads/' . $fileName;
            }
        }
        return null;
    }
}