<?php
class Category {
    private $db;
    private $error;

    public function __construct() {
        $this->db = new Database;
    }

    // Get categories by business ID with pagination
    public function getByBusinessId($businessId, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $this->db->query('SELECT c.* FROM categories c
            INNER JOIN menus m ON c.menu_id = m.id
            WHERE m.business_id = :business_id 
            ORDER BY c.sort_order 
            LIMIT :limit OFFSET :offset');
            
        $this->db->bind(':business_id', $businessId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }

    // Alias for getByBusinessId without pagination
    public function getCategoriesByBusinessId($businessId) {
        $this->db->query('SELECT c.* FROM categories c
            INNER JOIN menus m ON c.menu_id = m.id
            WHERE m.business_id = :business_id 
            ORDER BY c.sort_order');
            
        $this->db->bind(':business_id', $businessId);
        
        return $this->db->resultSet();
    }

    // Get total count of categories in a business
    public function getTotalByBusiness($businessId) {
        $this->db->query('SELECT COUNT(*) as total FROM categories c
            INNER JOIN menus m ON c.menu_id = m.id
            WHERE m.business_id = :business_id');
        $this->db->bind(':business_id', $businessId);
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }

    // Get category by ID
    public function getById($id) {
        $this->db->query('SELECT c.*, m.business_id 
            FROM categories c
            INNER JOIN menus m ON c.menu_id = m.id
            WHERE c.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single() ?: null;
    }

    // Get all menu items for a category
    public function getCategoryItems($categoryId) {
        $this->db->query('SELECT * FROM menu_items WHERE category_id = :category_id ORDER BY sort_order');
        $this->db->bind(':category_id', $categoryId);
        return $this->db->resultSet();
    }

    // Create new category
    public function create($data) {
        try {
            // Get max sort order
            $this->db->query('SELECT MAX(sort_order) as max_order FROM categories WHERE menu_id = :menu_id');
            $this->db->bind(':menu_id', $data['menu_id']);
            $result = $this->db->single();
            $sortOrder = ($result->max_order ?? 0) + 1;

            $this->db->query('INSERT INTO categories (
                menu_id, 
                name, 
                description, 
                sort_order
            ) VALUES (
                :menu_id, 
                :name, 
                :description, 
                :sort_order
            )');
            
            $this->db->bind(':menu_id', $data['menu_id']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':description', $data['description'] ?? '');
            $this->db->bind(':sort_order', $sortOrder);

            if($this->db->execute()) {
                return $this->db->getDbh()->lastInsertId();
            }
            
            $this->error = "Veritabanına kayıt eklenirken bir hata oluştu.";
            return false;
        } catch (Exception $e) {
            $this->error = "Hata: " . $e->getMessage();
            return false;
        }
    }

    // Update category
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        // Only update fields that are provided
        if (isset($data['name'])) {
            $fields[] = 'name = :name';
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }
        
        if (isset($data['sort_order'])) {
            $fields[] = 'sort_order = :sort_order';
            $params[':sort_order'] = $data['sort_order'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = 'UPDATE categories SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($sql);
        
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        return $this->db->execute();
    }

    // Delete category
    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // Önce bu kategoriye ait menü öğelerini silelim
            $this->db->query('DELETE FROM menu_items WHERE category_id = :category_id');
            $this->db->bind(':category_id', $id);
            $this->db->execute();

            // Şimdi kategoriyi silelim
            $this->db->query('DELETE FROM categories WHERE id = :id');
            $this->db->bind(':id', $id);
            
            if ($this->db->execute()) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            $this->error = "Kategori silinirken bir hata oluştu.";
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->error = "Hata: " . $e->getMessage();
            return false;
        }
    }

    // Update category order
    public function updateOrder($id, $sortOrder) {
        $this->db->query('UPDATE categories SET sort_order = :sort_order WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':sort_order', $sortOrder);
        return $this->db->execute();
    }

    // Get error message
    public function getError() {
        return $this->error;
    }
}
