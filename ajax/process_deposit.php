<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * AJAX Handler: Process Deposit Request
 */

// Set header to return JSON
header('Content-Type: application/json');

// Include core files
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Initialize response array
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Security Check: Ensure the user is logged in
if (!is_logged_in()) {
    $response['message'] = 'Authentication required. Please log in.';
    echo json_encode($response);
    exit();
}

// Security Check: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}

// --- Main Logic ---
try {
    // Sanitize and validate inputs
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $utr_number = sanitize_input($_POST['utr_number'] ?? '');
    $user_id = $_SESSION['user_id'];

    if ($amount === false || $amount <= 0) {
        throw new Exception('Invalid amount entered. Please enter a positive number.');
    }
    if (empty($utr_number)) {
        throw new Exception('UTR/Transaction ID is required.');
    }
    if (strlen($utr_number) < 12 || strlen($utr_number) > 22) {
        throw new Exception('Please enter a valid UTR/Transaction ID (12-22 characters).');
    }

    // Check for duplicate UTR number to prevent replay attacks
    $stmt = $conn->prepare("SELECT id FROM deposit_requests WHERE utr_number = ?");
    $stmt->bind_param("s", $utr_number);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        throw new Exception('This UTR/Transaction ID has already been submitted.');
    }
    $stmt->close();

    // Insert the deposit request into the database
    $stmt = $conn->prepare("INSERT INTO deposit_requests (user_id, amount, utr_number, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("ids", $user_id, $amount, $utr_number);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Your deposit request has been submitted successfully and is awaiting approval.';
    } else {
        throw new Exception('Database error: Could not submit your request. Please try again later.');
    }
    $stmt->close();

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$conn->close();
echo json_encode($response);
exit();