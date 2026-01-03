<?php
// Bootstrap - Initialize application
require_once __DIR__ . '/bootstrap.php';

// Enable error reporting for debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Router and View logic handled below


// Routes
// Advanced Router Logic
$script_name = $_SERVER['SCRIPT_NAME']; // e.g., /P1/index.php
$request_uri = $_SERVER['REQUEST_URI']; // e.g., /P1/login or /P1/index.php/login

// Base path is the folder containing index.php
$base_path = str_replace('index.php', '', $script_name); 

// Strip the base path from the beginning of the URI
$path = $request_uri;
if (strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}

// Also strip "index.php" if it appears at the start of the remaining path
if (strpos($path, 'index.php') === 0) {
    $path = substr($path, 9);
}

// Remove query strings
$path = explode('?', $path)[0];
$path = '/' . ltrim($path, '/');

// Define Base URL for links
$current_script = $_SERVER['SCRIPT_NAME'];
$request_uri = $_SERVER['REQUEST_URI'];
$is_index_in_url = (strpos($request_uri, 'index.php') !== false);

$base_url = $base_path;
if ($is_index_in_url) {
    $base_url .= 'index.php/';
}
define('BASE_URL', $base_url);



// Helper to load view
function renderView($viewName, $data = []) {
    // Make BASE_URL available in views
    $base = BASE_URL;


    extract($data);
    if (file_exists(__DIR__ . '/app/views/layout/header.php')) {
        require_once __DIR__ . '/app/views/layout/header.php';
    }
    
    $viewPath = __DIR__ . '/app/views/' . $viewName . '.php';
    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        echo "<div class='container mx-auto text-white pt-20 text-center'>
                <h1 class='text-4xl font-bold mb-4'>404 Not Found</h1>
                <p class='text-gray-400'>The view '$viewName' was not found.</p>
              </div>";
    }

    if (file_exists(__DIR__ . '/app/views/layout/footer.php')) {
        require_once __DIR__ . '/app/views/layout/footer.php';
    }
}

// Router Switch using Regex for parameters
if ($path === '/' || $path === '/home') {
    require_once __DIR__ . '/app/controllers/DiscoveryController.php';
    $controller = new DiscoveryController();
    $controller->index();

} elseif ($path === '/explore') {
    require_once __DIR__ . '/app/controllers/DiscoveryController.php';
    $controller = new DiscoveryController();
    $controller->explore();

} elseif ($path === '/login') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $controller = new AuthController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handleLogin();
    } else {
        $controller->showLogin();
    }

} elseif ($path === '/verify-otp') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $controller = new AuthController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handleVerify();
    } else {
        $controller->showVerify();
    }

} elseif ($path === '/profile/complete') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $controller = new AuthController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handleProfileComplete();
    } else {
        $controller->showProfileComplete();
    }

} elseif ($path === '/logout') {
    require_once __DIR__ . '/app/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->logout();

} elseif ($path === '/events/create') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->create(); // Needs auth check inside

} elseif ($path === '/events/preview') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->preview();

} elseif (preg_match('#^/event/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->show($matches[1]);

} elseif (preg_match('#^/event/(\d+)/chat$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->chat($matches[1]);

} elseif (preg_match('#^/event/(\d+)/send-message$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->sendMessage($matches[1]);

} elseif ($path === '/rsvp/submit') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->submitRSVP();

} elseif ($path === '/rsvp/success') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->rsvpSuccess();

} elseif ($path === '/organizer/dashboard') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->organizerDashboard();

} elseif ($path === '/profile') {
    require_once __DIR__ . '/app/controllers/ProfileController.php';
    $controller = new ProfileController();
    $controller->view();

} elseif ($path === '/profile/update') {
    require_once __DIR__ . '/app/controllers/ProfileController.php';
    $controller = new ProfileController();
    $controller->update();

} elseif (preg_match('#^/rsvp/(\d+)/approve$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->approveRSVP($matches[1]);

} elseif (preg_match('#^/rsvp/(\d+)/reject$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->rejectRSVP($matches[1]);

} elseif ($path === '/organizer/send-bulk-mail') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->sendBulkMail();

} elseif (preg_match('#^/event/photo/(\d+)/curate$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->toggleCuratePhoto($matches[1]);

} elseif (preg_match('#^/event/(\d+)/upload-photo$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->uploadPhoto($matches[1]);

} elseif (preg_match('#^/event/(\d+)/album$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->viewAlbum($matches[1]);

} elseif (preg_match('#^/event/(\d+)/analytics$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->analytics($matches[1]);

} elseif (preg_match('#^/event/(\d+)/memories$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->postMemory($matches[1]);


} elseif (preg_match('#^/event/(\d+)/refer$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->referFriend($matches[1]);

} elseif (preg_match('#^/event/(\d+)/download-kit$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/KitController.php';
    $controller = new KitController();
    $controller->download($matches[1]);

} elseif (preg_match('#^/ticket/(\d+)$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->ticket($matches[1]);

} elseif (preg_match('#^/event/(\d+)/polls$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->getPolls($matches[1]);

} elseif (preg_match('#^/event/(\d+)/create-poll$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->createPoll($matches[1]);

} elseif (preg_match('#^/event/(\d+)/chat$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->chat($matches[1]);

} elseif (preg_match('#^/event/(\d+)/send-message$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->sendMessage($matches[1]);

} elseif (preg_match('#^/event/(\d+)/vote-poll$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->votePoll($matches[1]);

} elseif (preg_match('#^/event/(\d+)/update-description$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->updateDescription($matches[1]);

} elseif (preg_match('#^/event/(\d+)/update-schedule$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->updateSchedule($matches[1]);

} elseif (preg_match('#^/event/(\d+)/update-venue$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->updateVenue($matches[1]);

} elseif (preg_match('#^/event/(\d+)/update-faqs$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->updateFaqs($matches[1]);

} elseif (preg_match('#^/event/(\d+)/delete$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->deleteEvent($matches[1]);

// Route for generating plan
} elseif ($path === '/organizer/generate-plan') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->generatePlan();

// Route for sending feedback
} elseif (preg_match('#^/event/(\d+)/send-feedback$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->sendEventFeedback($matches[1]);

} elseif (preg_match('#^/event/(\d+)/delete$#', $path, $matches)) {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->deleteEvent($matches[1]);

} elseif ($path === '/events/checkProfileCompleteness') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->checkProfileCompleteness();

} elseif ($path === '/events/updateOrganizerProfile') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->updateOrganizerProfile();

} elseif ($path === '/qr/validate') {
    require_once __DIR__ . '/app/controllers/EventController.php';
    $controller = new EventController();
    $controller->validateQRCode();

} else {
    http_response_code(404);
    renderView('404');
}
?>
