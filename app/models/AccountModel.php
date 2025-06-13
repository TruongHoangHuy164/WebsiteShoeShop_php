<?php
class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save($username, $fullName, $password, $role, $email = null, $phone = null, $avatar = null)
    {
        $query = "INSERT INTO " . $this->table_name . " (username, fullname, password, role, email, phone, avatar) 
                  VALUES (:username, :fullname, :password, :role, :email, :phone, :avatar)";
        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $fullName = htmlspecialchars(strip_tags($fullName));
        $password = password_hash($password, PASSWORD_BCRYPT);
        $role = htmlspecialchars(strip_tags($role));
        $email = $email ? htmlspecialchars(strip_tags($email)) : null;
        $phone = $phone ? htmlspecialchars(strip_tags($phone)) : null;
        $avatar = $avatar ? htmlspecialchars(strip_tags($avatar)) : null;
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullName);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':avatar', $avatar);
        return $stmt->execute();
    }

    public function getAllAccounts()
    {
        $query = "SELECT id, username, fullname, role, email, phone, avatar 
                  FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAccountById($id)
    {
        $query = "SELECT id, username, fullname, role, email, phone, avatar 
                  FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateAccount($id, $username, $fullname, $role, $email = null, $phone = null, $avatar = null, $password = null)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, fullname = :fullname, role = :role, 
                      email = :email, phone = :phone, avatar = :avatar";
        if ($password) {
            $query .= ", password = :password";
        }
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $username = htmlspecialchars(strip_tags($username));
        $fullname = htmlspecialchars(strip_tags($fullname));
        $role = htmlspecialchars(strip_tags($role));
        $email = $email ? htmlspecialchars(strip_tags($email)) : null;
        $phone = $phone ? htmlspecialchars(strip_tags($phone)) : null;
        $avatar = $avatar ? htmlspecialchars(strip_tags($avatar)) : null;
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':avatar', $avatar);
        
        if ($password) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password);
        }
        
        return $stmt->execute();
    }

    public function deleteAccount($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>