<?php
// Check if user is admin using User model
function isAdminUser() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $userModel = new User();
    return $userModel->isAdmin($_SESSION['user_id']);
}

// Check if user owns the business
function isBusinessOwner($businessId) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $businessModel = new Business();
    return $businessModel->isOwner($_SESSION['user_id'], $businessId);
}

// Check if user has access to business (either admin or owner)
function hasBusinessAccess($businessId) {
    return isAdminUser() || isBusinessOwner($businessId);
}

// Redirect if no access
function requireBusinessAccess($businessId) {
    if (!hasBusinessAccess($businessId)) {
        redirect('pages/error');
    }
}

// Get user's business ID (for business owners)
function getUserBusinessId() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $businessModel = new Business();
    $business = $businessModel->getBusinessByUserId($_SESSION['user_id']);
    return $business ? $business->id : null;
}
