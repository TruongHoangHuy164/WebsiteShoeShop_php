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

    private function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    private function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['username']);
    }

    private function handleFileUpload($file, &$errors)
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // Không có file được upload
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $uploadDir = 'uploads/avatars/';
        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (!in_array($file['type'], $allowedTypes)) {
            $errors['avatar'] = "Chỉ hỗ trợ file JPEG, PNG hoặc GIF!";
            return null;
        }
        if ($file['size'] > $maxSize) {
            $errors['avatar'] = "File ảnh không được lớn hơn 2MB!";
            return null;
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $filePath;
        } else {
            $errors['avatar'] = "Lỗi khi upload file ảnh!";
            return null;
        }
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
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $avatar = isset($_FILES['avatar']) ? $_FILES['avatar'] : null;

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
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ!";
            }
            if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ!";
            }

            $avatarPath = $this->handleFileUpload($avatar, $errors);

            if (empty($errors)) {
                $result = $this->accountModel->save($username, $fullName, $password, $role, $email, $phone, $avatarPath);
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
                    $_SESSION['user_id'] = $account->id; // Lưu ID để sử dụng
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
        unset($_SESSION['user_id']);
        header('Location: /WebBanHang/Product');
        exit;
    }

    public function listUsers()
    {
        if (!$this->isAdmin()) {
            header('Location: /WebBanHang/account/login');
            exit;
        }

        $users = $this->accountModel->getAllAccounts();
        include_once 'app/views/account/list_users.php';
    }

    public function addUser()
    {
        if (!$this->isAdmin()) {
            header('Location: /WebBanHang/account/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['fullname'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmpassword'] ?? '');
            $role = trim($_POST['role'] ?? 'user');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $avatar = isset($_FILES['avatar']) ? $_FILES['avatar'] : null;

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
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ!";
            }
            if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ!";
            }

            $avatarPath = $this->handleFileUpload($avatar, $errors);

            if (empty($errors)) {
                $result = $this->accountModel->save($username, $fullName, $password, $role, $email, $phone, $avatarPath);
                if ($result) {
                    $_SESSION['success'] = "Thêm người dùng thành công!";
                    header('Location: /WebBanHang/account/listUsers');
                    exit;
                } else {
                    $errors['general'] = "Đã có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại!";
                }
            }

            include_once 'app/views/account/add_user.php';
        } else {
            include_once 'app/views/account/add_user.php';
        }
    }

    public function editUser($id)
    {
        if (!$this->isAdmin()) {
            header('Location: /WebBanHang/account/login');
            exit;
        }

        $user = $this->accountModel->getAccountById($id);
        if (!$user) {
            $_SESSION['error'] = "Người dùng không tồn tại!";
            header('Location: /WebBanHang/account/listUsers');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['fullname'] ?? '');
            $role = trim($_POST['role'] ?? 'user');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmpassword'] ?? '');
            $avatar = isset($_FILES['avatar']) ? $_FILES['avatar'] : null;

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
            if (!in_array($role, ['admin', 'user'])) {
                $role = 'user';
            }
            if ($username !== $user->username && $this->accountModel->getAccountByUsername($username)) {
                $errors['account'] = "Tên tài khoản này đã tồn tại!";
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ!";
            }
            if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ!";
            }
            if (!empty($password) && strlen($password) < 6) {
                $errors['password'] = "Mật khẩu phải ít nhất 6 ký tự!";
            }
            if ($password !== $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận không khớp!";
            }

            $avatarPath = $this->handleFileUpload($avatar, $errors) ?? $user->avatar;
            if ($avatarPath && $user->avatar && file_exists($user->avatar) && $avatarPath !== $user->avatar) {
                unlink($user->avatar); // Xóa avatar cũ
            }

            if (empty($errors)) {
                $result = $this->accountModel->updateAccount($id, $username, $fullName, $role, $email, $phone, $avatarPath, $password ?: null);
                if ($result) {
                    $_SESSION['success'] = "Cập nhật thông tin người dùng thành công!";
                    header('Location: /WebBanHang/account/listUsers');
                    exit;
                } else {
                    $errors['general'] = "Đã có lỗi xảy ra khi cập nhật. Vui lòng thử lại!";
                }
            }

            include_once 'app/views/account/edit_user.php';
        } else {
            include_once 'app/views/account/edit_user.php';
        }
    }

    public function editProfile()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /WebBanHang/account/login');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $this->accountModel->getAccountByUsername($_SESSION['username']);
        if (!$user || $user->id != $_SESSION['user_id']) {
            $_SESSION['error'] = "Không thể truy cập thông tin người dùng!";
            header('Location: /WebBanHang/Product');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirmpassword'] ?? '');
            $avatar = isset($_FILES['avatar']) ? $_FILES['avatar'] : null;

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
            if ($username !== $user->username && $this->accountModel->getAccountByUsername($username)) {
                $errors['account'] = "Tên tài khoản này đã tồn tại!";
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email không hợp lệ!";
            }
            if (!empty($phone) && !preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors['phone'] = "Số điện thoại không hợp lệ!";
            }
            if (!empty($password) && strlen($password) < 6) {
                $errors['password'] = "Mật khẩu phải ít nhất 6 ký tự!";
            }
            if ($password !== $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận không khớp!";
            }

            $avatarPath = $this->handleFileUpload($avatar, $errors) ?? $user->avatar;
            if ($avatarPath && $user->avatar && file_exists($user->avatar) && $avatarPath !== $user->avatar) {
                unlink($user->avatar); // Xóa avatar cũ
            }

            if (empty($errors)) {
                $result = $this->accountModel->updateAccount($user->id, $username, $fullName, $user->role, $email, $phone, $avatarPath, $password ?: null);
                if ($result) {
                    $_SESSION['username'] = $username;
                    $_SESSION['fullname'] = $fullName;
                    $_SESSION['success'] = "Cập nhật thông tin cá nhân thành công!";
                    header('Location: /WebBanHang/Product');
                    exit;
                } else {
                    $errors['general'] = "Đã có lỗi xảy ra khi cập nhật. Vui lòng thử lại!";
                }
            }

            include_once 'app/views/account/edit_profile.php';
        } else {
            include_once 'app/views/account/edit_profile.php';
        }
    }

    public function deleteUser($id)
    {
        if (!$this->isAdmin()) {
            header('Location: /WebBanHang/account/login');
            exit;
        }

        $user = $this->accountModel->getAccountById($id);
        if (!$user) {
            $_SESSION['error'] = "Người dùng không tồn tại!";
            header('Location: /WebBanHang/account/listUsers');
            exit;
        }

        if ($this->accountModel->deleteAccount($id)) {
            if ($user->avatar && file_exists($user->avatar)) {
                unlink($user->avatar); // Xóa file avatar khi xóa người dùng
            }
            $_SESSION['success'] = "Xóa người dùng thành công!";
        } else {
            $_SESSION['error'] = "Đã có lỗi xảy ra khi xóa người dùng!";
        }
        header('Location: /WebBanHang/account/listUsers');
        exit;
    }
}
?>