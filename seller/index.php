<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.1 (Enhanced Seller Dashboard)
 * Author: SKYRHRG Technologies Systems
 *
 * Seller Dashboard
 */

$page_title = 'Seller Dashboard';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php'; // DB connection

$current_user_id = $_SESSION['user_id'];

// --- DATA FETCHING (Scoped to this Seller's created users) ---

// 1. Get an array of all user IDs created by this Seller
$sub_user_ids = [];
$sub_users_result = $conn->query("SELECT id FROM users WHERE created_by = $current_user_id");
if ($sub_users_result && $sub_users_result->num_rows > 0) {
    while ($row = $sub_users_result->fetch_assoc()) {
        $sub_user_ids[] = $row['id'];
    }
}
// Create a safe, comma-separated string for SQL IN() clause
$sub_user_id_list = !empty($sub_user_ids) ? implode(',', array_map('intval', $sub_user_ids)) : '0';

// 2. Fetch all required stats
$total_users_res = $conn->query("SELECT COUNT(id) as count FROM users WHERE created_by = $current_user_id");
$total_users = $total_users_res->fetch_assoc()['count'];


// 4. Financial Stats
$my_balance_res = $conn->query("SELECT balance FROM wallets WHERE user_id = $current_user_id");
$my_balance = $my_balance_res->fetch_assoc()['balance'] ?? 0;

// 5. Recent Activity
$recent_users_result = $conn->query("SELECT username, role, created_at FROM users WHERE created_by = $current_user_id ORDER BY created_at DESC LIMIT 5");
$recent_transactions_result = $conn->query("SELECT type, amount, description, created_at FROM transactions WHERE user_id = $current_user_id ORDER BY created_at DESC LIMIT 5");

?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Seller Dashboard</h1></div>
                <div class="col-sm-6">
                     <div class="float-sm-right">
                        <a href="create_user.php" class="btn btn-primary"><i class="fas fa-user-plus mr-2"></i>Create User</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>₹<?php echo number_format($my_balance, 2); ?></h3><p>My Wallet Balance</p></div><div class="icon"><i class="fas fa-wallet"></i></div><a href="wallet.php" class="small-box-footer">View History <i class="fas fa-arrow-circle-right"></i></a></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?php echo $total_users; ?></h3><p>Total Users You Created</p></div><div class="icon"><i class="fas fa-users"></i></div><a href="manage_users.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a></div></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info card-outline"><div class="card-header"><h3 class="card-title">Users You Recently Added</h3></div><div class="card-body p-0"><ul class="products-list product-list-in-card pl-2 pr-2">
                        <?php if($recent_users_result && $recent_users_result->num_rows > 0): while($user = $recent_users_result->fetch_assoc()): ?>
                            <li class="item"><div class="product-info ml-0"><span class="product-title"><?php echo htmlspecialchars($user['username']); ?><span class="badge badge-info float-right">User</span></span><span class="product-description">Joined: <?php echo date('d M Y', strtotime($user['created_at'])); ?></span></div></li>
                        <?php endwhile; else: echo '<li class="item text-center p-4">You have not created any users yet.</li>'; endif; ?>
                    </ul></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>