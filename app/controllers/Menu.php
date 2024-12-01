<?php
class Menu extends Controller {
    private $businessModel;
    private $categoryModel;
    private $menuItemModel;

    public function __construct() {
        $this->businessModel = $this->model('Business');
        $this->categoryModel = $this->model('Category');
        $this->menuItemModel = $this->model('MenuItem');
    }

    /**
     * Display public menu for a business
     * 
     * @param string $businessSlug Business slug
     * @return void
     */
    public function index($businessSlug = '') {
        // Get business by slug
        $business = $this->businessModel->getBusinessBySlug($businessSlug);
        
        if (!$business) {
            redirect('pages/error');
        }

        // Get categories with menu items
        $categories = $this->categoryModel->getCategoriesByBusiness($business->id);
        foreach ($categories as $category) {
            $category->items = $this->menuItemModel->getMenuItemsByCategory($category->id);
        }

        $data = [
            'business' => $business,
            'categories' => $categories,
            'title' => $business->name . ' - Menü'
        ];

        $this->view('menu/index', $data);
    }

    /**
     * Display demo menu
     * 
     * @return void
     */
    public function demo() {
        // Demo business data
        $business = (object)[
            'name' => 'Demo Restaurant',
            'description' => 'Modern ve lezzetli yemekleriyle sizleri bekliyor.',
            'address' => 'İstanbul, Türkiye',
            'phone' => '+90 (212) 123 45 67',
            'email' => 'info@demorestaurant.com',
            'cover_image' => 'demo-cover.jpg'
        ];

        // Demo categories with items
        $categories = [
            (object)[
                'name' => 'Başlangıçlar',
                'items' => [
                    (object)[
                        'name' => 'Mercimek Çorbası',
                        'description' => 'Geleneksel Türk mercimek çorbası',
                        'price' => '45.00',
                        'image' => 'demo-soup.jpg',
                        'is_available' => true
                    ],
                    (object)[
                        'name' => 'Karnıyarık',
                        'description' => 'Patlıcan, kıyma, domates ve biber ile',
                        'price' => '85.00',
                        'image' => 'demo-karniyarik.jpg',
                        'is_available' => true
                    ]
                ]
            ],
            (object)[
                'name' => 'Ana Yemekler',
                'items' => [
                    (object)[
                        'name' => 'Izgara Köfte',
                        'description' => 'Özel baharatlarla marine edilmiş dana köfte',
                        'price' => '120.00',
                        'image' => 'demo-kofte.jpg',
                        'is_available' => true
                    ],
                    (object)[
                        'name' => 'Tavuk Şiş',
                        'description' => 'Marine edilmiş tavuk göğsü, közlenmiş sebzeler ile',
                        'price' => '100.00',
                        'image' => 'demo-tavuk.jpg',
                        'is_available' => false
                    ]
                ]
            ],
            (object)[
                'name' => 'Tatlılar',
                'items' => [
                    (object)[
                        'name' => 'Künefe',
                        'description' => 'Antep fıstığı ile servis edilir',
                        'price' => '75.00',
                        'image' => 'demo-kunefe.jpg',
                        'is_available' => true
                    ],
                    (object)[
                        'name' => 'Sütlaç',
                        'description' => 'Fırında pişirilmiş geleneksel sütlaç',
                        'price' => '45.00',
                        'image' => 'demo-sutlac.jpg',
                        'is_available' => true
                    ]
                ]
            ],
            (object)[
                'name' => 'İçecekler',
                'items' => [
                    (object)[
                        'name' => 'Türk Kahvesi',
                        'description' => 'Geleneksel Türk kahvesi',
                        'price' => '30.00',
                        'image' => 'demo-kahve.jpg',
                        'is_available' => true
                    ],
                    (object)[
                        'name' => 'Ayran',
                        'description' => 'Ev yapımı ayran',
                        'price' => '15.00',
                        'image' => 'demo-ayran.jpg',
                        'is_available' => true
                    ]
                ]
            ]
        ];

        $data = [
            'business' => $business,
            'categories' => $categories,
            'title' => 'Demo Restaurant - Menü',
            'is_demo' => true
        ];

        $this->view('menu/index', $data);
    }

    /**
     * Generate and download QR code for a business
     * 
     * @param int $businessId Business ID
     * @return void
     */
    public function generateQR($businessId) {
        // Check if user is logged in and owns the business
        if (!isLoggedIn() || !$this->businessModel->verifyBusinessOwner($businessId, $_SESSION['user_id'])) {
            redirect('businesses');
        }

        $business = $this->businessModel->getBusinessById($businessId);
        if (!$business) {
            redirect('businesses');
        }

        // Generate QR code
        $qrGenerator = new QRGenerator();
        $qrData = $qrGenerator->generateMenuQR($business->id, $business->slug);

        if ($qrData) {
            // Update business with new QR code URL
            $this->businessModel->updateQRCode($business->id, $qrData['qr_url'], $qrData['menu_url']);
            
            flash('business_message', 'QR kod başarıyla oluşturuldu');
            redirect('businesses/manage/' . $business->id);
        } else {
            flash('business_message', 'QR kod oluşturulurken bir hata oluştu', 'alert alert-danger');
            redirect('businesses/manage/' . $business->id);
        }
    }
}
