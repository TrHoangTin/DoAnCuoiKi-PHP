<?php
require_once __DIR__ . '/../app/core/App.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/helpers/SessionHelper.php';

SessionHelper::startSession();

// Autoload models
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../app/models/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Kết nối database và load dữ liệu
$db = (new Database())->getConnection();
$productModel = new ProductModel($db);
$categoryModel = new CategoryModel($db);

// Lấy dữ liệu cho trang chủ
$featuredProducts = $productModel->getFeaturedProducts(8);
$newProducts = $productModel->getNewProducts(6);
$categories = $categoryModel->getCategoriesWithCount();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechShop - Cửa hàng công nghệ hàng đầu</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/webbanhang/public/assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--secondary-color);
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/webbanhang/public/assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        
        .product-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        
        .product-img {
            height: 200px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 20px;
        }
        
        .badge-sale {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
            background-color: var(--accent-color);
        }
        
        .category-card {
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            margin-bottom: 20px;
            height: 150px;
        }
        
        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .category-card:hover img {
            transform: scale(1.1);
        }
        
        .category-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px;
            margin: 0;
        }
        
        footer {
            background-color: var(--secondary-color);
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/webbanhang/">
                <i class="fas fa-laptop-code me-2"></i>TechShop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/webbanhang/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/webbanhang/product">Sản phẩm</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="/webbanhang/category/<?= $category->id ?>">
                                        <?= htmlspecialchars($category->name) ?> (<?= $category->product_count ?>)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Liên hệ</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="/webbanhang/cart" class="btn btn-outline-dark me-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-danger">3</span>
                    </a>
                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= SessionHelper::getUsername() ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/webbanhang/account/profile"><i class="fas fa-user me-2"></i>Tài khoản</a></li>
                                <li><a class="dropdown-item" href="/webbanhang/account/orders"><i class="fas fa-list-alt me-2"></i>Đơn hàng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/webbanhang/account/logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/webbanhang/account/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Công nghệ mới nhất 2023</h1>
            <p class="lead mb-5">Khám phá các sản phẩm công nghệ hàng đầu với mức giá tốt nhất</p>
            <a href="/webbanhang/product" class="btn btn-primary btn-lg px-4 me-2">
                <i class="fas fa-shopping-bag me-2"></i>Mua ngay
            </a>
            <a href="#featured" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-arrow-down me-2"></i>Khám phá
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="py-5 bg-light">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">Sản phẩm nổi bật</h2>
                <p class="text-muted">Những sản phẩm được ưa chuộng nhất</p>
            </div>
            
            <div class="owl-carousel owl-theme">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="item">
                    <div class="product-card">
                        <div class="position-relative">
                            <img src="/webbanhang/public/<?= $product->image ?>" class="product-img w-100" alt="<?= htmlspecialchars($product->name) ?>">
                            <span class="badge badge-sale">Sale</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger fw-bold"><?= number_format($product->price, 0, ',', '.') ?>₫</span>
                                <del class="text-muted small"><?= number_format($product->price * 1.2, 0, ',', '.') ?>₫</del>
                            </div>
                            <div class="d-grid mt-3">
                                <a href="/webbanhang/product/<?= $product->id ?>" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-5">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">Danh mục sản phẩm</h2>
                <p class="text-muted">Lựa chọn theo nhu cầu của bạn</p>
            </div>
            
            <div class="row">
                <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                <div class="col-md-4 col-6">
                    <a href="/webbanhang/category/<?= $category->id ?>" class="text-decoration-none">
                        <div class="category-card">
                            <img src="/webbanhang/public/assets/images/category-<?= $category->id ?>.jpg" alt="<?= htmlspecialchars($category->name) ?>">
                            <h3 class="category-title"><?= htmlspecialchars($category->name) ?> (<?= $category->product_count ?>)</h3>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="/webbanhang/category" class="btn btn-outline-primary">Xem tất cả danh mục</a>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-title text-center mb-5">
                <h2 class="fw-bold">Sản phẩm mới</h2>
                <p class="text-muted">Những sản phẩm mới nhất của chúng tôi</p>
            </div>
            
            <div class="row">
                <?php foreach ($newProducts as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="product-card h-100">
                        <div class="position-relative">
                            <img src="/webbanhang/public/<?= $product->image ?>" class="product-img w-100" alt="<?= htmlspecialchars($product->name) ?>">
                            <span class="badge bg-success">Mới</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                            <p class="card-text text-muted small"><?= substr(htmlspecialchars($product->description), 0, 100) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger fw-bold"><?= number_format($product->price, 0, ',', '.') ?>₫</span>
                                <a href="/webbanhang/cart/add/<?= $product->id ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="/webbanhang/product" class="btn btn-primary">Xem tất cả sản phẩm</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="p-4 rounded bg-white shadow-sm">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h4>Miễn phí vận chuyển</h4>
                        <p class="text-muted mb-0">Cho đơn hàng từ 500.000đ</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4 rounded bg-white shadow-sm">
                        <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                        <h4>Đổi trả trong 7 ngày</h4>
                        <p class="text-muted mb-0">Hoàn tiền 100% nếu không hài lòng</p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="p-4 rounded bg-white shadow-sm">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h4>Hỗ trợ 24/7</h4>
                        <p class="text-muted mb-0">Đội ngũ CSKH luôn sẵn sàng</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="text-white mb-4">TechShop</h4>
                    <p>Cửa hàng công nghệ hàng đầu Việt Nam với các sản phẩm chất lượng cao và giá cả hợp lý.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-white mb-4">Liên kết</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/webbanhang/" class="text-white-50">Trang chủ</a></li>
                        <li class="mb-2"><a href="/webbanhang/product" class="text-white-50">Sản phẩm</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Giới thiệu</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Liên hệ</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-white mb-4">Hỗ trợ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50">Trung tâm hỗ trợ</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Chính sách bảo mật</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Điều khoản dịch vụ</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Chính sách đổi trả</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Hướng dẫn mua hàng</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5 class="text-white mb-4">Liên hệ</h5>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2 text-white"></i> 123 Đường ABC, Quận 1, TP.HCM</li>
                        <li class="mb-2"><i class="fas fa-phone me-2 text-white"></i> (028) 1234 5678</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-white"></i> info@techshop.vn</li>
                        <li class="mb-2"><i class="fas fa-clock me-2 text-white"></i> 8:00 - 22:00 hàng ngày</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-0 mb-4 bg-light">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 small">&copy; 2023 TechShop. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <img src="/webbanhang/public/assets/images/payment-methods.png" alt="Payment Methods" class="img-fluid" style="max-height: 30px;">
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                responsive: {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 }
                }
            });
        });
    </script>
</body>
</html>