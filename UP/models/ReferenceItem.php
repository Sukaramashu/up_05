<?php
class ReferenceItem {
    private $conn;
    private $table = 'reference_items';

    public $id;
    public $type;
    public $name;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получить все записи по типу
    public function getByType($type) {
        $query = "SELECT * FROM {$this->table} WHERE type = ? ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$type]);
        return $stmt;
    }

    // Получить запись по ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->type = $row['type'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            return true;
        }
        return false;
    }

    // Создать новую запись
    public function create() {
        $query = "INSERT INTO {$this->table} (type, name, description) 
                  VALUES (:type, :name, :description)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        return $stmt->execute();
    }

    // Обновить существующую запись
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET type = :type,
                      name = :name,
                      description = :description
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Удалить запись
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Получить все записи (по умолчанию)
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>