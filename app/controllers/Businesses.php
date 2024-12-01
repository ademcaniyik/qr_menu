<?php
class Businesses extends Controller {
    private $businessModel;
    private $userModel;
    private $categoryModel;
    private $menuItemModel;

    public function __construct() {
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        $this->businessModel = $this->model('Business');
        $this->userModel = $this->model('User');
        $this->categoryModel = $this->model('Category');
        $this->menuItemModel = $this->model('MenuItem');
    }

    // Sanitize input
    private function sanitizeInput($input) {
        if(is_array($input)) {
            $sanitized = [];
            foreach($input as $key => $value) {
                $sanitized[$key] = $this->sanitizeInput($value);
            }
            return $sanitized;
        }
        
        // Remove HTML tags and encode special characters
        $sanitized = strip_tags($input);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return trim($sanitized);
    }

    public function index() {
        try {
            // Admin tüm işletmeleri görebilir
            if (isAdmin()) {
                $businesses = $this->businessModel->getBusinesses();
            } else {
                // İşletme sahibi sadece kendi işletmesini görebilir
                $businesses = $this->businessModel->getBusinessesByUserId($_SESSION['user_id']);
            }

            // Get user model for owner information
            $userModel = $this->model('User');

            $data = [
                'title' => 'İşletmeler',
                'businesses' => $businesses,
                'userModel' => $userModel
            ];

            $this->view('businesses/index', $data);
        } catch (Exception $e) {
            Logger::error('Businesses view error: ' . $e->getMessage());
            redirect('pages/error');
        }
    }

    public function add() {
        try {
            // Admin değilse ve zaten bir işletmesi varsa, yeni işletme ekleyemez
            if (!isAdmin()) {
                $existingBusiness = $this->businessModel->getBusinessByUserId($_SESSION['user_id']);
                if ($existingBusiness) {
                    flash('business_message', 'Zaten bir işletmeniz var', 'alert alert-danger');
                    redirect('businesses');
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Sanitize POST data
                $_POST = $this->sanitizeInput($_POST);

                $data = [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'address' => $_POST['address'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'user_id' => $_SESSION['user_id'],
                    'name_err' => '',
                    'description_err' => '',
                    'address_err' => '',
                    'phone_err' => '',
                    'email_err' => ''
                ];

                // Validate data
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen işletme adını girin';
                }
                if(empty($data['address'])) {
                    $data['address_err'] = 'Lütfen adresi girin';
                }
                if(empty($data['phone'])) {
                    $data['phone_err'] = 'Lütfen telefon numarasını girin';
                }
                if(empty($data['email'])) {
                    $data['email_err'] = 'Lütfen email adresini girin';
                }

                // Make sure no errors
                if(empty($data['name_err']) && empty($data['address_err']) && 
                   empty($data['phone_err']) && empty($data['email_err'])) {
                    // Add business
                    if($this->businessModel->add($data)) {
                        flash('business_message', 'İşletme başarıyla eklendi');
                        redirect('businesses');
                    } else {
                        throw new Exception('İşletme eklenirken bir hata oluştu');
                    }
                } else {
                    // Load view with errors
                    $this->view('businesses/add', $data);
                }
            } else {
                $data = [
                    'name' => '',
                    'description' => '',
                    'address' => '',
                    'phone' => '',
                    'email' => '',
                    'name_err' => '',
                    'description_err' => '',
                    'address_err' => '',
                    'phone_err' => '',
                    'email_err' => ''
                ];

                $this->view('businesses/add', $data);
            }
        } catch (Exception $e) {
            Logger::error('Business add error: ' . $e->getMessage());
            flash('business_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('businesses');
        }
    }

    public function edit($id) {
        try {
            // Check for business access
            requireBusinessAccess($id);

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Sanitize POST data
                $_POST = $this->sanitizeInput($_POST);

                $data = [
                    'id' => $id,
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'address' => $_POST['address'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'current_logo' => $this->businessModel->getBusinessById($id)->logo,
                    'name_err' => '',
                    'description_err' => '',
                    'address_err' => '',
                    'phone_err' => '',
                    'email_err' => '',
                    'logo_err' => ''
                ];

                // Validate data
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen işletme adını girin';
                }
                if(empty($data['address'])) {
                    $data['address_err'] = 'Lütfen adresi girin';
                }
                if(empty($data['phone'])) {
                    $data['phone_err'] = 'Lütfen telefon numarasını girin';
                }
                if(empty($data['email'])) {
                    $data['email_err'] = 'Lütfen email adresini girin';
                }

                // Handle logo upload if a new file is uploaded
                if(!empty($_FILES['logo']['name'])) {
                    $logoResult = $this->handleLogoUpload($id);
                    if($logoResult['success']) {
                        $data['logo'] = $logoResult['filename'];
                    } else {
                        $data['logo_err'] = $logoResult['error'];
                    }
                }

                // Make sure no errors
                if(empty($data['name_err']) && empty($data['address_err']) && 
                   empty($data['phone_err']) && empty($data['email_err']) && 
                   empty($data['logo_err'])) {
                    // Update business
                    if($this->businessModel->update($data)) {
                        flash('business_message', 'İşletme başarıyla güncellendi');
                        redirect('businesses');
                    } else {
                        throw new Exception('İşletme güncellenirken bir hata oluştu');
                    }
                } else {
                    // Load view with errors
                    $this->view('businesses/edit', $data);
                }
            } else {
                // Get business
                $business = $this->businessModel->getBusinessById($id);
                if (!$business) {
                    throw new Exception('İşletme bulunamadı');
                }

                $data = [
                    'id' => $id,
                    'name' => $business->name,
                    'description' => $business->description,
                    'address' => $business->address,
                    'phone' => $business->phone,
                    'email' => $business->email,
                    'current_logo' => $business->logo,
                    'name_err' => '',
                    'description_err' => '',
                    'address_err' => '',
                    'phone_err' => '',
                    'email_err' => '',
                    'logo_err' => ''
                ];

                $this->view('businesses/edit', $data);
            }
        } catch (Exception $e) {
            Logger::error('Business edit error: ' . $e->getMessage());
            flash('business_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('businesses');
        }
    }

    public function delete($id) {
        try {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Check for business access
                requireBusinessAccess($id);

                // Delete business
                if($this->businessModel->delete($id)) {
                    flash('business_message', 'İşletme başarıyla silindi');
                } else {
                    throw new Exception('İşletme silinirken bir hata oluştu');
                }
            }

            redirect('businesses');
        } catch (Exception $e) {
            Logger::error('Business delete error: ' . $e->getMessage());
            flash('business_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('businesses');
        }
    }

    /**
     * Show business management page
     */
    public function manage($id) {
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        $business = $this->businessModel->getBusinessById($id);
        
        // Check for owner
        if (!$this->businessModel->verifyBusinessOwner($id, $_SESSION['user_id'])) {
            redirect('businesses');
        }

        // Get categories
        $categories = $this->categoryModel->getCategoriesByBusiness($id);

        // Get total menu items
        $totalItems = 0;
        foreach ($categories as $category) {
            $items = $this->menuItemModel->getMenuItemsByCategory($category->id);
            $totalItems += count($items);
        }

        // Get view count (you may implement this differently)
        $viewCount = 0; // Placeholder for now

        $data = [
            'business' => $business,
            'categories' => $categories,
            'totalItems' => $totalItems,
            'viewCount' => $viewCount
        ];

        $this->view('businesses/manage', $data);
    }

    // Handle logo upload
    private function handleLogoUpload($businessId) {
        $uploadDir = 'public/uploads/logos/';
        if(!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Check file size (2MB limit)
        if ($_FILES['logo']['size'] > 2097152) {
            return [
                'success' => false,
                'error' => 'Logo dosyası 2MB\'dan büyük olamaz',
                'filename' => null
            ];
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['logo']['type'], $allowedTypes)) {
            return [
                'success' => false,
                'error' => 'Sadece JPG, PNG ve GIF formatları kabul edilir',
                'filename' => null
            ];
        }

        $fileName = $businessId . '_' . time() . '_' . $_FILES['logo']['name'];
        $targetFile = $uploadDir . $fileName;

        if(move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            return [
                'success' => true,
                'error' => null,
                'filename' => $fileName
            ];
        }

        return [
            'success' => false,
            'error' => 'Logo yüklenirken bir hata oluştu',
            'filename' => null
        ];
    }
}
