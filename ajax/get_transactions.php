<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * AJAX Handler: Get User Transactions
 */

// Set header to return JSON
header('Content-Type: application/json');

// Include core files
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Initialize response array
$response = ['success' => false, 'transactions' => [], 'message' => 'An unknown error occurred.'];

// Security Check: Ensure the user is logged in
if (!is_logged_in()) {
    $response['message'] = 'Authentication required. Please log in.';
    echo json_encode($response);
    exit();
}

// --- Main Logic ---
try {
    $user_id = $_SESSION['user_id'];

    // Fetch transactions for the current user, ordered by most recent
    $stmt = $conn->prepare("SELECT type, amount, description, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        // Format data for better frontend display
        $transactions[] = [
            'type' => $row['type'], // 'credit' or 'debit'
            'amount' => number_format($row['amount'], 2),
            'description' => htmlspecialchars($row['description']),
            'date' => date('d M Y, h:i A', strtotime($row['created_at']))
        ];
    }
    $stmt->close();

    $response['success'] = true;
    $response['transactions'] = $transactions;
    unset($response['message']); // No need for a message on success

} catch (Exception $e) {
    // In case of a database error
    $response['message'] = 'Could not fetch transaction history.';
}

$conn->close();
echo json_encode($response);
exit();