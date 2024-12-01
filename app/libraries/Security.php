<?php
class Security {
    /**
     * Sanitize any data
     * 
     * @param mixed $data Data to sanitize
     * @return mixed Sanitized data
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        } elseif (is_object($data)) {
            foreach (get_object_vars($data) as $key => $value) {
                $data->$key = self::sanitize($value);
            }
            return $data;
        } else {
            return self::sanitizeString($data);
        }
    }

    /**
     * Sanitize POST data
     * 
     * @param array $data POST data
     * @return array Sanitized data
     */
    public static function sanitizePost($data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::sanitizePost($value);
            } else {
                $data[$key] = self::sanitizeString($value);
            }
        }
        return $data;
    }

    /**
     * Sanitize GET data
     * 
     * @param array $data GET data
     * @return array Sanitized data
     */
    public static function sanitizeGet($data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::sanitizeGet($value);
            } else {
                $data[$key] = self::sanitizeString($value);
            }
        }
        return $data;
    }

    /**
     * Sanitize a string
     * 
     * @param string $string String to sanitize
     * @return string Sanitized string
     */
    public static function sanitizeString($string) {
        if (is_string($string)) {
            $string = trim($string);
            $string = strip_tags($string);
            $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        }
        return $string;
    }

    /**
     * Escape HTML
     * 
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeHtml($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate CSRF token
     * 
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if valid, false otherwise
     */
    public static function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate random string
     * 
     * @param int $length Length of string
     * @return string Random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Hash password
     * 
     * @param string $password Password to hash
     * @return string Hashed password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     * 
     * @param string $password Password to verify
     * @param string $hash Hash to verify against
     * @return bool True if valid, false otherwise
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Clean file name
     * 
     * @param string $filename Filename to clean
     * @return string Cleaned filename
     */
    public static function cleanFileName($filename) {
        // Remove any path info
        $filename = basename($filename);
        
        // Remove any characters that are not alphanumeric, dash, underscore or dot
        $filename = preg_replace("/[^a-zA-Z0-9-_.]/", "", $filename);
        
        // Convert to lowercase
        $filename = strtolower($filename);
        
        return $filename;
    }

    /**
     * Validate file type
     * 
     * @param string $filename Filename to validate
     * @param array $allowedTypes Allowed file types
     * @return bool True if valid, false otherwise
     */
    public static function validateFileType($filename, $allowedTypes) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $allowedTypes);
    }

    /**
     * Validate file size
     * 
     * @param int $filesize File size in bytes
     * @param int $maxSize Maximum size in bytes
     * @return bool True if valid, false otherwise
     */
    public static function validateFileSize($filesize, $maxSize) {
        return $filesize <= $maxSize;
    }

    /**
     * Validate email
     * 
     * @param string $email Email to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     * 
     * @param string $url URL to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate phone number
     * 
     * @param string $phone Phone number to validate
     * @return bool True if valid, false otherwise
     */
    public static function validatePhone($phone) {
        // Remove any non-digit characters
        $phone = preg_replace("/[^0-9]/", "", $phone);
        
        // Check if length is valid (10-15 digits)
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }
}
