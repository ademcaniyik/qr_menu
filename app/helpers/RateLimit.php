<?php
class RateLimit {
    private static $cachePrefix = 'rate_limit_';
    private static $config;

    /**
     * Initialize rate limiter
     */
    public static function init() {
        // Load rate limit configuration
        self::$config = require_once APPROOT . '/config/rate_limit.php';
        
        // Initialize cache
        Cache::init();
    }

    /**
     * Check if request is within rate limits
     * 
     * @param string $route Route being accessed
     * @param string $identifier Unique identifier (IP, user ID, etc.)
     * @return bool Whether request is allowed
     */
    public static function check($route, $identifier) {
        // Get rate limit settings for route
        $settings = self::getSettings($route);
        
        // Generate cache key
        $key = self::$cachePrefix . $route . '_' . $identifier;
        
        // Get current request count
        $data = Cache::get($key);
        
        if ($data === null) {
            // First request
            Cache::set($key, ['count' => 1, 'first_request' => time()], $settings['period']);
            return true;
        }
        
        // Check if period has expired
        if ((time() - $data['first_request']) > $settings['period']) {
            // Reset counter
            Cache::set($key, ['count' => 1, 'first_request' => time()], $settings['period']);
            return true;
        }
        
        // Increment counter
        $data['count']++;
        Cache::set($key, $data, $settings['period'] - (time() - $data['first_request']));
        
        // Check if limit exceeded
        return $data['count'] <= $settings['requests'];
    }

    /**
     * Get rate limit settings for route
     * 
     * @param string $route Route to check
     * @return array Rate limit settings
     */
    private static function getSettings($route) {
        // Check for exact route match
        if (isset(self::$config['routes'][$route])) {
            return self::$config['routes'][$route];
        }
        
        // Check for wildcard matches
        foreach (self::$config['routes'] as $pattern => $settings) {
            if (strpos($pattern, '*') !== false) {
                $pattern = str_replace('*', '.*', $pattern);
                if (preg_match('#^' . $pattern . '$#', $route)) {
                    return $settings;
                }
            }
        }
        
        // Return default settings
        return self::$config['default'];
    }

    /**
     * Get remaining requests for route and identifier
     * 
     * @param string $route Route being accessed
     * @param string $identifier Unique identifier
     * @return array Remaining requests and reset time
     */
    public static function getRemaining($route, $identifier) {
        $settings = self::getSettings($route);
        $key = self::$cachePrefix . $route . '_' . $identifier;
        $data = Cache::get($key);
        
        if ($data === null) {
            return [
                'remaining' => $settings['requests'],
                'reset' => time() + $settings['period']
            ];
        }
        
        $timeElapsed = time() - $data['first_request'];
        if ($timeElapsed > $settings['period']) {
            return [
                'remaining' => $settings['requests'],
                'reset' => time() + $settings['period']
            ];
        }
        
        return [
            'remaining' => max(0, $settings['requests'] - $data['count']),
            'reset' => $data['first_request'] + $settings['period']
        ];
    }
}
