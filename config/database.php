<?php
require_once __DIR__ . '/Config.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Load configuration
        $this->host = Config::get('DB_HOST', 'localhost');
        $this->db_name = Config::get('DB_NAME', 'event_platform');
        $this->username = Config::get('DB_USER', 'root');
        $this->password = Config::get('DB_PASS', '');
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            $errorMsg = "Database Connection Error: " . $exception->getMessage();
            
            // Provide helpful error messages
            if (strpos($exception->getMessage(), 'Access denied') !== false) {
                $errorMsg .= "\n\n<strong>Fix:</strong> Check your database credentials in the .env file.";
                $errorMsg .= "\n- Username: " . $this->username;
                $errorMsg .= "\n- Database: " . $this->db_name;
                $errorMsg .= "\n\nMake sure your MySQL password is correct in .env file.";
            } elseif (strpos($exception->getMessage(), 'Unknown database') !== false) {
                $errorMsg .= "\n\n<strong>Fix:</strong> The database '" . $this->db_name . "' does not exist.";
                $errorMsg .= "\n1. Open phpMyAdmin (http://localhost/phpmyadmin)";
                $errorMsg .= "\n2. Create a database named: " . $this->db_name;
                $errorMsg .= "\n3. Import the event_platform.sql file";
            }
            
            echo "<div style='background:#fee;border:2px solid #c00;padding:20px;margin:20px;font-family:monospace;'>";
            echo nl2br($errorMsg);
            echo "</div>";
        }
        return $this->conn;
    }
}
?>
