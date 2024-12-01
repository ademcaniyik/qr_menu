<?php
class Menus extends Controller {
    private $businessModel;
    private $categoryModel;
    private $menuItemModel;

    public function __construct() {
        if (!isLoggedIn()) {
            redirect('auth/login');
        }
        
        $this->businessModel = $this->model('Business');
        $this->categoryModel = $this->model('Category');
        $this->menuItemModel = $this->model('MenuItem');
    }

    /**
     * Display menu management page for a business
     * 
     * @param int $businessId Business ID
     * @return void
     */
    public function index($businessId = '') {
        // Check if business ID is provided
        if (empty($businessId)) {
            redirect('businesses');
        }

        // Get business
        $business = $this->businessModel->getBusinessById($businessId);
        
        // Check if business exists and user owns it
        if (!$business || $business->user_id !== $_SESSION['user_id']) {
            redirect('businesses');
        }

        // Get categories with menu items
        $categories = $this->categoryModel->getCategoriesByBusinessId($businessId);
        foreach ($categories as $category) {
            $category->items = $this->menuItemModel->getMenuItemsByCategoryId($category->id);
        }

        $data = [
            'business' => $business,
            'categories' => $categories,
            'title' => $business->name . ' - Menü Yönetimi'
        ];

        $this->view('menus/index', $data);
    }

    /**
     * Add new menu item
     * 
     * @param int $categoryId Category ID
     * @return void
     */
    public function add($categoryId = '') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Get category
            $category = $this->categoryModel->getById($categoryId);
            if (!$category) {
                redirect('businesses');
            }

            // Get business
            $business = $this->businessModel->getBusinessById($category->business_id);
            if (!$business || $business->user_id !== $_SESSION['user_id']) {
                redirect('businesses');
            }

            $data = [
                'category_id' => $categoryId,
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'price' => trim($_POST['price']),
                'name_err' => '',
                'price_err' => ''
            ];

            // Validate data
            if (empty($data['name'])) {
                $data['name_err'] = 'Lütfen ürün adını girin';
            }

            if (empty($data['price'])) {
                $data['price_err'] = 'Lütfen fiyat girin';
            } elseif (!is_numeric($data['price'])) {
                $data['price_err'] = 'Fiyat sayısal bir değer olmalıdır';
            }

            // Make sure no errors
            if (empty($data['name_err']) && empty($data['price_err'])) {
                // Add menu item
                if ($this->menuItemModel->create($data)) {
                    flash('menu_message', 'Ürün başarıyla eklendi');
                    redirect('menus/index/' . $business->id);
                } else {
                    die('Bir hata oluştu');
                }
            } else {
                // Load view with errors
                $this->view('menus/add', $data);
            }
        } else {
            // Get category
            $category = $this->categoryModel->getById($categoryId);
            if (!$category) {
                redirect('businesses');
            }

            // Get business
            $business = $this->businessModel->getBusinessById($category->business_id);
            if (!$business || $business->user_id !== $_SESSION['user_id']) {
                redirect('businesses');
            }

            $data = [
                'category_id' => $categoryId,
                'name' => '',
                'description' => '',
                'price' => '',
                'name_err' => '',
                'price_err' => ''
            ];

            $this->view('menus/add', $data);
        }
    }
}
