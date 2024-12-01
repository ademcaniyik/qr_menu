<?php
class Security {
    /**
     * Clean and sanitize input data
     * 
     * @param mixed $data Input data to sanitize
     * @return mixed Sanitized data
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        // Convert special characters to HTML entities
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        // Remove any script tags
        $data = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data);
        // Trim whitespace
        $data = trim($data);
        
        return $data;
    }

    /**
     * Generate and store CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if token is valid
     */
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Validate file upload
     * 
     * @param array $file $_FILES array element
     * @param array $options Validation options
     * @return array Result with status and message
     */
    public static function validateFileUpload($file, $options = []) {
        $defaults = [
            'maxSize' => 5 * 1024 * 1024, // 5MB
            'allowedTypes' => ['image/jpeg', 'image/jpg', 'image/png'],
            'maxWidth' => 2000,
            'maxHeight' => 2000
        ];
        
        $options = array_merge($defaults, $options);
        
        // Check if file was uploaded
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['status' => false, 'message' => 'Geçersiz dosya yüklemesi.'];
        }
        
        // Check upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return ['status' => false, 'message' => 'Dosya boyutu çok büyük.'];
            case UPLOAD_ERR_PARTIAL:
                return ['status' => false, 'message' => 'Dosya tam yüklenemedi.'];
            case UPLOAD_ERR_NO_FILE:
                return ['status' => false, 'message' => 'Dosya yüklenmedi.'];
            default:
                return ['status' => false, 'message' => 'Bilinmeyen bir hata oluştu.'];
        }
        
        // Check file size
        if ($file['size'] > $options['maxSize']) {
            return ['status' => false, 'message' => 'Dosya boyutu çok büyük (maksimum ' . ($options['maxSize'] / 1024 / 1024) . 'MB).'];
        }
        
        // Check MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $options['allowedTypes'])) {
            return ['status' => false, 'message' => 'Geçersiz dosya türü.'];
        }
        
        // Check image dimensions
        if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'])) {
            list($width, $height) = getimagesize($file['tmp_name']);
            if ($width > $options['maxWidth'] || $height > $options['maxHeight']) {
                return [
                    'status' => false, 
                    'message' => "Görsel boyutları çok büyük (maksimum {$options['maxWidth']}x{$options['maxHeight']} piksel)."
                ];
            }
        }
        
        return ['status' => true, 'message' => 'Dosya geçerli.'];
    }

    /**
     * Optimize and save image
     * 
     * @param string $source Source file path
     * @param string $destination Destination file path
     * @param array $options Optimization options
     * @return bool Success status
     */
    public static function optimizeImage($source, $destination, $options = []) {
        $defaults = [
            'maxWidth' => 1200,
            'maxHeight' => 1200,
            'quality' => 85
        ];
        
        $options = array_merge($defaults, $options);
        
        // Get image info
        list($width, $height, $type) = getimagesize($source);
        
        // Calculate new dimensions
        $ratio = min($options['maxWidth'] / $width, $options['maxHeight'] / $height);
        $newWidth = $width * $ratio;
        $newHeight = $height * $ratio;
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparency for PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Load source image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($source);
                break;
            default:
                return false;
        }
        
        // Resize image
        imagecopyresampled(
            $newImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );
        
        // Save image
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $destination, $options['quality']);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $destination, round(($options['quality'] / 100) * 9));
                break;
            default:
                return false;
        }
        
        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return true;
    }
}
