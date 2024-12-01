<?php
// Simple page redirect
function redirect($page) {
    header('location: ' . URLROOT . '/' . $page);
    exit;
}

// Get base url
function base_url($path = '') {
    return URLROOT . '/' . ltrim($path, '/');
}

// Asset URL helper
function asset_url($path = '') {
    return URLROOT . '/public/' . ltrim($path, '/');
}

// Generate CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
