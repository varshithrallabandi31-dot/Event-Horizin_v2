<?php
require_once __DIR__ . '/../../config/database.php';

class Message {
    private $conn;
    private $table_name = "messages";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($eventId, $userId, $content) {
        if (!$this->conn) return false;
        $query = "INSERT INTO " . $this->table_name . " (event_id, user_id, content) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$eventId, $userId, $content]);
    }

    public function getByEvent($eventId) {
        if (!$this->conn) return [];
        $query = "SELECT m.*, u.name as user_name, u.avatar_url 
                  FROM " . $this->table_name . " m 
                  JOIN users u ON m.user_id = u.id 
                  WHERE m.event_id = ? 
                  ORDER BY m.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
