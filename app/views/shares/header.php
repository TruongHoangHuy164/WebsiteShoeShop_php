<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/WebBanHang/';
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f4ff, #ffffff);
            color: #333;
        }
        .navbar {
            background: linear-gradient(to right, #ffccde, #b4e4e9);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .nav-link {
            color: #4a4a4a !important;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .nav-link:hover {
            color: #ff6b81 !important;
            transform: scale(1.1);
        }
        .container {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-cute {
            background-color: #ffccde;
            border: none;
            color: #fff;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        .btn-cute:hover {
            background-color: #ff6b81;
            transform: scale(1.05);
        }
        .btn-secondary-cute {
            background-color: #b4e4e9;
            border: none;
            color: #fff;
        }
        .btn-secondary-cute:hover {
            background-color: #81d4e3;
            transform: scale(1.05);
        }
        .cart-icon {
            position: relative;
            color: #4a4a4a !important;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .cart-icon:hover {
            color: #ff6b81 !important;
            transform: scale(1.1);
        }
        .cart-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #ff6b81;
            color: #fff;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: 500;
        }
        .user-info {
            color: #4a4a4a;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $base_url; ?>Product"><i class="fas fa-store me-2"></i>Quản lý sản phẩm</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>Product">Danh sách sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>Product/add">Thêm sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>Category/list">Danh sách danh mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>Product/orderHistory">Lịch sử đặt hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link cart-icon" href="<?php echo $base_url; ?>Product/cart">
                            <i class="fas fa-cart-shopping"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-count"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item d-flex align-items-center">
                            <span class="user-info me-3"><?php echo htmlspecialchars($_SESSION['fullname']); ?> (<?php echo $_SESSION['role']; ?>)</span>
                            <a class="nav-link" href="<?php echo $base_url; ?>account/logout"><i class="fas fa-sign-out-alt me-1"></i>Đăng xuất</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>account/login"><i class="fas fa-sign-in-alt me-1"></i>Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $base_url; ?>account/register"><i class="fas fa-user-plus me-1"></i>Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">