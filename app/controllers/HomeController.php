<?php
class HomeController {
    public function __construct() {
        echo "<h1>HomeController hoạt động!</h1>";
        
        // Hoặc test phương thức
        $this->index();
    }

    public function index() {
        echo "<p>Đây là trang chủ</p>";
        // Hoặc include view
        // include __DIR__.'/../views/home.php';
    }
}