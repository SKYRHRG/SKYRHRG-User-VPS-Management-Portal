<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Logout Script
 */

// Start the session to access session variables
require_once 'includes/session.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with a success message
header("Location: login.php?message=You have been logged out successfully.");
exit();
?>