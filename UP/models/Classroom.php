<?php
class Classroom {
    private $conn;
    private $table = 'classrooms';

    public $id;
    public $name;
    public $short_name;
    public $responsible_user_id;
    public $temp_responsible_user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT c.*, 
                  u.last_name as responsible_last_name, 
                  u.first_name as responsible_first_name
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.responsible_user_id = u.id
                  ORDER BY c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->short_name = $row['short_name'];
            $this->responsible_user_id = $row['responsible_user_id'];
            $this->temp_responsible_user_id = $row['temp_responsible_user_id'];
            return true;
        }
        return false;
    }

  public function create() {
    $query = "INSERT INTO classrooms 
              (name, short_name, responsible_user_id, temp_responsible_user_id, created_at, updated_at) 
              VALUES 
              (:name, :short_name, :responsible_user_id, :temp_responsible_user_id, NOW(), NOW())";
    
    $stmt = $this->conn->prepare($query);
    
    return $stmt->execute([
        ':name' => $this->name,
        ':short_name' => $this->short_name,
        ':responsible_user_id' => $this->responsible_user_id,
        ':temp_responsible_user_id' => $this->temp_responsible_user_id
    ]);
}

    public function update() {
        $query = "UPDATE {$this->table} 
                  SET name = :name,
                      short_name = :short_name,
                      responsible_user_id = :responsible_user_id,
                      temp_responsible_user_id = :temp_responsible_user_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':short_name', $this->short_name);
        $stmt->bindParam(':responsible_user_id', $this->responsible_user_id);
        $stmt->bindParam(':temp_responsible_user_id', $this->temp_responsible_user_id);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

   public function delete($id) {
    $query = "DELETE FROM classrooms WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([$id]);
}

    public function count() {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
        public function getWithEquipmentCount() {
        $query = "SELECT c.*, 
                  COUNT(e.id) as equipment_count,
                  u.last_name as responsible_last_name,
                  u.first_name as responsible_first_name
                  FROM {$this->table} c
                  LEFT JOIN equipment e ON c.id = e.current_classroom_id
                  LEFT JOIN users u ON c.responsible_user_id = u.id
                  GROUP BY c.id
                  ORDER BY c.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getEquipment($classroom_id) {
        $query = "SELECT e.id, e.name, e.inventory_number, s.name as status_name
                  FROM equipment e
                  JOIN reference_items s ON e.status_id = s.id AND s.type = 'status'
                  WHERE e.current_classroom_id = ?
                  ORDER BY e.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$classroom_id]);
        return $stmt;
    }
    public function countByResponsibleUser($user_id) {
    $query = "SELECT COUNT(*) FROM classrooms WHERE responsible_user_id = :user_id OR temp_responsible_user_id = :user_id";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchColumn();
}

public function clearResponsibleUser($user_id) {
    $query = "UPDATE classrooms SET 
              responsible_user_id = NULL, 
              temp_responsible_user_id = NULL 
              WHERE responsible_user_id = :user_id OR temp_responsible_user_id = :user_id";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([':user_id' => $user_id]);
}
}

?>