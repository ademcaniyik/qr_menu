<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }
    
    // Get orders by business ID with pagination and optional status filter
    public function getByBusinessId($businessId, $status = null, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = 'SELECT o.*, t.name as table_name 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            WHERE t.business_id = :business_id';
            
        if ($status) {
            $query .= ' AND o.status = :status';
        }
        
        $query .= ' ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset';
        
        $this->db->query($query);
        $this->db->bind(':business_id', $businessId);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    // Get total count of orders for a business
    public function getTotalByBusiness($businessId, $status = null) {
        $query = 'SELECT COUNT(*) as total 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            WHERE t.business_id = :business_id';
            
        if ($status) {
            $query .= ' AND o.status = :status';
        }
        
        $this->db->query($query);
        $this->db->bind(':business_id', $businessId);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        $result = $this->db->single();
        return $result->total;
    }
    
    // Get single order by ID
    public function getById($id) {
        $this->db->query('SELECT o.*, t.name as table_name 
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            WHERE o.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Get order items
    public function getOrderItems($orderId) {
        $this->db->query('SELECT oi.*, mi.name, mi.price 
            FROM order_items oi 
            JOIN menu_items mi ON oi.menu_item_id = mi.id 
            WHERE oi.order_id = :order_id');
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }
    
    // Get single order item
    public function getOrderItem($orderId, $itemId) {
        $this->db->query('SELECT * FROM order_items WHERE order_id = :order_id AND id = :id');
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':id', $itemId);
        return $this->db->single();
    }
    
    // Create new order
    public function create($data) {
        $this->db->query('INSERT INTO orders (
            table_id, 
            status, 
            notes
        ) VALUES (
            :table_id, 
            :status, 
            :notes
        )');
        
        $this->db->bind(':table_id', $data['table_id']);
        $this->db->bind(':status', 'pending');
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        // Execute
        if($this->db->execute()) {
            return $this->db->getDbh()->lastInsertId();
        } else {
            return false;
        }
    }
    
    // Add order item
    public function addOrderItem($orderId, $menuItemId, $quantity, $notes = null) {
        $this->db->query('INSERT INTO order_items (
            order_id, 
            menu_item_id, 
            quantity, 
            notes
        ) VALUES (
            :order_id, 
            :menu_item_id, 
            :quantity, 
            :notes
        )');
        
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':menu_item_id', $menuItemId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':notes', $notes);
        
        return $this->db->execute();
    }
    
    // Remove order item
    public function removeOrderItem($orderId, $itemId) {
        $this->db->query('DELETE FROM order_items WHERE order_id = :order_id AND id = :id');
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':id', $itemId);
        return $this->db->execute();
    }
    
    // Update order item quantity
    public function updateOrderItemQuantity($orderId, $itemId, $quantity) {
        $this->db->query('UPDATE order_items SET quantity = :quantity WHERE order_id = :order_id AND id = :id');
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':id', $itemId);
        $this->db->bind(':quantity', $quantity);
        return $this->db->execute();
    }
    
    // Update order status
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE orders SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }
    
    // Calculate order total
    public function calculateTotal($orderId) {
        $this->db->query('SELECT SUM(oi.quantity * mi.price) as total 
            FROM order_items oi 
            JOIN menu_items mi ON oi.menu_item_id = mi.id 
            WHERE oi.order_id = :order_id');
        $this->db->bind(':order_id', $orderId);
        $result = $this->db->single();
        return $result->total ?? 0;
    }
    
    // Get order statistics for a business
    public function getBusinessStats($businessId, $startDate = null, $endDate = null) {
        $query = 'SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_orders,
            AVG(CASE WHEN status = "delivered" 
                THEN TIMESTAMPDIFF(MINUTE, created_at, updated_at) 
                ELSE NULL 
            END) as avg_preparation_time
            FROM orders o 
            JOIN tables t ON o.table_id = t.id 
            WHERE t.business_id = :business_id';
            
        if ($startDate) {
            $query .= ' AND o.created_at >= :start_date';
        }
        if ($endDate) {
            $query .= ' AND o.created_at <= :end_date';
        }
        
        $this->db->query($query);
        $this->db->bind(':business_id', $businessId);
        
        if ($startDate) {
            $this->db->bind(':start_date', $startDate);
        }
        if ($endDate) {
            $this->db->bind(':end_date', $endDate);
        }
        
        return $this->db->single();
    }
}
