<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/OrderModel.php';

class ProductController
{
    private $productModel;
    private $orderModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            http_response_code(500);
            include __DIR__ . '/../views/product/notfound.php';
            exit();
        }
        $this->productModel = new ProductModel($this->db);
        $this->orderModel = new OrderModel($this->db);
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        if (isset($_GET['success'])) {
            $success = urldecode($_GET['success']);
        }
        include __DIR__ . '/../views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            include __DIR__ . '/../views/product/show.php';
        } else {
            include __DIR__ . '/../views/product/notfound.php';
        }
    }

    public function add()
    {
        $categories = (new CategoryModel($this->db))->getCategories();
        include __DIR__ . '/../views/product/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Tên sản phẩm là bắt buộc.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Tên sản phẩm phải từ 3 đến 100 ký tự.';
            }
            if (empty($description)) {
                $errors[] = 'Mô tả là bắt buộc.';
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors[] = 'Giá phải là số dương.';
            }
            if (empty($category_id) || !is_numeric($category_id)) {
                $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
            }

            // Xử lý hình ảnh
            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
                if ($result === true) {
                    header('Location: /WebBanHang/Product');
                    exit();
                } else {
                    $errors = is_array($result) ? $result : ['Lỗi khi thêm sản phẩm.'];
                }
            }

            $categories = (new CategoryModel($this->db))->getCategories();
            include __DIR__ . '/../views/product/add.php';
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include __DIR__ . '/../views/product/edit.php';
        } else {
            include __DIR__ . '/../views/product/notfound.php';
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $existing_image = trim($_POST['existing_image'] ?? '');

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($id) || !is_numeric($id)) {
                $errors[] = 'ID sản phẩm không hợp lệ.';
            }
            if (empty($name)) {
                $errors[] = 'Tên sản phẩm là bắt buộc.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Tên sản phẩm phải từ 3 đến 100 ký tự.';
            }
            if (empty($description)) {
                $errors[] = 'Mô tả là bắt buộc.';
            }
            if (!is_numeric($price) || $price <= 0) {
                $errors[] = 'Giá phải là số dương.';
            }
            if (empty($category_id) || !is_numeric($category_id)) {
                $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
            }

            // Xử lý hình ảnh
            $image = $existing_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
                if ($edit) {
                    header('Location: /WebBanHang/Product');
                    exit();
                } else {
                    $errors[] = 'Lỗi khi cập nhật sản phẩm.';
                }
            }

            $product = $this->productModel->getProductById($id);
            $categories = (new CategoryModel($this->db))->getCategories();
            include __DIR__ . '/../views/product/edit.php';
        }
    }

    public function delete($id)
    {
        if ($this->productModel->deleteProduct($id)) {
            header('Location: /WebBanHang/Product');
            exit();
        } else {
            include __DIR__ . '/../views/product/notfound.php';
        }
    }

    private function uploadImage($file)
    {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . uniqid() . '_' . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn (tối đa 10MB).");
        }
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file;
    }

    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            include __DIR__ . '/../views/product/notfound.php';
            return;
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image
            ];
        }
        header('Location: /WebBanHang/Product/cart');
    }

    public function cart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cart = $_SESSION['cart'] ?? [];
        include __DIR__ . '/../views/product/cart.php';
    }

    public function removeFromCart($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
            if (empty($_SESSION['cart'])) {
                unset($_SESSION['cart']);
            }
        }
        header('Location: /WebBanHang/Product/cart');
        exit();
    }

    public function checkout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: /WebBanHang/Product/cart');
            exit();
        }
        include __DIR__ . '/../views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $cart = $_SESSION['cart'] ?? [];

            if (empty($cart)) {
                header('Location: /WebBanHang/Product/cart');
                exit();
            }

            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Tên người nhận là bắt buộc.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Tên phải từ 3 đến 100 ký tự.';
            }
            if (empty($phone)) {
                $errors[] = 'Số điện thoại là bắt buộc.';
            } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                $errors[] = 'Số điện thoại không hợp lệ (10-11 chữ số).';
            }
            if (empty($address)) {
                $errors[] = 'Địa chỉ là bắt buộc.';
            } elseif (strlen($address) < 10) {
                $errors[] = 'Địa chỉ phải ít nhất 10 ký tự.';
            }

            if (empty($errors)) {
                // Bắt đầu giao dịch
                try {
                    $this->db->beginTransaction();

                    // Tạo đơn hàng
                    $order_id = $this->orderModel->createOrder($name, $phone, $address);
                    if (!$order_id) {
                        throw new Exception('Lỗi khi tạo đơn hàng.');
                    }

                    // Thêm chi tiết đơn hàng
                    foreach ($cart as $product_id => $item) {
                        $success = $this->orderModel->addOrderDetail(
                            $order_id,
                            $product_id,
                            $item['quantity'],
                            $item['price']
                        );
                        if (!$success) {
                            throw new Exception('Lỗi khi thêm chi tiết đơn hàng.');
                        }
                    }

                    // Xác nhận giao dịch
                    $this->db->commit();

                    // Xóa giỏ hàng
                    unset($_SESSION['cart']);

                    // Chuyển hướng đến trang xác nhận
                    header('Location: /WebBanHang/Product?success=Đơn hàng đã được đặt thành công!');
                    exit();
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = $e->getMessage();
                }
            }

            // Nếu có lỗi, hiển thị lại form
            include __DIR__ . '/../views/product/checkout.php';
        }
    }

    public function orderHistory()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $orders = $this->orderModel->getOrders();
        $orderModel = $this->orderModel; // Pass orderModel to view
        include __DIR__ . '/../views/product/order_history.php';
    }
}
?>