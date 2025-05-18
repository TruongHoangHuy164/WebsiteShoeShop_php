<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

/**
 * CategoryApiController handles RESTful API operations for categories.
 */
class CategoryApiController
{
    private $categoryModel;
    private $productModel;
    private $db;

    /**
     * Constructor initializes database connection and models.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            $this->sendResponse(500, ['error' => 'Không thể kết nối cơ sở dữ liệu']);
            exit();
        }
        $this->categoryModel = new CategoryModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }

    /**
     * GET /api/category - Retrieve all categories.
     */
    public function index()
    {
        $categories = $this->categoryModel->getCategories();
        $this->sendResponse(200, $categories);
    }

    /**
     * GET /api/category/{id} - Retrieve a category by ID.
     * @param int $id Category ID
     */
    public function show($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            $this->sendResponse(400, ['error' => 'ID danh mục không hợp lệ']);
            return;
        }

        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            $this->sendResponse(200, $category);
        } else {
            $this->sendResponse(404, ['error' => 'Danh mục không tồn tại']);
        }
    }

    /**
     * POST /api/category - Create a new category.
     */
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(400, ['error' => 'Dữ liệu JSON không hợp lệ']);
            return;
        }

        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Tên danh mục là bắt buộc';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Tên danh mục phải từ 3 đến 100 ký tự';
        }

        if (!empty($errors)) {
            $this->sendResponse(400, ['errors' => $errors]);
            return;
        }

        $result = $this->categoryModel->addCategory($name, $description);
        if ($result === true) {
            $this->sendResponse(201, [
                'message' => 'Tạo danh mục thành công',
                'id' => $this->db->lastInsertId()
            ]);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi tạo danh mục']);
        }
    }

    /**
     * PUT /api/category/{id} - Update an existing category.
     * @param int $id Category ID
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        if (!is_numeric($id) || $id <= 0) {
            $this->sendResponse(400, ['error' => 'ID danh mục không hợp lệ']);
            return;
        }

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $this->sendResponse(404, ['error' => 'Danh mục không tồn tại']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(400, ['error' => 'Dữ liệu JSON không hợp lệ']);
            return;
        }

        $name = trim($input['name'] ?? '');
        $description = trim($input['description'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Tên danh mục là bắt buộc';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Tên danh mục phải từ 3 đến 100 ký tự';
        }

        if (!empty($errors)) {
            $this->sendResponse(400, ['errors' => $errors]);
            return;
        }

        $result = $this->categoryModel->updateCategory($id, $name, $description);
        if ($result) {
            $this->sendResponse(200, ['message' => 'Cập nhật danh mục thành công']);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi cập nhật danh mục']);
        }
    }

    /**
     * DELETE /api/category/{id} - Delete a category.
     * @param int $id Category ID
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendResponse(405, ['error' => 'Phương thức không được phép']);
            return;
        }

        if (!is_numeric($id) || $id <= 0) {
            $this->sendResponse(400, ['error' => 'ID danh mục không hợp lệ']);
            return;
        }

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $this->sendResponse(404, ['error' => 'Danh mục không tồn tại']);
            return;
        }

        $products = $this->productModel->getProductsByCategoryId($id);
        if (!empty($products)) {
            $this->sendResponse(400, ['error' => 'Không thể xóa danh mục vì vẫn còn sản phẩm liên quan']);
            return;
        }

        if ($this->categoryModel->deleteCategory($id)) {
            $this->sendResponse(200, ['message' => 'Xóa danh mục thành công']);
        } else {
            $this->sendResponse(500, ['error' => 'Lỗi khi xóa danh mục']);
        }
    }

    /**
     * Send a JSON response with the specified status code.
     * @param int $statusCode HTTP status code
     * @param array $data Data to send
     */
    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
}
?>