<?php
class Cache {
    private static $cacheDir = APPROOT . '/../public/cache/';
    private static $defaultExpiry = 3600; // 1 hour

    /**
     * Initialize cache system
     */
    public static function init() {
        if (!file_exists(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
    }

    /**
     * Set cache data
     * 
     * @param string $key Cache key
     * @param mixed $data Data to cache
     * @param int $expiry Expiry time in seconds
     * @return bool Success status
     */
    public static function set($key, $data, $expiry = null) {
        $expiry = $expiry ?? self::$defaultExpiry;
        
        $cacheData = [
            'expiry' => time() + $expiry,
            'data' => $data
        ];
        
        $filename = self::getFilename($key);
        return file_put_contents($filename, serialize($cacheData)) !== false;
    }

    /**
     * Get cached data
     * 
     * @param string $key Cache key
     * @return mixed|null Cached data or null if not found/expired
     */
    public static function get($key) {
        $filename = self::getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $cacheData = unserialize(file_get_contents($filename));
        
        if ($cacheData['expiry'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $cacheData['data'];
    }

    /**
     * Delete cached data
     * 
     * @param string $key Cache key
     * @return bool Success status
     */
    public static function delete($key) {
        $filename = self::getFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }

    /**
     * Clear all cached data
     * 
     * @return bool Success status
     */
    public static function clear() {
        $files = glob(self::$cacheDir . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }

    /**
     * Clean expired cache files
     * 
     * @return bool Success status
     */
    public static function clean() {
        $files = glob(self::$cacheDir . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $cacheData = unserialize(file_get_contents($file));
                if ($cacheData['expiry'] < time()) {
                    unlink($file);
                }
            }
        }
        
        return true;
    }

    /**
     * Get cache filename for key
     * 
     * @param string $key Cache key
     * @return string Cache filename
     */
    private static function getFilename($key) {
        return self::$cacheDir . md5($key) . '.cache';
    }
}
