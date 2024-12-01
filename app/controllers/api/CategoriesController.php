<?php
class CategoriesController extends ApiController {
    private $categoryModel;
    private $businessModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->categoryModel = $this->model('Category');
        $this->businessModel = $this->model('Business');
    }
    
    /**
     * Get all categories for a business
     * GET /api/categories?business_id={id}
     */
    public function index() {
        $this->requireMethod('GET');
        
        $businessId = $this->requestData['business_id'] ?? null;
        if (!$businessId) {
            $this->sendError('Business ID is required');
        }
        
        $page = isset($this->requestData['page']) ? (int)$this->requestData['page'] : 1;
        $limit = isset($this->requestData['limit']) ? (int)$this->requestData['limit'] : 10;
        
        $categories = $this->categoryModel->getByBusinessId($businessId, $page, $limit);
        $total = $this->categoryModel->getTotalByBusiness($businessId);
        
        $this->sendResponse([
            'categories' => $categories,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Get single category with its menu items
     * GET /api/categories/{id}
     */
    public function get($id) {
        $this->requireMethod('GET');
        
        $category = $this->categoryModel->getById($id);
        
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        // Get menu items if requested
        if (isset($this->requestData['include_items']) && $this->requestData['include_items']) {
            $category->menu_items = $this->categoryModel->getCategoryItems($id);
        }
        
        $this->sendResponse($category);
    }
    
    /**
     * Create new category
     * POST /api/categories
     */
    public function create() {
        $this->requireMethod('POST');
        
        // Validate required fields
        $required = ['name', 'business_id'];
        $validation = $this->validateRequired($required);
        
        if ($validation !== true) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $validation
            ]);
        }
        
        // Check if business exists
        $business = $this->businessModel->getById($this->requestData['business_id']);
        if (!$business) {
            $this->sendError('Invalid business ID');
        }
        
        // Handle image upload if present
        if (isset($_FILES['image'])) {
            $uploadResult = $this->handleFileUpload($_FILES['image'], '../public/uploads/categories/');
            if ($uploadResult['status']) {
                $this->requestData['image'] = $uploadResult['filename'];
            }
        }
        
        // Create category
        $categoryId = $this->categoryModel->create($this->requestData);
        
        if (!$categoryId) {
            $this->sendError('Failed to create category');
        }
        
        $category = $this->categoryModel->getById($categoryId);
        $this->sendResponse($category, 201, 'Category created successfully');
    }
    
    /**
     * Update category
     * PUT /api/categories/{id}
     */
    public function update($id) {
        $this->requireMethod('PUT');
        
        // Check if category exists
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        // Handle image upload if present
        if (isset($_FILES['image'])) {
            $uploadResult = $this->handleFileUpload($_FILES['image'], '../public/uploads/categories/');
            if ($uploadResult['status']) {
                $this->requestData['image'] = $uploadResult['filename'];
                // Delete old image
                if ($category->image) {
                    unlink('../public/uploads/categories/' . $category->image);
                }
            }
        }
        
        // Update category
        $success = $this->categoryModel->update($id, $this->requestData);
        
        if (!$success) {
            $this->sendError('Failed to update category');
        }
        
        $category = $this->categoryModel->getById($id);
        $this->sendResponse($category, 200, 'Category updated successfully');
    }
    
    /**
     * Delete category
     * DELETE /api/categories/{id}
     */
    public function delete($id) {
        $this->requireMethod('DELETE');
        
        // Check if category exists
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        // Check if category has menu items
        $items = $this->categoryModel->getCategoryItems($id);
        if (!empty($items)) {
            $this->sendError('Cannot delete category with menu items. Please delete or move the menu items first.');
        }
        
        // Delete category image if exists
        if ($category->image) {
            unlink('../public/uploads/categories/' . $category->image);
        }
        
        // Delete category
        $success = $this->categoryModel->delete($id);
        
        if (!$success) {
            $this->sendError('Failed to delete category');
        }
        
        $this->sendResponse(null, 200, 'Category deleted successfully');
    }
    
    /**
     * Update category sort order
     * PUT /api/categories/{id}/sort-order
     */
    public function updateSortOrder($id) {
        $this->requireMethod('PUT');
        
        // Check if category exists
        $category = $this->categoryModel->getById($id);
        if (!$category) {
            $this->sendError('Category not found', 404);
        }
        
        if (!isset($this->requestData['sort_order'])) {
            $this->sendError('Sort order is required');
        }
        
        // Update sort order
        $success = $this->categoryModel->updateSortOrder($id, $this->requestData['sort_order']);
        
        if (!$success) {
            $this->sendError('Failed to update sort order');
        }
        
        $category = $this->categoryModel->getById($id);
        $this->sendResponse($category, 200, 'Sort order updated successfully');
    }
}
