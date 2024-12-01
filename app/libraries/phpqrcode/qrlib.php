<?php
/*
 * PHP QR Code encoder
 *
 * Main encoder classes.
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 */

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

class QRcode {
    
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) {
        require_once dirname(__FILE__).'/qrimage.php';
        
        $enc = QRencode::encode($text, $level, $size, $margin);
        return QRimage::png($enc, $outfile, $saveandprint);
    }
}

class QRencode {
    public static function encode($text, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        // Basit bir QR kod verisi oluştur
        return [
            'text' => $text,
            'level' => $level,
            'size' => $size,
            'margin' => $margin
        ];
    }
}
