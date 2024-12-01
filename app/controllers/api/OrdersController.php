<?php
class OrdersController extends ApiController {
    private $orderModel;
    private $businessModel;
    private $tableModel;
    
    public function __construct() {
        parent::__construct();
        
        $this->orderModel = $this->model('Order');
        $this->businessModel = $this->model('Business');
        $this->tableModel = $this->model('Table');
    }
    
    /**
     * Get all orders for a business
     * GET /api/orders?business_id={id}&status={status}
     */
    public function index() {
        $this->requireMethod('GET');
        
        $businessId = $this->requestData['business_id'] ?? null;
        if (!$businessId) {
            $this->sendError('Business ID is required');
        }
        
        $status = $this->requestData['status'] ?? null;
        $page = isset($this->requestData['page']) ? (int)$this->requestData['page'] : 1;
        $limit = isset($this->requestData['limit']) ? (int)$this->requestData['limit'] : 10;
        
        $orders = $this->orderModel->getByBusinessId($businessId, $status, $page, $limit);
        $total = $this->orderModel->getTotalByBusiness($businessId, $status);
        
        $this->sendResponse([
            'orders' => $orders,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }
    
    /**
     * Get single order with its items
     * GET /api/orders/{id}
     */
    public function get($id) {
        $this->requireMethod('GET');
        
        $order = $this->orderModel->getById($id);
        
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // Get order items
        $order->items = $this->orderModel->getOrderItems($id);
        
        $this->sendResponse($order);
    }
    
    /**
     * Create new order
     * POST /api/orders
     */
    public function create() {
        $this->requireMethod('POST');
        
        // Validate required fields
        $required = ['business_id', 'table_id', 'items'];
        $validation = $this->validateRequired($required);
        
        if ($validation !== true) {
            $this->sendError('Missing required fields', 400, [
                'missing_fields' => $validation
            ]);
        }
        
        // Validate items array
        if (!is_array($this->requestData['items']) || empty($this->requestData['items'])) {
            $this->sendError('Items must be a non-empty array');
        }
        
        // Check if business exists
        $business = $this->businessModel->getById($this->requestData['business_id']);
        if (!$business) {
            $this->sendError('Invalid business ID');
        }
        
        // Check if table exists and belongs to business
        $table = $this->tableModel->getById($this->requestData['table_id']);
        if (!$table || $table->business_id != $this->requestData['business_id']) {
            $this->sendError('Invalid table ID');
        }
        
        // Create order
        $orderId = $this->orderModel->create($this->requestData);
        
        if (!$orderId) {
            $this->sendError('Failed to create order');
        }
        
        // Add order items
        foreach ($this->requestData['items'] as $item) {
            if (!isset($item['menu_item_id']) || !isset($item['quantity'])) {
                continue;
            }
            
            $this->orderModel->addOrderItem($orderId, $item['menu_item_id'], $item['quantity'], $item['notes'] ?? null);
        }
        
        $order = $this->orderModel->getById($orderId);
        $order->items = $this->orderModel->getOrderItems($orderId);
        
        $this->sendResponse($order, 201, 'Order created successfully');
    }
    
    /**
     * Update order status
     * PUT /api/orders/{id}/status
     */
    public function updateStatus($id) {
        $this->requireMethod('PUT');
        
        // Check if order exists
        $order = $this->orderModel->getById($id);
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        if (!isset($this->requestData['status'])) {
            $this->sendError('Status is required');
        }
        
        // Validate status
        $validStatuses = ['pending', 'preparing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($this->requestData['status'], $validStatuses)) {
            $this->sendError('Invalid status. Valid statuses are: ' . implode(', ', $validStatuses));
        }
        
        // Update status
        $success = $this->orderModel->updateStatus($id, $this->requestData['status']);
        
        if (!$success) {
            $this->sendError('Failed to update status');
        }
        
        $order = $this->orderModel->getById($id);
        $this->sendResponse($order, 200, 'Status updated successfully');
    }
    
    /**
     * Add items to existing order
     * POST /api/orders/{id}/items
     */
    public function addItems($id) {
        $this->requireMethod('POST');
        
        // Check if order exists
        $order = $this->orderModel->getById($id);
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // Validate items array
        if (!isset($this->requestData['items']) || !is_array($this->requestData['items']) || empty($this->requestData['items'])) {
            $this->sendError('Items must be a non-empty array');
        }
        
        // Add items
        foreach ($this->requestData['items'] as $item) {
            if (!isset($item['menu_item_id']) || !isset($item['quantity'])) {
                continue;
            }
            
            $this->orderModel->addOrderItem($id, $item['menu_item_id'], $item['quantity'], $item['notes'] ?? null);
        }
        
        $order = $this->orderModel->getById($id);
        $order->items = $this->orderModel->getOrderItems($id);
        
        $this->sendResponse($order, 200, 'Items added successfully');
    }
    
    /**
     * Remove item from order
     * DELETE /api/orders/{id}/items/{item_id}
     */
    public function removeItem($id, $itemId) {
        $this->requireMethod('DELETE');
        
        // Check if order exists
        $order = $this->orderModel->getById($id);
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // Check if item exists in order
        $item = $this->orderModel->getOrderItem($id, $itemId);
        if (!$item) {
            $this->sendError('Item not found in order', 404);
        }
        
        // Remove item
        $success = $this->orderModel->removeOrderItem($id, $itemId);
        
        if (!$success) {
            $this->sendError('Failed to remove item');
        }
        
        $order = $this->orderModel->getById($id);
        $order->items = $this->orderModel->getOrderItems($id);
        
        $this->sendResponse($order, 200, 'Item removed successfully');
    }
    
    /**
     * Update order item quantity
     * PUT /api/orders/{id}/items/{item_id}
     */
    public function updateItemQuantity($id, $itemId) {
        $this->requireMethod('PUT');
        
        // Check if order exists
        $order = $this->orderModel->getById($id);
        if (!$order) {
            $this->sendError('Order not found', 404);
        }
        
        // Check if item exists in order
        $item = $this->orderModel->getOrderItem($id, $itemId);
        if (!$item) {
            $this->sendError('Item not found in order', 404);
        }
        
        if (!isset($this->requestData['quantity'])) {
            $this->sendError('Quantity is required');
        }
        
        $quantity = (int)$this->requestData['quantity'];
        if ($quantity < 1) {
            $this->sendError('Quantity must be greater than 0');
        }
        
        // Update quantity
        $success = $this->orderModel->updateOrderItemQuantity($id, $itemId, $quantity);
        
        if (!$success) {
            $this->sendError('Failed to update quantity');
        }
        
        $order = $this->orderModel->getById($id);
        $order->items = $this->orderModel->getOrderItems($id);
        
        $this->sendResponse($order, 200, 'Quantity updated successfully');
    }
}
