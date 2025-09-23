 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Session Management File
 */

// Set session cookie parameters for better security
$session_params = [
    'lifetime' => 1800, // 30 minutes
    'path' => '/',
    'domain' => '', // Set your domain in production
    'secure' => isset($_SERVER['HTTPS']), // Only send cookies over HTTPS
    'httponly' => true, // Prevent JavaScript access to session cookie
    'samesite' => 'Lax'
];

session_set_cookie_params($session_params);

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Regenerates the session ID to prevent session fixation attacks.
 * Call this function after a user logs in successfully.
 */
function regenerate_session() {
    session_regenerate_id(true);
}
?>