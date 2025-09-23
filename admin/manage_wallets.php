<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.3
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Manage Wallets (Fixed & Redesigned)
 */

$page_title = 'Manage Wallets';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- HANDLE FORM SUBMISSIONS (ADJUST BALANCE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'adjust_balance') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) die('Invalid CSRF token.');

    $user_id = (int)$_POST['user_id'];
    $adjustment_type = $_POST['adjustment_type'];
    $amount = (float)$_POST['amount'];
    $description = sanitize_input($_POST['description']);

    if ($user_id > 0 && $amount > 0 && !empty($description) && in_array($adjustment_type, ['credit', 'debit'])) {
        $conn->begin_transaction();
        try {
            if ($adjustment_type === 'credit') {
                $conn->query("INSERT INTO wallets (user_id, balance) VALUES ($user_id, $amount) ON DUPLICATE KEY UPDATE balance = balance + $amount");
            } else { // debit
                $wallet_res = $conn->query("SELECT balance FROM wallets WHERE user_id = $user_id FOR UPDATE");
                $current_balance = $wallet_res->fetch_assoc()['balance'] ?? 0;
                if ($current_balance < $amount) throw new Exception("User has insufficient funds to deduct.");
                $conn->query("UPDATE wallets SET balance = balance - $amount WHERE user_id = $user_id");
            }
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isds", $user_id, $adjustment_type, $amount, $description);
            $stmt->execute();
            $conn->commit();
            $_SESSION['success_message'] = 'Wallet balance adjusted successfully!';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = 'Failed to adjust balance: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = 'Invalid data provided for balance adjustment.';
    }
    // Redirect to the same page with the filter preserved
    $role_filter = $_POST['role_filter'] ?? 'all';
    redirect('manage_wallets.php?role_filter=' . $role_filter);
}

// --- DATA FETCHING WITH FILTER ---
$role_filter = $_GET['role_filter'] ?? 'all';
$sql_where = "";
$params = [];
$types = "";

if ($role_filter !== 'all' && in_array($role_filter, ['super_seller', 'seller', 'user'])) {
    $sql_where = " WHERE u.role = ?";
    $params[] = $role_filter;
    $types = "s";
}

$sql = "SELECT u.id, u.username, u.email, u.role, w.balance FROM users u LEFT JOIN wallets w ON u.id = w.user_id $sql_where ORDER BY w.balance DESC, u.username ASC";
$stmt = $conn->prepare($sql);
if ($role_filter !== 'all') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$wallets_result = $stmt->get_result();

$total_balance = $conn->query("SELECT SUM(balance) as total FROM wallets")->fetch_assoc()['total'] ?? 0;

$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
.user-avatar { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
.user-info { margin-left: 12px; }
.user-info .user-name { font-weight: 600; }
.user-info .user-email { color: #6c757d; font-size: 0.9em; }
.balance-amount { font-size: 1.1rem; font-weight: 600; }
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
                <div class="col-12"><div class="small-box bg-success"><div class="inner"><h3>₹<?php echo number_format($total_balance, 2); ?></h3><p>Total Balance in All Wallets</p></div><div class="icon"><i class="fas fa-wallet"></i></div></div></div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">All User Wallets</h3>
                    <div class="card-tools">
                        <form method="get" class="form-inline">
                            <label for="role_filter" class="mr-2">Filter by Role:</label>
                            <select name="role_filter" id="role_filter" class="form-select form-select-sm mr-2" onchange="this.form.submit()">
                                <option value="all" <?php if($role_filter == 'all') echo 'selected'; ?>>All Roles</option>
                                <option value="super_seller" <?php if($role_filter == 'super_seller') echo 'selected'; ?>>Super Sellers</option>
                                <option value="seller" <?php if($role_filter == 'seller') echo 'selected'; ?>>Sellers</option>
                                <option value="user" <?php if($role_filter == 'user') echo 'selected'; ?>>Users</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table id="walletsTable" class="table table-hover responsive" width="100%">
                        <thead><tr><th>User</th><th>Role</th><th>Balance</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($row = $wallets_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://placehold.co/40x40/007bff/ffffff?text=<?php echo strtoupper(substr($row['username'], 0, 1)); ?>" alt="Avatar" class="user-avatar">
                                        <div class="user-info">
                                            <div class="user-name"><?php echo htmlspecialchars($row['username']); ?></div>
                                            <div class="user-email"><?php echo htmlspecialchars($row['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $row['role']))); ?></span>
                                </td>
                                <td><span class="balance-amount">₹<?php echo number_format($row['balance'] ?? 0, 2); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary adjust-btn" 
                                            data-bs-toggle="modal" data-bs-target="#adjustBalanceModal"
                                            data-userid="<?php echo $row['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($row['username']); ?>">
                                        <i class="fas fa-edit"></i> Adjust
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="adjustBalanceModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Adjust Balance for <strong id="modal_username"></strong></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="manage_wallets.php" method="post">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="adjust_balance">
            <input type="hidden" name="user_id" id="modal_user_id">
            <input type="hidden" name="role_filter" value="<?php echo $role_filter; ?>"> <div class="mb-3"><label class="form-label">Adjustment Type</label><select class="form-select" name="adjustment_type" required><option value="credit">Credit (Add Funds)</option><option value="debit">Debit (Deduct Funds)</option></select></div>
            <div class="mb-3"><label class="form-label">Amount (₹)</label><input type="number" step="0.01" class="form-control" name="amount" required></div>
            <div class="mb-3"><label class="form-label">Reason / Description</label><input type="text" class="form-control" name="description" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Apply Adjustment</button></div>
    </form>
</div></div></div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#walletsTable').DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "order": [[2, 'desc']]
    });

    // FIX: Use event delegation to ensure buttons work on all pages
    $('#walletsTable').on('click', '.adjust-btn', function() {
        $('#modal_user_id').val($(this).data('userid'));
        $('#modal_username').text($(this).data('username'));
    });
    
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>