<?php
require_once __DIR__ . '/../../config/database.php';

class RSVP {
    private $conn;
    private $table_name = "rsvps";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getUserStatus($userId, $eventId) {
        if (!$this->conn) return null;
        
        $query = "SELECT status FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND event_id = :event_id 
                  LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['status'] : null;
    }
}
?>
