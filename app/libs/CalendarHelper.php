<?php

class CalendarHelper {
    
    public static function generateIcs($event) {
        $startTime = date('Ymd\THis', strtotime($event['start_time']));
        $endTime = $event['end_time'] 
            ? date('Ymd\THis', strtotime($event['end_time'])) 
            : date('Ymd\THis', strtotime($event['start_time'] . ' +2 hours')); // Default duration
            
        $uid = uniqid() . '@event-horizin.com';
        $title = self::escapeString($event['title']);
        $description = self::escapeString($event['description']);
        $location = self::escapeString($event['location_name']);
        
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Event Horizin//NONSGML Event//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:" . $uid . "\r\n";
        $ics .= "DTSTAMP:" . date('Ymd\THis') . "\r\n";
        $ics .= "DTSTART:" . $startTime . "\r\n";
        $ics .= "DTEND:" . $endTime . "\r\n";
        $ics .= "SUMMARY:" . $title . "\r\n";
        $ics .= "DESCRIPTION:" . $description . "\r\n";
        $ics .= "LOCATION:" . $location . "\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";
        
        return $ics;
    }
    
    private static function escapeString($string) {
        $string = preg_replace('/([\,;])/', '\\\$1', $string);
        $string = str_replace("\n", "\\n", $string);
        return $string;
    }
}
