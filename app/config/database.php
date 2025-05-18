<?php
class Database
{
    private $host = 'localhost';
    private $db_name = 'my_store'; // Sửa thành tên cơ sở dữ liệu đúng
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            // Thiết lập chế độ lỗi PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $exception) {
            // Ghi log lỗi thay vì in trực tiếp
            error_log("Connection error: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}
?>