<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';

class ProfileController {
    public function view() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        // Decode JSON interests for display
        if (!empty($user['interests'])) {
            $interestsArray = json_decode($user['interests'], true);
            if (is_array($interestsArray)) {
                $user['interests'] = implode(', ', $interestsArray);
            }
        }

        $eventModel = new Event();
        // Get Hosted Events
        $hostedEvents = $eventModel->getHostedEvents($_SESSION['user_id']);
        
        // Get Participated Events (Approved RSVPs)
        $participatedEvents = $eventModel->getParticipatedEvents($_SESSION['user_id']);

        // Check and Get Badges
        $newBadges = $userModel->checkBadges($_SESSION['user_id']);
        $badges = $userModel->getBadges($_SESSION['user_id']);

        require_once __DIR__ . '/../views/profile/view.php';
    }

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $userModel = new User();
        $data = [
            'name' => $_POST['name'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'interests' => $_POST['interests'] ?? ''
        ];

        // Handle Avatar Upload
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                $data['avatar_url'] = BASE_URL . 'public/uploads/avatars/' . $fileName;
            }
        }

        if ($userModel->updateProfile($_SESSION['user_id'], $data['name'], $data['bio'], $data['interests'])) {
            if (isset($data['avatar_url'])) {
                $_SESSION['user_avatar'] = $data['avatar_url'];
            }
            $_SESSION['user_name'] = $data['name'];
            header('Location: ' . BASE_URL . 'profile?success=updated');
        } else {
            header('Location: ' . BASE_URL . 'profile?error=failed');
        }
        exit;
    }
}
