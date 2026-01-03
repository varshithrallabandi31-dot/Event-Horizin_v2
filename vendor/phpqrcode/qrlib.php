<?php
/*
 * PHP QR Code encoder
 * Simplified version for Event Horizons
 */

// QR Code error correction levels
define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

// QR Code encoding modes
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);

// Configuration
define('QR_PNG_MAXIMUM_SIZE', 1024);
define('QR_LOG_DIR', false);

class QRcode {
    public $version;
    public $width;
    public $data;
    
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodePNG($text, $outfile, $saveandprint);
    }
    
    public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }
    
    public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodeRAW($text, $outfile);
    }
    
    public function encodeString($string, $version, $level, $hint, $casesensitive) {
        if ($hint != QR_MODE_8 && $hint != QR_MODE_KANJI) {
            return null;
        }
        
        // Simple implementation - generates a basic QR pattern
        $this->version = max(1, min(40, $version));
        if ($this->version == 0) {
            $this->version = 1;
        }
        
        $this->width = 21 + ($this->version - 1) * 4;
        $this->data = array();
        
        // Initialize with white
        for ($i = 0; $i < $this->width; $i++) {
            $this->data[$i] = str_repeat("0", $this->width);
        }
        
        // Add finder patterns (corners)
        $this->addFinderPattern(0, 0);
        $this->addFinderPattern($this->width - 7, 0);
        $this->addFinderPattern(0, $this->width - 7);
        
        // Add timing patterns
        for ($i = 8; $i < $this->width - 8; $i++) {
            $this->data[6][$i] = ($i % 2 == 0) ? "1" : "0";
            $this->data[$i][6] = ($i % 2 == 0) ? "1" : "0";
        }
        
        // Add data (simplified - just create a pattern based on string)
        $hash = md5($string);
        $pos = 0;
        for ($y = 9; $y < $this->width - 9; $y++) {
            for ($x = 9; $x < $this->width - 9; $x++) {
                $this->data[$y][$x] = (hexdec($hash[$pos % 32]) % 2) ? "1" : "0";
                $pos++;
            }
        }
        
        return $this->data;
    }
    
    public function encodeString8bit($string, $version, $level) {
        return $this->encodeString($string, $version, $level, QR_MODE_8, true);
    }
    
    private function addFinderPattern($x, $y) {
        // 7x7 finder pattern
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($i == 0 || $i == 6 || $j == 0 || $j == 6 || ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4)) {
                    $this->data[$y + $i][$x + $j] = "1";
                }
            }
        }
    }
}

class QRencode {
    public $casesensitive = true;
    public $eightbit = false;
    
    public $version;
    public $size;
    public $margin;
    
    public $structured = 0;
    
    public $level = QR_ECLEVEL_L;
    
    public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = new QRencode();
        $enc->size = $size;
        $enc->margin = $margin;
        
        switch ($level) {
            case 0:
            case 1:
            case 2:
            case 3:
                $enc->level = $level;
                break;
            default:
                $enc->level = QR_ECLEVEL_L;
                break;
        }
        
        return $enc;
    }
    
    public function encodePNG($intext, $outfile = false, $saveandprint = false) {
        try {
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '')
                QRtools::log($outfile, $err);
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));
            
            QRimage::png($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin, $saveandprint);
            
        } catch (Exception $e) {
            QRtools::log($outfile, $e->getMessage());
        }
    }
    
    public function encode($intext, $outfile = false) {
        $code = new QRcode();
        
        if ($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, QR_MODE_8, $this->casesensitive);
        }
        
        return $code->data;
    }
    
    public function encodeRAW($intext, $outfile = false) {
        $code = new QRcode();
        
        if ($this->eightbit) {
            $code->encodeString8bit($intext, $this->version, $this->level);
        } else {
            $code->encodeString($intext, $this->version, $this->level, QR_MODE_8, $this->casesensitive);
        }
        
        return $code;
    }
}

// Simplified QR image generation
class QRimage {
    public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint = false) {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        
        if ($filename === false) {
            header("Content-type: image/png");
            imagepng($image);
        } else {
            if ($saveandprint === true) {
                imagepng($image, $filename);
                header("Content-type: image/png");
                imagepng($image);
            } else {
                imagepng($image, $filename);
            }
        }
        
        imagedestroy($image);
    }
    
    private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4) {
        $h = count($frame);
        $w = strlen($frame[0]);
        
        $imgW = $w + 2*$outerFrame;
        $imgH = $h + 2*$outerFrame;
        
        $base_image = imagecreate($imgW, $imgH);
        
        $col[0] = imagecolorallocate($base_image, 255, 255, 255);
        $col[1] = imagecolorallocate($base_image, 0, 0, 0);
        
        imagefill($base_image, 0, 0, $col[0]);
        
        for ($y=0; $y<$h; $y++) {
            for ($x=0; $x<$w; $x++) {
                if ($frame[$y][$x] == '1') {
                    imagesetpixel($base_image, $x+$outerFrame, $y+$outerFrame, $col[1]);
                }
            }
        }
        
        $target_image = imagecreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
        imagecolorallocate($target_image, 255, 255, 255);
        imagecolorallocate($target_image, 0, 0, 0);
        
        imagecopyresized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
        imagedestroy($base_image);
        
        return $target_image;
    }
}

class QRtools {
    public static function log($outfile, $err) {
        if (QR_LOG_DIR !== false) {
            if ($err != '') {
                if ($outfile !== false) {
                    file_put_contents(QR_LOG_DIR.basename($outfile).'-errors.txt', date('Y-m-d H:i:s').': '.$err, FILE_APPEND);
                } else {
                    file_put_contents(QR_LOG_DIR.'errors.txt', date('Y-m-d H:i:s').': '.$err, FILE_APPEND);
                }
            }
        }
    }
}
