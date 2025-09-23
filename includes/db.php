 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Database Connection File
 */

// Define database credentials as constants
define('DB_HOST', 'localhost');
define('DB_USER', 'highdatacenter_xxxxxxxx'); // Replace with your database username
define('DB_PASS', 'highdatacenter_xxxxxxxx');     // Replace with your database password
define('DB_NAME', 'highdatacenter_xxxxxxxx'); // Replace with your database name

// Create a new MySQLi object
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    // If a connection error occurs, kill the script and display the error
    die("Connection Failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

?>