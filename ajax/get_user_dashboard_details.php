<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.3
 * Author: SKYRHRG Technologies Systems
 *
 * AJAX Handler: Get Comprehensive Dashboard Details for a Single User
 */

// Set header to return JSON
header('Content-Type: application/json');

// Include core files
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

// Initialize a default response
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// --- SECURITY CHECKS ---
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $response['message'] = 'Access Denied: Administrator privileges required.';
    echo json_encode($response);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit();
}
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    $response['message'] = 'Invalid security token.';
    echo json_encode($response);
    exit();
}

// --- MAIN LOGIC ---
try {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    if (!$user_id) {
        throw new Exception("Invalid User ID provided.");
    }

    $data = [];

    // 1. Get Wallet Balance
    $stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $data['wallet_balance'] = $stmt->get_result()->fetch_assoc()['balance'] ?? 0;
    $stmt->close();

    // 2. Get Total Active Services
    $stmt = $conn->prepare("SELECT COUNT(id) as total FROM orders WHERE user_id = ? AND status = 'active'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $data['total_active_services'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // 3. Get Service Count by Category
    $stmt = $conn->prepare("
        SELECT pc.name as category_name, COUNT(o.id) as service_count
        FROM orders o
        JOIN products p ON o.product_id = p.id
        JOIN product_categories pc ON p.category_id = pc.id
        WHERE o.user_id = ? AND o.status = 'active'
        GROUP BY pc.name
        ORDER BY pc.name
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $category_result = $stmt->get_result();
    $data['services_by_category'] = [];
    while ($row = $category_result->fetch_assoc()) {
        $data['services_by_category'][] = $row;
    }
    $stmt->close();

    // 4. Get Downline User Count (users they have created)
    $stmt = $conn->prepare("SELECT COUNT(id) as total FROM users WHERE created_by = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $data['downline_count'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // 5. Get Total Commission Earned
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'credit' AND description LIKE 'Commission from sale%'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $data['total_commission_earned'] = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    
    // If all queries are successful
    $response['success'] = true;
    $response['data'] = $data;
    unset($response['message']);

} catch (Exception $e) {
    // If any exception was thrown
    $response['message'] = 'Data Fetch Failed: ' . $e->getMessage();
}

// Close the database connection and send the JSON response
$conn->close();
echo json_encode($response);
exit();