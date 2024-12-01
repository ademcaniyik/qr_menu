<?php
class Cache {
    private $cacheDir;
    private $defaultTTL = 3600; // 1 saat varsayılan TTL

    public function __construct() {
        $this->cacheDir = dirname(dirname(__DIR__)) . '/cache/';
        
        // Cache dizinini oluştur
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    /**
     * Cache'den veri al
     * 
     * @param string $key Cache anahtarı
     * @param mixed $default Varsayılan değer
     * @return mixed Cache'deki veri veya varsayılan değer
     */
    public function get($key, $default = null) {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }

        $content = file_get_contents($filename);
        if ($content === false) {
            return $default;
        }

        $data = unserialize($content);
        if ($data === false) {
            return $default;
        }

        // TTL kontrolü
        if (isset($data['expires']) && time() > $data['expires']) {
            $this->delete($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Cache'e veri kaydet
     * 
     * @param string $key Cache anahtarı
     * @param mixed $value Cache'e kaydedilecek veri
     * @param int $ttl Cache süresi (saniye)
     * @return bool İşlem başarılı mı?
     */
    public function set($key, $value, $ttl = null) {
        $filename = $this->getCacheFilename($key);
        $ttl = $ttl ?? $this->defaultTTL;

        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];

        return file_put_contents($filename, serialize($data)) !== false;
    }

    /**
     * Cache'den veri sil
     * 
     * @param string $key Cache anahtarı
     * @return bool İşlem başarılı mı?
     */
    public function delete($key) {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return true;
    }

    /**
     * Tüm cache'i temizle
     * 
     * @return bool İşlem başarılı mı?
     */
    public function clear() {
        $files = glob($this->cacheDir . '*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }

    /**
     * Cache dosyası adını oluştur
     * 
     * @param string $key Cache anahtarı
     * @return string Cache dosyası yolu
     */
    private function getCacheFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }

    /**
     * Süresi dolmuş cache dosyalarını temizle
     */
    public function cleanup() {
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = unserialize($content);
            if ($data === false || !isset($data['expires'])) {
                unlink($file);
                continue;
            }

            if (time() > $data['expires']) {
                unlink($file);
            }
        }
    }
}
