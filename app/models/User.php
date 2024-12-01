<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register user
    public function register($data) {
        $this->db->query('INSERT INTO users (username, email, password, role) VALUES(:username, :email, :password, :role)');
        // Bind values
        $this->db->bind(':username', $data['name']); // Use name as username
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':role', $data['role'] ?? 'business_owner');

        // Execute
        if($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if($row) {
            $hashed_password = $row->password;
            if(password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        // Bind value
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get User by ID
    public function getUserById($id) {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        // Bind value
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Check if user is admin
    public function isAdmin($userId) {
        $this->db->query('SELECT role FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        
        $row = $this->db->single();
        return $row && $row->role === 'admin';
    }

    // Get user role
    public function getUserRole($userId) {
        $this->db->query('SELECT role FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        
        $row = $this->db->single();
        return $row ? $row->role : null;
    }

    // Get all business owners
    public function getAllBusinessOwners() {
        $this->db->query('SELECT * FROM users WHERE role = :role');
        $this->db->bind(':role', 'business_owner');
        
        return $this->db->resultSet();
    }
}
