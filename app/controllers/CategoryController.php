<?php
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($db);
    }

    public function index() {
        $categories = $this->categoryModel->getCategoriesWithCount();
        require_once __DIR__ . '/../views/category/list.php';
    }

    public function show($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            SessionHelper::setFlash('error_message', 'Danh mục không tồn tại');
            header('Location: /webbanhang/category');
            exit();
        }

        $products = (new ProductModel((new Database())->getConnection()))->getProductsByCategory($id);
        require_once __DIR__ . '/../views/category/show.php';
    }

    public function add() {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                SessionHelper::setFlash('error_message', 'Vui lòng nhập tên danh mục');
                header('Location: /webbanhang/category/add');
                exit();
            }

            $result = $this->categoryModel->addCategory($name, $description);
            if ($result) {
                SessionHelper::setFlash('success_message', 'Thêm danh mục thành công');
                header('Location: /webbanhang/category');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Thêm danh mục không thành công');
            }
        }

        require_once __DIR__ . '/../views/category/add.php';



    }
    

    public function edit($id) {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            SessionHelper::setFlash('error_message', 'Danh mục không tồn tại');
            header('Location: /webbanhang/category');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                SessionHelper::setFlash('error_message', 'Vui lòng nhập tên danh mục');
                header('Location: /webbanhang/category/edit/' . $id);
                exit();
            }

            $result = $this->categoryModel->updateCategory($id, $name, $description);
            if ($result) {
                SessionHelper::setFlash('success_message', 'Cập nhật danh mục thành công');
                header('Location: /webbanhang/category');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Cập nhật danh mục không thành công');
            }
        }

        require_once __DIR__ . '/../views/category/edit.php';
    }

    public function delete($id) {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::setFlash('error_message', 'Bạn không có quyền truy cập');
            header('Location: /webbanhang/');
            exit();
        }

        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            SessionHelper::setFlash('success_message', 'Xóa danh mục thành công');
        } else {
            SessionHelper::setFlash('error_message', 'Xóa danh mục không thành công');
        }
        header('Location: /webbanhang/category');
        exit();
    }
}