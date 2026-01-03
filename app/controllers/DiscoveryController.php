<?php
require_once __DIR__ . '/../models/Event.php';

class DiscoveryController {

    public function index() {
        // "For You" Feed Logic would go here (filtering by interests)
        // For now, just get upcoming events.
        $eventModel = new Event();
        $events = $eventModel->getAll(6); // Get top 6
        
        renderView('home', ['events' => $events]);
    }

    public function explore() {
        // Map Search view
        $eventModel = new Event();
        $events = $eventModel->getAll(20);
        
        renderView('events/explore', ['events' => $events]);
    }
}
?>
