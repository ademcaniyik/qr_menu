/**
 * Menu model for handling menu-related database operations
 */
class Menu {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    /**
     * Get menu by ID
     * @param int $id Menu ID
     * @return object|false Menu object or false if not found
     */
    public function getById($id) {
        $this->db->query('SELECT * FROM menus WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get categories with menu items for a menu
     * @param int $menuId Menu ID
     * @return array Array of category objects with menu_items property
     */
    public function getCategoriesWithItems($menuId) {
        // First get all categories for this menu
        $this->db->query('SELECT * FROM categories WHERE menu_id = :menu_id ORDER BY sort_order');
        $this->db->bind(':menu_id', $menuId);
        $categories = $this->db->resultSet();

        // For each category, get its menu items
        foreach ($categories as $category) {
            $this->db->query('SELECT * FROM menu_items WHERE category_id = :category_id ORDER BY sort_order');
            $this->db->bind(':category_id', $category->id);
            $category->menu_items = $this->db->resultSet() ?? [];
        }

        return $categories;
    }

    /**
     * Create new menu
     * @param array $data Menu data
     * @return int|false Last insert ID or false on failure
     */
    public function create($data) {
        $this->db->query('INSERT INTO menus (business_id, name) VALUES (:business_id, :name)');
        $this->db->bind(':business_id', $data['business_id']);
        $this->db->bind(':name', $data['name']);

        if ($this->db->execute()) {
            return $this->db->getDbh()->lastInsertId();
        }
        return false;
    }

    /**
     * Update menu
     * @param array $data Menu data
     * @return bool True on success, false on failure
     */
    public function update($data) {
        $this->db->query('UPDATE menus SET name = :name WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        return $this->db->execute();
    }

    /**
     * Delete menu
     * @param int $id Menu ID
     * @return bool True on success, false on failure
     */
    public function delete($id) {
        $this->db->query('DELETE FROM menus WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get all menus for a business
     * @param int $businessId Business ID
     * @return array Array of menu objects
     */
    public function getMenusByBusinessId($businessId) {
        $this->db->query('SELECT * FROM menus WHERE business_id = :business_id ORDER BY id DESC');
        $this->db->bind(':business_id', $businessId);
        return $this->db->resultSet() ?? [];
    }

    /**
     * Get active menu for business
     * @param int $businessId Business ID
     * @return object|false Menu object or false if not found
     */
    public function getActiveMenuByBusinessId($businessId) {
        $this->db->query('SELECT * FROM menus WHERE business_id = :business_id ORDER BY created_at DESC LIMIT 1');
        $this->db->bind(':business_id', $businessId);
        return $this->db->single();
    }

    /**
     * Get menu by business ID
     * @param int $businessId Business ID
     * @return object|false Menu object or false if not found
     */
    public function getByBusinessId($businessId) {
        $this->db->query('SELECT * FROM menus WHERE business_id = :business_id LIMIT 1');
        $this->db->bind(':business_id', $businessId);
        return $this->db->single();
    }

    /**
     * Check if menu belongs to business
     * @param int $menuId Menu ID
     * @param int $businessId Business ID
     * @return bool True if menu belongs to business, false otherwise
     */
    public function belongsToBusiness($menuId, $businessId) {
        $this->db->query('SELECT id FROM menus WHERE id = :menu_id AND business_id = :business_id');
        $this->db->bind(':menu_id', $menuId);
        $this->db->bind(':business_id', $businessId);
        return (bool)$this->db->single();
    }
}
