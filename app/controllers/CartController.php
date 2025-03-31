<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/helpers/SessionHelper.php');

class CartController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        SessionHelper::startSession();
    }

    // Xem giỏ hàng
    public function index() {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để xem giỏ hàng');
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

        include 'app/views/cart/index.php';
    }

    // Thêm sản phẩm vào giỏ hàng
    public function add($product_id) {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng');
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            $this->redirectWithError('/webbanhang/Product', 'Sản phẩm không tồn tại');
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

        $this->redirectWithSuccess('/webbanhang/Cart', 'Đã thêm sản phẩm vào giỏ hàng');
    }

    // Cập nhật số lượng sản phẩm
    public function update() {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để cập nhật giỏ hàng');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $quantity = (int)$quantity;
                    if ($quantity > 0) {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$product_id]);
                    }
                }
            }
            $this->redirectWithSuccess('/webbanhang/Cart', 'Cập nhật giỏ hàng thành công');
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function remove($product_id) {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để thao tác với giỏ hàng');
        }

        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $this->redirectWithSuccess('/webbanhang/Cart', 'Đã xóa sản phẩm khỏi giỏ hàng');
        } else {
            $this->redirectWithError('/webbanhang/Cart', 'Sản phẩm không có trong giỏ hàng');
        }
    }

    // Thanh toán
    public function checkout() {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để thanh toán');
        }

        if (empty($_SESSION['cart'])) {
            $this->redirectWithError('/webbanhang/Cart', 'Giỏ hàng trống, không thể thanh toán');
        }

        include 'app/views/cart/checkout.php';
    }

    // Xử lý thanh toán
    public function processCheckout() {
        if (!SessionHelper::isLoggedIn()) {
            $this->redirectWithError('/webbanhang/Account/login', 'Vui lòng đăng nhập để thanh toán');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $payment_method = $_POST['payment_method'] ?? 'cod';

            // Validate input
            $errors = [];
            if (empty($name)) $errors['name'] = 'Vui lòng nhập họ tên';
            if (empty($phone)) $errors['phone'] = 'Vui lòng nhập số điện thoại';
            if (empty($address)) $errors['address'] = 'Vui lòng nhập địa chỉ';

            if (!empty($errors)) {
                $_SESSION['checkout_errors'] = $errors;
                $_SESSION['old_checkout_input'] = $_POST;
                $this->redirectWithError('/webbanhang/Cart/checkout', 'Vui lòng kiểm tra lại thông tin');
            }

            // Lưu đơn hàng vào database
            try {
                $this->db->beginTransaction();

                // Lưu thông tin đơn hàng
                $query = "INSERT INTO orders (account_id, name, phone, address, payment_method, total) 
                          VALUES (:account_id, :name, :phone, :address, :payment_method, :total)";
                $stmt = $this->db->prepare($query);

                $total = 0;
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    $product = $this->productModel->getProductById($product_id);
                    $total += $product->price * $item['quantity'];
                }

                $stmt->bindParam(':account_id', SessionHelper::getUserId(), PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                $stmt->bindParam(':address', $address, PDO::PARAM_STR);
                $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
                $stmt->bindParam(':total', $total, PDO::PARAM_STR);
                $stmt->execute();

                $order_id = $this->db->lastInsertId();

                // Lưu chi tiết đơn hàng
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    $product = $this->productModel->getProductById($product_id);
                    
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    
                    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $product->price, PDO::PARAM_STR);
                    $stmt->execute();
                }

                $this->db->commit();

                // Xóa giỏ hàng
                unset($_SESSION['cart']);

                // Chuyển hướng đến trang cảm ơn
                $this->redirectWithSuccess('/webbanhang/Cart/thankyou', 'Đặt hàng thành công. Cảm ơn bạn!');
            } catch(Exception $e) {
                $this->db->rollBack();
                error_log("Checkout error: " . $e->getMessage());
                $this->redirectWithError('/webbanhang/Cart/checkout', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại');
            }
        }
    }

    // Trang cảm ơn sau khi đặt hàng
    public function thankyou() {
        include 'app/views/cart/thankyou.php';
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
}