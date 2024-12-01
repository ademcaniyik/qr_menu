<?php
class MenuItemsController extends ApiController {
    private $menuItemModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->menuItemModel = $this->model('MenuItem');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * Get all menu items for a category
     * GET /api/menu-items?category_id={id}
     */
    public function index() {
        $this->requireMethod('GET');
        
        $categoryId = $this->requestData['category_id'] ?? null;
        if (!$categoryId) {
            $this->sendError('Category ID is required');
        }
        
        $page = isset($this->requestData['page']) ? (int)$this->requestData['page'] : 1;
        $limit = isset($this->requestData['limit']) ? (int)$this->requestData['limit'] : 10;
        
        $items = $this->menuItemModel->getByCategoryId($categoryId, $page, $limit);
        $total = $this->menuItemModel->getTotalByCategory($categoryId);
        
        $this->sendResponse([
            'items' => $items,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Get single menu item
     * GET /api/menu-items/{id}
     */
    public function get($id) {
        $this->requireMethod('GET');
        
        $menuItem = $this->menuItemModel->getById($id);
        
        if (!$menuItem) {
            $this->sendError('Menu item not found', 404);
        }
        
        $this->sendResponse($menuItem);
    }
    
    /**
     * Create new menu item
     * POST /api/menu-items
     */
    public function create() {
        $this->requireMethod('POST');
        
        // Validate required fields
        $required = ['name', 'price', 'category_id'];
        $validation = $this->validateRequired($required);
        
        if ($validation !== true) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $validation
            ]);
        }
        
        // Check if category exists
        $category = $this->categoryModel->getById($this->requestData['category_id']);
        if (!$category) {
            $this->sendError('Invalid category ID');
        }
        
        // Handle image upload if present
        if (isset($_FILES['image'])) {
            $uploadResult = $this->handleFileUpload($_FILES['image'], '../public/uploads/menu_items/');
            if ($uploadResult['status']) {
                $this->requestData['image'] = $uploadResult['filename'];
            }
        }
        
        // Create menu item
        $itemId = $this->menuItemModel->create($this->requestData);
        
        if (!$itemId) {
            $this->sendError('Failed to create menu item');
        }
        
        $item = $this->menuItemModel->getById($itemId);
        $this->sendResponse($item, 201, 'Menu item created successfully');
    }
    
    /**
     * Update menu item
     * PUT /api/menu-items/{id}
     */
    public function update($id) {
        $this->requireMethod('PUT');
        
        // Check if item exists
        $item = $this->menuItemModel->getById($id);
        if (!$item) {
            $this->sendError('Menu item not found', 404);
        }
        
        // Handle image upload if present
        if (isset($_FILES['image'])) {
            $uploadResult = $this->handleFileUpload($_FILES['image'], '../public/uploads/menu_items/');
            if ($uploadResult['status']) {
                $this->requestData['image'] = $uploadResult['filename'];
                // Delete old image
                if ($item['image']) {
                    unlink('../public/uploads/menu_items/' . $item['image']);
                }
            }
        }
        
        // Update menu item
        $success = $this->menuItemModel->update($id, $this->requestData);
        
        if (!$success) {
            $this->sendError('Failed to update menu item');
        }
        
        $item = $this->menuItemModel->getById($id);
        $this->sendResponse($item, 200, 'Menu item updated successfully');
    }
    
    /**
     * Delete menu item
     * DELETE /api/menu-items/{id}
     */
    public function delete($id) {
        $this->requireMethod('DELETE');
        
        // Check if item exists
        $item = $this->menuItemModel->getById($id);
        if (!$item) {
            $this->sendError('Menu item not found', 404);
        }
        
        // Delete item image if exists
        if ($item['image']) {
            unlink('../public/uploads/menu_items/' . $item['image']);
        }
        
        // Delete menu item
        $success = $this->menuItemModel->delete($id);
        
        if (!$success) {
            $this->sendError('Failed to delete menu item');
        }
        
        $this->sendResponse(null, 200, 'Menu item deleted successfully');
    }
    
    /**
     * Update menu item availability
     * PUT /api/menu-items/{id}/availability
     */
    public function updateAvailability($id) {
        $this->requireMethod('PUT');
        
        // Check if item exists
        $item = $this->menuItemModel->getById($id);
        if (!$item) {
            $this->sendError('Menu item not found', 404);
        }
        
        // Validate availability status
        if (!isset($this->requestData['available'])) {
            $this->sendError('Availability status is required');
        }
        
        // Update availability
        $success = $this->menuItemModel->updateAvailability($id, $this->requestData['available']);
        
        if (!$success) {
            $this->sendError('Failed to update availability');
        }
        
        $item = $this->menuItemModel->getById($id);
        $this->sendResponse($item, 200, 'Availability updated successfully');
    }
}
