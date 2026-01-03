<?php
require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $conn;
    private $table_name = "notifications";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($userId, $message) {
        if (!$this->conn) return false;
        $query = "INSERT INTO " . $this->table_name . " (user_id, message, created_at) 
                  VALUES (:user_id, :message, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function getUnreadByUser($userId) {
        if (!$this->conn) return [];
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND is_read = FALSE 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_name . " SET is_read = TRUE WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
