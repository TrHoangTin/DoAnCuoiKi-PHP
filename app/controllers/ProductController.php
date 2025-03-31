<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductController {
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index() {
        try {
            $products = $this->productModel->getProducts();
            include 'app/views/product/list.php';
        } catch(Exception $e) {
            $this->handleError("Error loading product list: " . $e->getMessage());
        }
    }

    public function show($id) {
        try {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                include 'app/views/product/show.php';
            } else {
                $this->redirectWithError('/webbanhang/Product', 'Product not found');
            }
        } catch(Exception $e) {
            $this->handleError("Error showing product: " . $e->getMessage());
        }
    }

    public function add() {
        try {
            $categories = $this->categoryModel->getCategories();
            include 'app/views/product/add.php';
        } catch(Exception $e) {
            $this->handleError("Error loading add product form: " . $e->getMessage());
        }
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? '';
                $category_id = $_POST['category_id'] ?? null;
                
                $image = $this->handleImageUpload();

                $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

                if (is_array($result)) {
                    $categories = $this->categoryModel->getCategories();
                    $errors = $result;
                    include 'app/views/product/add.php';
                } elseif ($result) {
                    $this->redirectWithSuccess('/webbanhang/Product', 'Product added successfully');
                } else {
                    $this->redirectWithError('/webbanhang/Product/add', 'Failed to add product');
                }
            } catch(Exception $e) {
                $this->redirectWithError('/webbanhang/Product/add', 'Error: ' . $e->getMessage());
            }
        } else {
            $this->redirectWithError('/webbanhang/Product', 'Invalid request method');
        }
    }

    public function edit($id) {
        try {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $categories = $this->categoryModel->getCategories();
                include 'app/views/product/edit.php';
            } else {
                $this->redirectWithError('/webbanhang/Product', 'Product not found');
            }
        } catch(Exception $e) {
            $this->handleError("Error loading edit form: " . $e->getMessage());
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $id = $_POST['id'] ?? null;
                $name = $_POST['name'] ?? '';
                $description = $_POST['description'] ?? '';
                $price = $_POST['price'] ?? '';
                $category_id = $_POST['category_id'] ?? null;
                $existing_image = $_POST['existing_image'] ?? null;
                
                $image = $this->handleImageUpload();
                if (!$image && $existing_image) {
                    $image = $existing_image;
                }

                $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

                if ($result) {
                    $this->redirectWithSuccess('/webbanhang/Product', 'Product updated successfully');
                } else {
                    $this->redirectWithError('/webbanhang/Product/edit/' . $id, 'Failed to update product');
                }
            } catch(Exception $e) {
                $this->redirectWithError('/webbanhang/Product/edit/' . $id, 'Error: ' . $e->getMessage());
            }
        } else {
            $this->redirectWithError('/webbanhang/Product', 'Invalid request method');
        }
    }

    public function delete($id) {
        try {
            $result = $this->productModel->deleteProduct($id);
            if ($result) {
                $this->redirectWithSuccess('/webbanhang/Product', 'Product deleted successfully');
            } else {
                $this->redirectWithError('/webbanhang/Product', 'Failed to delete product');
            }
        } catch(Exception $e) {
            $this->redirectWithError('/webbanhang/Product', 'Error deleting product: ' . $e->getMessage());
        }
    }

    private function handleImageUpload() {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $file_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                throw new Exception("File is not an image");
            }

            // Check file size (max 10MB)
            if ($_FILES["image"]["size"] > 10 * 1024 * 1024) {
                throw new Exception("Image is too large (max 10MB)");
            }

            // Allow certain file formats
            $allowed_types = ['jpg', 'png', 'jpeg', 'gif'];
            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed");
            }

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                return $target_file;
            } else {
                throw new Exception("Error uploading image");
            }
        }
        return null;
    }

    private function redirectWithError($url, $message) {
        $_SESSION['error_message'] = $message;
        header("Location: $url");
        exit();
    }

    private function redirectWithSuccess($url, $message) {
        $_SESSION['success_message'] = $message;
        header("Location: $url");
        exit();
    }

    private function handleError($message) {
        error_log($message);
        $this->redirectWithError('/webbanhang/Product', 'An error occurred. Please try again later.');
    }
}