<?php
require_once __DIR__ . '/fpdf.php';

if (!class_exists('PDFWithAlpha')) {
    class PDFWithAlpha extends FPDF {
        protected $extgstates = array();

        function SetAlpha($alpha, $bm='Normal') {
            $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
            $this->SetExtGState($gs);
        }

        function AddExtGState($parms) {
            $n = count($this->extgstates)+1;
            $this->extgstates[$n]['parms'] = $parms;
            return $n;
        }

        function SetExtGState($gs) {
            $this->_out(sprintf('/GS%d gs', $gs));
        }

        function _enddoc() {
            if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
                $this->PDFVersion='1.4';
            parent::_enddoc();
        }

        function _putextgstates() {
            for ($i = 1; $i <= count($this->extgstates); $i++) {
                $this->_newobj();
                $this->extgstates[$i]['n'] = $this->n;
                $this->_put('<</Type /ExtGState');
                foreach ($this->extgstates[$i]['parms'] as $k=>$v)
                    $this->_put('/'.$k.' '.$v);
                $this->_put('>>');
                $this->_put('_endobj');
            }
        }

        function _putresourcedict() {
            parent::_putresourcedict();
            $this->_put('/ExtGState <<');
            foreach($this->extgstates as $k=>$v)
                $this->_put('/GS'.$k.' '.$v['n'].' 0 R');
            $this->_put('>>');
        }

        function _putresources() {
            $this->_putextgstates();
            parent::_putresources();
        }

        // Graphics Primitives
        function MoveTo($x, $y) {
            $this->_out(sprintf('%.2F %.2F m', $x*$this->k, ($this->h-$y)*$this->k));
        }

        function Curve($x1, $y1, $x2, $y2, $x3, $y3) {
            $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', 
                $x1*$this->k, ($this->h-$y1)*$this->k,
                $x2*$this->k, ($this->h-$y2)*$this->k,
                $x3*$this->k, ($this->h-$y3)*$this->k));
        }

        function FillStroke() {
            $this->_out('B');
        }
    }
}

class KitHelper {
    public static function generate($event, $user, $rsvpId = null, $qrToken = null) {
        // Suppress warnings that might corrupt PDF output
        $previous_error_reporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

        $pdf = new PDFWithAlpha();
        
        // Helper to sanitize text for FPDF (Standard fonts only support ISO-8859-1)
        $t = function($text) {
            return iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        };
        
        // Define simple invitation border function
        $addBorder = function($pdf) {
            // Classic Simple Invitation Border (Vector)
            $pdf->SetLineWidth(0.8);
            $pdf->SetDrawColor(212, 175, 55); // Gold color
            $pdf->Rect(5, 5, 200, 287); // Outer Gold Border
            
            $pdf->SetLineWidth(0.2);
            $pdf->SetDrawColor(0, 0, 0); // Black
            $pdf->Rect(7, 7, 196, 283); // Inner Black Border
            
            // Corner Ornaments (Simple Lines)
            $pdf->SetLineWidth(0.5);
            $pdf->Line(5, 5, 15, 15); // Top Left
            $pdf->Line(205, 5, 195, 15); // Top Right
            $pdf->Line(5, 292, 15, 282); // Bottom Left
            $pdf->Line(205, 292, 195, 282); // Bottom Right
        };

        // PAGE 1: WELCOME MESSAGE
        $pdf->AddPage();
        // Background Image if available
        if (!empty($event['image_url'])) {
            try {
                $pdf->Image($event['image_url'], 0, 0, 210, 297, (strpos($event['image_url'], 'unsplash') !== false ? 'JPG' : ''));
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetAlpha(0.7); 
                $pdf->Rect(0, 0, 210, 297, 'F'); 
                $pdf->SetAlpha(1.0);
            } catch (Exception $e) {}
        } else {
             $pdf->SetFillColor(30, 30, 30);
             $pdf->Rect(0, 0, 210, 297, 'F');
        }

        $pdf->SetY(80);
        $pdf->SetFont('Arial', 'B', 30);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 20, 'WELCOME', 0, 1, 'C');
        
        $pdf->SetY(110);
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, 10, 'TO THE EXPERIENCE', 0, 1, 'C');

        $pdf->SetY(140);
        $pdf->SetFont('Arial', 'B', 40);
        $pdf->MultiCell(0, 20, strtoupper($t($event['title'])), 0, 'C');

        $pdf->SetY(200);
        $pdf->SetFont('Arial', '', 16);
        $pdf->Cell(0, 10, 'PREPARED EXCLUSIVELY FOR', 0, 1, 'C');
        
        $pdf->SetY(215);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 15, strtoupper($t($user['name'] ?? 'VALUED GUEST')), 0, 1, 'C');


        // PAGE 2: ENTRY & QR
        $pdf->AddPage();
        $addBorder($pdf);
        $theme = ['color' => [30, 30, 30]]; // Default dark theme
        self::addHeader($pdf, $theme['color'], 'AUTHENTICATION & ENTRY');
        $pdf->SetY(50);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->Cell(0, 15, 'OFFICIAL ENTRY PASS', 0, 1, 'C');
        
        $pdf->SetY(70);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'EVENT DETAILS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, $t($event['title']), 0, 1, 'C');
        $pdf->Cell(0, 8, $t(date('F j, Y • g:i A', strtotime($event['start_time']))), 0, 1, 'C');
        $pdf->Cell(0, 8, $t($event['location_name']), 0, 1, 'C');

        $pdf->SetY(110);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'GUEST DETAILS', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Name: ' . $t($user['name'] ?? 'N/A'), 0, 1, 'C');
        $pdf->Cell(0, 8, 'Ticket ID: EH-' . strtoupper(substr(md5($user['id'].$event['id']), 0, 8)), 0, 1, 'C');

        // QR Code Generation (Real QR Code)
        $pdf->SetY(160);
        
        if ($rsvpId && $qrToken) {
            // Generate actual QR code
            require_once __DIR__ . '/QRHelper.php';
            
            $qrData = QRHelper::createRSVPPayload($rsvpId, $event['id'], $qrToken);
            $qrImagePath = sys_get_temp_dir() . '/qr_' . $rsvpId . '_' . time() . '.png';
            
            if (QRHelper::generate($qrData, $qrImagePath, 6, 1)) {
                // Embed QR code image in PDF
                $qrX = 80;
                $qrY = 160;
                $qrSize = 50;
                
                try {
                    $pdf->Image($qrImagePath, $qrX, $qrY, $qrSize, $qrSize, 'PNG');
                    // Clean up temp file
                    @unlink($qrImagePath);
                } catch (Exception $e) {
                    // Fallback to placeholder if image embedding fails
                    error_log("QR Code embedding failed: " . $e->getMessage());
                }
            }
        } else {
            // Fallback: Placeholder QR pattern if no token provided
            $pdf->SetFillColor(0, 0, 0); // Black for QR
            $startX = 80;
            $startY = 160;
            $size = 50;
            $pdf->Rect($startX, $startY, $size, $size); // Main box
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect($startX + 2, $startY + 2, $size - 4, $size - 4, 'F'); // White inner
            $pdf->SetFillColor(0, 0, 0);
            // Random "QR" pattern
            srand($user['id'] + $event['id']);
            for($i=0; $i<8; $i++) {
                for($j=0; $j<8; $j++) {
                    if(rand(0,1)) $pdf->Rect($startX + 5 + ($i*5), $startY + 5 + ($j*5), 5, 5, 'F');
                }
            }
        }
        
        $pdf->SetY(220);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, 'SCAN FOR ENTRY', 0, 1, 'C');


        // Helper for Dashed Rect (Simulated with small lines if SetDash not available, or just solid for stability)
        $drawDashedRect = function($pdf, $x, $y, $w, $h) {
            $pdf->SetLineWidth(0.5);
            $pdf->SetDrawColor(100, 100, 100);
            // Top & Bottom
            for($i=$x; $i<$x+$w; $i+=4) $pdf->Line($i, $y, $i+2, $y);
            for($i=$x; $i<$x+$w; $i+=4) $pdf->Line($i, $y+$h, $i+2, $y+$h);
            // Left & Right
            for($j=$y; $j<$y+$h; $j+=4) $pdf->Line($x, $j, $x, $j+2);
            for($j=$y; $j<$y+$h; $j+=4) $pdf->Line($x+$w, $j, $x+$w, $j+2);
        };

        // PAGES 3-11: THE 9 RITUAL KIT ITEMS
        $kitItems = [
            [
                'title' => 'Step-by-Step Ritual Guide',
                'type' => 'text',
                'description' => "A comprehensive walkthrough to guide you through every moment of the experience.",
                'content' => "1. ARRIVAL & GROUNDING\nFind a comfortable space where you won't be disturbed. Take a few moments to settle in, silence your phone, and disconnect from the outside world. This is your time.\n\n2. OPENING THE CIRCLE\nTake three deep, cleansing breaths. Inhale positivity, exhale tension. Visualize a protective circle of light around you, creating a safe container for your experience.\n\n3. ACTIVE ENGAGEMENT\nParticipate fully in every activity. Whether it's listening, sharing, or reflecting, give your whole heart to the process. The more you put in, the more you will receive.\n\n4. DEEP REFLECTION\nUse the provided journaling prompts to explore your inner landscape. Be honest with yourself. There is no right or wrong here, only your truth.\n\n5. CLOSING & INTEGRATION\nAs we conclude, take a moment to express gratitude for yourself and this community. Carry the insights and energy you've cultivated here into your daily life."
            ],
            [
                'title' => 'Letting Go Sheet',
                'type' => 'text',
                'description' => "A safe space to release old habits, limiting beliefs, and heavy burdens.",
                'content' => "INSTRUCTIONS: Write down everything that feels heavy or no longer serves your highest good. This is an act of release.\n\nWhat burdens are you carrying that you wish to set down?\n\n_________________________________________________________\n\n_________________________________________________________\n\n_________________________________________________________\n\n_________________________________________________________\n\n\nAFFIRMATION OF RELEASE:\n'I release these things with love and gratitude for the lessons they taught me. I am now free to move forward lighter and brighter.'"
            ],
            [
                'title' => 'Year in Review Memory Map',
                'type' => 'circle_chart', // NEW VISUAL TYPE
                'description' => "Visualize your journey through the seasons of the last 12 months.",
                'content' => "Map out your key moments."
            ],
            [
                'title' => 'Resolution Cards',
                'type' => 'cards_grid', // NEW VISUAL TYPE
                'description' => "Set meaningful, crystal-clear goals with actionable steps for the future.",
                'content' => "Cut these out and place them where you see them daily."
            ],
            [
                'title' => 'Legacy Certificate',
                'type' => 'certificate', // NEW SPECIAL STYLE
                'description' => "A formal commitment to your intentions and your future self.",
                'content' => ""
            ],
            [
                'title' => 'Affirmation Cards',
                'type' => 'text',
                'description' => "Daily reminders of your inherent power, worth, and purpose.",
                'content' => "\nI AM WORTHY OF ALL THE GOOD THAT FLOWS TO ME.\n\n- • -\n\nI TRUST THE TIMING OF MY LIFE AND MY UNIQUE PATH.\n\n- • -\n\nI HAVE THE POWER TO CREATE POSITIVE CHANGE.\n\n- • -\n\nI AM SURROUNDED BY LOVE AND SUPPORT."
            ],
            [
                'title' => 'Preparation Checklist',
                'type' => 'text',
                'description' => "Everything you need to be physically and mentally ready.",
                'content' => "PHYSICAL PREPARATION:\n[ ] Comfortable clothing that allows for easy movement\n[ ] A water bottle for hydration\n[ ] A journal and a favorite pen\n[ ] Healthy snacks for energy\n\nMENTAL & SPACE PREPARATION:\n[ ] A quiet, private space (if joining virtually)\n[ ] Good internet connection and charged device\n[ ] An open heart and a curious mind\n[ ] Willingness to be vulnerable and authentic"
            ],
            [
                'title' => 'Photo Prompt',
                'type' => 'text',
                'description' => "Capture the magic of the moment to look back on later.",
                'content' => "Photography is a way of feeling, of touching, of loving.\n\nTHEME: 'NEW BEGINNINGS'\n\nCHALLENGE:\nTake a photo that represents hope to you. It could be the sunrise, a blooming flower, a door opening, or a genuine smile.\n\nShare your creation with the community using:\n#EventHorizons #NewBeginnings #MyJourney"
            ],
            [
                'title' => 'Invitation Template',
                'type' => 'text',
                'description' => "Share the experience. Invite your loved ones to join the circle.",
                'content' => "YOU ARE CORDIALLY INVITED\n\nTo join: " . strtoupper($t($user['name'] ?? 'Me')) . "\n\nFor an evening of connection, celebration, and renewal.\n\n\nEvent: " . $t($event['title']) . "\nDate: " . date('F j, Y', strtotime($event['start_time'])) . "\nLocation: " . $t($event['location_name']) . "\n\n'Happiness is only real when shared.'"
            ]
        ];

        foreach ($kitItems as $item) {
            $pdf->AddPage();
            $type = $item['type'] ?? 'text';
            
            // ==========================================
            // CASE 1: CERTIFICATE (Custom Full Page)
            // ==========================================
            // ==========================================
            // CASE 1: CERTIFICATE (Custom Vector Design)
            // ==========================================
            if ($type === 'certificate') {
                 // 1. Premium Border (Vector Only - No Images)
                 $pdf->SetFillColor(255, 255, 255);
                 $pdf->Rect(0, 0, 210, 297, 'F'); // White Background
                 
                 // Double Border Gold
                 $pdf->SetDrawColor(218, 165, 32); // Gold
                 $pdf->SetLineWidth(1.5);
                 $pdf->Rect(10, 10, 190, 277);
                 
                 $pdf->SetLineWidth(0.5);
                 $pdf->Rect(13, 13, 184, 271);
                 
                 // Corner Ornaments (Vector)
                 $pdf->SetLineWidth(1);
                 $s = 15; // Size of corner
                 $m = 10; // Margin
                 
                 // Top Left
                 $pdf->Line($m, $m+$s, $m, $m); $pdf->Line($m, $m, $m+$s, $m);
                 $pdf->Line($m+3, $m+3+$s, $m+3, $m+3); $pdf->Line($m+3, $m+3, $m+3+$s, $m+3);
                 
                 // Top Right
                 $pdf->Line(210-$m-$s, $m, 210-$m, $m); $pdf->Line(210-$m, $m, 210-$m, $m+$s);
                 $pdf->Line(210-$m-3-$s, $m+3, 210-$m-3, $m+3); $pdf->Line(210-$m-3, $m+3, 210-$m-3, $m+3+$s);
                 
                 // Bottom Left
                 $pdf->Line($m, 297-$m-$s, $m, 297-$m); $pdf->Line($m, 297-$m, $m+$s, 297-$m);
                 $pdf->Line($m+3, 297-$m-3-$s, $m+3, 297-$m-3); $pdf->Line($m+3, 297-$m-3, $m+3+$s, 297-$m-3);
                 
                 // Bottom Right
                 $pdf->Line(210-$m-$s, 297-$m, 210-$m, 297-$m); $pdf->Line(210-$m, 297-$m, 210-$m, 297-$m-$s);
                 $pdf->Line(210-$m-3-$s, 297-$m-3, 210-$m-3, 297-$m-3); $pdf->Line(210-$m-3, 297-$m-3, 210-$m-3, 297-$m-3-$s);

                 // 2. Text Content
                 $pdf->SetTextColor(0, 0, 0);
                 
                 $pdf->SetY(60); 
                 $pdf->SetFont('Arial', 'B', 32);
                 $pdf->Cell(0, 15, 'OFFICIAL CERTIFICATE', 0, 1, 'C');
                 
                 $pdf->SetY(75);
                 $pdf->SetFont('Arial', '', 14);
                 $pdf->SetTextColor(100, 100, 100); // Gray
                 $pdf->Cell(0, 10, 'OF COMMITMENT', 0, 1, 'C');
                 
                 $pdf->SetY(120);
                 $pdf->SetFont('Arial', 'I', 14);
                 $pdf->SetTextColor(0, 0, 0);
                 $pdf->Cell(0, 10, 'This document certifies that', 0, 1, 'C');
                 
                 $pdf->SetY(145);
                 $pdf->SetFont('Arial', 'B', 36); 
                 $pdf->SetTextColor(218, 165, 32); // Gold
                 $nameStr = strtoupper($t($user['name'] ?? 'VALUED GUEST'));
                 $pdf->Cell(0, 15, $nameStr, 0, 1, 'C');
                 
                 // Dynamic Underline
                 $nw = $pdf->GetStringWidth($nameStr);
                 $cx = 105;
                 $pdf->SetLineWidth(0.7);
                 $pdf->Line($cx - ($nw/2) - 10, 162, $cx + ($nw/2) + 10, 162); // Underline Name
                 
                 $pdf->SetY(180);
                 $pdf->SetFont('Arial', '', 16);
                 $pdf->SetTextColor(50, 50, 50);
                 $pdf->MultiCell(150, 9, "Has successfully committed to living with purpose, intention, and courage for the year ahead.", 0, 'C');
                 
                 // 3. Signature Block
                 $pdf->SetY(230); 
                 $pdf->SetDrawColor(0, 0, 0); 
                 $pdf->SetLineWidth(0.5);
                 
                 $pdf->Line(40, 240, 90, 240); // Sign Line
                 $pdf->Line(120, 240, 170, 240); // Date Line
                 
                 // Labels
                 $pdf->SetFont('Arial', '', 10);
                 $pdf->SetTextColor(100, 100, 100); 
                 $pdf->SetXY(40, 242);
                 $pdf->Cell(50, 5, "Signature", 0, 0, 'C');
                 $pdf->SetXY(120, 242);
                 $pdf->Cell(50, 5, "Date: " . date('F j, Y'), 0, 0, 'C');

            } else {
                // ==========================================
                // CASE 2: STANDARD PAGES
                // ==========================================
                $addBorder($pdf);
                $pdf->SetTextColor(50, 50, 50);

                // Standard Title & Description
                $pdf->SetY(60);
                $pdf->SetFont('Arial', 'B', 20);
                $pdf->Cell(0, 10, strtoupper($t($item['title'])), 0, 1, 'C');

                $pdf->SetY(75);
                $pdf->SetFont('Arial', 'I', 12);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->SetXY(20, $pdf->GetY());
                $pdf->MultiCell(170, 6, $t($item['description']), 0, 'C');

                // Specific Item Content
                $pdf->SetTextColor(50, 50, 50);
                
                if ($type === 'circle_chart') {
                    // Circle Chart Drawing
                    $cx = 105; $cy = 160; $r = 60;
                    $pdf->SetDrawColor(100, 100, 100);
                    $pdf->SetLineWidth(0.5);
                    
                    // Circle approximation
                    $k = 0.552284749831;
                    $c = $k * $r;
                    $pdf->MoveTo($cx + $r, $cy);
                    $pdf->Curve($cx + $r, $cy + $c, $cx + $c, $cy + $r, $cx, $cy + $r);
                    $pdf->Curve($cx - $c, $cy + $r, $cx - $r, $cy + $c, $cx - $r, $cy);
                    $pdf->Curve($cx - $r, $cy - $c, $cx - $c, $cy - $r, $cx, $cy - $r);
                    $pdf->Curve($cx + $c, $cy - $r, $cx + $r, $cy - $c, $cx + $r, $cy);
                    
                    // Cross Lines
                    $pdf->Line($cx, $cy - $r, $cx, $cy + $r);
                    $pdf->Line($cx - $r, $cy, $cx + $r, $cy);
                    
                    // Labels
                    $pdf->SetFont('Arial', 'B', 10);
                    $pdf->Text($cx - 45, $cy - 25, "WINTER (Jan-Mar)");
                    $pdf->Text($cx + 15, $cy - 25, "SPRING (Apr-Jun)");
                    $pdf->Text($cx - 45, $cy + 25, "AUTUMN (Oct-Dec)");
                    $pdf->Text($cx + 15, $cy + 25, "SUMMER (Jul-Sep)");

                } elseif ($type === 'cards_grid') {
                    // Resolution Cards
                    $pdf->SetY(120);
                    $cardW = 80; $cardH = 65;
                    $startX = 20; $startY = 120;
                    
                    $drawDashedRect = function($pdf, $x, $y, $w, $h) {
                        $pdf->SetSetDashing(3, 3); // Assume method exists or ignore if not
                        $pdf->Rect($x, $y, $w, $h);
                        // Reset dashing if needed, or SetDrawColor/LineWidth
                    };
                    // Basic Rect fallback if dashing not available
                    $pdf->SetDrawColor(100,100,100);
                    
                    $drawCardContent = function($pdf, $x, $y, $w) {
                        $pad = 8;
                        $pdf->SetXY($x+$pad, $y+6); $pdf->SetFont('Arial','B',8); $pdf->Cell(0,0,"MY GOAL:");
                        $pdf->Line($x+$pad, $y+20, $x+$w-$pad, $y+20);
                        $pdf->SetXY($x+$pad, $y+28); $pdf->Cell(0,0,"WHY IT MATTERS:");
                        $pdf->Line($x+$pad, $y+42, $x+$w-$pad, $y+42);
                        $pdf->SetXY($x+$pad, $y+50); $pdf->Cell(0,0,"FIRST STEP:");
                        $pdf->Line($x+$pad, $y+62, $x+$w-$pad, $y+62);
                    };

                    // Draw 4 cards
                    $pdf->Rect($startX, $startY, $cardW, $cardH);
                    $drawCardContent($pdf, $startX, $startY, $cardW);
                    
                    $pdf->Rect($startX+$cardW+10, $startY, $cardW, $cardH);
                    $drawCardContent($pdf, $startX+$cardW+10, $startY, $cardW);
                    
                    $startY2 = $startY + $cardH + 10;
                    $pdf->Rect($startX, $startY2, $cardW, $cardH);
                    $drawCardContent($pdf, $startX, $startY2, $cardW);
                    
                    $pdf->Rect($startX+$cardW+10, $startY2, $cardW, $cardH);
                    $drawCardContent($pdf, $startX+$cardW+10, $startY2, $cardW);

                } else {
                    // Standard Text Content
                    $pdf->SetY(95); 
                    $pdf->SetFont('Arial', '', 11);
                    $pdf->SetXY(30, 95); 
                    $pdf->MultiCell(150, 7, $t($item['content']), 0, 'C');
                }
            }
        }



        // FINAL PAGE: TERMS & PRECAUTIONS (Shifted to Page 13 now)
        $pdf->AddPage();
        $addBorder($pdf);
        
        $pdf->SetY(50);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'TERMS & PRECAUTIONS', 0, 1, 'C');

        $pdf->SetY(70);
        $pdf->SetFont('Arial', 'B', 12);
        // Indent Content for neatness
        $pdf->SetX(20);
        $pdf->Cell(0, 10, 'Health & Safety Precautions', 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(50, 50, 50);
        $precautions = "1. Please adhere to all local health guidelines and venue-specific safety protocols.\n2. Stay hydrated and take breaks if you feel overwhelmed.\n3. Be mindful of your personal boundaries and respect those of others.\n4. If you have any medical conditions, please consult with a professional before participating in physical activities.";
        $pdf->SetX(20);
        $pdf->MultiCell(170, 6, $t($precautions)); // Restricting width to 170

        $pdf->SetY(120);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX(20);
        $pdf->Cell(0, 10, 'Terms and Conditions', 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(50, 50, 50);
        $terms = "1. Ticket Validity: This digital kit and your QR code serve as your ticket. It is non-transferable unless explicitly stated otherwise.\n\n2. Code of Conduct: We are committed to creating a safe and inclusive environment. Harassment, discrimination, or disruptive behavior will not be tolerated and may result in removal from the event.\n\n3. Media Release: By attending this event, you grant permission for the use of photography and video recording for promotional purposes.\n\n4. Liability: The organizers are not responsible for any personal injury or loss of property during the event. Please keep your belongings secure.\n\n5. Changes & Cancellations: The event schedule and details are subject to change. We will notify you of any significant updates via email.";
        $pdf->SetX(20);
        $pdf->MultiCell(170, 6, $t($terms));
        


        // Restore error reporting
        error_reporting($previous_error_reporting);

        return $pdf->Output('S');
    }

    private static function addHeader($pdf, $color, $text) {
        if(empty($color)) $color = [30, 30, 30];
        $pdf->SetFillColor($color[0], $color[1], $color[2]);
        // Draw Header Rect
        $pdf->Rect(0, 0, 210, 35, 'F');
        
        // Text
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(0, 0);
        $pdf->Cell(210, 35, strtoupper($text), 0, 1, 'C');
        
        // Reset Text Color
        $pdf->SetTextColor(0, 0, 0);
    }
}
