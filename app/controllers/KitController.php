<?php
require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

require_once __DIR__ . '/../libs/KitHelper.php';

class KitController {

    public function download($eventId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $eventModel = new Event();
        
        // Check if user is approved for this event and get RSVP ID
        $query = "SELECT id, status, qr_code FROM rsvps WHERE event_id = :event_id AND user_id = :user_id LIMIT 1";
        $database = new Database();
        $conn = $database->getConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$rsvp || $rsvp['status'] !== 'approved') {
            die("Access Denied: You must be approved to download the kit.");
        }

        $event = $eventModel->getById($eventId);
        $userModel = new User();
        $user = $userModel->findById($userId);

        require_once __DIR__ . '/../libs/KitHelper.php';
        require_once __DIR__ . '/../libs/QRHelper.php';
        
        // Generate QR token if not exists
        $qrToken = $rsvp['qr_code'];
        if (empty($qrToken)) {
            $qrToken = QRHelper::createToken();
            $updateStmt = $conn->prepare("UPDATE rsvps SET qr_code = ? WHERE id = ?");
            $updateStmt->execute([$qrToken, $rsvp['id']]);
        }
        
        // Generate PDF with QR code
        $pdfContent = KitHelper::generate($event, $user, $rsvp['id'], $qrToken);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Digital_Kit_' . str_replace(' ', '_', $event['title']) . '.pdf"');
        echo $pdfContent;
        exit;
    }
}
