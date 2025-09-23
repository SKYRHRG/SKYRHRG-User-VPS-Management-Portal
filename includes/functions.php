 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Core Functions File
 */

// Ensure the session is started before using session variables
if (session_status() == PHP_SESSION_NONE) {
    require_once 'session.php';
}

/**
 * Sanitizes user input to prevent XSS attacks.
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirects the user to a specified URL.
 * @param string $url The URL to redirect to.
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Checks if a user is logged in by verifying session variables.
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Checks if the logged-in user has the required role to access a page.
 * If the user is not logged in or does not have the correct role, they are redirected.
 * @param array $allowed_roles An array of roles that are allowed to access the page.
 */
function authorize_access($allowed_roles) {
    if (!is_logged_in()) {
        // If not logged in, redirect to the login page
        redirect('../login.php?error=unauthorized');
    }

    // Check if the user's role is in the list of allowed roles
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // If the role is not allowed, show an access denied message or redirect
        die('<h1>Access Denied</h1><p>You do not have permission to view this page.</p>');
    }
}

/**
 * Generates a CSRF token and stores it in the session.
 * @return string The generated CSRF token.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies the submitted CSRF token against the one in the session.
 * @param string $token The token submitted from a form.
 * @return bool True if the token is valid, false otherwise.
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>