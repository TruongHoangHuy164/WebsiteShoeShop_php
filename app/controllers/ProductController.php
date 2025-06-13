<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/helpers/SessionHelper.php';

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

    private function isAdmin()
    {
        return SessionHelper::isAdmin();
    }

    public function index()
    {
        $categoryModel = new CategoryModel($this->db);
        $categories = $categoryModel->getCategories();

        $filters = [
            'category_id' => isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int)$_GET['category_id'] : null,
            'price_min' => isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float)$_GET['price_min'] : null,
            'price_max' => isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float)$_GET['price_max'] : null,
            'search_name' => isset($_GET['search_name']) && !empty(trim($_GET['search_name'])) ? trim($_GET['search_name']) : null,
            'sort' => isset($_GET['sort']) && in_array($_GET['sort'], ['price_asc', 'price_desc']) ? $_GET['sort'] : null,
        ];

        $products = $this->productModel->getProducts($filters);

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
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này!';
            header('Location: /WebBanHang/Product');
            exit;
        }

        $categories = (new CategoryModel($this->db))->getCategories();
        include __DIR__ . '/../views/product/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $quantity = trim($_POST['quantity'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');

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
            if (!is_numeric($quantity) || $quantity < 0) {
                $errors[] = 'Số lượng phải là số không âm.';
            }
            if (empty($category_id) || !is_numeric($category_id)) {
                $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
            }

            $image = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                $result = $this->productModel->addProduct($name, $description, $price, $quantity, $category_id, $image);
                if ($result === true) {
                    $_SESSION['success'] = 'Thêm sản phẩm thành công!';
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
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này!';
            header('Location: /WebBanHang/Product');
            exit;
        }

        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include __DIR__ . '/../views/product/edit.php';
        } else {
            $_SESSION['error'] = 'Sản phẩm không tồn tại!';
            header('Location: /WebBanHang/Product');
            exit;
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $quantity = trim($_POST['quantity'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $existing_image = trim($_POST['existing_image'] ?? '');

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
            if (!is_numeric($price) || $price <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
                $errors[] = 'Giá phải là số dương với tối đa 2 chữ số thập phân.';
            }
            if (!is_numeric($quantity) || $quantity < 0 || floor($quantity) != $quantity) {
                $errors[] = 'Số lượng phải là số nguyên không âm.';
            }
            if (empty($category_id) || !is_numeric($category_id)) {
                $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
            } else {
                $categoryModel = new CategoryModel($this->db);
                $categoryCheck = $categoryModel->getCategoryById($category_id);
                if (!$categoryCheck) {
                    $errors[] = 'Danh mục không tồn tại. Vui lòng chọn danh mục khác.';
                }
            }

            $image = $existing_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            if (empty($errors)) {
                $edit = $this->productModel->updateProduct($id, $name, $description, $price, $quantity, $category_id, $image);
                if ($edit) {
                    $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
                    header('Location: /WebBanHang/Product');
                    exit();
                } else {
                    $errors[] = 'Lỗi khi cập nhật sản phẩm. Vui lòng thử lại.';
                }
            }

            $product = $this->productModel->getProductById($id);
            $categories = (new CategoryModel($this->db))->getCategories();
            include __DIR__ . '/../views/product/edit.php';
        }
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này!';
            header('Location: /WebBanHang/Product');
            exit;
        }

        if ($this->productModel->deleteProduct($id)) {
            $_SESSION['success'] = 'Xóa sản phẩm thành công!';
            header('Location: /WebBanHang/Product');
            exit();
        } else {
            $_SESSION['error'] = 'Sản phẩm không tồn tại!';
            header('Location: /WebBanHang/Product');
            exit;
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
        if ($this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này!';
            header('Location: /WebBanHang/Product');
            exit;
        }

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
            $email = trim($_POST['email'] ?? '');
            $note = trim($_POST['note'] ?? '');

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
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ.';
            }

            foreach ($cart as $product_id => $item) {
                $product = $this->productModel->getProductById($product_id);
                if (!$product) {
                    $errors[] = "Sản phẩm {$item['name']} không tồn tại.";
                } elseif (($product->quantity ?? 0) < $item['quantity']) {
                    $errors[] = "Sản phẩm {$item['name']} chỉ còn " . ($product->quantity ?? 0) . " trong kho.";
                }
            }

            if (empty($errors)) {
                try {
                    $this->db->beginTransaction();

                    $order_id = $this->orderModel->createOrder($name, $phone, $address, $email, $note);
                    if (!$order_id) {
                        throw new Exception('Lỗi khi tạo đơn hàng.');
                    }

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

                        $query = "UPDATE product SET quantity = quantity - :quantity WHERE id = :product_id";
                        $stmt = $this->db->prepare($query);
                        $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                        if (!$stmt->execute()) {
                            throw new Exception('Lỗi khi cập nhật số lượng sản phẩm.');
                        }
                    }

                    $this->db->commit();
                    unset($_SESSION['cart']);
                    $_SESSION['success'] = 'Đơn hàng đã được đặt thành công!';
                    header('Location: /WebBanHang/Product');
                    exit();
                } catch (Exception $e) {
                    $this->db->rollBack();
                    $errors[] = $e->getMessage();
                }
            }

            include __DIR__ . '/../views/product/checkout.php';
        }
    }

    public function updateCartQuantity()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'] ?? '';
            $quantity = (int)($_POST['quantity'] ?? 0);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $response = ['success' => false, 'message' => ''];

            if (!isset($_SESSION['cart'][$product_id])) {
                $response['message'] = 'Sản phẩm không có trong giỏ hàng.';
                echo json_encode($response);
                exit();
            }

            $product = $this->productModel->getProductById($product_id);
            if (!$product) {
                $response['message'] = 'Sản phẩm không tồn tại.';
                echo json_encode($response);
                exit();
            }

            $available_quantity = $product->quantity ?? 0;
            if ($quantity > $available_quantity) {
                $response['message'] = 'Số lượng yêu cầu vượt quá số lượng trong kho (' . $available_quantity . ').';
                echo json_encode($response);
                exit();
            }

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$product_id]);
                if (empty($_SESSION['cart'])) {
                    unset($_SESSION['cart']);
                    $response = ['success' => true, 'redirect' => true];
                } else {
                    $response = ['success' => true];
                }
            } else {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                $response = ['success' => true];
            }

            $total = 0;
            if (isset($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            }

            $response['total'] = $total;
            echo json_encode($response);
            exit();
        }
    }

    public function orderHistory()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $orders = $this->orderModel->getOrders();
        $orderModel = $this->orderModel;
        include __DIR__ . '/../views/product/order_history.php';
    }
}
?>