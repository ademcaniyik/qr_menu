<?php
class MenuItems extends Controller {
    private $menuItemModel;
    private $categoryModel;
    private $businessModel;

    public function __construct() {
        if(!isLoggedIn()) {
            redirect('users/login');
        }

        $this->menuItemModel = $this->model('MenuItem');
        $this->categoryModel = $this->model('Category');
        $this->businessModel = $this->model('Business');
    }

    // Sanitize input data
    private function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    // Show all menu items for a category
    public function index($categoryId) {
        try {
            // Get category
            $category = $this->categoryModel->getById($categoryId);
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

            $menuItems = $this->menuItemModel->getMenuItemsByCategoryId($categoryId);

            $data = [
                'category' => $category,
                'business' => $business,
                'menuItems' => $menuItems
            ];

            $this->view('menu_items/index', $data);
        } catch (Exception $e) {
            Logger::error('Menu items view error: ' . $e->getMessage());
            redirect('pages/error');
        }
    }

    // Add new menu item
    public function add($categoryId) {
        try {
            // Get category
            $category = $this->categoryModel->getById($categoryId);
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
                    'category_id' => $categoryId,
                    'name' => $postData['name'] ?? '',
                    'description' => $postData['description'] ?? '',
                    'price' => $postData['price'] ?? '',
                    'image' => $_FILES['image']['name'] ?? '',
                    'name_err' => '',
                    'description_err' => '',
                    'price_err' => '',
                    'image_err' => ''
                ];

                // Validate name
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen menü öğesi adını girin';
                }

                // Validate price
                if(empty($data['price'])) {
                    $data['price_err'] = 'Lütfen fiyat girin';
                } elseif(!is_numeric($data['price'])) {
                    $data['price_err'] = 'Fiyat sayısal bir değer olmalıdır';
                }

                // Check for image
                if(!empty($_FILES['image']['name'])) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $maxSize = 5 * 1024 * 1024; // 5MB

                    if(!in_array($_FILES['image']['type'], $allowedTypes)) {
                        $data['image_err'] = 'Sadece JPG, PNG ve GIF dosyaları yüklenebilir';
                    } elseif($_FILES['image']['size'] > $maxSize) {
                        $data['image_err'] = 'Dosya boyutu 5MB\'dan küçük olmalıdır';
                    }
                }

                // Make sure no errors
                if(empty($data['name_err']) && empty($data['price_err']) && empty($data['image_err'])) {
                    // Handle file upload
                    if(!empty($_FILES['image']['name'])) {
                        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $fileName = uniqid() . '.' . $fileExt;
                        $uploadDir = 'uploads/menu_items/';
                        
                        if(!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                            $data['image'] = $fileName;
                        } else {
                            $data['image_err'] = 'Dosya yüklenirken bir hata oluştu';
                        }
                    }

                    if(empty($data['image_err'])) {
                        // Add menu item
                        if($this->menuItemModel->add($data)) {
                            flash('menu_item_message', 'Menü öğesi başarıyla eklendi');
                            redirect('menu-items/index/' . $categoryId);
                        } else {
                            throw new Exception('Menü öğesi eklenirken bir hata oluştu');
                        }
                    }
                }

                $this->view('menu_items/add', $data);
            } else {
                $data = [
                    'category_id' => $categoryId,
                    'name' => '',
                    'description' => '',
                    'price' => '',
                    'image' => '',
                    'name_err' => '',
                    'description_err' => '',
                    'price_err' => '',
                    'image_err' => ''
                ];

                $this->view('menu_items/add', $data);
            }
        } catch (Exception $e) {
            Logger::error('Menu item add error: ' . $e->getMessage());
            flash('menu_item_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('menu-items/index/' . $categoryId);
        }
    }

    // Edit menu item
    public function edit($id) {
        try {
            // Get menu item
            $menuItem = $this->menuItemModel->getById($id);
            if (!$menuItem) {
                throw new Exception('Menü öğesi bulunamadı');
            }

            // Get category
            $category = $this->categoryModel->getById($menuItem->category_id);
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
                    'category_id' => $menuItem->category_id,
                    'name' => $postData['name'] ?? '',
                    'description' => $postData['description'] ?? '',
                    'price' => $postData['price'] ?? '',
                    'current_image' => $menuItem->image,
                    'name_err' => '',
                    'description_err' => '',
                    'price_err' => '',
                    'image_err' => ''
                ];

                // Validate name
                if(empty($data['name'])) {
                    $data['name_err'] = 'Lütfen menü öğesi adını girin';
                }

                // Validate price
                if(empty($data['price'])) {
                    $data['price_err'] = 'Lütfen fiyat girin';
                } elseif(!is_numeric($data['price'])) {
                    $data['price_err'] = 'Fiyat sayısal bir değer olmalıdır';
                }

                // Check for image
                if(!empty($_FILES['image']['name'])) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    $maxSize = 5 * 1024 * 1024; // 5MB

                    if(!in_array($_FILES['image']['type'], $allowedTypes)) {
                        $data['image_err'] = 'Sadece JPG, PNG ve GIF dosyaları yüklenebilir';
                    } elseif($_FILES['image']['size'] > $maxSize) {
                        $data['image_err'] = 'Dosya boyutu 5MB\'dan küçük olmalıdır';
                    }
                }

                // Make sure no errors
                if(empty($data['name_err']) && empty($data['price_err']) && empty($data['image_err'])) {
                    // Handle file upload
                    if(!empty($_FILES['image']['name'])) {
                        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $fileName = uniqid() . '.' . $fileExt;
                        $uploadDir = 'uploads/menu_items/';
                        
                        if(!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }
                        
                        if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                            // Delete old image
                            if(!empty($data['current_image'])) {
                                @unlink($uploadDir . $data['current_image']);
                            }
                            $data['image'] = $fileName;
                        } else {
                            $data['image_err'] = 'Dosya yüklenirken bir hata oluştu';
                        }
                    } else {
                        $data['image'] = $data['current_image'];
                    }

                    if(empty($data['image_err'])) {
                        // Update menu item
                        if($this->menuItemModel->update($data)) {
                            flash('menu_item_message', 'Menü öğesi başarıyla güncellendi');
                            redirect('menu-items/index/' . $menuItem->category_id);
                        } else {
                            throw new Exception('Menü öğesi güncellenirken bir hata oluştu');
                        }
                    }
                }

                $this->view('menu_items/edit', $data);
            } else {
                $data = [
                    'id' => $id,
                    'category_id' => $menuItem->category_id,
                    'name' => $menuItem->name,
                    'description' => $menuItem->description,
                    'price' => $menuItem->price,
                    'current_image' => $menuItem->image,
                    'name_err' => '',
                    'description_err' => '',
                    'price_err' => '',
                    'image_err' => ''
                ];

                $this->view('menu_items/edit', $data);
            }
        } catch (Exception $e) {
            Logger::error('Menu item edit error: ' . $e->getMessage());
            flash('menu_item_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('menu-items/index/' . $menuItem->category_id);
        }
    }

    // Delete menu item
    public function delete($id) {
        try {
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get menu item
                $menuItem = $this->menuItemModel->getById($id);
                if (!$menuItem) {
                    throw new Exception('Menü öğesi bulunamadı');
                }

                // Get category
                $category = $this->categoryModel->getById($menuItem->category_id);
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

                // Delete menu item
                if($this->menuItemModel->delete($id)) {
                    // Delete image if exists
                    if(!empty($menuItem->image)) {
                        @unlink('uploads/menu_items/' . $menuItem->image);
                    }
                    
                    flash('menu_item_message', 'Menü öğesi başarıyla silindi');
                } else {
                    throw new Exception('Menü öğesi silinirken bir hata oluştu');
                }
            }

            redirect('menu-items/index/' . $menuItem->category_id);
        } catch (Exception $e) {
            Logger::error('Menu item delete error: ' . $e->getMessage());
            flash('menu_item_message', 'Bir hata oluştu', 'alert alert-danger');
            redirect('menu-items/index/' . $menuItem->category_id);
        }
    }
}
