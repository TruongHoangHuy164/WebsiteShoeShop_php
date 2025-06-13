<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getProductById($id)
    {
        $query = "SELECT p.*, c.name as category_name  
FROM " . $this->table_name . " p  
LEFT JOIN category c ON p.category_id = c.id  
WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getProducts($filters = [])
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.quantity, p.image, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            // Verify category exists
            $categoryCheck = $this->conn->prepare("SELECT id FROM category WHERE id = :category_id");
            $categoryCheck->bindParam(':category_id', $filters['category_id'], PDO::PARAM_INT);
            $categoryCheck->execute();
            if ($categoryCheck->rowCount() > 0) {
                $query .= " AND p.category_id = :category_id";
                $params[':category_id'] = (int)$filters['category_id'];
            }
        }
        if (!empty($filters['price_min']) && is_numeric($filters['price_min']) && $filters['price_min'] >= 0) {
            $query .= " AND p.price >= :price_min";
            $params[':price_min'] = (float)$filters['price_min'];
        }
        if (!empty($filters['price_max']) && is_numeric($filters['price_max']) && $filters['price_max'] >= 0) {
            $query .= " AND p.price <= :price_max";
            $params[':price_max'] = (float)$filters['price_max'];
        }
        if (!empty($filters['search_name'])) {
            $query .= " AND p.name LIKE :search_name";
            $params[':search_name'] = '%' . $filters['search_name'] . '%';
        }
        if (!empty($filters['sort'])) {
            if ($filters['sort'] === 'price_asc') {
                $query .= " ORDER BY p.price ASC";
            } elseif ($filters['sort'] === 'price_desc') {
                $query .= " ORDER BY p.price DESC";
            }
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addProduct($name, $description, $price, $quantity, $category_id, $image)
    {
        $errors = [];
        if (empty($name)) {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }
        if (empty($description)) {
            $errors['description'] = 'Mô tả không được để trống';
        }
        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }
        if (count($errors) > 0) {
            return $errors;
        }
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, quantity, category_id, image) VALUES (:name, :description, :price, :quantity, :category_id, :image)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $quantity = htmlspecialchars(strip_tags($quantity));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':image', $image);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateProduct($id, $name, $description, $price, $quantity, $category_id, $image)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, price = :price, 
                      quantity = :quantity, category_id = :category_id, image = :image 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $price = htmlspecialchars(strip_tags($price));
        $quantity = htmlspecialchars(strip_tags($quantity));
        $category_id = htmlspecialchars(strip_tags($category_id));
        $image = htmlspecialchars(strip_tags($image));
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image);
        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function getProductsByCategoryId($category_id)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductsWithFilters($filters = [])
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name
                  FROM " . $this->table_name . " p
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE 1=1";

        $params = [];

        // Lọc theo tên sản phẩm
        if (!empty($filters['name'])) {
            $query .= " AND p.name LIKE :name";
            $params[':name'] = '%' . $filters['name'] . '%';
        }

        // Lọc theo ID danh mục
        if (!empty($filters['category_id'])) {
            $query .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        // Lọc theo khoảng giá
        if (!empty($filters['min_price'])) {
            $query .= " AND p.price >= :min_price";
            $params[':min_price'] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $query .= " AND p.price <= :max_price";
            $params[':max_price'] = $filters['max_price'];
        }

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getTotalProducts($filters = [])
    {
        $query = "SELECT COUNT(*) as total 
                 FROM product p 
                 WHERE 1=1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $query .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['price_min'])) {
            $query .= " AND p.price >= ?";
            $params[] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $query .= " AND p.price <= ?";
            $params[] = $filters['price_max'];
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}
?>