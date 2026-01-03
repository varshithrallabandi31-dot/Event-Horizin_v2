<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

class EventController {

    public function show($id) {
        $eventModel = new Event();
        $event = $eventModel->getById($id);

        if (!$event) {
            http_response_code(404);
            renderView('404'); 
            return;
        }

        require_once __DIR__ . '/../models/RSVP.php';
        require_once __DIR__ . '/../models/User.php';
        $rsvpModel = new RSVP();
        $userModel = new User();
        $rsvpStatus = null;
        $currentUser = null;
        
        if (isset($_SESSION['user_id'])) {
            $rsvpStatus = $rsvpModel->getUserStatus($_SESSION['user_id'], $id);
            $currentUser = $userModel->findById($_SESSION['user_id']); // Fetch user info
        }

        $ticketTiers = $eventModel->getTicketTiers($id);
        $memories = $eventModel->getMemories($id);
        $faqs = $eventModel->getFaqs($id);

        // Fetch Custom Sections
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM event_sections WHERE event_id = ? ORDER BY `order` ASC");
        $stmt->execute([$id]);
        $customSections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        renderView('events/show', [
            'event' => $event, 
            'rsvpStatus' => $rsvpStatus,
            'ticketTiers' => $ticketTiers,
            'memories' => $memories,
            'faqs' => $faqs,
            'customSections' => $customSections,
            'currentUser' => $currentUser
        ]);
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
            return;
        }

        renderView('events/create');
    }

    public function preview() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header('Location: ' . BASE_URL . 'events/create');
             exit;
        }

        // Handle File Upload for Preview
        $image_url = $_POST['image'] ?? '';
        if (isset($_FILES['header_file']) && $_FILES['header_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/temp/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['header_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'preview_' . ($_SESSION['user_id'] ?? 'guest') . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['header_file']['tmp_name'], $targetPath)) {
                $image_url = BASE_URL . 'public/uploads/temp/' . $fileName;
            }
        }

        // Construct temporary event object from POST data
        $event = [
            'id' => 0, // Dummy ID
            'organizer_id' => $_SESSION['user_id'] ?? 0,
            'title' => $_POST['title'] ?? 'Untitled Event',
            'description' => $_POST['description'] ?? '',
            'start_time' => ($_POST['date'] ?? date('Y-m-d')) . ' ' . ($_POST['time'] ?? '12:00'),
            'location_name' => $_POST['location_name'] ?? 'TBD',
            'city' => $_POST['city'] ?? '',
            'category' => $_POST['category'] ?? 'Social',
            'image_url' => $image_url,
            'header_type' => $_POST['header_type'] ?? 'image',
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'requires_approval' => isset($_POST['requires_approval']) ? (int)$_POST['requires_approval'] : 1,
            'organizer_name' => 'You (Preview)', // simplified
        ];

        // ... (rest of preview logic: tiers, custom sections)
        // Parse Tiers
        $ticketTiers = [];
        if (isset($_POST['tiers'])) {
            $tiersData = json_decode($_POST['tiers'], true);
            if (is_array($tiersData)) {
                foreach($tiersData as $t) {
                     $ticketTiers[] = [
                         'id' => rand(100, 999),
                         'name' => $t['name'],
                         'price' => $t['price'],
                         'quantity_available' => 100
                     ];
                }
            }
        }

        // Parse Custom Sections
        $customSections = [];
        if (isset($_POST['custom_sections'])) {
            $sectionsData = json_decode($_POST['custom_sections'], true);
            if (is_array($sectionsData)) {
                 $customSections = $sectionsData;
            }
        }

        // Render Show View with Preview Flag
        renderView('events/show', [
            'event' => $event,
            'rsvpStatus' => null,
            'ticketTiers' => $ticketTiers,
            'memories' => [],
            'faqs' => [],
            'customSections' => $customSections,
            'currentUser' => ['name' => 'Preview User', 'email' => 'preview@example.com'],
            'isPreview' => true
        ]);
        exit;
    }

    private function handleCreate() {
        // Validate session
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error', 
                'message' => 'Session expired. Please login again.',
                'redirect' => BASE_URL . 'login'
            ]);
            exit;
        }
        
        $eventModel = new Event();
        
        $image_url = $_POST['image'] ?? '';

        // Handle File Upload
        if (isset($_FILES['header_file']) && $_FILES['header_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/events/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['header_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'event_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['header_file']['tmp_name'], $targetPath)) {
                $image_url = BASE_URL . 'public/uploads/events/' . $fileName;
            }
        }

        // Prepare data from POST
        $data = [
            'organizer_id' => $_SESSION['user_id'],
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'start_time' => ($_POST['date'] ?? '') . ' ' . ($_POST['time'] ?? ''),
            'location_name' => $_POST['location_name'] ?? '',
            'city' => $_POST['city'] ?? '',
            'category' => $_POST['category'] ?? 'Social',
            'image_url' => $image_url,
            'header_type' => $_POST['header_type'] ?? 'image', 
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'requires_approval' => isset($_POST['requires_approval']) ? (int)$_POST['requires_approval'] : 1
        ];

        $tiers = [];
        if (isset($_POST['tiers'])) {
            $tiers = json_decode($_POST['tiers'], true);
            if (!is_array($tiers)) $tiers = [];
        }

        // LOGGING DATA
        file_put_contents(__DIR__ . '/../../debug_controller.log', "Creating Event:\n" . print_r($data, true) . "\nTiers: " . print_r($tiers, true) . "\n", FILE_APPEND);

        $eventId = $eventModel->create($data, $tiers);

        if ($eventId) {
            // Save Custom Sections
            if (isset($_POST['custom_sections'])) {
                $sections = json_decode($_POST['custom_sections'], true);
                if (is_array($sections) && !empty($sections)) {
                    $db = new Database();
                    $conn = $db->getConnection();
                    $stmt = $conn->prepare("INSERT INTO event_sections (event_id, title, content, type, `order`) VALUES (?, ?, ?, ?, ?)");
                    
                    foreach ($sections as $index => $section) {
                        if (!empty($section['title']) && !empty($section['content'])) {
                            $stmt->execute([
                                $eventId,
                                $section['title'],
                                $section['content'],
                                $section['type'] ?? 'custom',
                                $index // Use index as order
                            ]);
                        }
                    }
                }
            }

            // Auto-Generate Standard FAQs
            $faqs = [
                ['question' => 'Is parking available?', 'answer' => 'Yes, we have a dedicated lot.'],
                ['question' => 'Is there a dress code?', 'answer' => 'Smart casual is recommended.'],
                ['question' => 'Can I bring a plus one?', 'answer' => 'Please check the ticket details.'],
                ['question' => 'Is food provided?', 'answer' => 'Yes, generic snacks will be available.'],
                ['question' => 'Is there wheelchair access?', 'answer' => 'Yes, the venue is fully accessible.'],
                ['question' => 'What is the refund policy?', 'answer' => 'Refunds are available up to 24 hours before.'],
                ['question' => 'Are pets allowed?', 'answer' => 'Service animals only.']
            ];

            $db = new Database();
            $conn = $db->getConnection();
            $faqStmt = $conn->prepare("INSERT INTO faqs (event_id, question, answer, created_at) VALUES (?, ?, ?, NOW())");
            
            foreach ($faqs as $faq) {
                $faqStmt->execute([$eventId, $faq['question'], $faq['answer']]);
            }
            // Send Email Notification to Host
            if (isset($_SESSION['user_id'])) {
                $userModel = new User();
                $organizer = $userModel->findById($_SESSION['user_id']);
                
                if ($organizer && !empty($organizer['email'])) {
                    require_once __DIR__ . '/../libs/MailHelper.php';
                    require_once __DIR__ . '/../libs/EmailTemplates.php';
                    
                    $eventDate = date('F j, Y, g:i a', strtotime($data['start_time']));
                    $eventLink = BASE_URL . 'event/' . $eventId;
                    
                    $emailBody = EmailTemplates::eventCreated($organizer['name'], $data['title'], $eventDate, $eventLink);
                    MailHelper::send($organizer['email'], "Event Published: " . $data['title'], $emailBody);
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'id' => $eventId, 'redirect' => BASE_URL]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to save event']);
        }
        exit;
    }
    

    public function ticket($eventId) {
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if(!$event) {
            http_response_code(404);
            renderView('404');
            return;
        }

        renderView('events/ticket', ['event' => $event]);
    }
    public function organizerDashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $eventModel = new Event();
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Fetch Request Stats (Total Pending Requests)
        $query = "SELECT COUNT(*) as count FROM rsvps r 
                  JOIN events e ON r.event_id = e.id 
                  WHERE e.organizer_id = ? AND r.status = 'pending'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        $pendingRequests = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        // 2. Fetch Events created by this user
        // Fetch events hosted by this user
        // Sort by start_time ASC so finding the "next" event is easy for Planner
        $query = "SELECT *, 
                  (SELECT COUNT(*) FROM rsvps WHERE event_id = events.id AND status = 'approved') as rsvp_count 
                  FROM events 
                  WHERE organizer_id = ? 
                  ORDER BY start_time ASC";
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        $hostedEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Fetch All Recent RSVP Activity (for the list)
        $query = "SELECT r.*, e.title as event_title, u.name as user_name, u.email as user_email 
                  FROM rsvps r
                  JOIN events e ON r.event_id = e.id
                  JOIN users u ON r.user_id = u.id
                  WHERE e.organizer_id = ? 
                  ORDER BY r.created_at DESC LIMIT 50";
        $stmt = $conn->prepare($query);
        $stmt->execute([$userId]);
        $recentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        renderView('events/organizer_dashboard', [
            'hostedEvents' => $hostedEvents,
            'pendingRequests' => $pendingRequests,
            'rsvps' => $recentRequests
        ]);
    }

    public function analytics($eventId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        // Ensure user is the organizer
        if($event['organizer_id'] != $_SESSION['user_id']) {
             die("Unauthorized");
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // 1. Get Status Counts
        $stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM rsvps WHERE event_id = ? GROUP BY status");
        $stmt->execute([$eventId]);
        $rawStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Map DB statuses to UI Expected Keys
        $analytics = [
            'total_registrations' => array_sum($rawStats),
            'checked_in' => $rawStats['checked_in'] ?? 0, // Assuming 'checked_in' status exists or is future feature
            'confirmed' => $rawStats['approved'] ?? 0,
            'cancelled' => ($rawStats['rejected'] ?? 0) + ($rawStats['cancelled'] ?? 0),
            'trend' => []
        ];

        // 2. Get Trend (Last 7 Days)
        $stmt = $conn->prepare("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM rsvps 
            WHERE event_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $stmt->execute([$eventId]);
        $trendData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fill empty days for better chart
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            foreach($trendData as $day) {
                if($day['date'] == $date) {
                     $last7Days[] = ['date' => $date, 'count' => $day['count']];
                     $found = true;
                     break;
                }
            }
            if(!$found) $last7Days[] = ['date' => $date, 'count' => 0];
        }
        $analytics['trend'] = $last7Days;

        renderView('events/analytics', [
            'event' => $event,
            'analytics' => $analytics // Pass the variable view expects
        ]);
    }

    public function configureSeating() {
        if (!isset($_SESSION['user_id'])) {
             header('Content-Type: application/json');
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }
        
        $eventId = $_POST['event_id'] ?? 0;
        $rows = (int)($_POST['rows'] ?? 10);
        $cols = (int)($_POST['cols'] ?? 10);
        $pricing = [
            'standard' => $_POST['price_standard'] ?? 0,
            'vip' => $_POST['price_vip'] ?? 0
        ];
        
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
             header('Content-Type: application/json');
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        if ($eventModel->generateSeats($eventId, $rows, $cols, $pricing)) {
             header('Content-Type: application/json');
             echo json_encode(['status' => 'success']);
        } else {
             header('Content-Type: application/json');
             echo json_encode(['status' => 'error']);
        }
        exit;
    }

    public function getSeatsJSON($eventId) {
        $eventModel = new Event();
        $seats = $eventModel->getSeats($eventId);
        header('Content-Type: application/json');
        echo json_encode(['seats' => $seats]);
        exit;
    }

    public function submitRSVP() {
        if (!isset($_SESSION['user_id'])) {
             $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
             header('Location: ' . BASE_URL . 'login');
             exit;
        }

        $eventId = $_POST['event_id'] ?? 0;
        $interest = $_POST['interest'] ?? '';
        $name = $_POST['name'] ?? ''; 
        $email = $_POST['email'] ?? ''; 
        $ticketTierId = !empty($_POST['ticket_tier_id']) ? $_POST['ticket_tier_id'] : null;
        $seatId = !empty($_POST['seat_id']) ? $_POST['seat_id'] : null;
        
        $userId = $_SESSION['user_id'];
        
        if (!empty($name) || !empty($email)) {
            $userModel = new User();
            $userModel->updateBasicInfo($userId, $name, !empty($email) ? $email : null);
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Check event and ticket details
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        $ticketPrice = 0;
        
        if ($ticketTierId) {
            $query = "SELECT * FROM ticket_tiers WHERE id = ? AND event_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$ticketTierId, $eventId]);
            $tier = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tier) {
                $ticketPrice = $tier['price'];
            }
        }
        
        // Handle Seat Price Override if Seat Selected
        if ($seatId) {
             $stmt = $conn->prepare("SELECT * FROM seats WHERE id = ? AND event_id = ?");
             $stmt->execute([$seatId, $eventId]);
             $seat = $stmt->fetch(PDO::FETCH_ASSOC);
             
             if ($seat && $seat['status'] === 'available') {
                  // Override ticket price with seat price if higher/set? 
                  // For now assuming seat price IS the ticket price.
                  if ($seat['tier_price'] > 0) {
                      $ticketPrice = $seat['tier_price'];
                  }
                  
                  // Reserve Seat (Temporary until payment or final save)
                  // preventing race conditions ideally requires transaction
                  $conn->query("UPDATE seats SET status = 'reserved' WHERE id = $seatId");
             } else {
                  // Seat taken
                  header('Content-Type: application/json');
                  echo json_encode(['status' => 'error', 'message' => 'Seat already taken']);
                  exit;
             }
        }

        // Check if exists
        try {
            $stmt = $conn->prepare("SELECT id, status, payment_status FROM rsvps WHERE event_id = ? AND user_id = ?");
            $stmt->execute([$eventId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($existing) {
                // Allow multiple RSVPs if different seats? For now 1 user 1 RSVP.
                header('Location: ' . BASE_URL . 'rsvp/success');
                exit;
            }
        } catch (PDOException $e) { }
        
        // Determine Status
        $status = 'pending';
        // Auto-approve if free AND no approval required
        if ($ticketPrice == 0 && isset($event['requires_approval']) && $event['requires_approval'] == 0) {
            $status = 'approved';
        }

        $paymentStatus = ($ticketPrice > 0) ? 'pending' : 'completed';
        $itemStatus = ($ticketPrice > 0) ? 'pending' : $status;

        $answers = json_encode(['interest' => $interest]);

        $stmt = $conn->prepare("INSERT INTO rsvps (event_id, user_id, ticket_tier_id, seat_id, answers, status, payment_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        if($stmt->execute([$eventId, $userId, $ticketTierId, $seatId, $answers, $itemStatus, $paymentStatus])) {
            $rsvpId = $conn->lastInsertId();
            
            if ($seatId && $paymentStatus === 'completed') {
                  $conn->query("UPDATE seats SET status = 'booked' WHERE id = $seatId");
            }
            
            if ($ticketPrice > 0) {
                 // Return JSON for PayPal integration to handle flow
                 header('Content-Type: application/json');
                 echo json_encode([
                     'status' => 'payment_required',
                     'rsvp_id' => $rsvpId, 
                     'amount' => $ticketPrice,
                     'currency' => 'USD'
                 ]);
                 exit;
            }

            if ($status === 'approved') {
                $this->sendApprovalEmail($userId, $eventId);
            }
            header('Location: ' . BASE_URL . 'rsvp/success');
        } else {
             die("Database Error");
        }
        exit;
    }

    public function verifyPayment() {
        header('Content-Type: application/json');
        
        $rsvpId = $_POST['rsvp_id'] ?? 0;
        $orderId = $_POST['order_id'] ?? '';
        
        if (!$rsvpId || !$orderId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // In a real app, verify $orderId with PayPal API here
        // For now, we trust the client-side success (Sandbox mode)
        
        $stmt = $conn->prepare("UPDATE rsvps SET payment_status = 'completed', payment_id = ?, status = 'approved', approved_at = NOW() WHERE id = ?");
        if ($stmt->execute([$orderId, $rsvpId])) {
            // Confirm Seat Booking
            $stmt = ($conn->prepare("SELECT seat_id FROM rsvps WHERE id = ?"));
            $stmt->execute([$rsvpId]);
            $rsvpData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rsvpData && $rsvpData['seat_id']) {
                 $conn->query("UPDATE seats SET status = 'booked' WHERE id = " . $rsvpData['seat_id']);
            }
            
            // Send Email
            $stmt = $conn->prepare("SELECT user_id, event_id FROM rsvps WHERE id = ?");
            $stmt->execute([$rsvpId]);
            $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($rsvp) {
                $this->sendApprovalEmail($rsvp['user_id'], $rsvp['event_id']);
            }
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Update failed']);
        }
        exit;
    }

    private function sendApprovalEmail($userId, $eventId) {
        try {
            require_once __DIR__ . '/../libs/MailHelper.php';
            require_once __DIR__ . '/../libs/EmailTemplates.php';
            require_once __DIR__ . '/../libs/KitHelper.php';
            require_once __DIR__ . '/../libs/QRHelper.php';
            require_once __DIR__ . '/../models/User.php';
            require_once __DIR__ . '/../models/Event.php';

            $userModel = new User();
            $eventModel = new Event();
            
            $user = $userModel->findById($userId);
            $event = $eventModel->getById($eventId);
            
            if (!$user || !$event) {
                error_log("sendApprovalEmail: User or Event not found. UserID: $userId, EventID: $eventId");
                return;
            }
            
            if (empty($user['email'])) {
                error_log("sendApprovalEmail: User email is empty. UserID: $userId");
                return;
            }

            require_once __DIR__ . '/../libs/CalendarHelper.php';
            
            // Get RSVP ID and generate QR token
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT id, qr_code FROM rsvps WHERE user_id = ? AND event_id = ?");
            $stmt->execute([$userId, $eventId]);
            $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$rsvp) {
                error_log("sendApprovalEmail: RSVP not found. UserID: $userId, EventID: $eventId");
                return;
            }
            
            // Generate or use existing QR token
            $qrToken = $rsvp['qr_code'];
            if (empty($qrToken)) {
                $qrToken = $this->generateQRToken($rsvp['id'], $eventId);
                error_log("sendApprovalEmail: Generated new QR token for RSVP ID: " . $rsvp['id']);
            } else {
                error_log("sendApprovalEmail: Using existing QR token for RSVP ID: " . $rsvp['id']);
            }
            
            // Generate PDF with QR code
            error_log("sendApprovalEmail: Generating PDF for RSVP ID: " . $rsvp['id']);
            $pdfContent = KitHelper::generate($event, $user, $rsvp['id'], $qrToken);
            $pdfName = 'EventKit_' . preg_replace('/[^a-zA-Z0-9]/', '_', $event['title']) . '.pdf';
            
            // Generate standalone QR code PNG
            $qrData = QRHelper::createRSVPPayload($rsvp['id'], $eventId, $qrToken);
            $qrImagePath = sys_get_temp_dir() . '/qr_email_' . $rsvp['id'] . '_' . time() . '.png';
            $qrImageContent = null;
            
            if (QRHelper::generate($qrData, $qrImagePath, 8, 2)) {
                $qrImageContent = file_get_contents($qrImagePath);
                @unlink($qrImagePath); // Clean up temp file
                error_log("sendApprovalEmail: QR code PNG generated successfully");
            } else {
                error_log("sendApprovalEmail: QR code PNG generation failed");
            }

            // Generate ICS Calendar File
            $icsContent = CalendarHelper::generateIcs($event);
            $icsName = 'event_invite.ics';
            
            // Generate Email Body
            $eventLink = BASE_URL . 'event/' . $eventId;
            $kitLink = BASE_URL . 'event/' . $eventId . '/download-kit';
            $body = EmailTemplates::rsvpConfirmation($user['name'], $event['title'], $eventLink, $kitLink);
            
            // Prepare attachments
            $attachments = [
                ['content' => $pdfContent, 'name' => $pdfName, 'type' => 'application/pdf'],
                ['content' => $icsContent, 'name' => $icsName, 'type' => 'text/calendar']
            ];
            
            if ($qrImageContent) {
                $qrImageName = 'QRCode_' . preg_replace('/[^a-zA-Z0-9]/', '_', $event['title']) . '.png';
                $attachments[] = ['content' => $qrImageContent, 'name' => $qrImageName, 'type' => 'image/png'];
            }
            
            // Send with multiple attachments
            error_log("sendApprovalEmail: Attempting to send email to: " . $user['email']);
            $result = MailHelper::sendWithMultipleAttachments($user['email'], "You're In! - " . $event['title'], $body, $attachments);
            
            if ($result) {
                error_log("sendApprovalEmail: Email sent successfully to: " . $user['email']);
            } else {
                error_log("sendApprovalEmail: Email sending failed to: " . $user['email']);
            }
        } catch (Exception $e) {
            error_log("sendApprovalEmail ERROR: " . $e->getMessage() . " | " . $e->getTraceAsString());
        }
    }
    
    private function sendRejectionEmail($userId, $eventId) {
        try {
            require_once __DIR__ . '/../libs/MailHelper.php';
            require_once __DIR__ . '/../libs/EmailTemplates.php';
            require_once __DIR__ . '/../models/User.php';
            require_once __DIR__ . '/../models/Event.php';

            $userModel = new User();
            $eventModel = new Event();
            
            $user = $userModel->findById($userId);
            $event = $eventModel->getById($eventId);
            
            if (!$user || !$event) {
                error_log("sendRejectionEmail: User or Event not found. UserID: $userId, EventID: $eventId");
                return;
            }
            
            if (empty($user['email'])) {
                error_log("sendRejectionEmail: User email is empty. UserID: $userId");
                return;
            }
            
            // Generate Email Body
            $eventLink = BASE_URL . 'explore';
            $body = EmailTemplates::rsvpRejection($user['name'], $event['title'], $eventLink);
            
            // Send
            error_log("sendRejectionEmail: Attempting to send rejection email to: " . $user['email']);
            $result = MailHelper::send($user['email'], "RSVP Update: " . $event['title'], $body);
            
            if ($result) {
                error_log("sendRejectionEmail: Email sent successfully to: " . $user['email']);
            } else {
                error_log("sendRejectionEmail: Email sending failed to: " . $user['email']);
            }
        } catch (Exception $e) {
            error_log("sendRejectionEmail ERROR: " . $e->getMessage() . " | " . $e->getTraceAsString());
        }
    }

    public function rsvpSuccess() {
        $event = null;
        if (isset($_SESSION['user_id'])) {
            $db = new Database();
            $conn = $db->getConnection();
            // Get latest RSVP
            $stmt = $conn->prepare("SELECT e.* FROM rsvps r JOIN events e ON r.event_id = e.id WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        renderView('events/rsvp_success', ['event' => $event]);
    }
    
    private function generateQRToken($rsvpId, $eventId) {
        require_once __DIR__ . '/../libs/QRHelper.php';
        
        $token = QRHelper::createToken();
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // Store token in database
        $stmt = $conn->prepare("UPDATE rsvps SET qr_code = ? WHERE id = ?");
        $stmt->execute([$token, $rsvpId]);
        
        return $token;
    }
    
    public function validateQRCode() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['qr_data'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }
        
        require_once __DIR__ . '/../libs/QRHelper.php';
        require_once __DIR__ . '/../models/User.php';
        
        // Parse QR code payload
        $qrPayload = QRHelper::parsePayload($data['qr_data']);
        
        if (!$qrPayload) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid QR code format']);
            exit;
        }
        
        $rsvpId = $qrPayload['rsvp_id'];
        $token = $qrPayload['token'];
        $eventId = $qrPayload['event_id'];
        
        $db = new Database();
        $conn = $db->getConnection();
        
        // Verify RSVP exists and token matches
        $stmt = $conn->prepare("
            SELECT r.*, u.name, u.email, u.phone, e.title as event_title, e.organizer_id
            FROM rsvps r
            JOIN users u ON r.user_id = u.id
            JOIN events e ON r.event_id = e.id
            WHERE r.id = ? AND r.qr_code = ? AND r.event_id = ?
        ");
        $stmt->execute([$rsvpId, $token, $eventId]);
        $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rsvp) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user - QR code not found']);
            exit;
        }
        
        // Verify the organizer is viewing their own event
        if ($rsvp['organizer_id'] != $_SESSION['user_id']) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid QR code for this event']);
            exit;
        }
        
        // Check if already checked in
        if ($rsvp['status'] === 'checked_in') {
            $checkedInTime = date('M d, Y g:i A', strtotime($rsvp['checked_in_at']));
            echo json_encode([
                'status' => 'warning',
                'message' => 'Already checked in',
                'checked_in_at' => $checkedInTime,
                'user' => [
                    'name' => $rsvp['name'],
                    'email' => $rsvp['email'],
                    'phone' => $rsvp['phone']
                ]
            ]);
            exit;
        }
        
        // Check if approved
        if ($rsvp['status'] !== 'approved') {
            echo json_encode(['status' => 'error', 'message' => 'RSVP not approved - Status: ' . $rsvp['status']]);
            exit;
        }
        
        // Mark as checked in
        $stmt = $conn->prepare("UPDATE rsvps SET status = 'checked_in', checked_in_at = NOW() WHERE id = ?");
        $stmt->execute([$rsvpId]);
        
        // Return success with user details
        echo json_encode([
            'status' => 'success',
            'message' => 'Entry Validated',
            'user' => [
                'name' => $rsvp['name'],
                'email' => $rsvp['email'],
                'phone' => $rsvp['phone']
            ],
            'event' => $rsvp['event_title']
        ]);
        exit;
    }

    public function approveRSVP($rsvpId) {
        $this->updateRSVPStatus($rsvpId, 'approved');
    }

    public function rejectRSVP($rsvpId) {
         $this->updateRSVPStatus($rsvpId, 'rejected');
    }

    private function updateRSVPStatus($rsvpId, $status) {
         if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
         }
         
         $db = new Database();
         $conn = $db->getConnection();
         
         // Verify ownership and fetch RSVP details
         $stmt = $conn->prepare("SELECT r.id, r.user_id, r.event_id FROM rsvps r JOIN events e ON r.event_id = e.id WHERE r.id = ? AND e.organizer_id = ?");
         $stmt->execute([$rsvpId, $_SESSION['user_id']]);
         if($stmt->rowCount() == 0) {
             die("Unauthorized");
         }
         
         $rsvp = $stmt->fetch(PDO::FETCH_ASSOC);

         $stmt = $conn->prepare("UPDATE rsvps SET status = ? WHERE id = ?");
         $stmt->execute([$status, $rsvpId]);
         
         // Send email based on status
         if ($status === 'approved') {
             $this->sendApprovalEmail($rsvp['user_id'], $rsvp['event_id']);
         } elseif ($status === 'rejected') {
             $this->sendRejectionEmail($rsvp['user_id'], $rsvp['event_id']);
         }
         
         header('Location: ' . $_SERVER['HTTP_REFERER']);
         exit;
    }

    public function chat($eventId) {
        if (!isset($_SESSION['user_id'])) {
             // Ensure clean output
             if (ob_get_length()) ob_clean();
             header('Content-Type: application/json');
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Fetch messages with User info
        $stmt = $conn->prepare("
            SELECT m.*, u.name as user_name, m.created_at 
            FROM messages m 
            JOIN users u ON m.user_id = u.id 
            WHERE m.event_id = ? 
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$eventId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $formatted = [];
        foreach($messages as $msg) {
            $formatted[] = [
                'user' => $msg['user_name'],
                // Decode potentially double-encoded chars or just simple escaping
                'content' => htmlspecialchars($msg['content']), 
                'time' => date('h:i A', strtotime($msg['created_at'])),
                'is_me' => ($msg['user_id'] == $_SESSION['user_id'])
            ];
        }

        // Ensure clean output before JSON
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        echo json_encode(['status' => 'success', 'messages' => $formatted]);
        exit;
    }

    public function sendMessage($eventId) {
        if (!isset($_SESSION['user_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        $userId = $_SESSION['user_id'];
        $message = trim($_POST['message'] ?? '');
        
        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Message empty']);
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("INSERT INTO messages (event_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
        if($stmt->execute([$eventId, $userId, $message])) {
             // Return the message structure so JS can append it immediately
             $userModel = new User();
             $user = $userModel->findById($userId);
             echo json_encode([
                 'status' => 'success',
                 'message' => [
                     'user' => $user['name'],
                     'content' => htmlspecialchars($message),
                     'time' => date('h:i A'),
                     'is_me' => true
                 ]
             ]);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        exit;
    }

    public function getPolls($eventId) {
        $userId = $_SESSION['user_id'] ?? null;
        require_once __DIR__ . '/../models/Poll.php';
        $pollModel = new Poll();
        $polls = $pollModel->getByEventId($eventId, $userId);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'polls' => $polls]);
        exit;
    }

    public function createPoll($eventId) {
        if (!isset($_SESSION['user_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        $userId = $_SESSION['user_id'];
        $question = $_POST['question'] ?? '';
        $options = $_POST['options'] ?? [];

        if (empty($question) || count($options) < 2) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid poll data']);
             exit;
        }

        require_once __DIR__ . '/../models/Poll.php';
        $pollModel = new Poll();
        $result = $pollModel->create($eventId, $userId, $question, $options);
        
        if (is_numeric($result)) {
             echo json_encode(['status' => 'success']);
        } else {
             // Result contains the error message
             echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $result]);
        }
        exit;
    }

    public function votePoll($eventId) {
        if (!isset($_SESSION['user_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        $userId = $_SESSION['user_id'];
        $pollId = $_POST['poll_id'] ?? 0;
        $optionId = $_POST['option_id'] ?? 0;

        require_once __DIR__ . '/../models/Poll.php';
        $pollModel = new Poll();
        if ($pollModel->vote($pollId, $optionId, $userId)) {
             echo json_encode(['status' => 'success']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Already voted or error']);
        }
        exit;
    }

    public function referFriend($eventId) {
        if (!isset($_SESSION['user_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
             exit;
        }

        $friendName = $_POST['friend_name'] ?? '';
        $friendEmail = $_POST['friend_email'] ?? '';

        if (empty($friendName) || empty($friendEmail) || !filter_var($friendEmail, FILTER_VALIDATE_EMAIL)) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid details']);
             exit;
        }

        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event) {
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            exit;
        }

        require_once __DIR__ . '/../libs/MailHelper.php';
        require_once __DIR__ . '/../libs/EmailTemplates.php';

        $senderName = $_SESSION['user_name'];
        $eventLink = BASE_URL . 'event/' . $eventId;
        $eventDate = date('l, M j, Y @ g:i A', strtotime($event['start_time']));
        
        $emailContent = EmailTemplates::referralInvite(
            $senderName, 
            $event['title'], 
            $eventDate, 
            $event['location_name'], 
            $eventLink
        );

        if (MailHelper::send($friendEmail, "Invitation: " . $event['title'], $emailContent)) {
             echo json_encode(['status' => 'success']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Failed to send email']);
        }
        exit;
    }
    
    public function postMemory($eventId) {
         if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
         }
         
         $imageUrl = $_POST['image_url'] ?? '';
         $caption = $_POST['caption'] ?? '';
         
         if (!empty($imageUrl)) {
             $eventModel = new Event();
             $eventModel->addMemory($eventId, $_SESSION['user_id'], $imageUrl, $caption);
         }
         
         header('Location: ' . $_SERVER['HTTP_REFERER']);
         exit;
    }

    public function sendBulkMail() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header('Location: ' . BASE_URL . 'organizer/dashboard');
             exit;
        }

        $organizerId = $_SESSION['user_id'];
        $organizerName = $_SESSION['user_name'];
        $subject = $_POST['subject'] ?? 'Update from Organizer';
        $message = $_POST['message'] ?? '';
        
        if (empty($message)) {
             header('Location: ' . BASE_URL . 'organizer/dashboard?error=empty_message');
             exit;
        }

        require_once __DIR__ . '/../libs/MailHelper.php';
        require_once __DIR__ . '/../libs/EmailTemplates.php';

        $db = new Database();
        $conn = $db->getConnection();
        
        // Fetch all approved/pending rsvps for this organizer's events
        // We group by email to avoid sending duplicate emails if a user registered for multiple events
        // But we might want to mention which event it is for?
        // For "All Events", let's keep it general or comma separate titles? 
        // Simpler approach: Send one email per user, saying "regarding your events with [Organizer]"
        
        $query = "SELECT DISTINCT u.email, u.name, GROUP_CONCAT(DISTINCT e.title SEPARATOR ', ') as event_titles
                  FROM rsvps r
                  JOIN events e ON r.event_id = e.id
                  JOIN users u ON r.user_id = u.id
                  WHERE e.organizer_id = ?
                  GROUP BY u.email";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$organizerId]);
        $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($recipients as $recipient) {
            $emailContent = EmailTemplates::formalUpdate(
                $organizerName,
                $subject,
                $message,
                $recipient['event_titles'] // "Tech Meetup, Music Fest"
            );

            if(MailHelper::send($recipient['email'], $subject, $emailContent)) {
                $count++;
            }
        }

        header('Location: ' . BASE_URL . 'organizer/dashboard?success=mail_sent&count=' . $count);
        exit;
    }

    public function deleteEvent($eventId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);

        // Verify ownership
        if (!$event || $event['organizer_id'] != $userId) {
            die("Unauthorized: You are not the organizer of this event.");
        }

        require_once __DIR__ . '/../libs/MailHelper.php';
        require_once __DIR__ . '/../libs/EmailTemplates.php';

        $db = new Database();
        $conn = $db->getConnection();

        // Fetch all registered users for this event
        $query = "SELECT DISTINCT u.name, u.email 
                  FROM rsvps r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.event_id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([$eventId]);
        $registeredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send cancellation emails to all registered users
        $eventDate = date('l, M j, Y @ g:i A', strtotime($event['start_time']));
        
        foreach ($registeredUsers as $user) {
            if (!empty($user['email'])) {
                $emailContent = EmailTemplates::eventCancellation(
                    $user['name'],
                    $event['title'],
                    $eventDate,
                    $event['location_name']
                );
                
                MailHelper::send($user['email'], "Event Cancelled: " . $event['title'], $emailContent);
            }
        }

        // Delete event and related data (cascade)
        // Delete related data first to avoid foreign key constraints
        // Use try-catch to handle missing tables gracefully
        try {
            $conn->prepare("DELETE FROM event_memories WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM messages WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM poll_votes WHERE poll_id IN (SELECT id FROM polls WHERE event_id = ?)")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM poll_options WHERE poll_id IN (SELECT id FROM polls WHERE event_id = ?)")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM polls WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM faqs WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM rsvps WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        try {
            $conn->prepare("DELETE FROM ticket_tiers WHERE event_id = ?")->execute([$eventId]);
        } catch (PDOException $e) {
            // Table might not exist, continue
        }
        
        $conn->prepare("DELETE FROM events WHERE id = ?")->execute([$eventId]);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
        exit;
    }

    public function generatePlan() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $eventModel = new Event();
        $hostedEvents = $eventModel->getHostedEvents($userId);

        // Simple "AI" Logic: Analyze past events to suggest the next one
        $categories = [];
        $titles = [];
        
        foreach ($hostedEvents as $event) {
            $categories[] = $event['category'];
            $titles[] = $event['title'];
        }

        // Determine most frequent category
        $vals = array_count_values($categories);
        arsort($vals);
        $favoriteCategory = array_key_first($vals) ?? 'Social';

        // Suggest a date (2 weeks from now, on a Friday)
        $date = new DateTime('+2 weeks');
        $date->modify('next Friday');
        $suggestedDate = $date->format('Y-m-d');
        $suggestedTime = '19:00';

        // simple templates based on category
        $templates = [
            'Tech' => [
                'titles' => ['Next Gen Tech Talk', 'Coding Workshop: Advanced Levels', 'Future of AI Symposium', 'Tech Networking Night'],
                'description' => "Join us for an evening of innovation and networking. We'll dive deep into the latest trends in technology and connect with industry leaders."
            ],
            'Music' => [
                'titles' => ['Acoustic Sunset Sessions', 'Electronic Beats Night', 'Jazz & Wine Evening', 'Indie Rock Showcase'],
                'description' => "Experience an unforgettable night of live music. Great vibes, amazing artists, and a community of music lovers."
            ],
            'Social' => [
                'titles' => ['Community Mixer', 'Weekend Chillout', 'The Great Gathering', 'Rooftop Social'],
                'description' => "Let's get together and make some memories! A relaxed atmosphere to meet new people and catch up with old friends."
            ],
            'Business' => [
                'titles' => ['Entrepreneurship Summit', 'Startup Pitch Night', 'Business Strategy Workshop', 'Networking Breakfast'],
                'description' => "Elevate your business game. precise insights, networking opportunities, and actionable strategies for growth."
            ],
            'Art' => [
               'titles' => ['Gallery Opening Night', 'Creative Arts Workshop', 'Artist Talk & Showcase', 'Modern Art Exhibition'],
               'description' => "Immerse yourself in the world of art. Discover new perspectives and meet the creators behind the masterpieces."
            ]
        ];

        $template = $templates[$favoriteCategory] ?? $templates['Social'];
        $suggestedTitle = $template['titles'][array_rand($template['titles'])];

        // "AI" variance: ensure we don't suggest the exact same title if possible (very basic check)
        if (in_array($suggestedTitle, $titles)) {
             $suggestedTitle .= ' II'; // Sequel!
        }

        $plan = [
            'title' => $suggestedTitle,
            'category' => $favoriteCategory,
            'date' => $suggestedDate,
            'time' => $suggestedTime,
            'description' => $template['description'],
            'reasoning' => "Based on your history of hosting succesful {$favoriteCategory} events, we think your audience would love this!"
        ];

        echo json_encode(['status' => 'success', 'plan' => $plan]);
        exit;
    }

    public function sendEventFeedback($eventId) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $message = $_POST['message'] ?? '';

        if (empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
            exit;
        }

        $eventModel = new Event();
        $event = $eventModel->getById($eventId);

        if (!$event || $event['organizer_id'] != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        // 1. Post to Chat
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            // Check if messages table exists, if so insert
            $stmt = $conn->prepare("INSERT INTO messages (event_id, user_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$eventId, $userId, $message]);
        } catch (Exception $e) {
            // Ignore if chat not set up, but log it
            error_log("Chat insert failed: " . $e->getMessage());
        }

        // 2. Email Attendees (Approved RSVPs)
        require_once __DIR__ . '/../libs/MailHelper.php';
        require_once __DIR__ . '/../libs/EmailTemplates.php';

        $query = "SELECT u.email, u.name FROM rsvps r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.event_id = ? AND r.status = 'approved'";
        $stmt = $conn->prepare($query);
        $stmt->execute([$eventId]);
        $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $count = 0;
        foreach ($attendees as $attendee) {
             $emailContent = EmailTemplates::formalUpdate(
                $_SESSION['user_name'] ?? 'The Organizer', 
                "Feedback Request: " . $event['title'],
                $message,
                $event['title']
            );
            
            if (MailHelper::send($attendee['email'], "Update from " . $event['title'], $emailContent)) {
                $count++;
            }
        }

        echo json_encode(['status' => 'success', 'emailed_count' => $count]);
        exit;
    }

    // Check if organizer has complete profile (name and email)
    public function checkProfileCompleteness() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit;
        }
        
        $isComplete = !empty($user['name']) && !empty($user['email']);
        
        echo json_encode([
            'status' => 'success',
            'is_complete' => $isComplete,
            'has_name' => !empty($user['name']),
            'has_email' => !empty($user['email']),
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? ''
        ]);
        exit;
    }
    
    // Update organizer profile (name and email)
    public function updateOrganizerProfile() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($name)) {
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            exit;
        }
        
        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email is required']);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
            exit;
        }
        
        $userModel = new User();
        $result = $userModel->updateBasicInfo($_SESSION['user_id'], $name, $email);
        
        if ($result) {
            // Update session
            $_SESSION['user_name'] = $name;
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Profile updated successfully'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to update profile'
            ]);
        }
        exit;
    }
    
    // Update event description (About section)
    public function updateDescription($eventId) {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $description = trim($_POST['description'] ?? '');
        
        if (empty($description)) {
            echo json_encode(['status' => 'error', 'message' => 'Description cannot be empty']);
            exit;
        }
        
        // Verify ownership
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event || $event['organizer_id'] != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized: You are not the organizer of this event']);
            exit;
        }
        
        // Update description
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            $stmt = $conn->prepare("UPDATE events SET description = ? WHERE id = ?");
            $result = $stmt->execute([$description, $eventId]);
            
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Description updated successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update description'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    // Update event schedule
    public function updateSchedule($eventId) {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $scheduleJson = $_POST['schedule'] ?? '[]';
        $schedule = json_decode($scheduleJson, true);
        
        if (!is_array($schedule)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid schedule format']);
            exit;
        }
        
        // Sanitize
        foreach ($schedule as &$item) {
            $item['time'] = htmlspecialchars(trim($item['time'] ?? ''));
            $item['title'] = htmlspecialchars(trim($item['title'] ?? ''));
        }
        
        // Verify ownership
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event || $event['organizer_id'] != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        // Update schedule
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            $stmt = $conn->prepare("UPDATE events SET schedule = ? WHERE id = ?");
            $result = $stmt->execute([json_encode($schedule), $eventId]);
            
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Schedule updated successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update schedule'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    // Update event venue
    public function updateVenue($eventId) {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event || $event['organizer_id'] != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $locationName = htmlspecialchars(trim($_POST['location_name'] ?? ''));
        $city = htmlspecialchars(trim($_POST['city'] ?? ''));
        $latitude = filter_var($_POST['latitude'] ?? 0, FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($_POST['longitude'] ?? 0, FILTER_VALIDATE_FLOAT);
        
        if (empty($locationName)) {
            echo json_encode(['status' => 'error', 'message' => 'Location name is required']);
            exit;
        }
        
        // Update venue
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            $stmt = $conn->prepare("UPDATE events SET location_name = ?, city = ?, latitude = ?, longitude = ? WHERE id = ?");
            $result = $stmt->execute([$locationName, $city, $latitude, $longitude, $eventId]);
            
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Venue updated successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update venue'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    // Update FAQs
    public function updateFaqs($eventId) {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        $eventModel = new Event();
        $event = $eventModel->getById($eventId);
        
        if (!$event || $event['organizer_id'] != $userId) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }
        
        $faqsJson = $_POST['faqs'] ?? '[]';
        $faqs = json_decode($faqsJson, true);
        
        if (!is_array($faqs)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data format']);
            exit;
        }
        
        $db = new Database();
        $conn = $db->getConnection();
        
        try {
            $conn->beginTransaction();
            
            // Delete existing FAQs
            $stmt = $conn->prepare("DELETE FROM faqs WHERE event_id = ?");
            $stmt->execute([$eventId]);
            
            // Insert new FAQs
            $stmt = $conn->prepare("INSERT INTO faqs (event_id, question, answer) VALUES (?, ?, ?)");
            
            foreach ($faqs as $faq) {
                $question = htmlspecialchars(trim($faq['question'] ?? ''));
                $answer = htmlspecialchars(trim($faq['answer'] ?? ''));
                
                if (!empty($question) && !empty($answer)) {
                    $stmt->execute([$eventId, $question, $answer]);
                }
            }
            
            $conn->commit();
            
            echo json_encode([
                'status' => 'success',
                'message' => 'FAQs updated successfully'
            ]);
            
        } catch (PDOException $e) {
            $conn->rollBack();
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
}

