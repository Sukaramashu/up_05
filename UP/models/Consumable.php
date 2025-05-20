<?php
class Consumable {
    private $conn;
    private $table = 'consumables';

    public $id;
    public $name;
    public $description;
    public $receipt_date;
    public $photo_path;
    public $quantity;
    public $responsible_user_id;
    public $temp_responsible_user_id;
    public $type_id;
    public $equipment_id;
    
    // Дополнительные поля для JOIN запросов
    public $type_name;
    public $responsible_last_name;
    public $responsible_first_name;
    public $equipment_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT c.*, 
                  t.name as type_name,
                  u.last_name as responsible_last_name,
                  u.first_name as responsible_first_name,
                  e.name as equipment_name
                  FROM {$this->table} c
                  LEFT JOIN reference_items t ON c.type_id = t.id AND t.type = 'consumable_type'
                  LEFT JOIN users u ON c.responsible_user_id = u.id
                  LEFT JOIN equipment e ON c.equipment_id = e.id
                  ORDER BY c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT c.*, 
                  t.name as type_name,
                  u.last_name as responsible_last_name,
                  u.first_name as responsible_first_name,
                  e.name as equipment_name
                  FROM {$this->table} c
                  LEFT JOIN reference_items t ON c.type_id = t.id AND t.type = 'consumable_type'
                  LEFT JOIN users u ON c.responsible_user_id = u.id
                  LEFT JOIN equipment e ON c.equipment_id = e.id
                  WHERE c.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->receipt_date = $row['receipt_date'];
            $this->photo_path = $row['photo_path'];
            $this->quantity = $row['quantity'];
            $this->responsible_user_id = $row['responsible_user_id'];
            $this->temp_responsible_user_id = $row['temp_responsible_user_id'];
            $this->type_id = $row['type_id'];
            $this->equipment_id = $row['equipment_id'];
            
            // Дополнительные поля
            $this->type_name = $row['type_name'];
            $this->responsible_last_name = $row['responsible_last_name'];
            $this->responsible_first_name = $row['responsible_first_name'];
            $this->equipment_name = $row['equipment_name'];
            
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} 
                  SET name = :name,
                      description = :description,
                      receipt_date = :receipt_date,
                      quantity = :quantity,
                      responsible_user_id = :responsible_user_id,
                      type_id = :type_id,
                      equipment_id = :equipment_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':receipt_date', $this->receipt_date);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':responsible_user_id', $this->responsible_user_id);
        $stmt->bindParam(':type_id', $this->type_id);
        $stmt->bindParam(':equipment_id', $this->equipment_id);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE {$this->table} 
                  SET name = :name,
                      description = :description,
                      receipt_date = :receipt_date,
                      quantity = :quantity,
                      responsible_user_id = :responsible_user_id,
                      type_id = :type_id,
                      equipment_id = :equipment_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':receipt_date', $this->receipt_date);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':responsible_user_id', $this->responsible_user_id);
        $stmt->bindParam(':type_id', $this->type_id);
        $stmt->bindParam(':equipment_id', $this->equipment_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>