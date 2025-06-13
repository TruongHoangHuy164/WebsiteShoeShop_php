<?php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';

class ApiController
{
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        if (!$this->db) {
            $this->sendResponse(500, ['error' => 'Database connection failed']);
            exit();
        }
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function sendResponse($statusCode, $data)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    public function test()
    {
        include __DIR__ . '/../views/api_test.php';
    }
    // Product CRUD Endpoints
    public function getAllProducts()
    {
        $products = $this->productModel->getProducts([]);
        $this->sendResponse(200, ['data' => $products]);
    }

    public function getProducts()
    {
        $filters = [
            'category_id' => isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int) $_GET['category_id'] : null,
            'price_min' => isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? (float) $_GET['price_min'] : null,
            'price_max' => isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? (float) $_GET['price_max'] : null,
            'search_name' => isset($_GET['search_name']) && !empty(trim($_GET['search_name'])) ? trim($_GET['search_name']) : null,
            'sort' => isset($_GET['sort']) && in_array($_GET['sort'], ['price_asc', 'price_desc']) ? $_GET['sort'] : null,
        ];
        $products = $this->productModel->getProducts($filters);
        $this->sendResponse(200, ['data' => $products]);
    }

    public function getProduct($id)
    {
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid product ID']);
        }
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $this->sendResponse(200, ['data' => $product]);
        } else {
            $this->sendResponse(404, ['error' => 'Product not found']);
        }
    }

    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = trim($data['price'] ?? '');
        $quantity = trim($data['quantity'] ?? '');
        $category_id = trim($data['category_id'] ?? '');
        $image = trim($data['image'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Product name is required';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Product name must be between 3 and 100 characters';
        }
        if (empty($description)) {
            $errors[] = 'Description is required';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Price must be a positive number';
        }
        if (!is_numeric($quantity) || $quantity < 0) {
            $errors[] = 'Quantity must be a non-negative number';
        }
        if (empty($category_id) || !is_numeric($category_id)) {
            $errors[] = 'Valid category ID is required';
        }

        if (empty($errors)) {
            $result = $this->productModel->addProduct($name, $description, $price, $quantity, $category_id, $image);
            if ($result === true) {
                $this->sendResponse(201, ['message' => 'Product added successfully']);
            } else {
                $this->sendResponse(400, ['errors' => is_array($result) ? $result : ['Failed to add product']]);
            }
        } else {
            $this->sendResponse(400, ['errors' => $errors]);
        }
    }

    public function updateProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid product ID']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');
        $price = trim($data['price'] ?? '');
        $quantity = trim($data['quantity'] ?? '');
        $category_id = trim($data['category_id'] ?? '');
        $image = trim($data['image'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Product name is required';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Product name must be between 3 and 100 characters';
        }
        if (empty($description)) {
            $errors[] = 'Description is required';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Price must be a positive number';
        }
        if (!is_numeric($quantity) || $quantity < 0) {
            $errors[] = 'Quantity must be a non-negative number';
        }
        if (empty($category_id) || !is_numeric($category_id)) {
            $errors[] = 'Valid category ID is required';
        }

        if (empty($errors)) {
            $result = $this->productModel->updateProduct($id, $name, $description, $price, $quantity, $category_id, $image);
            if ($result) {
                $this->sendResponse(200, ['message' => 'Product updated successfully']);
            } else {
                $this->sendResponse(400, ['error' => 'Failed to update product']);
            }
        } else {
            $this->sendResponse(400, ['errors' => $errors]);
        }
    }

    public function deleteProduct($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid product ID']);
        }

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            $this->sendResponse(200, ['message' => 'Product deleted successfully']);
        } else {
            $this->sendResponse(404, ['error' => 'Product not found']);
        }
    }

    // Category CRUD Endpoints
    public function getAllCategories()
    {
        $categories = $this->categoryModel->getCategories();
        $this->sendResponse(200, ['data' => $categories]);
    }

    public function getCategories()
    {
        $categories = $this->categoryModel->getCategories();
        $this->sendResponse(200, ['data' => $categories]);
    }

    public function getCategory($id)
    {
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid category ID']);
        }
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            $this->sendResponse(200, ['data' => $category]);
        } else {
            $this->sendResponse(404, ['error' => 'Category not found']);
        }
    }

    public function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Category name is required';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Category name must be between 3 and 100 characters';
        }

        if (empty($errors)) {
            $result = $this->categoryModel->addCategory($name, $description);
            if ($result === true) {
                $this->sendResponse(201, ['message' => 'Category added successfully']);
            } else {
                $this->sendResponse(400, ['errors' => is_array($result) ? $result : ['Failed to add category']]);
            }
        } else {
            $this->sendResponse(400, ['errors' => $errors]);
        }
    }

    public function updateCategory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid category ID']);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $name = trim($data['name'] ?? '');
        $description = trim($data['description'] ?? '');

        $errors = [];
        if (empty($name)) {
            $errors[] = 'Category name is required';
        } elseif (strlen($name) < 3 || strlen($name) > 100) {
            $errors[] = 'Category name must be between 3 and 100 characters';
        }

        if (empty($errors)) {
            $result = $this->categoryModel->updateCategory($id, $name, $description);
            if ($result) {
                $this->sendResponse(200, ['message' => 'Category updated successfully']);
            } else {
                $this->sendResponse(400, ['error' => 'Failed to update category']);
            }
        } else {
            $this->sendResponse(400, ['errors' => $errors]);
        }
    }

    public function deleteCategory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
        if (!is_numeric($id)) {
            $this->sendResponse(400, ['error' => 'Invalid category ID']);
        }

        $products = $this->productModel->getProductsByCategoryId($id);
        if (!empty($products)) {
            $this->sendResponse(400, ['error' => 'Cannot delete category with associated products']);
        }

        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            $this->sendResponse(200, ['message' => 'Category deleted successfully']);
        } else {
            $this->sendResponse(404, ['error' => 'Category not found']);
        }
    }
}

?>