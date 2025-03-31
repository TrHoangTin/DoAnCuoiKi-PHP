<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class CartController {
    private $productModel;
    private $orderModel;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->productModel = new ProductModel($db);
        $this->orderModel = new OrderModel($db);
        SessionHelper::startSession();
    }

    public function index() {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập để xem giỏ hàng');
            header('Location: /webbanhang/account/login');
            exit();
        }

        $cart = $_SESSION['cart'] ?? [];
        $products = [];
        $total = 0;

        foreach ($cart as $product_id => $item) {
            $product = $this->productModel->getProductById($product_id);
            if ($product) {
                $product->quantity = $item['quantity'];
                $product->subtotal = $product->price * $item['quantity'];
                $products[] = $product;
                $total += $product->subtotal;
            }
        }

        require_once __DIR__ . '/../views/cart/index.php';
    }

    public function add($product_id) {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập để thêm sản phẩm');
            header('Location: /webbanhang/account/login');
            exit();
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            SessionHelper::setFlash('error_message', 'Sản phẩm không tồn tại');
            header('Location: /webbanhang/product');
            exit();
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity']++;
        } else {
            $_SESSION['cart'][$product_id] = [
                'quantity' => 1,
                'price' => $product->price
            ];
        }

        SessionHelper::setFlash('success_message', 'Đã thêm sản phẩm vào giỏ hàng');
        header('Location: /webbanhang/cart');
        exit();
    }

    public function update() {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập');
            header('Location: /webbanhang/account/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                $quantity = (int)$quantity;
                if (isset($_SESSION['cart'][$product_id])) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$product_id]);
                    }
                }
            }
            SessionHelper::setFlash('success_message', 'Cập nhật giỏ hàng thành công');
        }
        header('Location: /webbanhang/cart');
        exit();
    }

    public function remove($product_id) {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập');
            header('Location: /webbanhang/account/login');
            exit();
        }

        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            SessionHelper::setFlash('success_message', 'Đã xóa sản phẩm khỏi giỏ hàng');
        } else {
            SessionHelper::setFlash('error_message', 'Sản phẩm không có trong giỏ hàng');
        }
        header('Location: /webbanhang/cart');
        exit();
    }

    public function checkout() {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập');
            header('Location: /webbanhang/account/login');
            exit();
        }

        if (empty($_SESSION['cart'])) {
            SessionHelper::setFlash('error_message', 'Giỏ hàng trống');
            header('Location: /webbanhang/cart');
            exit();
        }

        $cart = $_SESSION['cart'];
        $products = [];
        $total = 0;

        foreach ($cart as $product_id => $item) {
            $product = $this->productModel->getProductById($product_id);
            if ($product) {
                $product->quantity = $item['quantity'];
                $product->subtotal = $product->price * $item['quantity'];
                $products[] = $product;
                $total += $product->subtotal;
            }
        }

        require_once __DIR__ . '/../views/cart/checkout.php';
    }

    public function processCheckout() {
        if (!SessionHelper::isLoggedIn()) {
            SessionHelper::setFlash('error_message', 'Vui lòng đăng nhập');
            header('Location: /webbanhang/account/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $payment_method = $_POST['payment_method'] ?? 'cod';
            $notes = $_POST['notes'] ?? '';

            // Validate
            $errors = [];
            if (empty($name)) $errors['name'] = 'Vui lòng nhập họ tên';
            if (empty($phone)) $errors['phone'] = 'Vui lòng nhập số điện thoại';
            if (empty($address)) $errors['address'] = 'Vui lòng nhập địa chỉ';

            if (!empty($errors)) {
                $_SESSION['checkout_errors'] = $errors;
                $_SESSION['old_checkout_input'] = $_POST;
                header('Location: /webbanhang/cart/checkout');
                exit();
            }

            // Tính tổng tiền
            $cart = $_SESSION['cart'];
            $total = 0;
            $items = [];
            foreach ($cart as $product_id => $item) {
                $product = $this->productModel->getProductById($product_id);
                if ($product) {
                    $total += $product->price * $item['quantity'];
                    $items[] = [
                        'product_id' => $product_id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ];
                }
            }

            // Tạo đơn hàng
            try {
                $order_id = $this->orderModel->createOrder(
                    SessionHelper::getUserId(),
                    $name,
                    $phone,
                    $address,
                    $payment_method,
                    $total,
                    $items
                );

                // Xóa giỏ hàng
                unset($_SESSION['cart']);

                SessionHelper::setFlash('success_message', 'Đặt hàng thành công. Mã đơn hàng: #' . $order_id);
                header('Location: /webbanhang/cart/thankyou');
                exit();
            } catch (Exception $e) {
                error_log('Checkout error: ' . $e->getMessage());
                SessionHelper::setFlash('error_message', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại');
                header('Location: /webbanhang/cart/checkout');
                exit();
            }
        }

        header('Location: /webbanhang/cart');
        exit();
    }

    public function thankyou() {
        require_once __DIR__ . '/../views/cart/thankyou.php';
    }
}