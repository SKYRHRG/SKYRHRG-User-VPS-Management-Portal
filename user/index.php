<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * User - Main Dashboard (Updated with VPS Integration)
 */

$page_title = 'Dashboard';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];

// --- [UPDATED] DATA FETCHING for Dashboard Widgets ---

// 1. Get Wallet Balance
$wallet_stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$wallet_stmt->bind_param("i", $current_user_id);
$wallet_stmt->execute();
$current_balance = $wallet_stmt->get_result()->fetch_assoc()['balance'] ?? 0;
$wallet_stmt->close();

// 2. Get Active VPS Services Count (from the new vps_orders table)
$services_stmt = $conn->prepare("SELECT COUNT(id) as count FROM vps_orders WHERE user_id = ? AND status = 'active'");
$services_stmt->bind_param("i", $current_user_id);
$services_stmt->execute();
$active_vps_count = $services_stmt->get_result()->fetch_assoc()['count'];
$services_stmt->close();

// 3. Get Pending VPS Orders Count (from the new vps_orders table)
$pending_orders_stmt = $conn->prepare("SELECT COUNT(id) as count FROM vps_orders WHERE user_id = ? AND status = 'pending'");
$pending_orders_stmt->bind_param("i", $current_user_id);
$pending_orders_stmt->execute();
$pending_orders_count = $pending_orders_stmt->get_result()->fetch_assoc()['count'];
$pending_orders_stmt->close();

// 4. Get Recent Transactions (limited to last 5 for the dashboard)
$transactions_stmt = $conn->prepare("SELECT type, amount, description, created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$transactions_stmt->bind_param("i", $current_user_id);
$transactions_stmt->execute();
$recent_transactions = $transactions_stmt->get_result();
$transactions_stmt->close();

require_once 'includes/navbar.php';
require_once 'includes/sidebar.php';
?>

<!-- Custom CSS for modern dashboard elements -->
<style>
    .stat-card-item {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }
    .stat-card-item:last-child {
        border-bottom: none;
    }
    .stat-card-item .stat-icon {
        font-size: 1.5rem;
        color: var(--primary-color);
        margin-right: 1rem;
        width: 40px;
        text-align: center;
    }
    .stat-card-item .stat-info .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .stat-card-item .stat-info .stat-label {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
    .quick-action-link {
        display: block;
        padding: 1.5rem 1rem;
        background-color: var(--bg-light);
        border-radius: 0.5rem;
        text-align: center;
        color: var(--text-secondary);
        transition: background-color 0.3s, color 0.3s;
        border: 1px solid var(--border-color);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .quick-action-link:hover {
        background-color: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    .quick-action-link i {
        font-size: 2rem;
        display: block;
        margin-bottom: 0.75rem;
    }
    .quick-action-link span {
        font-weight: 500;
    }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-8">
                    <!-- Quick Actions Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header"><h3 class="card-title">Quick Actions</h3></div>
                        <div class="card-body">
                           <div class="row">
                               <div class="col-6 col-md-3 mb-3">
                                   <a href="add_fund.php" class="quick-action-link"><i class="fas fa-wallet"></i> <span>Add Funds</span></a>
                               </div>
                               <div class="col-6 col-md-3 mb-3">
                                   <a href="../vps_store.php" class="quick-action-link"><i class="fas fa-shopping-cart"></i> <span>VPS Store</span></a>
                               </div>
                               <div class="col-6 col-md-3 mb-3">
                                   <a href="my_orders.php" class="quick-action-link"><i class="fas fa-history"></i> <span>My Orders</span></a>
                               </div>
                               <div class="col-6 col-md-3 mb-3">
                                   <a href="profile.php" class="quick-action-link"><i class="fas fa-user-edit"></i> <span>My Profile</span></a>
                               </div>
                           </div>
                        </div>
                    </div>

                    <!-- Recent Transactions Card -->
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title">Recent Wallet Activity</h3></div>
                        <div class="card-body p-0">
                           <div class="px-3">
                               <?php if ($recent_transactions->num_rows > 0): ?>
                                   <?php while($tx = $recent_transactions->fetch_assoc()): ?>
                                   <div class="transaction-item">
                                       <div>
                                           <strong><?php echo htmlspecialchars($tx['description']); ?></strong>
                                           <div class="text-muted small"><?php echo date('d M Y, h:i A', strtotime($tx['created_at'])); ?></div>
                                       </div>
                                       <div class="transaction-amount-<?php echo $tx['type']; ?>">
                                           <?php echo ($tx['type'] === 'credit' ? '+' : '-'); ?> ₹ <?php echo number_format($tx['amount'], 2); ?>
                                       </div>
                                   </div>
                                   <?php endwhile; ?>
                               <?php else: ?>
                                   <p class="text-center text-muted p-4">You have no recent transactions.</p>
                               <?php endif; ?>
                           </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="add_fund.php">View Full Transaction History</a>
                        </div>
                    </div>
                </div>

                <!-- Account Overview Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title">Account Overview</h3></div>
                        <div class="card-body p-0">
                            <div class="stat-card-item">
                                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                                <div class="stat-info">
                                    <div class="stat-number">₹ <?php echo number_format($current_balance, 2); ?></div>
                                    <div class="stat-label">Wallet Balance</div>
                                </div>
                            </div>
                            <div class="stat-card-item">
                                <div class="stat-icon"><i class="fas fa-server"></i></div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $active_vps_count; ?></div>
                                    <div class="stat-label">Active VPS Services</div>
                                </div>
                            </div>
                            <div class="stat-card-item">
                                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                                <div class="stat-info">
                                    <div class="stat-number"><?php echo $pending_orders_count; ?></div>
                                    <div class="stat-label">Pending Orders</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>