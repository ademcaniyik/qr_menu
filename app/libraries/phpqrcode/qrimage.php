<?php

class QRimage {
    
    //----------------------------------------------------------------------
    public static function png($frame, $filename = false, $saveandprint=false) {
        $width = 200;  // QR kod genişliği
        $height = 200; // QR kod yüksekliği
        
        // Yeni bir resim oluştur
        $image = imagecreatetruecolor($width, $height);
        
        // Beyaz arka plan
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $white);
        
        // Siyah QR kod
        $black = imagecolorallocate($image, 0, 0, 0);
        
        // Basit bir QR kod deseni çiz
        $text = $frame['text'];
        $fontSize = 2;
        $x = 10;
        $y = 100;
        
        // Metin yaz (gerçek QR kod yerine geçici olarak)
        imagestring($image, $fontSize, $x, $y, $text, $black);
        
        if($filename === false) {
            Header("Content-type: image/png");
            imagepng($image);
        } else {
            if($saveandprint === true) {
                imagepng($image, $filename);
                header("Content-type: image/png");
                imagepng($image);
            } else {
                imagepng($image, $filename);
            }
        }
        
        imagedestroy($image);
        
        return true;
    }
}
