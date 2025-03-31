<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

class HomeController {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db);
    }

    public function index() {
        $featuredProducts = $this->productModel->getFeaturedProducts(8);
        $newProducts = $this->productModel->getNewProducts(6);
        $categories = $this->categoryModel->getCategoriesWithCount();
        
        $viewPath = __DIR__ . '/../views/home/index.php';
        require_once $viewPath;
    }
}