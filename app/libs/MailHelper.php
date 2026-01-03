<?php
require_once __DIR__ . '/../../config/Config.php';

class MailHelper {
    private static $smtpHost;
    private static $smtpPort;
    private static $smtpUser;
    private static $smtpPass;

    private static function init() {
        if (self::$smtpHost === null) {
            self::$smtpHost = Config::get('SMTP_HOST', 'smtp.gmail.com');
            self::$smtpPort = Config::get('SMTP_PORT', 587);
            self::$smtpUser = Config::get('SMTP_USER', '');
            self::$smtpPass = Config::get('SMTP_PASS', '');
        }
    }

    public static function send($to, $subject, $message) {
        return self::sendWithAttachment($to, $subject, $message, '', '');
    }

    public static function sendWithAttachment($to, $subject, $message, $attachmentContent = '', $attachmentName = '') {
        return self::sendWithMultipleAttachments($to, $subject, $message, [
            ['content' => $attachmentContent, 'name' => $attachmentName, 'type' => 'application/pdf']
        ]);
    }

    public static function sendWithMultipleAttachments($to, $subject, $message, $attachments = []) {
        self::init();
        
        // Check if email is configured
        if (empty(self::$smtpUser) || empty(self::$smtpPass)) {
            error_log("Email not configured. Skipping email to: $to");
            error_log("Please configure SMTP_USER and SMTP_PASS in .env file to enable emails.");
            return false; // Fail silently in development
        }
        
        $boundary = md5(time());
        $eol = "\r\n";

        // Headers
        $headers = "MIME-Version: 1.0" . $eol;
        $headers .= "From: EventHorizons <" . self::$smtpUser . ">" . $eol;
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"" . $eol;

        // Body
        $body = "--$boundary" . $eol;
        $body .= "Content-Type: text/html; charset=\"UTF-8\"" . $eol;
        $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
        $body .= $message . $eol . $eol;

        // Attachments
        foreach ($attachments as $attachment) {
            if (!empty($attachment['content']) && !empty($attachment['name'])) {
                $type = $attachment['type'] ?? 'application/octet-stream';
                $body .= "--$boundary" . $eol;
                $body .= "Content-Type: $type; name=\"{$attachment['name']}\"" . $eol;
                $body .= "Content-Transfer-Encoding: base64" . $eol;
                $body .= "Content-Disposition: attachment; filename=\"{$attachment['name']}\"" . $eol . $eol;
                $body .= chunk_split(base64_encode($attachment['content'])) . $eol . $eol;
            }
        }

        $body .= "--$boundary--";

        try {
            self::smtpSend($to, $subject, $headers . $eol . $body);
            return true;
        } catch (Exception $e) {
            // Fallback to simulation/logging if fails
            error_log("Email failed: " . $e->getMessage());
            return false;
        }
    }

    private static function smtpSend($to, $subject, $data) {
        $host = 'ssl://' . self::$smtpHost; 
        $port = 465; // Use SSL port 465 for Gmail wrapper or TLS on 587

        $socket = fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) throw new Exception("Connection failed: $errno $errstr");

        self::readResponse($socket); // banner

        self::sendCommand($socket, "EHLO " . gethostname());
        self::sendCommand($socket, "AUTH LOGIN");
        self::sendCommand($socket, base64_encode(self::$smtpUser));
        self::sendCommand($socket, base64_encode(self::$smtpPass));

        self::sendCommand($socket, "MAIL FROM: <" . self::$smtpUser . ">");
        self::sendCommand($socket, "RCPT TO: <$to>");
        self::sendCommand($socket, "DATA");

        $emailContent = "Subject: $subject\r\n";
        $emailContent .= "To: $to\r\n";
        $emailContent .= $data . "\r\n.";

        self::sendCommand($socket, $emailContent);
        self::sendCommand($socket, "QUIT");

        fclose($socket);
    }

    private static function sendCommand($socket, $command) {
        fwrite($socket, $command . "\r\n");
        return self::readResponse($socket);
    }

    private static function readResponse($socket) {
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        return $response;
    }
}
?>
