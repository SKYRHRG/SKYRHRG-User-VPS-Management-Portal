 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Main Router
 */

// Include core files
require_once 'includes/session.php';
require_once 'includes/functions.php';

// Check if the user is logged in
if (is_logged_in()) {
    // Get the user's role from the session
    $role = $_SESSION['role'];

    // Redirect to the appropriate dashboard based on the role
    switch ($role) {
        case 'admin':
            redirect('admin/index.php');
            break;
        case 'super_seller':
            redirect('super_seller/index.php');
            break;
        case 'seller':
            redirect('seller/index.php');
            break;
        case 'user':
            redirect('user/index.php');
            break;
        default:
            // If role is not recognized, log them out for safety
            redirect('logout.php');
            break;
    }
} else {
    // If not logged in, redirect to the login page
    redirect('login.php');
}
?>