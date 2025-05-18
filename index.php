<?php
// Khởi tạo session
session_start();

// Yêu cầu các file cần thiết
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/ProductModel.php';
require_once __DIR__ . '/app/models/CategoryModel.php';

// Lấy và xử lý URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Nếu URL rỗng, chuyển hướng đến danh sách sản phẩm
if (empty($urlParts[0])) {
    header('Location: /WebBanHang/Product');
    exit();
}

// Xác định controller và action
$controllerName = ucfirst($urlParts[0]) . 'Controller';
$action = $urlParts[1] ?? 'index';

// Kiểm tra file controller tồn tại
$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Yêu cầu file controller
require_once $controllerFile;

// Kiểm tra lớp controller tồn tại
if (!class_exists($controllerName)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Tạo instance của controller
$controller = new $controllerName();

// Kiểm tra action tồn tại
if (!method_exists($controller, $action)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Gọi action với các tham số còn lại
call_user_func_array([$controller, $action], array_slice($urlParts, 2));
?>