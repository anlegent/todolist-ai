<?php
// models/Todo.php - Modèle Todo

class Todo
{
    private $conn;
    private $table = 'todos';

    public $id;
    public $title;
    public $description;
    public $completed;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Créer une todo
    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET title = :title, 
                      description = :description, 
                      completed = :completed';

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->completed = isset($this->completed) ? $this->completed : 0;

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':completed', $this->completed);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Lire toutes les todos
    public function readAll()
    {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lire une todo par ID
    public function readOne()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->completed = $row['completed'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Mettre à jour une todo
    public function update()
    {
        $query = 'UPDATE ' . $this->table . ' 
                  SET title = :title, 
                      description = :description, 
                      completed = :completed 
                  WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':completed', $this->completed);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Supprimer une todo
    public function delete()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
