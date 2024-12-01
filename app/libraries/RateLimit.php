<?php
class RateLimit {
    private static $requests = [];
    private static $cache = null;
    
    // Rate limit settings
    private static $defaultLimit = 60; // requests per minute
    private static $windowSize = 60; // seconds
    
    /**
     * Initialize rate limiter
     */
    public static function init() {
        // Initialize cache if available
        if (class_exists('Cache')) {
            self::$cache = new Cache();
        }
        
        // Clean old entries periodically
        self::cleanup();
    }
    
    /**
     * Check if request is allowed
     * 
     * @param string $route Route being accessed
     * @param string $identifier Unique identifier (e.g. IP address)
     * @param int $limit Optional custom limit
     * @return bool Whether request is allowed
     */
    public static function check($route, $identifier, $limit = null) {
        $key = "rate_limit:{$route}:{$identifier}";
        $now = time();
        $limit = $limit ?? self::$defaultLimit;
        
        // Try cache first if available
        if (self::$cache !== null) {
            return self::checkCache($key, $now, $limit);
        }
        
        return self::checkMemory($key, $now, $limit);
    }
    
    /**
     * Check rate limit using cache storage
     */
    private static function checkCache($key, $now, $limit) {
        $requests = self::$cache->get($key, []);
        
        // Add current timestamp
        $requests[] = $now;
        
        // Remove old timestamps
        $requests = array_filter(
            $requests,
            function($timestamp) use ($now) {
                return $timestamp > ($now - self::$windowSize);
            }
        );
        
        // Store updated requests
        self::$cache->set($key, $requests, self::$windowSize);
        
        // Count requests in current window
        $count = count($requests);
        
        return $count <= $limit;
    }
    
    /**
     * Check rate limit using in-memory storage
     */
    private static function checkMemory($key, $now, $limit) {
        // Initialize array for key if not exists
        if (!isset(self::$requests[$key])) {
            self::$requests[$key] = [];
        }
        
        // Add current timestamp
        self::$requests[$key][] = $now;
        
        // Remove old timestamps
        self::$requests[$key] = array_filter(
            self::$requests[$key],
            function($timestamp) use ($now) {
                return $timestamp > ($now - self::$windowSize);
            }
        );
        
        // Count requests in current window
        $count = count(self::$requests[$key]);
        
        return $count <= $limit;
    }
    
    /**
     * Reset rate limit for a route and identifier
     * 
     * @param string $route Route to reset
     * @param string $identifier Unique identifier
     */
    public static function reset($route, $identifier) {
        $key = "rate_limit:{$route}:{$identifier}";
        
        if (self::$cache !== null) {
            self::$cache->delete($key);
        }
        
        unset(self::$requests[$key]);
    }
    
    /**
     * Get current request count
     * 
     * @param string $route Route to check
     * @param string $identifier Unique identifier
     * @return int Current request count
     */
    public static function getCount($route, $identifier) {
        $key = "rate_limit:{$route}:{$identifier}";
        $now = time();
        
        if (self::$cache !== null) {
            $requests = self::$cache->get($key, []);
        } else {
            if (!isset(self::$requests[$key])) {
                return 0;
            }
            $requests = self::$requests[$key];
        }
        
        // Clean old entries first
        $requests = array_filter(
            $requests,
            function($timestamp) use ($now) {
                return $timestamp > ($now - self::$windowSize);
            }
        );
        
        if (self::$cache !== null) {
            self::$cache->set($key, $requests, self::$windowSize);
        } else {
            self::$requests[$key] = $requests;
        }
        
        return count($requests);
    }
    
    /**
     * Get remaining requests allowed
     * 
     * @param string $route Route to check
     * @param string $identifier Unique identifier
     * @param int $limit Optional custom limit
     * @return int Number of remaining requests
     */
    public static function getRemaining($route, $identifier, $limit = null) {
        $limit = $limit ?? self::$defaultLimit;
        $count = self::getCount($route, $identifier);
        return max(0, $limit - $count);
    }
    
    /**
     * Clean up old entries
     */
    private static function cleanup() {
        $now = time();
        
        if (self::$cache !== null) {
            // Cache handles expiration automatically
            return;
        }
        
        foreach (self::$requests as $key => $timestamps) {
            self::$requests[$key] = array_filter(
                $timestamps,
                function($timestamp) use ($now) {
                    return $timestamp > ($now - self::$windowSize);
                }
            );
            
            if (empty(self::$requests[$key])) {
                unset(self::$requests[$key]);
            }
        }
    }
    
    /**
     * Set custom rate limit settings
     * 
     * @param int $limit Requests per window
     * @param int $windowSize Window size in seconds
     */
    public static function setLimits($limit, $windowSize) {
        self::$defaultLimit = $limit;
        self::$windowSize = $windowSize;
    }
}
