<?php
require_once __DIR__ . '/../../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $phone;
    public $name;
    public $bio;
    public $interests;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Find user by phone
    public function findByPhone($phone) {
        if (!$this->conn) return false;
        $query = "SELECT * FROM " . $this->table_name . " WHERE phone = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $phone);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->phone = $row['phone'];
            $this->name = $row['name'];
            $this->email = $row['email']; // Added email
            $this->bio = $row['bio'];
            $this->interests = $row['interests'];
            return $row;
        }
        return false;
    }

    // Find user by Email
    public function findByEmail($email) {
        if (!$this->conn) return false;
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->phone = $row['phone'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->bio = $row['bio'];
            $this->interests = $row['interests'];
            return $row;
        }
        return false;
    }



    // Find user by ID
    public function findById($id) {
        if (!$this->conn) return false;
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->phone = $row['phone'];
            $this->name = $row['name'];
            $this->email = $row['email']; // Added email
            $this->bio = $row['bio'];
            $this->interests = $row['interests'];
            return $row;
        }
        return false;
    }

    // Create new user with just phone (first step)
    public function createWithPhone($phone) {
        if (!$this->conn) return false;
        $query = "INSERT INTO " . $this->table_name . " SET phone = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Create new user with just email
    public function createWithEmail($email) {
        if (!$this->conn) return false;
        // NOTE: phone is currently NOT NULL in DB. We might need to handle this.
        // Assuming we relax the constraint OR strictly use this for existing users?
        // For now, I'll attempt insert. If it fails due to phone constraint, I'll update schema.
        $query = "INSERT INTO " . $this->table_name . " SET email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Update profile
    public function updateProfile($id, $name, $bio, $interests) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, bio = :bio, interests = :interests 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $name = htmlspecialchars(strip_tags($name));
        $bio = htmlspecialchars(strip_tags($bio));
        
        // Ensure interests is valid JSON, default to empty JSON array if empty or invalid
        if (empty($interests)) {
            $interests = '[]';
        } else {
            // Verify if it is already valid JSON
            json_decode($interests);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // If not valid JSON, treat as comma-separated string and convert to JSON array
                $interestsArray = array_map('trim', explode(',', $interests));
                $interests = json_encode($interestsArray);
            }
        }
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':interests', $interests); // Now guaranteed to be valid JSON
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Update name and email
    public function updateBasicInfo($id, $name, $email = null) {
        if (!$this->conn) return false;
        
        $query = "UPDATE " . $this->table_name . " SET name = :name";
        if ($email) {
            $query .= ", email = :email";
        }
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $name = htmlspecialchars(strip_tags($name));
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        
        if ($email) {
            $email = htmlspecialchars(strip_tags($email));
            $stmt->bindParam(':email', $email);
        }

        return $stmt->execute();
    }

    // Get User Badges
    public function getBadges($userId) {
        if (!$this->conn) return [];
        $query = "SELECT b.* FROM badges b 
                  JOIN user_badges ub ON b.id = ub.badge_id 
                  WHERE ub.user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check and Award Badges
    public function checkBadges($userId) {
        if (!$this->conn) return;

        // 1. Get Stats
        // Hosted Events
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM events WHERE organizer_id = ?");
        $stmt->execute([$userId]);
        $hostedCount = $stmt->fetchColumn();

        // Attended Events (Approved RSVPs)
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM rsvps WHERE user_id = ? AND status = 'approved'");
        $stmt->execute([$userId]);
        $attendedCount = $stmt->fetchColumn();

        // 2. Fetch All Badges
        $stmt = $this->conn->prepare("SELECT * FROM badges");
        $stmt->execute();
        $badges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Check Criteria
        $awarded = [];
        foreach ($badges as $badge) {
            $eligible = false;
            
            if ($badge['criteria_type'] === 'events_hosted' && $hostedCount >= $badge['criteria_value']) {
                $eligible = true;
            } elseif ($badge['criteria_type'] === 'events_attended' && $attendedCount >= $badge['criteria_value']) {
                $eligible = true;
            }

            if ($eligible) {
                // Award Badge (IGNORE to prevent dups)
                $insert = $this->conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
                $insert->execute([$userId, $badge['id']]);
                if ($insert->rowCount() > 0) {
                    $awarded[] = $badge['name'];
                }
            }
        }
        
        return $awarded;
    }
}
