<?php
/**
 * Configuration Manager
 * Loads configuration from .env file and provides fallback defaults
 */
class Config {
    private static $config = [];
    private static $loaded = false;

    /**
     * Load configuration from .env file
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../.env';
        
        // Load .env file if it exists
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if (preg_match('/^(["\'])(.*)\1$/', $value, $matches)) {
                        $value = $matches[2];
                    }
                    
                    self::$config[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }

    /**
     * Get configuration value
     * @param string $key Configuration key
     * @param mixed $default Default value if not found
     * @return mixed Configuration value
     */
    public static function get($key, $default = null) {
        self::load();
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    /**
     * Check if configuration key exists and is not empty
     * @param string $key Configuration key
     * @return bool
     */
    public static function has($key) {
        self::load();
        return isset(self::$config[$key]) && !empty(self::$config[$key]);
    }

    /**
     * Get all configuration
     * @return array
     */
    public static function all() {
        self::load();
        return self::$config;
    }

    /**
     * Validate required configuration
     * @return array Array of missing required keys
     */
    public static function validateRequired() {
        $required = ['DB_NAME'];
        $missing = [];
        
        foreach ($required as $key) {
            if (!self::has($key)) {
                $missing[] = $key;
            }
        }
        
        return $missing;
    }

    /**
     * Check if email is configured
     * @return bool
     */
    public static function isEmailConfigured() {
        return self::has('SMTP_USER') && self::has('SMTP_PASS');
    }
}
?>
