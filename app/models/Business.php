<?php
class Business {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all businesses
    public function getBusinesses() {
        $this->db->query('SELECT * FROM businesses ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Get business by user ID
    public function getBusinessByUserId($userId) {
        $this->db->query('SELECT * FROM businesses WHERE user_id = :user_id LIMIT 1');
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    // Get businesses by user ID
    public function getBusinessesByUserId($userId) {
        $this->db->query('SELECT * FROM businesses WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // Get single business
    public function getBusinessById($id) {
        $this->db->query('SELECT * FROM businesses WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get business by slug
    public function getBusinessBySlug($slug) {
        $this->db->query('SELECT * FROM businesses WHERE slug = :slug');
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    // Add business
    public function add($data) {
        try {
            $this->db->beginTransaction();

            // Create slug
            $slug = $this->createSlug($data['name']);

            $this->db->query('INSERT INTO businesses (name, description, address, phone, email, user_id, slug) 
                VALUES (:name, :description, :address, :phone, :email, :user_id, :slug)');

            // Bind values
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':slug', $slug);

            // Execute
            if($this->db->execute()) {
                $businessId = $this->db->getDbh()->lastInsertId();
                
                // Create default menu for business
                $this->db->query('INSERT INTO menus (business_id, name) VALUES (:business_id, :name)');
                $this->db->bind(':business_id', $businessId);
                $this->db->bind(':name', 'Varsayılan Menü');
                
                if($this->db->execute()) {
                    $this->db->commit();
                    return $businessId;
                }
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Update business
    public function update($data) {
        try {
            $this->db->beginTransaction();

            $sql = 'UPDATE businesses 
                SET name = :name, 
                    description = :description, 
                    address = :address, 
                    phone = :phone, 
                    email = :email';
            
            // Add logo to update if provided
            if(isset($data['logo'])) {
                $sql .= ', logo = :logo';
            }
            
            $sql .= ' WHERE id = :id';

            $this->db->query($sql);

            // Bind values
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);

            // Bind logo if provided
            if(isset($data['logo'])) {
                $this->db->bind(':logo', $data['logo']);
            }

            // Execute
            if($this->db->execute()) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Update business logo
    public function updateLogo($businessId, $logoFileName) {
        try {
            $this->db->query('UPDATE businesses SET logo = :logo WHERE id = :id');
            $this->db->bind(':logo', $logoFileName);
            $this->db->bind(':id', $businessId);
            return $this->db->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Delete business
    public function delete($id) {
        try {
            $this->db->beginTransaction();

            // Delete all related records
            $this->db->query('DELETE FROM menu_items WHERE category_id IN (SELECT id FROM categories WHERE menu_id IN (SELECT id FROM menus WHERE business_id = :id))');
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query('DELETE FROM categories WHERE menu_id IN (SELECT id FROM menus WHERE business_id = :id)');
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query('DELETE FROM menus WHERE business_id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query('DELETE FROM businesses WHERE id = :id');
            $this->db->bind(':id', $id);

            // Execute
            if($this->db->execute()) {
                $this->db->commit();
                return true;
            }
            
            $this->db->rollBack();
            return false;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Check if user owns the business
    public function isOwner($userId, $businessId) {
        $this->db->query('SELECT id FROM businesses WHERE id = :business_id AND user_id = :user_id');
        $this->db->bind(':business_id', $businessId);
        $this->db->bind(':user_id', $userId);
        
        return $this->db->rowCount() > 0;
    }

    // Verify business owner
    public function verifyBusinessOwner($businessId, $userId) {
        $this->db->query('SELECT * FROM businesses WHERE id = :business_id AND user_id = :user_id');
        $this->db->bind(':business_id', $businessId);
        $this->db->bind(':user_id', $userId);
        
        $row = $this->db->single();
        return $row ? true : false;
    }

    // Create URL friendly slug
    private function createSlug($string) {
        $slug = mb_strtolower($string, 'UTF-8'); // Convert to lowercase
        $slug = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $slug); // Turkish characters
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug); // Replace non-alphanumeric with dash
        $slug = preg_replace('/-+/', '-', $slug); // Replace multiple dashes with single dash
        $slug = trim($slug, '-'); // Trim dashes from start and end
        
        // Make sure slug is unique
        $originalSlug = $slug;
        $count = 1;
        
        while($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    // Check if slug exists
    private function slugExists($slug) {
        $this->db->query('SELECT id FROM businesses WHERE slug = :slug');
        $this->db->bind(':slug', $slug);
        return $this->db->rowCount() > 0;
    }
}
