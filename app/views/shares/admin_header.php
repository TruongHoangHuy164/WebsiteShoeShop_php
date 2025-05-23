<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../helpers/SessionHelper.php';
$base_url = '/WebBanHang/';
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
if (!SessionHelper::isAdmin()) {
    $_SESSION['error'] = 'Bạn cần quyền quản trị để truy cập trang này.';
    header('Location: /WebBanHang/Product');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị - Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #f8f9fa;
            color: #212529;
        }
        .navbar {
            background: #343a40;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .nav-link:hover {
            color: #17a2b8 !important;
            transform: scale(1.05);
        }
        .container {
            animation: slideIn 0.5s ease-in;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .btn-admin {
            background-color: #17a2b8;
            border: none;
            color: #fff;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        .btn-admin:hover {
            background-color: #138496;
            transform: scale(1.05);
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background: #212529;
            padding-top: 60px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #adb5bd !important;
            padding: 10px 20px;
            font-size: 16px;
        }
        .sidebar .nav-link:hover {
            color: #17a2b8 !important;
            background: #343a40;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .user-info {
            color: #ffffff;
            font-weight: 500;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_url; ?>Product"><i class="fas fa-cogs me-2"></i>Bảng điều khiển</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item d-flex align-items-center">
                        <span class="user-info"><?php echo htmlspecialchars($_SESSION['fullname']); ?> (Admin)</span>
                        <a class="nav-link" href="<?php echo $base_url; ?>account/logout"><i class="fas fa-sign-out-alt me-1"></i>Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>Product"><i class="fas fa-boxes me-2"></i>Quản lý sản phẩm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>Category/list"><i class="fas fa-tags me-2"></i>Quản lý danh mục</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo $base_url; ?>Product/orderHistory"><i class="fas fa-history me-2"></i>Lịch sử đặt hàng</a>
            </li>
        </ul>
    </div>
    <div class="content">
        <div class="container mt-5">
<?php
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['error']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']);
}
?>