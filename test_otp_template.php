<?php
/**
 * Test the new OTP email template
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Testing New OTP Email Template...\n";
echo "==================================\n\n";

if (!Config::isEmailConfigured()) {
    echo "❌ SMTP not configured!\n";
    exit(1);
}

$testEmail = Config::get('SMTP_USER'); // Send to self
$testOTP = "123456"; // Test OTP

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = Config::get('SMTP_HOST', 'smtp.gmail.com');
    $mail->SMTPAuth   = true;
    $mail->Username   = Config::get('SMTP_USER');
    $mail->Password   = Config::get('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = Config::get('SMTP_PORT', 587);
    $mail->Timeout    = 10;

    // Recipients
    $mail->setFrom(Config::get('SMTP_USER'), 'EventHorizons');
    $mail->addAddress($testEmail);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your Login Code - EventHorizons';
    $otp = $testOTP;
    $mail->Body    = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Your Login Code</title>
</head>
<body style='margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif; background-color: #f5f5f5;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f5f5f5; padding: 40px 20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                    
                    <!-- Header -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); padding: 40px; text-align: center;'>
                            <h1 style='margin: 0; color: #C9A961; font-size: 32px; font-weight: 600; letter-spacing: 2px;'>EventHorizons</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style='padding: 50px 40px;'>
                            <p style='margin: 0 0 10px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #999; font-weight: 600;'>LOGIN VERIFICATION</p>
                            
                            <h2 style='margin: 0 0 20px; font-size: 28px; color: #1a1a1a; font-weight: 600;'>Your Login Code</h2>
                            
                            <p style='margin: 0 0 30px; font-size: 16px; color: #333; line-height: 1.6;'>
                                Use the code below to complete your login to EventHorizons:
                            </p>
                            
                            <!-- OTP Box -->
                            <table width='100%' cellpadding='0' cellspacing='0' style='margin: 30px 0;'>
                                <tr>
                                    <td style='background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #C9A961; padding: 30px; border-radius: 8px;'>
                                        <p style='margin: 0 0 10px; font-size: 14px; color: #666; font-weight: 500;'>Your Verification Code</p>
                                        <p style='margin: 0; font-size: 42px; font-weight: 700; color: #C9A961; letter-spacing: 8px; font-family: \"Courier New\", monospace;'>$otp</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style='margin: 25px 0 0; font-size: 14px; color: #666; line-height: 1.6;'>
                                This code will expire in <strong style='color: #333;'>10 minutes</strong>.
                            </p>
                            
                            <p style='margin: 15px 0 0; font-size: 14px; color: #999; line-height: 1.6;'>
                                If you didn't request this code, please ignore this email.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f8f9fa; padding: 30px 40px; text-align: center; border-top: 1px solid #e9ecef;'>
                            <p style='margin: 0 0 10px; font-size: 13px; color: #999;'>
                                © " . date('Y') . " EventHorizons. All rights reserved.
                            </p>
                            <p style='margin: 0; font-size: 12px; color: #aaa;'>
                                This is an automated message, please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
    ";

    echo "Sending test email to: $testEmail\n";
    echo "Test OTP: $testOTP\n\n";
    
    $mail->send();
    
    echo "✅ SUCCESS! Test email sent.\n";
    echo "Check your inbox to see the new professional template!\n";
    
} catch (Exception $e) {
    echo "❌ FAILED: {$mail->ErrorInfo}\n";
}
