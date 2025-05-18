<?php
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            http_response_code(500);
            include __DIR__ . '/../views/product/notfound.php';
            exit();
        }
        $this->accountModel = new AccountModel($this->db);
    }

    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include_once 'app/views/account/register.php';
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        include_once 'app/views/account/login.php';
    }

    public function save()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['fullname'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmpassword'] ?? '');
            $role = trim($_POST['role'] ?? 'user');

            $errors = [];
            if (empty($username)) {
                $errors['username'] = "Vui lòng nhập tên tài khoản!";
            } elseif (strlen($username) < 3 || strlen($username) > 50) {
                $errors['username'] = "Tên tài khoản phải từ 3 đến 50 ký tự!";
            }
            if (empty($fullName)) {
                $errors['fullname'] = "Vui lòng nhập họ và tên!";
            } elseif (strlen($fullName) < 3 || strlen($fullName) > 100) {
                $errors['fullname'] = "Họ và tên phải từ 3 đến 100 ký tự!";
            }
            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            } elseif (strlen($password) < 6) {
                $errors['password'] = "Mật khẩu phải ít nhất 6 ký tự!";
            }
            if ($password !== $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận không khớp!";
            }
            if (!in_array($role, ['admin', 'user'])) {
                $role = 'user';
            }
            if ($this->accountModel->getAccountByUsername($username)) {
                $errors['account'] = "Tài khoản này đã tồn tại!";
            }

            if (empty($errors)) {
                $result = $this->accountModel->save($username, $fullName, $password, $role);
                if ($result) {
                    $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
                    header('Location: /WebBanHang/account/login');
                    exit;
                } else {
                    $errors['general'] = "Đã có lỗi xảy ra khi đăng ký. Vui lòng thử lại!";
                }
            }

            include_once 'app/views/account/register.php';
        }
    }

    public function checkLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $errors = [];
            if (empty($username)) {
                $errors['username'] = "Vui lòng nhập tên tài khoản!";
            }
            if (empty($password)) {
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            }

            if (empty($errors)) {
                $account = $this->accountModel->getAccountByUsername($username);
                if ($account && password_verify($password, $account->password)) {
                    $_SESSION['username'] = $account->username;
                    $_SESSION['role'] = $account->role;
                    $_SESSION['fullname'] = $account->fullname;
                    header('Location: /WebBanHang/Product');
                    exit;
                } else {
                    $errors['login'] = $account ? "Mật khẩu không đúng!" : "Tài khoản không tồn tại!";
                }
            }

            include_once 'app/views/account/login.php';
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        unset($_SESSION['fullname']);
        header('Location: /WebBanHang/Product');
        exit;
    }
}
?>