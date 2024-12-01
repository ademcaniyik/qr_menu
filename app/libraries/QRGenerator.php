<?php
class QRGenerator {
    private $baseUrl;
    private $cachePath;
    private $cacheUrl;

    public function __construct() {
        $this->baseUrl = URL_ROOT;
        $this->cachePath = APPROOT . '/../public/cache/qr/';
        $this->cacheUrl = URL_ROOT . '/cache/qr/';
        
        // Create cache directory if it doesn't exist
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /**
     * Generate QR code for a business menu
     * 
     * @param int $businessId Business ID
     * @param string $businessSlug Business slug for URL-friendly name
     * @return array|null Array containing QR code image URL and menu URL, or null on failure
     */
    public function generateMenuQR($businessId, $businessSlug) {
        // Generate unique menu URL
        $menuUrl = $this->baseUrl . '/menu/' . $businessSlug;
        
        // Generate unique filename for QR code
        $filename = 'qr_' . $businessId . '_' . time() . '.png';
        $qrPath = $this->cachePath . $filename;
        
        // QR code parameters
        $size = 300; // Size in pixels
        $margin = 10; // Margin around QR code
        
        // Generate QR code using Google Charts API
        $googleChartUrl = 'https://chart.googleapis.com/chart?';
        $params = [
            'cht' => 'qr', // Chart type: QR code
            'chs' => $size . 'x' . $size, // Size
            'chl' => $menuUrl, // Data to encode
            'chld' => 'H|' . $margin // Error correction level (H=high) and margin
        ];
        
        // Get QR code image from Google Charts
        $qrImage = @file_get_contents($googleChartUrl . http_build_query($params));
        
        // Save QR code image
        if ($qrImage && file_put_contents($qrPath, $qrImage)) {
            return [
                'qr_url' => $this->cacheUrl . $filename,
                'menu_url' => $menuUrl
            ];
        }
        
        return null; 
    }

    /**
     * Delete old QR code for a business
     * 
     * @param int $businessId Business ID
     * @return bool Success status
     */
    public function deleteOldQR($businessId) {
        $pattern = $this->cachePath . 'qr_' . $businessId . '_*.png';
        $oldFiles = glob($pattern);
        
        if ($oldFiles) {
            foreach ($oldFiles as $file) {
                unlink($file);
            }
        }
        
        return true;
    }
}
