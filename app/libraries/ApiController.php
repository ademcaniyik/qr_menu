<?php
/**
 * Base API Controller
 * Handles common API functionality
 */
class ApiController extends Controller {
    protected $requestMethod;
    protected $requestData;
    protected $apiVersion = '1.0';
    
    public function __construct() {
        parent::__construct();
        
        // Set JSON response header
        header('Content-Type: application/json');
        
        // Add CORS headers
        $this->setCorsHeaders();
        
        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
        
        // Get request method
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Get request data
        $this->requestData = $this->getRequestData();
        
        // Validate API version
        $this->validateApiVersion();
        
        // Add rate limit headers
        $this->addRateLimitHeaders();
    }
    
    /**
     * Send JSON response
     * 
     * @param mixed $data Response data
     * @param int $statusCode HTTP status code
     * @param string $message Optional message
     */
    protected function sendResponse($data, $statusCode = 200, $message = '') {
        http_response_code($statusCode);
        
        $response = [
            'status' => $statusCode < 400 ? 'success' : 'error',
            'message' => $message,
            'data' => $data
        ];
        
        // Log response
        Logger::info('API Response', [
            'status_code' => $statusCode,
            'response' => $response,
            'endpoint' => $_SERVER['REQUEST_URI']
        ]);
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $errors Additional error details
     */
    protected function sendError($message, $statusCode = 400, $errors = []) {
        http_response_code($statusCode);
        
        $response = [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ];
        
        // Log error
        Logger::error('API Error', [
            'status_code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'endpoint' => $_SERVER['REQUEST_URI']
        ]);
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Get request data based on method
     * 
     * @return array Request data
     */
    protected function getRequestData() {
        $data = [];
        
        switch ($this->requestMethod) {
            case 'GET':
                $data = $_GET;
                break;
                
            case 'POST':
                $rawData = file_get_contents('php://input');
                if (!empty($rawData)) {
                    $data = json_decode($rawData, true) ?? [];
                } else {
                    $data = $_POST;
                }
                break;
                
            case 'PUT':
            case 'DELETE':
                $rawData = file_get_contents('php://input');
                $data = json_decode($rawData, true) ?? [];
                break;
        }
        
        return $this->cleanInput($data);
    }
    
    /**
     * Set CORS headers
     */
    protected function setCorsHeaders() {
        $allowedOrigins = $this->getAllowedOrigins();
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        }
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Version');
        header('Access-Control-Max-Age: 86400'); // 24 hours
    }
    
    /**
     * Get allowed origins for CORS
     * 
     * @return array Allowed origins
     */
    protected function getAllowedOrigins() {
        // Add your allowed origins here
        return [
            '*' // Allow all origins for now
            // 'https://yourdomain.com',
            // 'https://api.yourdomain.com'
        ];
    }
    
    /**
     * Validate API version from request header
     */
    protected function validateApiVersion() {
        $requestVersion = $_SERVER['HTTP_X_API_VERSION'] ?? '1.0';
        
        if (version_compare($requestVersion, $this->apiVersion, '>')) {
            $this->sendError('Unsupported API version', 400, [
                'current_version' => $this->apiVersion,
                'requested_version' => $requestVersion
            ]);
        }
    }
    
    /**
     * Validate required fields in request data
     * 
     * @param array $required Required fields
     * @return bool|array True if valid, array of missing fields if invalid
     */
    protected function validateRequired($required) {
        $missing = [];
        
        foreach ($required as $field) {
            if (!isset($this->requestData[$field]) || empty($this->requestData[$field])) {
                $missing[] = $field;
            }
        }
        
        return empty($missing) ? true : $missing;
    }
    
    /**
     * Check if request method matches expected method
     * 
     * @param string $expected Expected HTTP method
     */
    protected function requireMethod($expected) {
        if ($this->requestMethod !== strtoupper($expected)) {
            $this->sendError('Method not allowed', 405, [
                'expected' => $expected,
                'actual' => $this->requestMethod
            ]);
        }
    }
}
