<?php
require_once APPROOT . '/libraries/phpqrcode/qrlib.php';

class TablesController extends ApiController {
    private $tableModel;
    private $businessModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->tableModel = $this->model('Table');
        $this->businessModel = $this->model('Business');
    }
    
    /**
     * Get all tables for a business
     * GET /api/tables?business_id={id}
     */
    public function index() {
        $this->requireMethod('GET');
        
        $businessId = $this->requestData['business_id'] ?? null;
        if (!$businessId) {
            $this->sendError('Business ID is required');
        }
        
        $page = isset($this->requestData['page']) ? (int)$this->requestData['page'] : 1;
        $limit = isset($this->requestData['limit']) ? (int)$this->requestData['limit'] : 10;
        
        $tables = $this->tableModel->getByBusinessId($businessId, $page, $limit);
        $total = $this->tableModel->getTotalByBusiness($businessId);
        
        $this->sendResponse([
            'tables' => $tables,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Get single table
     * GET /api/tables/{id}
     */
    public function get($id) {
        $this->requireMethod('GET');
        
        $table = $this->tableModel->getById($id);
        
        if (!$table) {
            $this->sendError('Table not found', 404);
        }
        
        $this->sendResponse($table);
    }
    
    /**
     * Create new table
     * POST /api/tables
     */
    public function create() {
        $this->requireMethod('POST');
        
        // Validate required fields
        $required = ['name', 'business_id', 'capacity'];
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
        
        // Create table
        $tableId = $this->tableModel->create($this->requestData);
        
        if (!$tableId) {
            $this->sendError('Failed to create table');
        }
        
        // Generate QR code for the table
        $qrCode = $this->generateQRCode($tableId);
        
        // Update table with QR code
        $this->tableModel->updateQRCode($tableId, $qrCode);
        
        $table = $this->tableModel->getById($tableId);
        $this->sendResponse($table, 201, 'Table created successfully');
    }
    
    /**
     * Update table
     * PUT /api/tables/{id}
     */
    public function update($id) {
        $this->requireMethod('PUT');
        
        // Check if table exists
        $table = $this->tableModel->getById($id);
        if (!$table) {
            $this->sendError('Table not found', 404);
        }
        
        // Update table
        $success = $this->tableModel->update($id, $this->requestData);
        
        if (!$success) {
            $this->sendError('Failed to update table');
        }
        
        $table = $this->tableModel->getById($id);
        $this->sendResponse($table, 200, 'Table updated successfully');
    }
    
    /**
     * Delete table
     * DELETE /api/tables/{id}
     */
    public function delete($id) {
        $this->requireMethod('DELETE');
        
        // Check if table exists
        $table = $this->tableModel->getById($id);
        if (!$table) {
            $this->sendError('Table not found', 404);
        }
        
        // Check if table has active orders
        $activeOrders = $this->tableModel->hasActiveOrders($id);
        if ($activeOrders) {
            $this->sendError('Cannot delete table with active orders');
        }
        
        // Delete QR code file if exists
        if ($table->qr_code) {
            unlink('../public/uploads/qr_codes/' . $table->qr_code);
        }
        
        // Delete table
        $success = $this->tableModel->delete($id);
        
        if (!$success) {
            $this->sendError('Failed to delete table');
        }
        
        $this->sendResponse(null, 200, 'Table deleted successfully');
    }
    
    /**
     * Regenerate QR code for table
     * POST /api/tables/{id}/qr-code
     */
    public function regenerateQRCode($id) {
        $this->requireMethod('POST');
        
        // Check if table exists
        $table = $this->tableModel->getById($id);
        if (!$table) {
            $this->sendError('Table not found', 404);
        }
        
        // Delete old QR code if exists
        if ($table->qr_code) {
            unlink('../public/uploads/qr_codes/' . $table->qr_code);
        }
        
        // Generate new QR code
        $qrCode = $this->generateQRCode($id);
        
        // Update table with new QR code
        $success = $this->tableModel->updateQRCode($id, $qrCode);
        
        if (!$success) {
            $this->sendError('Failed to regenerate QR code');
        }
        
        $table = $this->tableModel->getById($id);
        $this->sendResponse($table, 200, 'QR code regenerated successfully');
    }
    
    /**
     * Generate QR code for table
     */
    private function generateQRCode($tableId) {
        // Generate unique filename
        $filename = uniqid('qr_') . '.png';
        $path = '../public/uploads/qr_codes/' . $filename;
        
        // Create QR code content (URL to menu)
        $url = URLROOT . '/menu/' . $tableId;
        
        // Generate QR code
        QRcode::png($url, $path, QR_ECLEVEL_L, 10);
        
        return $filename;
    }
    
    /**
     * Get table status
     * GET /api/tables/{id}/status
     */
    public function getStatus($id) {
        $this->requireMethod('GET');
        
        // Check if table exists
        $table = $this->tableModel->getById($id);
        if (!$table) {
            $this->sendError('Table not found', 404);
        }
        
        // Get active orders for table
        $activeOrders = $this->tableModel->getActiveOrders($id);
        
        $this->sendResponse([
            'table' => $table,
            'is_occupied' => !empty($activeOrders),
            'active_orders' => $activeOrders
        ]);
    }
}
