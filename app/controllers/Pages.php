<?php
class Pages extends Controller {
    public function __construct() {
        
    }

    /**
     * Default method - redirects to menu
     */
    public function index() {
        // If user is logged in, redirect to businesses
        if (isset($_SESSION['user_id'])) {
            redirect('businesses/index');
        }
        
        // Otherwise show landing page
        $data = [
            'title' => 'QR Menü Sistemi',
            'description' => 'Modern ve kullanıcı dostu dijital menü çözümü'
        ];

        $this->view('pages/index', $data);
    }

    /**
     * About page
     */
    public function about() {
        $data = [
            'title' => 'Hakkımızda',
            'description' => 'QR Menü Sistemi, restoranlar için modern ve kullanıcı dostu bir dijital menü çözümüdür.'
        ];

        $this->view('pages/about', $data);
    }

    /**
     * Contact page
     */
    public function contact() {
        $data = [
            'title' => 'İletişim',
            'description' => 'Bizimle iletişime geçin'
        ];

        $this->view('pages/contact', $data);
    }

    /**
     * Terms page
     */
    public function terms() {
        $data = [
            'title' => 'Kullanım Koşulları',
            'description' => 'QR Menü Sistemi kullanım koşulları'
        ];

        $this->view('pages/terms', $data);
    }

    /**
     * Privacy policy page
     */
    public function privacy() {
        $data = [
            'title' => 'Gizlilik Politikası',
            'description' => 'QR Menü Sistemi gizlilik politikası'
        ];

        $this->view('pages/privacy', $data);
    }
}
