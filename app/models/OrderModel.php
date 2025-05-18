<?php
class OrderModel
{
    private $conn;
    private $order_table = "orders";
    private $order_details_table = "order_details";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createOrder($name, $phone, $address)
    {
        $query = "INSERT INTO " . $this->order_table . " (name, phone, address) VALUES (:name, :phone, :address)";
        $stmt = $this->conn->prepare($query);
        $name = htmlspecialchars(strip_tags($name));
        $phone = htmlspecialchars(strip_tags($phone));
        $address = htmlspecialchars(strip_tags($address));
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderDetail($order_id, $product_id, $quantity, $price)
    {
        $query = "INSERT INTO " . $this->order_details_table . " (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);

        return $stmt->execute();
    }

    public function getOrders()
    {
        $query = "SELECT id, name, phone, address, created_at FROM " . $this->order_table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getOrderDetails($order_id)
    {
        $query = "SELECT od.*, p.name as product_name, p.image as product_image 
                  FROM " . $this->order_details_table . " od 
                  LEFT JOIN product p ON od.product_id = p.id 
                  WHERE od.order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>