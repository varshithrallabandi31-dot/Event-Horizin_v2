<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Config.php';

echo "Checking SMTP Configuration...\n";
echo "================================\n";

if (Config::isEmailConfigured()) {
    echo "✅ SMTP IS CONFIGURED!\n\n";
    echo "SMTP Host: " . Config::get('SMTP_HOST') . "\n";
    echo "SMTP Port: " . Config::get('SMTP_PORT') . "\n";
    echo "SMTP User: " . Config::get('SMTP_USER') . "\n";
    echo "SMTP Pass: " . (Config::get('SMTP_PASS') ? "****** (SET)" : "NOT SET") . "\n";
} else {
    echo "❌ SMTP NOT CONFIGURED\n";
    echo "Please check your .env file\n";
}
