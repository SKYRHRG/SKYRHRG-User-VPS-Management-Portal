<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller Dashboard (Modern Design)
 */

$page_title = 'Super Seller Dashboard';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php'; // DB connection

$current_user_id = $_SESSION['user_id'];

// --- DATA FETCHING (Scoped to this Super Seller) ---

// 1. Get an array of all user IDs created by this Super Seller
$sub_user_ids = [];
$sub_users_result = $conn->query("SELECT id FROM users WHERE created_by = $current_user_id");
if ($sub_users_result && $sub_users_result->num_rows > 0) {
    while ($row = $sub_users_result->fetch_assoc()) {
        $sub_user_ids[] = $row['id'];
    }
}
$sub_user_id_list = !empty($sub_user_ids) ? implode(',', $sub_user_ids) : '0';

// 2. Fetch all required stats
$total_sellers = 0;
$total_users = 0;
if (!empty($sub_user_ids)) {
    $total_sellers_res = $conn->query("SELECT COUNT(id) as count FROM users WHERE role = 'seller' AND created_by = $current_user_id");
    $total_sellers = $total_sellers_res->fetch_assoc()['count'];
    
    $total_users_res = $conn->query("SELECT COUNT(id) as count FROM users WHERE role = 'user' AND created_by = $current_user_id");
    $total_users = $total_users_res->fetch_assoc()['count'];
}

// Financial Stats
$my_balance_res = $conn->query("SELECT balance FROM wallets WHERE user_id = $current_user_id");
$my_balance = $my_balance_res->fetch_assoc()['balance'] ?? 0;

// Recent Activity
$recent_users_result = $conn->query("SELECT username, role, created_at FROM users WHERE created_by = $current_user_id ORDER BY created_at DESC LIMIT 5");
$recent_transactions_result = $conn->query("SELECT type, amount, description, created_at FROM transactions WHERE user_id = $current_user_id ORDER BY created_at DESC LIMIT 5");

?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Super Seller Dashboard</h1>
                </div>
                <div class="col-sm-6">
                     <div class="float-sm-right">
                        <a href="create_seller.php" class="btn btn-primary"><i class="fas fa-user-plus mr-2"></i>Create Seller</a>
                        <a href="transfer_balance.php" class="btn btn-success"><i class="fas fa-exchange-alt mr-2"></i>Transfer Balance</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>₹<?php echo number_format($my_balance, 2); ?></h3><p>My Wallet Balance</p></div><div class="icon"><i class="fas fa-wallet"></i></div><a href="wallet.php" class="small-box-footer">View History <i class="fas fa-arrow-circle-right"></i></a></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-info"><div class="inner"><h3><?php echo $total_sellers; ?></h3><p>Total Sellers You Created</p></div><div class="icon"><i class="fas fa-user-tie"></i></div><a href="manage_sellers.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a></div></div>
                <div class="col-lg-3 col-6"><div class="small-box bg-primary"><div class="inner"><h3><?php echo $total_users; ?></h3><p>Total Users You Created</p></div><div class="icon"><i class="fas fa-users"></i></div><a href="manage_users.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a></div></div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="card card-primary card-outline"><div class="card-header"><h3 class="card-title">Your Recent Transactions</h3></div><div class="card-body p-0"><table class="table table-striped"><tbody>
                        <?php if($recent_transactions_result && $recent_transactions_result->num_rows > 0): while($tx = $recent_transactions_result->fetch_assoc()): ?>
                            <tr><td><i class="fas fa-<?php echo $tx['type'] == 'credit' ? 'plus text-success' : 'minus text-danger'; ?>"></i></td><td><strong>₹<?php echo number_format($tx['amount'], 2); ?></strong></td><td><?php echo htmlspecialchars($tx['description']); ?></td><td class="text-right text-muted"><?php echo date('d M, h:i A', strtotime($tx['created_at'])); ?></td></tr>
                        <?php endwhile; else: echo '<tr><td colspan="4" class="text-center p-3">No recent transactions found.</td></tr>'; endif; ?>
                    </tbody></table></div></div>
                </div>
                <div class="col-md-5">
                    <div class="card card-info card-outline"><div class="card-header"><h3 class="card-title">Users You Recently Added</h3></div><div class="card-body p-0"><ul class="products-list product-list-in-card pl-2 pr-2">
                        <?php if($recent_users_result && $recent_users_result->num_rows > 0): while($user = $recent_users_result->fetch_assoc()): ?>
                            <li class="item"><div class="product-info ml-0"><span class="product-title"><?php echo htmlspecialchars($user['username']); ?><span class="badge <?php echo ($user['role'] == 'seller') ? 'badge-success' : 'badge-info'; ?> float-right"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span></span><span class="product-description">Joined: <?php echo date('d M Y', strtotime($user['created_at'])); ?></span></div></li>
                        <?php endwhile; else: echo '<li class="item text-center p-4">You have not created any users yet.</li>'; endif; ?>
                    </ul></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>