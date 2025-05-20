<?php
class Inventory {
    private $conn;
    private $table = 'inventories';

    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $created_by_user_id;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Получить последние записи с информацией о пользователе
    public function getRecent($limit = 5) {
        $query = "SELECT i.*, 
                  u.last_name as user_last_name,
                  u.first_name as user_first_name
                  FROM {$this->table} i
                  LEFT JOIN users u ON i.created_by_user_id = u.id
                  ORDER BY i.start_date DESC
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Получить все записи
    public function getAll() {
        $query = "SELECT i.*, 
                  u.last_name as user_last_name,
                  u.first_name as user_first_name
                  FROM {$this->table} i
                  LEFT JOIN users u ON i.created_by_user_id = u.id
                  ORDER BY i.start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Получить по ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->created_by_user_id = $row['created_by_user_id'];
            $this->status = $row['status'];
            return true;
        }
        return false;
    }

    // Создать новую запись
    public function create() {
        $query = "INSERT INTO {$this->table} (name, start_date, end_date, created_by_user_id, status)
                  VALUES (:name, :start_date, :end_date, :created_by_user_id, :status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':created_by_user_id', $this->created_by_user_id);
        $stmt->bindParam(':status', $this->status);
        return $stmt->execute();
    }

    // Обновить существующую запись
    public function update() {
        $query = "UPDATE {$this->table} 
                  SET name = :name,
                      start_date = :start_date,
                      end_date = :end_date,
                      created_by_user_id = :created_by_user_id,
                      status = :status
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':created_by_user_id', $this->created_by_user_id);
        $stmt->bindParam(':status', $this->status);
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
}
?>