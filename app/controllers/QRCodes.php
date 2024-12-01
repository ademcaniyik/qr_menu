<?php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

class QRCodes extends Controller {
    private $menuModel;
    private $businessModel;
    private $rateLimit;

    public function __construct() {
        $this->menuModel = $this->model('Menu');
        $this->businessModel = $this->model('Business');
        
        // Rate limit başlat
        RateLimit::init();
    }

    // Default index method
    public function index() {
        redirect('pages/index');
    }

    // View QR code menu
    public function showMenu($businessSlug) {
        // Rate limit kontrolü
        if (!RateLimit::check('showMenu', $_SERVER['REMOTE_ADDR'])) {
            http_response_code(429);
            die('Too Many Requests');
        }

        try {
            // Get business by slug
            $business = $this->businessModel->getBusinessBySlug($businessSlug);
            if (!$business) {
                redirect('pages/error');
            }

            // Get active menu for business
            $menu = $this->menuModel->getActiveMenuByBusinessId($business->id);
            if (!$menu) {
                redirect('pages/error');
            }

            // Get categories with menu items
            $categories = $this->menuModel->getCategoriesWithItems($menu->id);

            $data = [
                'menu' => $menu,
                'business' => $business,
                'categories' => $categories
            ];

            // Cache headers
            header('Cache-Control: public, max-age=300'); // 5 dakika cache
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($menu->updated_at)) . ' GMT');

            // Doğrudan view'ı yükle, header ve footer olmadan
            $this->loadView('qrcodes/view', $data);
        } catch (Exception $e) {
            Logger::error('Menu view error: ' . $e->getMessage());
            redirect('pages/error');
        }
    }

    // View QR code page
    public function viewQrCode($businessId) {
        // Rate limit kontrolü
        if (!RateLimit::check('viewQrCode', $_SERVER['REMOTE_ADDR'])) {
            http_response_code(429);
            die('Too Many Requests');
        }

        try {
            // Get business
            $business = $this->businessModel->getBusinessById($businessId);
            if (!$business) {
                redirect('pages/error');
            }

            // Get active menu
            $menu = $this->menuModel->getActiveMenuByBusinessId($businessId);
            if (!$menu) {
                redirect('pages/error');
            }

            $data = [
                'title' => $business->name . ' - QR Kod',
                'business' => $business,
                'menu' => $menu
            ];

            // Cache headers
            header('Cache-Control: public, max-age=3600'); // 1 saat cache
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($business->updated_at)) . ' GMT');

            $this->view('qrcodes/qr_view', $data);
        } catch (Exception $e) {
            Logger::error('QR code view error: ' . $e->getMessage());
            redirect('pages/error');
        }
    }

    public function generate($slug) {
        // Rate limit kontrolü
        if (!RateLimit::check('generate', $_SERVER['REMOTE_ADDR'], 30)) { // Dakikada 30 istek
            http_response_code(429);
            die('Too Many Requests');
        }

        try {
            $business = $this->businessModel->getBusinessBySlug($slug);
            if (!$business) {
                throw new Exception('İşletme bulunamadı');
            }

            // QR kod URL'i
            $url = URLROOT . '/' . $slug;

            // QR kod oluştur
            $qrCode = QrCode::create($url)
                ->setSize(300)
                ->setMargin(10)
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255))
                ->setEncoding(new Encoding('UTF-8'))
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Cache headers
            header('Cache-Control: public, max-age=86400'); // 24 saat cache
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($business->updated_at)) . ' GMT');

            // QR kodu PNG olarak göster
            header('Content-Type: ' . $result->getMimeType());
            echo $result->getString();
        } catch (Exception $e) {
            Logger::error('QR code generation error: ' . $e->getMessage());
            http_response_code(500);
            die('QR kod oluşturulurken bir hata oluştu');
        }
    }

    public function download($slug) {
        // Rate limit kontrolü
        if (!RateLimit::check('download', $_SERVER['REMOTE_ADDR'], 20)) { // Dakikada 20 indirme
            http_response_code(429);
            die('Too Many Requests');
        }

        try {
            $business = $this->businessModel->getBusinessBySlug($slug);
            if (!$business) {
                throw new Exception('İşletme bulunamadı');
            }

            // QR kod URL'i
            $url = URLROOT . '/' . $slug;

            // QR kod oluştur
            $qrCode = QrCode::create($url)
                ->setSize(300)
                ->setMargin(10)
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255))
                ->setEncoding(new Encoding('UTF-8'))
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // İndirme başlıkları
            header('Content-Type: ' . $result->getMimeType());
            header('Content-Disposition: attachment; filename="' . $slug . '_qr.png"');
            header('Content-Length: ' . strlen($result->getString()));
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            echo $result->getString();
        } catch (Exception $e) {
            Logger::error('QR code download error: ' . $e->getMessage());
            http_response_code(500);
            die('QR kod indirilirken bir hata oluştu');
        }
    }

    // Özel view yükleme metodu
    private function loadView($view, $data = []) {
        // Extract data
        extract($data);
        
        // Get view
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}
