<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.3
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller - Manage Sellers (Fixed & Modernized)
 */

// Recommended for debugging - REMOVE IN PRODUCTION
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Manage Sellers';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- HELPER FUNCTION FOR BADGES ---
function get_seller_badge_class($type, $value) {
    if ($type == 'status') {
        switch (strtolower($value)) {
            case 'active': return 'badge-success';
            case 'suspended': return 'badge-warning';
            case 'banned': return 'badge-danger';
            default: return 'badge-light';
        }
    }
    return 'badge-light';
}

// --- DATA FETCHING (Scoped to this Super Seller) ---
$current_user_id = $_SESSION['user_id'];
$users_result = null;
$wallet_stmt = null;
$orders_stmt = null;

// A reliable query to get the primary list of sellers.
$query = "SELECT id, username, email, role, status FROM users WHERE role = 'seller' AND created_by = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Database Error: Could not prepare the main user query. " . $conn->error);
}

$stmt->bind_param("i", $current_user_id);

if (!$stmt->execute()) {
    die("Database Error: Could not execute the main user query. " . $stmt->error);
}
$users_result = $stmt->get_result();

// Pre-compile statements for performance inside the loop
$wallet_stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$orders_stmt = $conn->prepare("SELECT status, COUNT(id) as count FROM orders WHERE user_id = ? GROUP BY status");

// Check for session messages
$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
/* Custom Badge & User Display Styles */
.badge { font-size: 0.8rem; padding: 0.4em 0.7em; border-radius: 12px; }
.user-avatar { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
.user-info { margin-left: 12px; }
.user-info .user-name { font-weight: 600; }
.user-info .user-email { color: #6c757d; font-size: 0.9em; }
</style>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0"><?php echo $page_title; ?></h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">All Sellers Created By You</h3>
                    <a href="create_seller.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Add New Seller
                    </a>
                </div>
                <div class="card-body">
                    <table id="sellersTable" class="table table-hover responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Active Orders</th>
                            <th>Pending Orders</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($users_result && $users_result->num_rows > 0): ?>
                            <?php while ($row = $users_result->fetch_assoc()): 
                                $user_id = $row['id'];
                                
                                // Get wallet balance for this user
                                $balance = 0;
                                if ($wallet_stmt) {
                                    $wallet_stmt->bind_param("i", $user_id);
                                    $wallet_stmt->execute();
                                    $balance_result = $wallet_stmt->get_result();
                                    $balance_row = $balance_result->fetch_assoc();
                                    $balance = $balance_row['balance'] ?? 0;
                                }

                                // Get order counts for this user
                                $counts = ['active' => 0, 'pending' => 0];
                                if ($orders_stmt) {
                                    $orders_stmt->bind_param("i", $user_id);
                                    $orders_stmt->execute();
                                    $order_counts_result = $orders_stmt->get_result();
                                    while($order_row = $order_counts_result->fetch_assoc()) {
                                        if (isset($counts[$order_row['status']])) {
                                            $counts[$order_row['status']] = $order_row['count'];
                                        }
                                    }
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://placehold.co/40x40/28a745/ffffff?text=<?php echo strtoupper(substr($row['username'], 0, 1)); ?>" alt="User Avatar" class="user-avatar">
                                            <div class="user-info">
                                                <div class="user-name"><?php echo htmlspecialchars($row['username']); ?></div>
                                                <div class="user-email"><?php echo htmlspecialchars($row['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo get_seller_badge_class('status', $row['status']); ?>">
                                            <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                        </span>
                                    </td>
                                    <td><strong>₹<?php echo number_format($balance, 2); ?></strong></td>
                                    <td class="text-center"><?php echo $counts['active']; ?></td>
                                    <td class="text-center"><?php echo $counts['pending']; ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info" title="Edit"><i class="fas fa-edit"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
// Gracefully close all database statements and connections
if ($stmt) $stmt->close();
if ($wallet_stmt) $wallet_stmt->close();
if ($orders_stmt) $orders_stmt->close();
require_once 'includes/footer.php'; 
?>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(function () {
    $("#sellersTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": []
    });

    // --- TOAST NOTIFICATIONS ---
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    <?php if ($success_message): ?>
        Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' });
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' });
    <?php endif; ?>
});
</script>