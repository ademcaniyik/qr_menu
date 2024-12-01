<?php
class MenuItem {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all menu items for a category
    public function getMenuItemsByCategoryId($categoryId) {
        $this->db->query('SELECT * FROM menu_items WHERE category_id = :category_id ORDER BY sort_order');
        $this->db->bind(':category_id', $categoryId);
        return $this->db->resultSet();
    }

    // Get all menu items for a business
    public function getMenuItemsByBusinessId($businessId) {
        $this->db->query('
            SELECT mi.*, c.name as category_name 
            FROM menu_items mi 
            JOIN categories c ON mi.category_id = c.id 
            JOIN menus m ON c.menu_id = m.id 
            WHERE m.business_id = :business_id 
            ORDER BY c.sort_order, mi.sort_order
        ');
        $this->db->bind(':business_id', $businessId);
        return $this->db->resultSet();
    }

    // Get menu items by category with pagination
    public function getByCategoryId($categoryId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $this->db->query('SELECT * FROM menu_items 
            WHERE category_id = :category_id 
            ORDER BY sort_order 
            LIMIT :limit OFFSET :offset');
            
        $this->db->bind(':category_id', $categoryId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Get total count of menu items in a category
    public function getTotalByCategory($categoryId) {
        $this->db->query('SELECT COUNT(*) as total FROM menu_items WHERE category_id = :category_id');
        $this->db->bind(':category_id', $categoryId);
        $result = $this->db->single();
        return $result->total;
    }

    // Get single menu item by ID
    public function getById($id) {
        $this->db->query('SELECT * FROM menu_items WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Create new menu item
    public function create($data) {
        // Get max sort order
        $this->db->query('SELECT MAX(sort_order) as max_order FROM menu_items WHERE category_id = :category_id');
        $this->db->bind(':category_id', $data['category_id']);
        $result = $this->db->single();
        $sortOrder = ($result->max_order ?? 0) + 1;

        $this->db->query('INSERT INTO menu_items (
            category_id, 
            name, 
            description, 
            price, 
            image, 
            sort_order, 
            is_available
        ) VALUES (
            :category_id, 
            :name, 
            :description, 
            :price, 
            :image, 
            :sort_order, 
            :is_available
        )');
        
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':image', $data['image'] ?? null);
        $this->db->bind(':sort_order', $sortOrder);
        $this->db->bind(':is_available', $data['is_available'] ?? true);

        // Execute
        if($this->db->execute()) {
            return $this->db->getDbh()->lastInsertId();
        } else {
            return false;
        }
    }

    // Update menu item
    public function update($data) {
        $this->db->query('UPDATE menu_items SET 
            name = :name, 
            description = :description, 
            price = :price, 
            is_available = :is_available 
            WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':is_available', $data['is_available'] ?? 1);

        // Execute
        return $this->db->execute();
    }

    // Update availability status
    public function updateAvailability($id, $available) {
        $this->db->query('UPDATE menu_items SET is_available = :is_available WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':is_available', $available);
        return $this->db->execute();
    }

    // Update menu item image
    public function updateImage($id, $image) {
        $this->db->query('UPDATE menu_items SET image = :image WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':image', $image);

        // Execute
        return $this->db->execute();
    }

    // Delete menu item
    public function delete($id) {
        $this->db->query('DELETE FROM menu_items WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        return $this->db->execute();
    }

    // Update sort order
    public function updateSortOrder($id, $newOrder) {
        $this->db->query('UPDATE menu_items SET sort_order = :sort_order WHERE id = :id');
        
        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':sort_order', $newOrder);

        // Execute
        return $this->db->execute();
    }

    // Check if menu item belongs to business
    public function belongsToBusiness($menuItemId, $businessId) {
        $this->db->query('
            SELECT mi.id 
            FROM menu_items mi 
            JOIN categories c ON mi.category_id = c.id 
            JOIN menus m ON c.menu_id = m.id 
            WHERE mi.id = :menu_item_id AND m.business_id = :business_id
        ');
        $this->db->bind(':menu_item_id', $menuItemId);
        $this->db->bind(':business_id', $businessId);
        
        $row = $this->db->single();
        return !empty($row);
    }
}
