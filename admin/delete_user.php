<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Delete User Script
 */

// Include core files
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Authorize access: only 'admin' role is allowed
authorize_access(['admin']);

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id > 0) {
    // CRITICAL: Prevent admin from deleting their own account
    if ($user_id === $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'Error: You cannot delete your own account.';
        redirect('manage_users.php');
    }

    // Use prepared statement to delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Check if any row was actually deleted
        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = 'User has been deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'User not found or could not be deleted.';
        }
    } else {
        $_SESSION['error_message'] = 'An error occurred while trying to delete the user.';
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = 'Invalid user ID provided.';
}

$conn->close();

// Redirect back to the user management page
redirect('manage_users.php');
?>