<?php
require_once __DIR__ . '/../../vendor/phpqrcode/qrlib.php';

class QRHelper {
    /**
     * Generate a cryptographically secure random token
     * @return string 32-character hexadecimal token
     */
    public static function createToken() {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Generate QR code PNG image from data
     * @param string $data Data to encode in QR code
     * @param string $outputPath Path where to save the PNG file
     * @param int $size Size of QR code (1-10, default 4)
     * @param int $margin Margin around QR code (default 2)
     * @return bool Success status
     */
    public static function generate($data, $outputPath, $size = 4, $margin = 2) {
        try {
            // Ensure directory exists
            $dir = dirname($outputPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            
            // Use QRServer API - most reliable for scannable QR codes
            $pixelSize = $size * 50; // Convert to actual pixel size
            $qrSize = max(200, min(1000, $pixelSize)); // Between 200-1000px
            
            // QRServer.com API - free and reliable
            $url = 'https://api.qrserver.com/v1/create-qr-code/?' . http_build_query([
                'size' => $qrSize . 'x' . $qrSize,
                'data' => $data,
                'format' => 'png',
                'margin' => $margin,
                'qzone' => $margin,
                'ecc' => 'L' // Error correction level
            ]);
            
            error_log("QRHelper: Generating QR code via QRServer API: $url");
            
            // Use cURL for better error handling
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode == 200 && $imageData && strlen($imageData) > 100) {
                if (file_put_contents($outputPath, $imageData)) {
                    error_log("QRHelper: QR code generated successfully, size: " . strlen($imageData) . " bytes");
                    return true;
                }
            } else {
                error_log("QRHelper: QRServer API failed - HTTP $httpCode, Error: $error");
            }
            
            return false;
        } catch (Exception $e) {
            error_log("QR Code generation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate QR code and return as base64 encoded string for embedding
     * @param string $data Data to encode in QR code
     * @param int $size Size of QR code (1-10, default 4)
     * @param int $margin Margin around QR code (default 2)
     * @return string|false Base64 encoded PNG or false on failure
     */
    public static function generateBase64($data, $size = 4, $margin = 2) {
        try {
            // Generate to temporary file
            $tempFile = sys_get_temp_dir() . '/qr_' . uniqid() . '.png';
            
            QRcode::png($data, $tempFile, QR_ECLEVEL_L, $size, $margin);
            
            if (file_exists($tempFile)) {
                $imageData = file_get_contents($tempFile);
                unlink($tempFile); // Clean up
                return base64_encode($imageData);
            }
            
            return false;
        } catch (Exception $e) {
            error_log("QR Code generation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create QR data payload for RSVP
     * @param int $rsvpId RSVP ID
     * @param int $eventId Event ID
     * @param string $token Unique token
     * @return string JSON encoded data
     */
    public static function createRSVPPayload($rsvpId, $eventId, $token) {
        return json_encode([
            'rsvp_id' => $rsvpId,
            'event_id' => $eventId,
            'token' => $token,
            'type' => 'event_entry'
        ]);
    }
    
    /**
     * Parse QR code payload
     * @param string $qrData QR code data string
     * @return array|false Parsed data or false on failure
     */
    public static function parsePayload($qrData) {
        $data = json_decode($qrData, true);
        
        if (!$data || !isset($data['rsvp_id']) || !isset($data['token']) || !isset($data['event_id'])) {
            return false;
        }
        
        return $data;
    }
}
