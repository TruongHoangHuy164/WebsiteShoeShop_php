<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ProductApiController
{
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            $this->sendResponse(500, ['error' => 'Không thể kết nối cơ sở dữ liệu']);
            exit();
        }
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index()
    {
        $products = $this->productModel->getProducts();
        $this->sendResponse(200, $products);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'ID sản phẩm không hợp lệ']);
            return;
        }
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $this->sendResponse(200, $product);
        } else {
            $this->sendResponse(404, ['error' => 'Sản phẩm không tồn tại']);
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = trim($_POST['price'] ?? '');
        $category_id = trim($_POST['category_id'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Tên sản phẩm là bắt buộc';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Tên sản phẩm phải từ 3 đến 100 ký tự';
        }
        if (empty($description)) {
            $errors[] = 'Mô tả là bắt buộc';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Giá phải là số dương';
        }
        if (empty($category_id) || !is_numeric($category_id)) {
            $errors[] = 'Danh mục không hợp lệ';
        } else {
            $category = $this->categoryModel->getCategoryById($category_id);
            if (!$category) {
                $errors[] = 'Danh mục không tồn tại';
            }
        }

        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            try {
                $image = $this->uploadImage($_FILES['image']);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->sendResponse(400, ['errors' => $errors]);
            return;
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
        if ($result === true) {
            $this->sendResponse(201, ['message' => 'Tạo sản phẩm thành công', 'id' => $this->db->lastInsertId()]);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi tạo sản phẩm']);
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'ID sản phẩm không hợp lệ']);
            return;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->sendResponse(404, ['error' => 'Sản phẩm không tồn tại']);
            return;
        }

        // Handle PUT with form-data (since file uploads are involved)
        parse_str(file_get_contents('php://input'), $input);
        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');
        $price = trim($input['price'] ?? '');
        $category_id = trim($input['category_id'] ?? '');
        $existing_image = trim($input['existing_image'] ?? $product->image);

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Tên sản phẩm là bắt buộc';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Tên sản phẩm phải từ 3 đến 100 ký tự';
        }
        if (empty($description)) {
            $errors[] = 'Mô tả là bắt buộc';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Giá phải là số dương';
        }
        if (empty($category_id) || !is_numeric($category_id)) {
            $errors[] = 'Danh mục không hợp lệ';
        } else {
            $category = $this->categoryModel->getCategoryById($category_id);
            if (!$category) {
                $errors[] = 'Danh mục không tồn tại';
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

        if (!empty($errors)) {
            $this->sendResponse(400, ['errors' => $errors]);
            return;
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
        if ($result) {
            $this->sendResponse(200, ['message' => 'Cập nhật sản phẩm thành công']);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi cập nhật sản phẩm']);
        }
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'ID sản phẩm không hợp lệ']);
            return;
        }

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->sendResponse(404, ['error' => 'Sản phẩm không tồn tại']);
            return;
        }

        if ($this->productModel->deleteProduct($id)) {
            $this->sendResponse(200, ['message' => 'Xóa sản phẩm thành công']);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi xóa sản phẩm']);
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
            throw new Exception("File không phải là hình ảnh");
        }
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh quá lớn (tối đa 10MB)");
        }
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép JPG, JPEG, PNG, GIF");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Lỗi khi tải lên hình ảnh");
        }
        return $target_file;
    }

    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
?>