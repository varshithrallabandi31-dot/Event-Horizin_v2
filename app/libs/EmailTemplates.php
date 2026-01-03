<?php

class EmailTemplates {

    private static $styles = "
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; text-align: center;}
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: left; }
        .header { background: #1a1a1a; padding: 30px; text-align: center; }
        .header h1 { color: #C19A6B; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px; }
        .content { padding: 40px 30px; }
        .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #888; }
        .button { display: inline-block; padding: 12px 24px; background-color: #C19A6B; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
        .label { font-size: 11px; text-transform: uppercase; color: #888; letter-spacing: 1px; font-weight: bold; }
        .highlight { color: #C19A6B; font-weight: bold; }
    ";

    public static function wrap($title, $content, $actionText = '', $actionUrl = '') {
        $year = date('Y');
        $buttonHtml = '';
        if (!empty($actionText) && !empty($actionUrl)) {
            $buttonHtml = "
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='$actionUrl' class='button' style='color: #ffffff;'>$actionText</a>
                </div>
            ";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>$title</title>
            <style>" . self::$styles . "</style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>EventHorizons</h1>
                </div>
                <div class='content'>
                    $content
                    $buttonHtml
                </div>
                <div class='footer'>
                    &copy; $year EventHorizons. All rights reserved.<br>
                    You are receiving this email because you are part of our community.
                </div>
            </div>
        </body>
        </html>
        ";
    }

    public static function referralInvite($senderName, $eventName, $eventDate, $eventLocation, $eventLink) {
        $content = "
            <p class='label'>Exclusive Invitation</p>
            <h2 style='margin-top: 5px; color: #111;'>You've been invited!</h2>
            <p><strong>$senderName</strong> thinks you'd love to join them at an upcoming event on EventHorizons.</p>
            
            <div style='background: #fdfbf7; padding: 20px; border-left: 4px solid #C19A6B; margin: 20px 0;'>
                <h3 style='margin: 0 0 10px 0; color: #111;'>$eventName</h3>
                <p style='margin: 0 0 5px 0;'>📅 $eventDate</p>
                <p style='margin: 0;'>📍 $eventLocation</p>
            </div>
            
            <p>Come join us for a memorable experience. Click the button below to view the event details and request your spot.</p>
        ";
        
        return self::wrap("You're invited: $eventName", $content, "View Invitation", $eventLink);
    }

    public static function formalUpdate($organizerName, $subject, $message, $eventName) {
        // Convert newlines to breaks for the message body
        $formattedMessage = nl2br(htmlspecialchars($message));
        
        $content = "
            <p class='label'>Update from Organizer</p>
            <h2 style='margin-top: 5px; color: #111;'>$subject</h2>
            <p><strong>$organizerName</strong> sent a message regarding <strong>$eventName</strong>:</p>
            
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
            
            <div style='font-size: 16px; color: #444; line-height: 1.8;'>
                $formattedMessage
            </div>
            
            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
        ";
        
        return self::wrap($subject, $content);
    }
    public static function rsvpConfirmation($userName, $eventName, $eventLink, $kitDownloadLink) {
        $content = "
            <p class='label'>Registration Confirmed</p>
            <h2 style='margin-top: 5px; color: #111;'>You're In!</h2>
            <p>Hi <strong>$userName</strong>,</p>
            <p>Your spot for <strong>$eventName</strong> has been confirmed. We can't wait to see you there!</p>
            
            <div style='background: #fdfbf7; padding: 20px; border-left: 4px solid #C19A6B; margin: 20px 0;'>
                <h3 style='margin: 0 0 10px 0; color: #111;'>Your Digital Kit</h3>
                <p style='margin: 0;'>We've attached your exclusive <strong>Digital Event Kit</strong> to this email. It contains your entry pass, guide, and more.</p>
            </div>
            
            <p>You can also download it anytime from the event page.</p>
        ";
        
        return self::wrap("Confirmed: $eventName", $content, "View Event Details", $eventLink);
    }

    public static function eventCancellation($userName, $eventName, $eventDate, $eventLocation) {
        $content = "
            <p class='label'>Event Cancellation Notice</p>
            <h2 style='margin-top: 5px; color: #111;'>We Regret to Inform You</h2>
            <p>Hi <strong>$userName</strong>,</p>
            <p>We sincerely apologize, but the following event has been cancelled by the organizer:</p>
            
            <div style='background: #fff5f5; padding: 20px; border-left: 4px solid #e53e3e; margin: 20px 0;'>
                <h3 style='margin: 0 0 10px 0; color: #111;'>$eventName</h3>
                <p style='margin: 0 0 5px 0;'>📅 $eventDate</p>
                <p style='margin: 0;'>📍 $eventLocation</p>
            </div>
            
            <p><strong>Refund Information:</strong></p>
            <p>If you made any payment for this event, your refund will be processed within <strong>24 hours</strong>. The amount will be credited back to your original payment method.</p>
            
            <p>We understand this may cause inconvenience and we truly apologize for any disruption to your plans.</p>
            
            <p>If you have any questions or concerns, please don't hesitate to reach out to us.</p>
            
            <p style='margin-top: 30px;'>Thank you for your understanding.</p>
        ";
        
        return self::wrap("Event Cancelled: $eventName", $content);
    }

    public static function rsvpRejection($userName, $eventName, $eventLink) {
        $content = "
            <p class='label'>RSVP Update</p>
            <h2 style='margin-top: 5px; color: #111;'>Thank You for Your Interest</h2>
            <p>Hi <strong>$userName</strong>,</p>
            <p>Thank you for your interest in <strong>$eventName</strong>.</p>
            
            <div style='background: #fff5f5; padding: 20px; border-left: 4px solid #e53e3e; margin: 20px 0;'>
                <p style='margin: 0;'>Unfortunately, we are unable to approve your RSVP at this time. This could be due to capacity limits or event-specific requirements.</p>
            </div>
            
            <p>We truly appreciate your interest and hope you'll join us at future events.</p>
            
            <p>Keep an eye on our platform for more exciting events coming soon!</p>
        ";
        
        return self::wrap("RSVP Update: $eventName", $content, "Browse Other Events", $eventLink);
    }
    public static function eventCreated($organizerName, $eventName, $eventDate, $eventLink) {
        $content = "
            <p class='label'>Event Created Successfully</p>
            <h2 style='margin-top: 5px; color: #111;'>Your Event is Live!</h2>
            <p>Hi <strong>$organizerName</strong>,</p>
            <p>Congratulations! Your event <strong>$eventName</strong> has been successfully created and published.</p>
            
            <div style='background: #fdfbf7; padding: 20px; border-left: 4px solid #C19A6B; margin: 20px 0;'>
                <h3 style='margin: 0 0 10px 0; color: #111;'>$eventName</h3>
                <p style='margin: 0;'>📅 $eventDate</p>
            </div>
            
            <p>You can now share your event, manage RSVPs, and track engagement from your dashboard.</p>
        ";
        
        return self::wrap("Event Published: $eventName", $content, "View Event", $eventLink);
    }
}
?>
