<?php
class Categories extends Controller {
    private $categoryModel;
    private $businessModel;
    private $menuItemModel;
    private $menuModel;

    public function __construct() {
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        $this->categoryModel = $this->model('Category');
        $this->businessModel = $this->model('Business');
        $this->menuItemModel = $this->model('MenuItem');
        $this->menuModel = $this->model('Menu');
    }

    // Sanitize input data
    private function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public function index($businessId) {
        try {
            // Get business
            $business = $this->businessModel->getBusinessById($businessId);
            if (!$business) {
                throw new Exception('İşletme bulunamadı');
            }
            
            // Check if user owns the business
            if(!$this->businessModel->isOwner($_SESSION['user_id'], $business->id) && !isAdmin()) {
                redirect('businesses');
            }

            $categories = $this->categoryModel->getCategoriesByBusinessId($businessId);

            $data = [
                'business' => $business,
                'categories' => $categories
            ];

            $this->view('categories/index', $data);
        } catch (Exception $e) {
            Logger::error('Categories view error: ' . $e->getMessage());
            redirect('pages/error');
        }
    }

    public function add($businessId) {
        try {
            // Get business
            $business = $this->businessModel->getBusinessById($businessId);
            if (!$business) {
                throw new Exception('İşletme bulunamadı');
            }
            
            // Check if user owns the business
            if(!$this->businessModel->isOwner($_SESSION['user_id'], $business->id) && !isAdmin()) {
                redirect('businesses');
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Sanitize POST data
                $postData = $this->sanitizeInput($_POST);

                // Get menu for this business
                $menu = $this->menuModel->getByBusinessId($businessId);
                if (!$menu) {
                    throw new Exception('Menü bulunamadı');
                }

                $data = [
                    'business_id' => $businessId,
                    'menu_id' => $menu->id,
                    'name' => $postData['name'] ?? '',
                    'description' => $postData['description'] ?? '',
                    'name_err' => '',
                    'description_err' => ''
                ];

                // Validate name
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen kategori adını girin';
                }

                // Make sure no errors
                if(empty($data['name_err'])) {
                    // Add category
                    if($this->categoryModel->create($data)) {
                        flash('category_message', 'Kategori başarıyla eklendi');
                        redirect('categories/index/' . $businessId);
                    } else {
                        throw new Exception('Kategori eklenirken bir hata oluştu');
                    }
                }

                $this->view('categories/add', $data);
            } else {
                $data = [
                    'business_id' => $businessId,
                    'name' => '',
                    'description' => '',
                    'name_err' => '',
                    'description_err' => ''
                ];

                $this->view('categories/add', $data);
            }
        } catch (Exception $e) {
            Logger::error('Category add error: ' . $e->getMessage());
            flash('category_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('categories/index/' . $businessId);
        }
    }

    public function edit($id) {
        try {
            // Get category
            $category = $this->categoryModel->getById($id);
            if (!$category) {
                throw new Exception('Kategori bulunamadı');
            }

            // Get business
            $business = $this->businessModel->getBusinessById($category->business_id);
            if (!$business) {
                throw new Exception('İşletme bulunamadı');
            }
            
            // Check if user owns the business
            if(!$this->businessModel->isOwner($_SESSION['user_id'], $business->id) && !isAdmin()) {
                redirect('businesses');
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Sanitize POST data
                $postData = $this->sanitizeInput($_POST);

                $data = [
                    'id' => $id,
                    'business_id' => $category->business_id,
                    'name' => $postData['name'] ?? '',
                    'description' => $postData['description'] ?? '',
                    'name_err' => '',
                    'description_err' => ''
                ];

                // Validate name
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen kategori adını girin';
                }

                // Make sure no errors
                if(empty($data['name_err'])) {
                    // Update category
                    $updateData = [
                        'name' => $data['name'],
                        'description' => $data['description']
                    ];
                    if($this->categoryModel->update($id, $updateData)) {
                        flash('category_message', 'Kategori başarıyla güncellendi');
                        redirect('categories/index/' . $category->business_id);
                    } else {
                        throw new Exception('Kategori güncellenirken bir hata oluştu');
                    }
                }

                $this->view('categories/edit', $data);
            } else {
                $data = [
                    'id' => $id,
                    'business_id' => $category->business_id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'name_err' => '',
                    'description_err' => ''
                ];

                $this->view('categories/edit', $data);
            }
        } catch (Exception $e) {
            Logger::error('Category edit error: ' . $e->getMessage());
            flash('category_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('categories/index/' . $category->business_id);
        }
    }

    public function delete($id) {
        try {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get category
                $category = $this->categoryModel->getById($id);
                if (!$category) {
                    throw new Exception('Kategori bulunamadı');
                }

                // Get business
                $business = $this->businessModel->getBusinessById($category->business_id);
                if (!$business) {
                    throw new Exception('İşletme bulunamadı');
                }
                
                // Check if user owns the business
                if(!$this->businessModel->isOwner($_SESSION['user_id'], $business->id) && !isAdmin()) {
                    redirect('businesses');
                }

                // Delete category
                if($this->categoryModel->delete($id)) {
                    flash('category_message', 'Kategori başarıyla silindi');
                } else {
                    throw new Exception('Kategori silinirken bir hata oluştu');
                }
            }

            redirect('categories/index/' . $category->business_id);
        } catch (Exception $e) {
            Logger::error('Category delete error: ' . $e->getMessage());
            flash('category_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('categories/index/' . $category->business_id);
        }
    }

    public function reorder($businessId) {
        try {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get business
                $business = $this->businessModel->getBusinessById($businessId);
                if (!$business) {
                    throw new Exception('İşletme bulunamadı');
                }
                
                // Check if user owns the business
                if(!$this->businessModel->isOwner($_SESSION['user_id'], $business->id) && !isAdmin()) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim']);
                    return;
                }

                // Get POST data
                $input = json_decode(file_get_contents('php://input'), true);
                if (!isset($input['categoryIds']) || !is_array($input['categoryIds'])) {
                    throw new Exception('Geçersiz veri formatı');
                }

                // Update order
                $order = 1;
                foreach ($input['categoryIds'] as $categoryId) {
                    $this->categoryModel->updateOrder($categoryId, $order);
                    $order++;
                }

                echo json_encode(['success' => true]);
            }
        } catch (Exception $e) {
            Logger::error('Category reorder error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Bir hata oluştu']);
        }
    }
}
