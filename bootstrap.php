<?php
/**
 * Bootstrap file - Initialize application
 * Auto-creates directories and validates configuration
 */

// Load Composer Autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load configuration
require_once __DIR__ . '/config/Config.php';

/**
 * Auto-create required directories
 */
function createRequiredDirectories() {
    $directories = [
        __DIR__ . '/public/uploads/events',
        __DIR__ . '/public/uploads/temp',
        __DIR__ . '/public/uploads/avatars',
        __DIR__ . '/public/uploads/memories',
        __DIR__ . '/public/qrcodes',
        __DIR__ . '/logs'
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}

/**
 * Check if .env file exists
 */
function checkEnvFile() {
    $envFile = __DIR__ . '/.env';
    $envExample = __DIR__ . '/.env.example';
    
    if (!file_exists($envFile)) {
        // Show setup instructions
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Setup Required - Event Horizin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .step { background: #f9f9f9; padding: 15px; margin: 15px 0; border-left: 4px solid #4CAF50; }
        .step h3 { margin-top: 0; color: #4CAF50; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .warning { background: #fff3cd; border-left-color: #ffc107; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; }
        .success { background: #d4edda; border-left-color: #28a745; padding: 15px; margin: 15px 0; border-left: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Welcome to Event Horizin!</h1>
        <p>Before you can use the application, you need to complete a quick one-time setup.</p>
        
        <div class="warning">
            <strong>⚠️ Configuration File Missing</strong><br>
            The <code>.env</code> file is missing. This file contains your database and email credentials.
        </div>
        
        <div class="step">
            <h3>Step 1: Create .env File</h3>
            <p>Copy the example configuration file:</p>
            <p><strong>Windows:</strong></p>
            <code>copy .env.example .env</code>
            <p><strong>Mac/Linux:</strong></p>
            <code>cp .env.example .env</code>
        </div>
        
        <div class="step">
            <h3>Step 2: Configure Database</h3>
            <p>Open <code>.env</code> file and set your MySQL credentials:</p>
            <pre style="background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto;">
DB_HOST=localhost
DB_NAME=event_platform
DB_USER=root
DB_PASS=<span style="color: #f92672;">your_mysql_password_here</span></pre>
            <p><strong>Most XAMPP users:</strong> Leave <code>DB_PASS</code> empty (no password)</p>
        </div>
        
        <div class="step">
            <h3>Step 3: Import Database</h3>
            <ol>
                <li>Open phpMyAdmin: <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
                <li>Create a new database named: <code>event_platform</code></li>
                <li>Import the <code>event_platform.sql</code> file</li>
            </ol>
        </div>
        
        <div class="step">
            <h3>Step 4: Configure Email (Optional)</h3>
            <p>To enable email notifications, add your Gmail credentials:</p>
            <pre style="background: #2d2d2d; color: #f8f8f2; padding: 15px; border-radius: 4px; overflow-x: auto;">
SMTP_USER=<span style="color: #f92672;">your_email@gmail.com</span>
SMTP_PASS=<span style="color: #f92672;">your_16_char_app_password</span></pre>
            <p><small>Get App Password: <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></small></p>
            <p><strong>Note:</strong> You can skip this for now. The app will work without emails.</p>
        </div>
        
        <div class="success">
            <strong>✅ That\'s it!</strong><br>
            After completing these steps, refresh this page and your application will be ready to use.
        </div>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Need help? Check the <code>SETUP_GUIDE.md</code> file for detailed instructions.
        </p>
    </div>
</body>
</html>';
        exit;
    }
}

// Run initialization
createRequiredDirectories();
checkEnvFile();

// Configuration is loaded automatically by Config class
?>
