<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/ProductModel.php';

class CategoryController
{
    private $categoryModel;
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            http_response_code(500);
            include __DIR__ . '/../views/product/notfound.php';
            exit();
        }
        $this->categoryModel = new CategoryModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }

    public function list()
    {
        $categories = $this->categoryModel->getCategories();
        include __DIR__ . '/../views/category/list.php';
    }

    public function add()
    {
        include __DIR__ . '/../views/category/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Tên danh mục là bắt buộc.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Tên danh mục phải từ 3 đến 100 ký tự.';
            }

            if (empty($errors)) {
                $result = $this->categoryModel->addCategory($name, $description);
                if ($result === true) {
                    header('Location: /WebBanHang/Category/list');
                    exit();
                } else {
                    $errors = is_array($result) ? $result : ['Lỗi khi thêm danh mục.'];
                }
            }

            // Nếu có lỗi, hiển thị lại form
            include __DIR__ . '/../views/category/add.php';
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            include __DIR__ . '/../views/category/edit.php';
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

            // Kiểm tra dữ liệu đầu vào
            $errors = [];
            if (empty($id) || !is_numeric($id)) {
                $errors[] = 'ID danh mục không hợp lệ.';
            }
            if (empty($name)) {
                $errors[] = 'Tên danh mục là bắt buộc.';
            } elseif (strlen($name) < 3 || strlen($name) > 100) {
                $errors[] = 'Tên danh mục phải từ 3 đến 100 ký tự.';
            }

            if (empty($errors)) {
                $update = $this->categoryModel->updateCategory($id, $name, $description);
                if ($update) {
                    header('Location: /WebBanHang/Category/list');
                    exit();
                } else {
                    $errors[] = 'Lỗi khi cập nhật danh mục.';
                }
            }

            // Nếu có lỗi, hiển thị lại form chỉnh sửa
            $category = $this->categoryModel->getCategoryById($id);
            include __DIR__ . '/../views/category/edit.php';
        }
    }

    public function delete($id)
    {
        // Kiểm tra xem danh mục có sản phẩm liên quan không
        $products = $this->productModel->getProductsByCategoryId($id);
        if (!empty($products)) {
            $error = 'Không thể xóa danh mục này vì vẫn còn sản phẩm liên quan.';
            $categories = $this->categoryModel->getCategories();
            include __DIR__ . '/../views/category/list.php';
            return;
        }

        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: /WebBanHang/Category/list');
            exit();
        } else {
            include __DIR__ . '/../views/product/notfound.php';
        }
    }
}
?>