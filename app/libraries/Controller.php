<?php
/**
 * Base Controller
 * Loads models and views
 */
class Controller {
    protected $currentRoute;

    public function __construct() {
        // Initialize rate limiter
        RateLimit::init();
        
        // Get current route
        $this->currentRoute = trim($_GET['url'] ?? '', '/');
        
        // Check rate limit
        if (!$this->checkRateLimit()) {
            http_response_code(429);
            die('Too many requests. Please try again later.');
        }
    }

    /**
     * Check rate limit for current request
     * 
     * @return bool Whether request is allowed
     */
    protected function checkRateLimit() {
        // Get client IP
        $identifier = $_SERVER['REMOTE_ADDR'];
        
        // Check rate limit
        return RateLimit::check($this->currentRoute, $identifier);
    }

    /**
     * Load model
     * 
     * @param string $model Model name
     * @return object Model instance
     */
    public function model($model) {
        // Require model file
        require_once APPROOT . '/models/' . $model . '.php';
        // Instantiate model
        return new $model();
    }

    /**
     * Load view
     * 
     * @param string $view View name
     * @param array $data Data to pass to view
     * @return void
     */
    public function view($view, $data = []) {
        // Clean data
        $data = Security::sanitize($data);
        
        // Check for view file
        if(file_exists(APPROOT . '/views/' . $view . '.php')) {
            require_once APPROOT . '/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }

    /**
     * Handle file upload with security checks and optimization
     * 
     * @param array $file $_FILES array element
     * @param string $uploadDir Upload directory
     * @param array $options Validation and optimization options
     * @return array Result with status, message and filename
     */
    protected function handleFileUpload($file, $uploadDir, $options = []) {
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file
        $validation = Security::validateFileUpload($file, $options);
        if (!$validation['status']) {
            return ['status' => false, 'message' => $validation['message']];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = $uploadDir . $filename;

        // Move and optimize image
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            if (Security::optimizeImage($destination, $destination, $options)) {
                return [
                    'status' => true,
                    'message' => 'Dosya başarıyla yüklendi.',
                    'filename' => $filename
                ];
            } else {
                unlink($destination);
                return [
                    'status' => false,
                    'message' => 'Görsel optimizasyonu başarısız oldu.'
                ];
            }
        }

        return [
            'status' => false,
            'message' => 'Dosya yüklenirken bir hata oluştu.'
        ];
    }

    /**
     * Validate request method
     * 
     * @param string $method Expected request method
     * @return bool Valid status
     */
    protected function validateMethod($method) {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * Validate CSRF token for POST requests
     * 
     * @return bool Valid status
     */
    protected function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            return Security::verifyCSRFToken($token);
        }
        return true;
    }

    /**
     * Clean input data
     * 
     * @param array $data Input data
     * @return array Cleaned data
     */
    protected function cleanInput($data) {
        return Security::sanitize($data);
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders() {
        $identifier = $_SERVER['REMOTE_ADDR'];
        $remaining = RateLimit::getRemaining($this->currentRoute, $identifier);
        
        header('X-RateLimit-Remaining: ' . $remaining['remaining']);
        header('X-RateLimit-Reset: ' . $remaining['reset']);
    }
}
