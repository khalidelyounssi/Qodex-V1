<?php
class Category {
    private $conn;
    private $table_name = "categories";

    public $id;
    public $nom;
    public $description;
    public $id_enseignant;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nom, description, id_enseignant) 
                  VALUES (:nom, :description, :id_enseignant)";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id_enseignant = htmlspecialchars(strip_tags($this->id_enseignant));

        $stmt->bindParam(":nom", $this->nom);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":id_enseignant", $this->id_enseignant);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readAllByTeacher($teacher_id) {
        $query = "SELECT c.*, 
                    (SELECT COUNT(*) FROM quiz q WHERE q.id_categorie = c.id) as quiz_count 
                  FROM " . $this->table_name . " c
                  WHERE c.id_enseignant = :teacher_id
                  ORDER BY c.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();

        return $stmt;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND id_enseignant = :id_enseignant";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->id_enseignant = htmlspecialchars(strip_tags($this->id_enseignant));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_enseignant", $this->id_enseignant);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nom = :nom, description = :description 
                  WHERE id = :id AND id_enseignant = :id_enseignant";

        $stmt = $this->conn->prepare($query);

        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->id_enseignant = htmlspecialchars(strip_tags($this->id_enseignant));

        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':id_enseignant', $this->id_enseignant);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>