<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nom;
    public $email;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($nom, $email, $password, $role) {
        $query = "INSERT INTO " . $this->table_name . " (nom, email, password, role) VALUES (:nom, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $role);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                return false; 
            }
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT id, nom, password, role FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->nom = $row['nom'];
                $this->role = $row['role'];
                return true;
            }
        }
        return false;
    }
}
?>