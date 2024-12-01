<?php
class Table {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }
    
    // Get tables by business ID with pagination
    public function getByBusinessId($businessId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $this->db->query('SELECT * FROM tables 
            WHERE business_id = :business_id 
            ORDER BY name 
            LIMIT :limit OFFSET :offset');
            
        $this->db->bind(':business_id', $businessId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    // Get total count of tables for a business
    public function getTotalByBusiness($businessId) {
        $this->db->query('SELECT COUNT(*) as total FROM tables WHERE business_id = :business_id');
        $this->db->bind(':business_id', $businessId);
        $result = $this->db->single();
        return $result->total;
    }
    
    // Get single table by ID
    public function getById($id) {
        $this->db->query('SELECT * FROM tables WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Create new table
    public function create($data) {
        $this->db->query('INSERT INTO tables (
            business_id, 
            name, 
            capacity, 
            location,
            status
        ) VALUES (
            :business_id, 
            :name, 
            :capacity, 
            :location,
            :status
        )');
        
        $this->db->bind(':business_id', $data['business_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':capacity', $data['capacity']);
        $this->db->bind(':location', $data['location'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'available');
        
        if($this->db->execute()) {
            return $this->db->getDbh()->lastInsertId();
        }
        return false;
    }
    
    // Update table
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        // Only update fields that are provided
        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['capacity'])) {
            $fields[] = 'capacity = :capacity';
            $params[':capacity'] = $data['capacity'];
        }
        
        if (isset($data['location'])) {
            $fields[] = 'location = :location';
            $params[':location'] = $data['location'];
        }
        
        if (isset($data['status'])) {
            $fields[] = 'status = :status';
            $params[':status'] = $data['status'];
        }
        
        if (empty($fields)) {
            return true; // Nothing to update
        }
        
        $query = 'UPDATE tables SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($query);
        
        // Bind all parameters
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }
    
    // Update QR code
    public function updateQRCode($id, $qrCode) {
        $this->db->query('UPDATE tables SET qr_code = :qr_code WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':qr_code', $qrCode);
        return $this->db->execute();
    }
    
    // Delete table
    public function delete($id) {
        $this->db->query('DELETE FROM tables WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // Check if table has active orders
    public function hasActiveOrders($id) {
        $this->db->query('SELECT COUNT(*) as count FROM orders 
            WHERE table_id = :table_id 
            AND status IN ("pending", "preparing", "ready")');
        $this->db->bind(':table_id', $id);
        $result = $this->db->single();
        return $result->count > 0;
    }
    
    // Get active orders for table
    public function getActiveOrders($id) {
        $this->db->query('SELECT * FROM orders 
            WHERE table_id = :table_id 
            AND status IN ("pending", "preparing", "ready")
            ORDER BY created_at DESC');
        $this->db->bind(':table_id', $id);
        return $this->db->resultSet();
    }
    
    // Update table status
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE tables SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }
    
    // Get table statistics for a business
    public function getBusinessStats($businessId) {
        $this->db->query('SELECT 
            COUNT(*) as total_tables,
            SUM(CASE WHEN status = "occupied" THEN 1 ELSE 0 END) as occupied_tables,
            SUM(capacity) as total_capacity,
            SUM(CASE WHEN status = "available" THEN capacity ELSE 0 END) as available_capacity
            FROM tables 
            WHERE business_id = :business_id');
        $this->db->bind(':business_id', $businessId);
        return $this->db->single();
    }
}
