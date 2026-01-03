<?php
class Poll {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function getByEventId($eventId, $userId = null) {
        // Fetch polls
        $query = "SELECT p.*, 
                  (SELECT COUNT(*) FROM poll_votes WHERE poll_id = p.id) as total_votes,
                  (SELECT option_id FROM poll_votes WHERE poll_id = p.id AND user_id = ?) as user_voted_option
                  FROM polls p 
                  WHERE p.event_id = ? 
                  ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $eventId]);
        $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch options for each poll
        foreach ($polls as &$poll) {
            $poll['options'] = $this->getOptions($poll['id']);
        }

        return $polls;
    }

    public function getOptions($pollId) {
        $query = "SELECT po.*, 
                  (SELECT COUNT(*) FROM poll_votes WHERE option_id = po.id) as votes 
                  FROM poll_options po 
                  WHERE po.poll_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$pollId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($eventId, $creatorId, $question, $options) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("INSERT INTO polls (event_id, question, created_by, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$eventId, $question, $creatorId]);
            $pollId = $this->conn->lastInsertId();

            $stmtOption = $this->conn->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
            foreach ($options as $option) {
                if (!empty(trim($option))) {
                    $stmtOption->execute([$pollId, trim($option)]);
                }
            }

            $this->conn->commit();
            return $pollId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    public function vote($pollId, $optionId, $userId) {
        // Check if already voted
        $stmt = $this->conn->prepare("SELECT id FROM poll_votes WHERE poll_id = ? AND user_id = ?");
        $stmt->execute([$pollId, $userId]);
        if ($stmt->rowCount() > 0) {
            return false; // Already voted
        }

        $stmt = $this->conn->prepare("INSERT INTO poll_votes (poll_id, option_id, user_id, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$pollId, $optionId, $userId]);
    }
}
?>
