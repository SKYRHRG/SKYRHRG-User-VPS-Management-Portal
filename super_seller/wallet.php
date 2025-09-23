<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller - My Wallet (Modern Design)
 */

$page_title = 'My Wallet';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];

// --- DATA FETCHING ---

// Fetch current wallet balance
$stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$wallet = $result->fetch_assoc();
$current_balance = $wallet['balance'] ?? 0;
$stmt->close();

// Fetch total credited amount
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'credit'");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$total_credited = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch total debited amount
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM transactions WHERE user_id = ? AND type = 'debit'");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$total_debited = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch all transactions for the history table
$transactions_result = $conn->query("SELECT type, amount, description, created_at FROM transactions WHERE user_id = $current_user_id ORDER BY created_at DESC");

?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
.balance-card {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: .5rem;
}
.balance-card .display-4 { font-weight: 700; }
.balance-card .btn-light { font-weight: 600; }
.text-credit { color: #28a745; font-weight: 600; }
.text-debit { color: #dc3545; font-weight: 600; }
.badge { font-size: 0.8rem; padding: 0.4em 0.7em; border-radius: 12px; }
</style>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card card-body balance-card text-center h-100 justify-content-center">
                        <h6 class="text-uppercase text-white-50">Current Balance</h6>
                        <h2 class="display-4">₹<?php echo number_format($current_balance, 2); ?></h2>
                        <div class="mt-3">
                            <a href="transfer_balance.php" class="btn btn-light"><i class="fas fa-exchange-alt mr-2"></i>Transfer Balance</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-md-6"><div class="small-box bg-success"><div class="inner"><h3>₹<?php echo number_format($total_credited, 2); ?></h3><p>Total Credited (All Time)</p></div><div class="icon"><i class="fas fa-arrow-down"></i></div></div></div>
                        <div class="col-md-6"><div class="small-box bg-danger"><div class="inner"><h3>₹<?php echo number_format($total_debited, 2); ?></h3><p>Total Debited (All Time)</p></div><div class="icon"><i class="fas fa-arrow-up"></i></div></div></div>
                        <div class="col-12"><div class="small-box bg-info"><div class="inner"><h3>₹<?php echo number_format($total_credited - $total_debited, 2); ?></h3><p>Total Turnover</p></div><div class="icon"><i class="fas fa-calculator"></i></div></div></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Full Transaction History</h3>
                        </div>
                        <div class="card-body">
                            <table id="transactionsTable" class="table table-hover responsive" width="100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($transactions_result && $transactions_result->num_rows > 0): ?>
                                        <?php while($tx = $transactions_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('d M Y, h:i A', strtotime($tx['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($tx['description']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo ($tx['type'] == 'credit') ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($tx['type']); ?>
                                                </span>
                                            </td>
                                            <td class="text-<?php echo ($tx['type'] == 'credit') ? 'credit' : 'debit'; ?>">
                                                <?php echo ($tx['type'] == 'credit' ? '+' : '-'); ?> ₹<?php echo number_format($tx['amount'], 2); ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(function() {
    $('#transactionsTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, 'desc']] // Sort by date descending by default
    });
});
</script>