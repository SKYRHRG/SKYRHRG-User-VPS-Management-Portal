<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0 (Modernized & Secured)
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Manage Super Sellers (Fully Integrated Modal UI)
 */

$page_title = 'Manage Super Sellers';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- [OPTIMIZED] AJAX Handler for Custom Pricing Table ---
// Placed at the top for performance. It exits immediately after returning data.
if (isset($_GET['ajax_get_prices'])) {
    session_start(); // Required for CSRF token if you add more security
    $target_user_id_ajax = (int)$_GET['user_id'];
    
    $sql = "
        SELECT p.id, p.name, p.price_super_seller, upp.price as custom_price
        FROM products p
        LEFT JOIN user_product_prices upp ON p.id = upp.product_id AND upp.user_id = ?
        ORDER BY p.name ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $target_user_id_ajax);
    $stmt->execute();
    $products = $stmt->get_result();

    ob_start();
    ?>
    <div class="table-responsive">
        <p class="text-muted small">Set a new price for each product. To revert a product to its default price, leave its input field blank or enter 0.</p>
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Product Name</th>
                    <th class="text-center">Base Price</th>
                    <th class="text-center">Current Custom Price</th>
                    <th style="width: 25%;">Set New Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="text-center"><strong>₹<?php echo number_format($product['price_super_seller'], 2); ?></strong></td>
                        <td class="text-center">
                            <?php echo ($product['custom_price'] !== null && $product['custom_price'] >= 0) ? '₹' . number_format($product['custom_price'], 2) : '<span class="badge bg-secondary">Default</span>'; ?>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" 
                                       name="prices[<?php echo $product['id']; ?>]"
                                       placeholder="<?php echo number_format($product['price_super_seller'], 2); ?>"
                                       value="<?php echo ($product['custom_price'] > 0) ? htmlspecialchars($product['custom_price']) : ''; ?>"
                                       min="0">
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    echo ob_get_clean();
    exit();
}


// --- [SECURED & FIXED] HANDLE ALL FORM SUBMISSIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) die('Invalid CSRF token.');
    $action = $_POST['action'] ?? '';
    $current_admin_id = $_SESSION['user_id'];

    // --- [SECURED] Logic for 'Adjust Balance' Modal ---
    if ($action === 'adjust_balance') {
        $user_id = (int)$_POST['user_id'];
        $adjustment_type = $_POST['adjustment_type']; // 'credit' or 'debit'
        $amount = abs((float)$_POST['amount']); // Ensure amount is positive
        $description = sanitize_input($_POST['description']);

        if ($user_id > 0 && $amount > 0 && !empty($description) && in_array($adjustment_type, ['credit', 'debit'])) {
            $conn->begin_transaction();
            try {
                // Use prepared statements to prevent SQL Injection
                if ($adjustment_type === 'credit') {
                    $stmt_wallet = $conn->prepare("INSERT INTO wallets (user_id, balance) VALUES (?, ?) ON DUPLICATE KEY UPDATE balance = balance + ?");
                    $stmt_wallet->bind_param("idd", $user_id, $amount, $amount);
                } else { // Debit
                    $stmt_wallet = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ? AND balance >= ?");
                    $stmt_wallet->bind_param("dii", $amount, $user_id, $amount);
                }
                $stmt_wallet->execute();

                // Check if the debit was successful
                if ($adjustment_type === 'debit' && $stmt_wallet->affected_rows === 0) {
                    throw new Exception("User has insufficient funds or does not exist.");
                }

                $stmt_trans = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, ?, ?, ?)");
                $stmt_trans->bind_param("isds", $user_id, $adjustment_type, $amount, $description);
                $stmt_trans->execute();
                
                $conn->commit();
                $_SESSION['success_message'] = 'Wallet adjusted successfully!';
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error_message'] = 'Failed to adjust balance: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error_message'] = 'Invalid data provided for balance adjustment.';
        }
        redirect('manage_super_sellers.php');
    }

    // --- [FIXED] Logic for 'Set Custom Prices' Modal ---
    if ($action === 'set_prices') {
        $target_user_id = (int)$_POST['user_id'];
        $prices = $_POST['prices'] ?? [];
        
        $conn->begin_transaction();
        try {
            // Prepare statements outside the loop for efficiency
            $cost_stmt = $conn->prepare("SELECT price_super_seller FROM products WHERE id = ?");
            $upsert_stmt = $conn->prepare("INSERT INTO user_product_prices (user_id, product_id, price, set_by_user_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
            $delete_stmt = $conn->prepare("DELETE FROM user_product_prices WHERE user_id = ? AND product_id = ?");

            foreach ($prices as $product_id => $price_str) {
                $product_id = (int)$product_id;
                $price = !empty($price_str) ? (float)$price_str : 0.0;
                
                // If price is 0 or less, it's a reset request. Delete the custom price.
                if ($price <= 0) {
                    $delete_stmt->bind_param("ii", $target_user_id, $product_id);
                    $delete_stmt->execute();
                } else {
                    // It's a new price. Validate against base price.
                    $cost_stmt->bind_param("i", $product_id);
                    $cost_stmt->execute();
                    $product = $cost_stmt->get_result()->fetch_assoc();

                    if ($product && $price < $product['price_super_seller']) {
                        throw new Exception("Custom price for a product cannot be lower than its base price of ₹" . number_format($product['price_super_seller'], 2));
                    }
                    
                    // Price is valid, perform insert/update
                    $upsert_stmt->bind_param("iidi", $target_user_id, $product_id, $price, $current_admin_id);
                    $upsert_stmt->execute();
                }
            }
            $conn->commit();
            $_SESSION['success_message'] = 'Custom prices updated successfully!';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = $e->getMessage();
        }
        redirect('manage_super_sellers.php');
    }
}

// --- DATA FETCHING for the main list ---
$users_result = $conn->query("SELECT u.id, u.username, u.email, u.status, w.balance FROM users u LEFT JOIN wallets w ON u.id = w.user_id WHERE u.role = 'super_seller' ORDER BY u.username ASC");
$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* --- [MODERNIZED] Custom CSS for new design --- */
    .user-info .user-name {
        font-weight: 600;
        color: #333;
        display: block;
    }
    .user-info .user-email {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .actions-dropdown .dropdown-toggle::after {
        display: none; /* Hide default caret */
    }
    .actions-dropdown .btn {
        border-radius: 50px;
    }
    .card-header-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .form-control-icon {
        position: relative;
    }
    .form-control-icon .form-icon {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .form-control-icon .form-control {
        padding-left: 2.8rem;
    }
</style>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Manage Super Sellers</h1>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header card-header-action">
                    <h3 class="card-title"><i class="fas fa-users-cog me-2"></i>All Super Seller Accounts</h3>
                    <a href="add_user.php?role=super_seller" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Create New</a>
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>User Details</th>
                                <th class="text-end">Balance</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                                            <span class="user-email"><?php echo htmlspecialchars($user['email']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold">₹<?php echo number_format($user['balance'] ?? 0, 2); ?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-<?php echo ($user['status'] == 'active') ? 'success' : 'danger'; ?>"><?php echo ucfirst($user['status']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown actions-dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item set-pricing-btn" href="#" data-userid="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>"><i class="fas fa-tags fa-fw me-2"></i>Set Prices</a></li>
                                                <li><a class="dropdown-item adjust-balance-btn" href="#" data-userid="<?php echo $user['id']; ?>" data-username="<?php echo htmlspecialchars($user['username']); ?>"><i class="fas fa-wallet fa-fw me-2"></i>Adjust Balance</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="edit_user.php?id=<?php echo $user['id']; ?>"><i class="fas fa-edit fa-fw me-2"></i>Edit User</a></li>
                                                <li><a class="dropdown-item text-danger delete-btn" href="#" data-userid="<?php echo $user['id']; ?>"><i class="fas fa-trash fa-fw me-2"></i>Delete User</a></li>
                                            </ul>
                                        </div>
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

<div class="modal fade" id="adjustBalanceModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Adjust Balance for <strong id="modal_username_balance"></strong></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="manage_super_sellers.php" method="post">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="adjust_balance">
            <input type="hidden" name="user_id" id="modal_user_id_balance">
            
            <div class="mb-3"><label class="form-label">Type</label><select class="form-select" name="adjustment_type" required><option value="credit">Credit (Add Funds)</option><option value="debit">Debit (Remove Funds)</option></select></div>
            <div class="mb-3 form-control-icon"><label class="form-label">Amount</label><i class="fas fa-rupee-sign form-icon"></i><input type="number" step="0.01" class="form-control" name="amount" required min="0.01"></div>
            <div class="mb-3 form-control-icon"><label class="form-label">Reason / Description</label><i class="fas fa-pencil-alt form-icon"></i><input type="text" class="form-control" name="description" required></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Apply Change</button></div>
    </form>
</div></div></div>

<div class="modal fade" id="pricingModal" tabindex="-1"><div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="pricingModalLabel">Custom Prices for...</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="manage_super_sellers.php" method="post">
        <div class="modal-body">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="action" value="set_prices">
            <input type="hidden" name="user_id" id="modal_user_id_pricing">
            <div id="pricingTableContainer" class="text-center p-4">
                 <div class="d-flex justify-content-center align-items-center p-5"><div class="spinner-border text-primary" role="status"></div><strong class="ms-3">Loading pricing...</strong></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save Custom Prices</button></div>
    </form>
</div></div></div>

<form id="deleteUserForm" action="manage_users.php" method="post" style="display: none;">
    <input type="hidden" name="delete_id" id="delete_user_id">
</form>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const table = $('#usersTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[0, 'asc']],
        "language": { "emptyTable": "No super sellers found." },
        "columnDefs": [
            { "orderable": false, "targets": 3 }, // Disable sorting on Actions column
            { "className": "dt-center", "targets": [2, 3] },
            { "className": "dt-right", "targets": 1 }
        ]
    });

    const pricingModal = new bootstrap.Modal(document.getElementById('pricingModal'));
    const balanceModal = new bootstrap.Modal(document.getElementById('adjustBalanceModal'));

    // Event Delegation for action buttons
    $('#usersTable tbody').on('click', '.set-pricing-btn', function(e) {
        e.preventDefault();
        const userId = $(this).data('userid');
        const userName = $(this).data('username');
        const container = $('#pricingTableContainer');
        
        $('#pricingModalLabel').text('Custom Prices for ' + userName);
        $('#modal_user_id_pricing').val(userId);
        container.html('<div class="d-flex justify-content-center align-items-center p-5"><div class="spinner-border text-primary" role="status"></div><strong class="ms-3">Loading pricing...</strong></div>');
        pricingModal.show();

        $.get('manage_super_sellers.php', { ajax_get_prices: 1, user_id: userId })
            .done(responseHtml => container.html(responseHtml))
            .fail(() => container.html('<div class="alert alert-danger">Failed to load pricing information.</div>'));
    });

    $('#usersTable tbody').on('click', '.adjust-balance-btn', function(e) {
        e.preventDefault();
        $('#modal_user_id_balance').val($(this).data('userid'));
        $('#modal_username_balance').text($(this).data('username'));
        balanceModal.show();
    });
    
    // --- [SECURED] Handle delete action safely ---
    $('#usersTable tbody').on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const userId = $(this).data('userid');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit a hidden form for deletion to use POST method
                $('#delete_user_id').val(userId);
                $('#deleteUserForm').submit();
            }
        });
    });

    // Toast notifications for success/error messages
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>