
<?php
// Initialize session only for non-API requests
if (!str_starts_with($_SERVER['REQUEST_URI'], '/WebBanHang/api')) {
    session_start();
}

// Require essential files
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/models/ProductModel.php';
require_once __DIR__ . '/app/models/CategoryModel.php';
require_once __DIR__ . '/app/helpers/SessionHelper.php';
require_once __DIR__ . '/app/controllers/ApiController.php';

// Get and process URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle API requests
if (str_starts_with($request_uri, '/WebBanHang/api')) {
    $controller = new ApiController();

    switch ($request_uri) {
        // API Documentation Route
        case '/WebBanHang/api':
            if ($method === 'GET') {
                header('Content-Type: text/html');
                $docFile = __DIR__ . '/public/api-docs/webapi.html';
                if (file_exists($docFile)) {
                    readfile($docFile);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'API documentation file not found']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            exit();

        // Product Routes
        case '/WebBanHang/api/all-products':
            if ($method === 'GET') {
                $controller->getAllProducts();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        case '/WebBanHang/api/products':
            if ($method === 'GET') {
                $controller->getProducts();
            } elseif ($method === 'POST') {
                $controller->addProduct();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        case (preg_match('/\/WebBanHang\/api\/products\/(\d+)/', $request_uri, $matches) ? true : false):
            $id = $matches[1];
            if ($method === 'GET') {
                $controller->getProduct($id);
            } elseif ($method === 'PUT') {
                $controller->updateProduct($id);
            } elseif ($method === 'DELETE') {
                $controller->deleteProduct($id);
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        // Category Routes
        case '/WebBanHang/api/all-categories':
            if ($method === 'GET') {
                $controller->getAllCategories();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        case '/WebBanHang/api/categories':
            if ($method === 'GET') {
                $controller->getCategories();
            } elseif ($method === 'POST') {
                $controller->addCategory();
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        case (preg_match('/\/WebBanHang\/api\/categories\/(\d+)/', $request_uri, $matches) ? true : false):
            $id = $matches[1];
            if ($method === 'GET') {
                $controller->getCategory($id);
            } elseif ($method === 'PUT') {
                $controller->updateCategory($id);
            } elseif ($method === 'DELETE') {
                $controller->deleteCategory($id);
            } else {
                http_response_code(405);
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
    exit();
}

// Handle MVC routing for non-API requests
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// Redirect to default route if URL is empty
if (empty($urlParts[0])) {
    header('Location: /WebBanHang/Product');
    exit();
}

// Determine controller and action
$controllerName = ucfirst($urlParts[0]) . 'Controller';
$action = $urlParts[1] ?? 'index';

// Check if controller file exists
$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Require controller file
require_once $controllerFile;

// Check if controller class exists
if (!class_exists($controllerName)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Instantiate controller
$controller = new $controllerName();

// Check if action exists
if (!method_exists($controller, $action)) {
    http_response_code(404);
    include __DIR__ . '/app/views/product/notfound.php';
    exit();
}

// Call action with remaining URL parameters
call_user_func_array([$controller, $action], array_slice($urlParts, 2));
?>
