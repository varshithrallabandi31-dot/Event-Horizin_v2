<?php
require_once __DIR__ . '/../models/User.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class AuthController {

    public function showLogin() {
        renderView('auth/login');
    }

    public function handleLogin() {
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $isEmail = !empty($email);
        $identity = $isEmail ? $email : $phone;

        // Basic validation
        if (empty($identity)) {
            renderView('auth/login', ['error' => 'Phone number or Email is required']);
            return;
        }

        // Generate Mock OTP
        $otp = rand(100000, 999999);
        
        // Store in Session
        $_SESSION['login_otp'] = $otp;
        if ($isEmail) {
            $_SESSION['login_email'] = $email;
            unset($_SESSION['login_phone']); // clear previous
            
            // Send Email OTP
            $emailSent = $this->sendEmailOTP($email, $otp);
            
            // Check if email sending failed
            if (!$emailSent) {
                $errorMsg = $_SESSION['email_error'] ?? 'Failed to send verification email. Please try again.';
                unset($_SESSION['email_error']);
                renderView('auth/login', ['error' => $errorMsg]);
                return;
            }
            
        } else {
            $_SESSION['login_phone'] = $phone;
            unset($_SESSION['login_email']); // clear previous
            
            // "Send" SMS (Simulated)
        }
        
        header('Location: ' . BASE_URL . 'verify-otp');
        exit;
    }

    private function sendEmailOTP($email, $otp) {
        if (!Config::isEmailConfigured()) {
            // Store error in session for user feedback
            $_SESSION['email_error'] = 'Email service not configured. Please contact administrator.';
            error_log("SMTP Not Configured. OTP for $email is $otp");
            return false;
        }

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
            $mail->Timeout    = 10; // 10 second timeout

            // Recipients
            $mail->setFrom(Config::get('SMTP_USER'), 'EventHorizons');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Login Code - EventHorizons';
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
            $mail->AltBody = "Your EventHorizons Login Verification Code: $otp\n\nThis code will expire in 10 minutes.\n\nIf you didn't request this code, please ignore this email.\n\n© " . date('Y') . " EventHorizons. All rights reserved.";

            $mail->send();
            error_log("Email OTP sent successfully to: $email");
            return true;
            
        } catch (Exception $e) {
            // Log detailed error
            $errorMsg = "Email sending failed to $email. Error: {$mail->ErrorInfo}";
            error_log($errorMsg);
            
            // Store user-friendly error in session
            $_SESSION['email_error'] = 'Failed to send verification email. Please check your email address or try again later.';
            return false;
        }
    }

    public function showVerify() {
        if (!isset($_SESSION['login_phone']) && !isset($_SESSION['login_email'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
        
        // PASS OTP TO VIEW FOR DEMO/TESTING PURPOSES
        $demo_otp = $_SESSION['login_otp'] ?? '';
        $identity = $_SESSION['login_email'] ?? $_SESSION['login_phone'];
        $isEmail = isset($_SESSION['login_email']);
        
        renderView('auth/verify', [
            'phone' => $identity, 
            'is_email' => $isEmail,
            'demo_otp' => $demo_otp 
        ]);
    }

    public function handleVerify() {
        $input_otp = $_POST['otp'] ?? '';
        $input_otp = trim($input_otp);
        $stored_otp = isset($_SESSION['login_otp']) ? trim((string)$_SESSION['login_otp']) : '';

        $identity = $_SESSION['login_email'] ?? $_SESSION['login_phone'] ?? '';
        $isEmail = isset($_SESSION['login_email']);

        if ($input_otp !== $stored_otp) {
            renderView('auth/verify', [
                'phone' => $identity, 
                'is_email' => $isEmail,
                'error' => 'Invalid OTP code. Please try again.',
                'demo_otp' => $_SESSION['login_otp'] ?? 'expired'
            ]);
            return;
        }

        // OTP Verified
        $userModel = new User();
        $user = false;

        if ($isEmail) {
            $user = $userModel->findByEmail($identity);
            if (!$user) {
                // Create user with Email
                if ($userModel->createWithEmail($identity)) {
                    $user = $userModel->findByEmail($identity);
                }
            }
        } else {
            $user = $userModel->findByPhone($identity);
            if (!$user) {
                // Create user with Phone
                if ($userModel->createWithPhone($identity)) {
                    $user = $userModel->findByPhone($identity);
                }
            }
        }
        
        if (!$user) {
             die("Error creating user/logging in");
        }

        // Login User
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_phone'] = $user['phone']; 
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];

        // Clear OTP
        unset($_SESSION['login_otp']);
        unset($_SESSION['login_phone']);
        unset($_SESSION['login_email']);

        header('Location: ' . BASE_URL);
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;
    }
}
?>
