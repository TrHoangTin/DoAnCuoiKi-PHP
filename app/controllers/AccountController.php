<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/utils/JWTHandler.php');

class AccountController {
    private $accountModel;
    private $jwtHandler;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    // Hiển thị form đăng nhập
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            include 'app/views/account/login.php';
        }
    }

    // Xử lý đăng nhập
    private function processLogin() {
        try {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $account = $this->accountModel->authenticate($username, $password);

            if ($account) {
                $_SESSION['user_id'] = $account->id;
                $_SESSION['username'] = $account->username;
                $_SESSION['role'] = $account->role;
                
                // Tạo JWT token cho API
                $token = $this->jwtHandler->encode([
                    'id' => $account->id,
                    'username' => $account->username,
                    'role' => $account->role
                ]);

                $_SESSION['jwt_token'] = $token;

                $this->redirectWithSuccess('/webbanhang/Product', 'Đăng nhập thành công');
            } else {
                $this->redirectWithError('/webbanhang/Account/login', 'Tên đăng nhập hoặc mật khẩu không đúng');
            }
        } catch(Exception $e) {
            $this->redirectWithError('/webbanhang/Account/login', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // Hiển thị form đăng ký
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegistration();
        } else {
            include 'app/views/account/register.php';
        }
    }

    // Xử lý đăng ký
    private function processRegistration() {
        try {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $fullname = $_POST['fullname'] ?? '';

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($username)) $errors['username'] = 'Vui lòng nhập tên đăng nhập';
            if (empty($password)) $errors['password'] = 'Vui lòng nhập mật khẩu';
            if ($password !== $confirm_password) $errors['confirm_password'] = 'Mật khẩu không khớp';
            if (empty($fullname)) $errors['fullname'] = 'Vui lòng nhập họ tên';

            // Kiểm tra username đã tồn tại chưa
            if ($this->accountModel->getAccountByUsername($username)) {
                $errors['username'] = 'Tên đăng nhập đã được sử dụng';
            }

            if (!empty($errors)) {
                $_SESSION['register_errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                $this->redirectWithError('/webbanhang/Account/register', 'Vui lòng kiểm tra lại thông tin');
            }

            // Tạo tài khoản
            $result = $this->accountModel->createAccount($username, $password, $fullname);

            if ($result) {
                $this->redirectWithSuccess('/webbanhang/Account/login', 'Đăng ký thành công. Vui lòng đăng nhập');
            } else {
                $this->redirectWithError('/webbanhang/Account/register', 'Đăng ký không thành công. Vui lòng thử lại');
            }
        } catch(Exception $e) {
            $this->redirectWithError('/webbanhang/Account/register', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // Đăng xuất
    public function logout() {
        session_unset();
        session_destroy();
        $this->redirectWithSuccess('/webbanhang/Account/login', 'Đăng xuất thành công');
    }

    // Chuyển hướng với thông báo lỗi
    private function redirectWithError($url, $message) {
        $_SESSION['error_message'] = $message;
        header("Location: $url");
        exit();
    }

    // Chuyển hướng với thông báo thành công
    private function redirectWithSuccess($url, $message) {
        $_SESSION['success_message'] = $message;
        header("Location: $url");
        exit();
    }
}