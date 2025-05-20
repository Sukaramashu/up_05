<?php
class Equipment {
    private $conn;
    private $table = 'equipment';

    public $id;
    public $name;
    public $photo_path;
    public $inventory_number;
    public $current_classroom_id;
    public $responsible_user_id;
    public $temp_responsible_user_id;
    public $cost;
    public $direction_id;
    public $status_id;
    public $model_id;
    public $comments;
    
    // Дополнительные поля для JOIN запросов
    public $classroom_name;
    public $responsible_last_name;
    public $responsible_first_name;
    public $direction_name;
    public $status_name;
    public $model_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = '', $status_id = '', $classroom_id = '') {
        $query = "SELECT 
                    e.*, 
                    c.name as classroom_name,
                    u.last_name as responsible_last_name,
                    u.first_name as responsible_first_name,
                    d.name as direction_name,
                    s.name as status_name,
                    m.name as model_name
                  FROM {$this->table} e
                  LEFT JOIN classrooms c ON e.current_classroom_id = c.id
                  LEFT JOIN users u ON e.responsible_user_id = u.id
                  LEFT JOIN reference_items d ON e.direction_id = d.id AND d.type = 'direction'
                  LEFT JOIN reference_items s ON e.status_id = s.id AND s.type = 'status'
                  LEFT JOIN equipment_models m ON e.model_id = m.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (e.name LIKE :search OR e.inventory_number LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($status_id)) {
            $query .= " AND e.status_id = :status_id";
            $params[':status_id'] = $status_id;
        }
        
        if (!empty($classroom_id)) {
            $query .= " AND e.current_classroom_id = :classroom_id";
            $params[':classroom_id'] = $classroom_id;
        }
        
        $query .= " ORDER BY e.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT 
                    e.*, 
                    c.name as classroom_name,
                    u.last_name as responsible_last_name,
                    u.first_name as responsible_first_name,
                    d.name as direction_name,
                    s.name as status_name,
                    m.name as model_name
                  FROM {$this->table} e
                  LEFT JOIN classrooms c ON e.current_classroom_id = c.id
                  LEFT JOIN users u ON e.responsible_user_id = u.id
                  LEFT JOIN reference_items d ON e.direction_id = d.id AND d.type = 'direction'
                  LEFT JOIN reference_items s ON e.status_id = s.id AND s.type = 'status'
                  LEFT JOIN equipment_models m ON e.model_id = m.id
                  WHERE e.id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->photo_path = $row['photo_path'];
            $this->inventory_number = $row['inventory_number'];
            $this->current_classroom_id = $row['current_classroom_id'];
            $this->responsible_user_id = $row['responsible_user_id'];
            $this->temp_responsible_user_id = $row['temp_responsible_user_id'];
            $this->cost = $row['cost'];
            $this->direction_id = $row['direction_id'];
            $this->status_id = $row['status_id'];
            $this->model_id = $row['model_id'];
            $this->comments = $row['comments'];
            
            // Дополнительные поля
            $this->classroom_name = $row['classroom_name'];
            $this->responsible_last_name = $row['responsible_last_name'];
            $this->responsible_first_name = $row['responsible_first_name'];
            $this->direction_name = $row['direction_name'];
            $this->status_name = $row['status_name'];
            $this->model_name = $row['model_name'];
            
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO {$this->table} 
                  SET name = :name,
                      inventory_number = :inventory_number,
                      current_classroom_id = :current_classroom_id,
                      responsible_user_id = :responsible_user_id,
                      cost = :cost,
                      direction_id = :direction_id,
                      status_id = :status_id,
                      model_id = :model_id,
                      comments = :comments";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':inventory_number', $this->inventory_number);
        $stmt->bindParam(':current_classroom_id', $this->current_classroom_id);
        $stmt->bindParam(':responsible_user_id', $this->responsible_user_id);
        $stmt->bindParam(':cost', $this->cost);
        $stmt->bindParam(':direction_id', $this->direction_id);
        $stmt->bindParam(':status_id', $this->status_id);
        $stmt->bindParam(':model_id', $this->model_id);
        $stmt->bindParam(':comments', $this->comments);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE {$this->table} 
                  SET name = :name,
                      inventory_number = :inventory_number,
                      current_classroom_id = :current_classroom_id,
                      responsible_user_id = :responsible_user_id,
                      cost = :cost,
                      direction_id = :direction_id,
                      status_id = :status_id,
                      model_id = :model_id,
                      comments = :comments
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':inventory_number', $this->inventory_number);
        $stmt->bindParam(':current_classroom_id', $this->current_classroom_id);
        $stmt->bindParam(':responsible_user_id', $this->responsible_user_id);
        $stmt->bindParam(':cost', $this->cost);
        $stmt->bindParam(':direction_id', $this->direction_id);
        $stmt->bindParam(':status_id', $this->status_id);
        $stmt->bindParam(':model_id', $this->model_id);
        $stmt->bindParam(':comments', $this->comments);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function count() {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function getByStatus($status, $limit = 5) {
        $query = "SELECT e.id, e.name, e.inventory_number, c.name as classroom_name
                  FROM {$this->table} e
                  LEFT JOIN classrooms c ON e.current_classroom_id = c.id
                  JOIN reference_items s ON e.status_id = s.id AND s.type = 'status' AND s.name = ?
                  ORDER BY e.name
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $status);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getMovementHistory() {
        $query = "SELECT 
                    h.from_id, 
                    h.to_id, 
                    h.changed_at, 
                    h.comments,
                    fc.name as from_classroom_name,
                    tc.name as to_classroom_name,
                    u.last_name as user_last_name,
                    u.first_name as user_first_name
                  FROM change_history h
                  LEFT JOIN classrooms fc ON h.from_id = fc.id
                  JOIN classrooms tc ON h.to_id = tc.id
                  JOIN users u ON h.changed_by_user_id = u.id
                  WHERE h.entity_type = 'equipment_movement' AND h.equipment_id = ?
                  ORDER BY h.changed_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt;
    }

    public function getNetworkSettings() {
        $query = "SELECT * FROM network_settings WHERE equipment_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt;
    }

    public function getConsumables() {
        $query = "SELECT c.id, c.name, c.quantity, t.name as type_name
                  FROM consumables c
                  LEFT JOIN reference_items t ON c.type_id = t.id AND t.type = 'consumable_type'
                  WHERE c.equipment_id = ?
                  ORDER BY c.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        return $stmt;
    }
    public function getByResponsibleUser($user_id) {
    $query = "SELECT e.id, e.name, e.inventory_number, s.name as status_name
              FROM {$this->table} e
              JOIN reference_items s ON e.status_id = s.id AND s.type = 'status'
              WHERE e.responsible_user_id = :user_id
              ORDER BY e.name";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt;
}
}
?>