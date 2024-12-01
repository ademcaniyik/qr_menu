<?php
/**
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 */
class Core {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];
    
    public function __construct() {
        // Initialize error handler
        ErrorHandler::init();
        
        try {
            // Get URL
            $url = $this->getUrl();
            
            // Look in controllers for first value
            if (isset($url[0])) {
                if (file_exists(APPROOT . '/controllers/' . ucwords($url[0]) . '.php')) {
                    // If exists, set as controller
                    $this->currentController = ucwords($url[0]);
                    // Unset 0 Index
                    unset($url[0]);
                } else {
                    Logger::warning('Controller not found: ' . ucwords($url[0]));
                    throw new Exception('Page not found', 404);
                }
            }
            
            // Require the controller
            require_once APPROOT . '/controllers/' . $this->currentController . '.php';
            
            // Instantiate controller class
            $this->currentController = new $this->currentController;
            
            // Check for second part of url
            if (isset($url[1])) {
                // Check to see if method exists in controller
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];
                    // Unset 1 index
                    unset($url[1]);
                } else {
                    Logger::warning('Method not found: ' . $url[1] . ' in controller ' . get_class($this->currentController));
                    throw new Exception('Page not found', 404);
                }
            }
            
            // Get params
            $this->params = $url ? array_values($url) : [];
            
            // Log route access
            Logger::info('Route accessed', [
                'controller' => get_class($this->currentController),
                'method' => $this->currentMethod,
                'params' => $this->params,
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            
            // Call a callback with array of params
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
            
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    /**
     * Get URL from request
     * 
     * @return array URL parts
     */
    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
    
    /**
     * Handle exceptions in routing
     * 
     * @param Exception $e Exception object
     */
    protected function handleException($e) {
        if ($e->getCode() === 404) {
            http_response_code(404);
            require_once '../app/views/errors/404.php';
        } else {
            throw $e; // Let the global error handler deal with it
        }
    }
}
