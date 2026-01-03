<?php
/**
 * Email Connection Test Script
 * Run this to diagnose SMTP connection issues
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "=== Email Configuration Test ===\n\n";

// Check if email is configured
echo "1. Checking Configuration...\n";
$isConfigured = Config::isEmailConfigured();
echo "   SMTP Configured: " . ($isConfigured ? "YES ✓" : "NO ✗") . "\n";

if ($isConfigured) {
    echo "   SMTP Host: " . Config::get('SMTP_HOST', 'smtp.gmail.com') . "\n";
    echo "   SMTP Port: " . Config::get('SMTP_PORT', 587) . "\n";
    echo "   SMTP User: " . Config::get('SMTP_USER') . "\n";
    echo "   SMTP Pass: " . (Config::get('SMTP_PASS') ? "****** (configured)" : "NOT SET") . "\n\n";
} else {
    echo "\n❌ ERROR: SMTP credentials not configured in .env file!\n";
    echo "Please add:\n";
    echo "SMTP_USER=your_email@gmail.com\n";
    echo "SMTP_PASS=your_app_password\n\n";
    exit(1);
}

// Test connection
echo "2. Testing SMTP Connection...\n";
$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "   DEBUG: $str\n";
    };

    // Server settings
    $mail->isSMTP();
    $mail->Host       = Config::get('SMTP_HOST', 'smtp.gmail.com');
    $mail->SMTPAuth   = true;
    $mail->Username   = Config::get('SMTP_USER');
    $mail->Password   = Config::get('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = Config::get('SMTP_PORT', 587);
    
    // Set timeout
    $mail->Timeout = 10;

    // Recipients
    $mail->setFrom(Config::get('SMTP_USER'), 'Event Horizons');
    $mail->addAddress(Config::get('SMTP_USER')); // Send to self for testing

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    $mail->Body    = '<h1>Test Successful!</h1><p>Your SMTP connection is working correctly.</p>';
    $mail->AltBody = 'Test Successful! Your SMTP connection is working correctly.';

    echo "\n3. Sending Test Email...\n";
    $mail->send();
    
    echo "\n✅ SUCCESS! Email sent successfully.\n";
    echo "Check your inbox at: " . Config::get('SMTP_USER') . "\n";
    
} catch (Exception $e) {
    echo "\n❌ FAILED! Email could not be sent.\n";
    echo "Error: {$mail->ErrorInfo}\n\n";
    
    // Common issues
    echo "Common Solutions:\n";
    echo "1. Enable 2-Step Verification on your Google Account\n";
    echo "2. Generate an App Password: https://myaccount.google.com/apppasswords\n";
    echo "3. Use the 16-character App Password (no spaces) in .env\n";
    echo "4. Check if 'Less Secure Apps' is enabled (deprecated by Google)\n";
    echo "5. Verify your Gmail username and password are correct\n";
    echo "6. Check firewall/antivirus blocking port 587\n";
}
