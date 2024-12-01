<?php
class BusinessesController extends ApiController {
    private $businessModel;
    private $menuItemModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->businessModel = $this->model('Business');
        $this->menuItemModel = $this->model('MenuItem');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * Get all businesses
     * GET /api/businesses
     */
    public function index() {
        $this->requireMethod('GET');
        
        $page = isset($this->requestData['page']) ? (int)$this->requestData['page'] : 1;
        $limit = isset($this->requestData['limit']) ? (int)$this->requestData['limit'] : 10;
        
        $businesses = $this->businessModel->getAll($page, $limit);
        $total = $this->businessModel->getTotal();
        
        $this->sendResponse([
            'businesses' => $businesses,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Get single business
     * GET /api/businesses/{id}
     */
    public function get($id) {
        $this->requireMethod('GET');
        
        $business = $this->businessModel->getById($id);
        
        if (!$business) {
            $this->sendError('Business not found', 404);
        }
        
        $this->sendResponse($business);
    }
    
    /**
     * Create new business
     * POST /api/businesses
     */
    public function create() {
        $this->requireMethod('POST');
        
        // Validate required fields
        $required = ['name', 'email', 'phone', 'address'];
        $validation = $this->validateRequired($required);
        
        if ($validation !== true) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $validation
            ]);
        }
        
        // Create business
        $businessId = $this->businessModel->create($this->requestData);
        
        if (!$businessId) {
            $this->sendError('Failed to create business');
        }
        
        $business = $this->businessModel->getById($businessId);
        $this->sendResponse($business, 201, 'Business created successfully');
    }
    
    /**
     * Update business
     * PUT /api/businesses/{id}
     */
    public function update($id) {
        $this->requireMethod('PUT');
        
        // Check if business exists
        $business = $this->businessModel->getById($id);
        if (!$business) {
            $this->sendError('Business not found', 404);
        }
        
        // Update business
        $success = $this->businessModel->update($id, $this->requestData);
        
        if (!$success) {
            $this->sendError('Failed to update business');
        }
        
        $business = $this->businessModel->getById($id);
        $this->sendResponse($business, 200, 'Business updated successfully');
    }
    
    /**
     * Delete business
     * DELETE /api/businesses/{id}
     */
    public function delete($id) {
        $this->requireMethod('DELETE');
        
        // Check if business exists
        $business = $this->businessModel->getById($id);
        if (!$business) {
            $this->sendError('Business not found', 404);
        }
        
        // Delete business
        $success = $this->businessModel->delete($id);
        
        if (!$success) {
            $this->sendError('Failed to delete business');
        }
        
        $this->sendResponse(null, 200, 'Business deleted successfully');
    }
    
    /**
     * Get business menu
     * GET /api/businesses/{id}/menu
     */
    public function menu($id) {
        $this->requireMethod('GET');
        
        // Check if business exists
        $business = $this->businessModel->getById($id);
        if (!$business) {
            $this->sendError('Business not found', 404);
        }
        
        // Get categories with menu items
        $categories = $this->categoryModel->getByBusiness($id);
        
        foreach ($categories as &$category) {
            $category['items'] = $this->menuItemModel->getByCategoryId($category['id']);
        }
        
        $this->sendResponse([
            'business' => $business,
            'categories' => $categories
        ]);
    }
    
    /**
     * Get business statistics
     * GET /api/businesses/{id}/stats
     */
    public function stats($id) {
        $this->requireMethod('GET');
        
        // Check if business exists
        $business = $this->businessModel->getById($id);
        if (!$business) {
            $this->sendError('Business not found', 404);
        }
        
        // Get date range from request
        $startDate = $this->requestData['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $this->requestData['end_date'] ?? date('Y-m-d');
        
        // Get statistics
        $stats = [
            'menu_views' => $this->businessModel->getMenuViews($id, $startDate, $endDate),
            'popular_items' => $this->menuItemModel->getPopularItems($id, $startDate, $endDate),
            'view_by_hour' => $this->businessModel->getViewsByHour($id, $startDate, $endDate)
        ];
        
        $this->sendResponse($stats);
    }
}
