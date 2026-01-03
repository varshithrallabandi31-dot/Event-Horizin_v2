<?php
require_once __DIR__ . '/../../config/database.php';

class Event {
    private $conn;
    private $table_name = "events";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll($limit = 10) {
        if (!$this->conn) return [];
        $query = "SELECT e.*, u.name as organizer_name 
                  FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.organizer_id = u.id
                  ORDER BY e.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Create Event
    public function create($data, $tiers = []) {
        if (!$this->conn) return false;
        
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . "
                      SET organizer_id=:organizer_id, title=:title, description=:description,
                          start_time=:start_time, location_name=:location_name, city=:city,
                          latitude=:latitude, longitude=:longitude, category=:category, 
                          image_url=:image_url, header_type=:header_type, requires_approval=:requires_approval, created_at=NOW()";
                          
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($data as $key => $value) {
                $stmt->bindValue(':'.$key, $value);
            }
            
            if (!$stmt->execute()) {
                 $this->conn->rollBack();
                 return false;
            }
            
            $eventId = $this->conn->lastInsertId();

            // Insert Ticket Tiers
            if (!empty($tiers)) {
                $tierQuery = "INSERT INTO ticket_tiers (event_id, name, price) VALUES (?, ?, ?)";
                $tierStmt = $this->conn->prepare($tierQuery);
                foreach ($tiers as $tier) {
                    $tierStmt->execute([$eventId, $tier['name'], $tier['price']]);
                }
            }

            $this->conn->commit();
            return $eventId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            file_put_contents(__DIR__ . '/../../debug_error.log', $e->getMessage() . "\n" . $query . "\n" . print_r($data, true), FILE_APPEND);
            return false;
        }
    }
    
    public function getById($id) {
        if (!$this->conn) return false;
         $query = "SELECT e.*, u.name as organizer_name 
                  FROM " . $this->table_name . " e
                  LEFT JOIN users u ON e.organizer_id = u.id
                  WHERE e.id = ? 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getHostedEvents($userId) {
        if (!$this->conn) return [];
        $query = "SELECT events.*, (SELECT COUNT(*) FROM rsvps WHERE rsvps.event_id = events.id) as rsvp_count 
                  FROM events 
                  WHERE organizer_id = ? 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParticipatedEvents($userId) {
        if (!$this->conn) return [];
        $query = "SELECT e.*, r.status as rsvp_status, r.qr_code, r.payment_status, r.ticket_tier_id, t.name as ticket_name, t.price as ticket_price
                  FROM events e
                  JOIN rsvps r ON e.id = r.event_id
                  LEFT JOIN ticket_tiers t ON r.ticket_tier_id = t.id
                  WHERE r.user_id = ? AND r.status = 'approved'
                  ORDER BY e.start_time ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ticket Tiers
    public function getTicketTiers($eventId) {
        if (!$this->conn) return [];
        $query = "SELECT * FROM ticket_tiers WHERE event_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Memories
    public function getMemories($eventId) {
        if (!$this->conn) return [];
        $query = "SELECT m.*, u.name as user_name, u.avatar_url 
                  FROM event_memories m 
                  LEFT JOIN users u ON m.user_id = u.id 
                  WHERE m.event_id = ? 
                  ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMemory($eventId, $userId, $imageUrl, $caption) {
        if (!$this->conn) return false;
        $query = "INSERT INTO event_memories (event_id, user_id, image_url, caption, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$eventId, $userId, $imageUrl, $caption]);
    }

    // Chat Messages
    public function getMessages($eventId) {
         if (!$this->conn) return [];
         $query = "SELECT m.*, u.name as user_name 
                   FROM messages m 
                   JOIN users u ON m.user_id = u.id 
                   WHERE m.event_id = ? 
                   ORDER BY m.created_at ASC";
         $stmt = $this->conn->prepare($query);
         $stmt->execute([$eventId]);
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // FAQs
    public function getFaqs($eventId) {
        if (!$this->conn) return [];
        $query = "SELECT * FROM faqs WHERE event_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Seating Logic
    public function generateSeats($eventId, $rows, $cols, $pricing) {
        if (!$this->conn) return false;
        
        try {
            $this->conn->beginTransaction();
            
            // Clear existing seats & RSVPs with seats? No, just clears seats. RSVPs would lose seat_id (set null) due to cascade/constraint?
            // Constraint was SET NULL.
            $stmt = $this->conn->prepare("DELETE FROM seats WHERE event_id = ?");
            $stmt->execute([$eventId]);
            
            // Update Event Config
            $config = json_encode(['rows' => $rows, 'cols' => $cols]);
            $stmt = $this->conn->prepare("UPDATE events SET has_seating = 1, seating_config = ? WHERE id = ?");
            $stmt->execute([$config, $eventId]);
            
            // Insert Seats
            $sql = "INSERT INTO seats (event_id, row_label, col_label, section, tier_price, status) VALUES (?, ?, ?, ?, ?, 'available')";
            $stmt = $this->conn->prepare($sql);
            
            $rowLabels = range('A', 'Z'); 
            
            for ($r = 0; $r < $rows; $r++) {
                $rowLabel = $rowLabels[$r] ?? 'R'.($r+1);
                for ($c = 1; $c <= $cols; $c++) {
                    $price = $pricing['standard'] ?? 0;
                    $section = 'Standard';
                    
                    if ($r < 2) { // First 2 rows VIP
                        $price = $pricing['vip'] ?? $price;
                        $section = 'VIP';
                    }
                    
                    $stmt->execute([$eventId, $rowLabel, $c, $section, $price]);
                }
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getSeats($eventId) {
        if (!$this->conn) return [];
        $stmt = $this->conn->prepare("SELECT * FROM seats WHERE event_id = ? ORDER BY row_label, col_label");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reserveSeat($seatId, $userId) {
        // Simple reservation (first come first serve)
        $stmt = $this->conn->prepare("UPDATE seats SET status = 'reserved' WHERE id = ? AND status = 'available'");
        return $stmt->execute([$seatId]);
    }
}
