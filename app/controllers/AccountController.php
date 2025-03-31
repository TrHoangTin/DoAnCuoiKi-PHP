<?php
require_once __DIR__ . '/../models/AccountModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../utils/JWTHandler.php';

class AccountController {
    private $accountModel;
    private $jwtHandler;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($db);
        $this->jwtHandler = new JWTHandler();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $account = $this->accountModel->authenticate($username, $password);

            if ($account) {
                SessionHelper::setUser($account);
                
                // Tạo JWT token
                $token = $this->jwtHandler->encode([
                    'id' => $account->id,
                    'username' => $account->username,
                    'role' => $account->role
                ]);
                
                $_SESSION['jwt_token'] = $token;

                SessionHelper::setFlash('success_message', 'Đăng nhập thành công');
                header('Location: /webbanhang/');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Tên đăng nhập hoặc mật khẩu không đúng');
                header('Location: /webbanhang/account/login');
                exit();
            }
        }

        require_once __DIR__ . '/../views/account/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $fullname = $_POST['fullname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';

            // Validate
            $errors = [];
            if (empty($username)) $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            if (empty($password)) $errors['password'] = 'Vui lòng nhập mật khẩu';
            if ($password !== $confirm_password) $errors['confirm_password'] = 'Mật khẩu không khớp';
            if (empty($fullname)) $errors['fullname'] = 'Vui lòng nhập họ tên';

            // Kiểm tra username tồn tại
            if ($this->accountModel->getAccountByUsername($username)) {
                $errors['username'] = 'Tên đăng nhập đã được sử dụng';
            }

            if (empty($errors)) {
                $result = $this->accountModel->createAccount(
                    $username, 
                    $password, 
                    $fullname, 
                    $email, 
                    $phone
                );

                if ($result) {
                    SessionHelper::setFlash('success_message', 'Đăng ký thành công. Vui lòng đăng nhập');
                    header('Location: /webbanhang/account/login');
                    exit();
                } else {
                    $errors['system'] = 'Đăng ký không thành công. Vui lòng thử lại';
                }
            }

            $_SESSION['register_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /webbanhang/account/register');
            exit();
        }

        require_once __DIR__ . '/../views/account/register.php';
    }

    public function logout() {
        SessionHelper::destroySession();
        SessionHelper::setFlash('success_message', 'Đăng xuất thành công');
        header('Location: /webbanhang/');
        exit();
    }

    public function profile() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /webbanhang/account/login');
            exit();
        }

        $userId = SessionHelper::getUserId();
        $account = $this->accountModel->getAccountById($userId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            $result = $this->accountModel->updateAccount($userId, [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address
            ]);

            if ($result) {
                SessionHelper::setFlash('success_message', 'Cập nhật thông tin thành công');
                header('Location: /webbanhang/account/profile');
                exit();
            } else {
                SessionHelper::setFlash('error_message', 'Cập nhật thông tin không thành công');
            }
        }

        require_once __DIR__ . '/../views/account/profile.php';
    }

    public function orders() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /webbanhang/account/login');
            exit();
        }

        $orderModel = new OrderModel((new Database())->getConnection());
        $orders = $orderModel->getOrdersByAccount(SessionHelper::getUserId());

        require_once __DIR__ . '/../views/account/orders.php';
    }
}