<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * VPS Store Page
 */

$page_title = 'VPS Store';

// The store is part of the user-facing area, so we use the user's header
require_once 'user/includes/header.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/vps_providers_config.php';

// --- HANDLE PURCHASE REQUEST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }

    $package_id = (int)($_POST['package_id'] ?? 0);
    $user_id = $_SESSION['user_id'];

    if ($package_id > 0) {
        $conn->begin_transaction();
        try {
            // 1. Get package details and lock the row to prevent race conditions
            $package_stmt = $conn->prepare("SELECT price, name FROM vps_packages WHERE id = ? FOR UPDATE");
            $package_stmt->bind_param("i", $package_id);
            $package_stmt->execute();
            $package = $package_stmt->get_result()->fetch_assoc();
            $package_stmt->close();

            // 2. Get user's wallet balance and lock the row
            $wallet_stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ? FOR UPDATE");
            $wallet_stmt->bind_param("i", $user_id);
            $wallet_stmt->execute();
            $wallet = $wallet_stmt->get_result()->fetch_assoc();
            $wallet_stmt->close();

            $user_balance = $wallet['balance'] ?? 0;

            // 3. Check for sufficient funds
            if (!$package || $user_balance < $package['price']) {
                throw new Exception("You do not have sufficient funds to purchase this package. Please add funds to your wallet.");
            }

            // 4. Deduct amount from wallet
            $new_balance = $user_balance - $package['price'];
            $update_wallet_stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
            $update_wallet_stmt->bind_param("di", $new_balance, $user_id);
            $update_wallet_stmt->execute();
            $update_wallet_stmt->close();

            // 5. Log the debit transaction
            $description = "Purchased VPS Package: " . $package['name'];
            $log_trans_stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'debit', ?, ?)");
            $log_trans_stmt->bind_param("ids", $user_id, $package['price'], $description);
            $log_trans_stmt->execute();
            $log_trans_stmt->close();
            
            // 6. Create the VPS order with 'pending' status
            $create_order_stmt = $conn->prepare("INSERT INTO vps_orders (user_id, package_id, status) VALUES (?, ?, 'pending')");
            $create_order_stmt->bind_param("ii", $user_id, $package_id);
            $create_order_stmt->execute();
            $create_order_stmt->close();

            // If all steps succeeded, commit the transaction
            $conn->commit();
            $_SESSION['success_message'] = "Package purchased successfully! Your order is now pending approval.";

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = "Purchase failed: " . $e->getMessage();
            log_api_error('VPS Store Purchase', $e->getMessage(), ['user_id' => $user_id, 'package_id' => $package_id]);
        }
    }
    redirect('vps_store.php');
}


// --- DATA FETCHING FOR DISPLAY ---

// Get provider names from config
$providers = require __DIR__ . '/includes/vps_providers_config.php';

// Fetch all packages and group them by provider
$packages_result = $conn->query("SELECT * FROM vps_packages ORDER BY provider_key, price ASC");
$grouped_packages = [];
while ($row = $packages_result->fetch_assoc()) {
    $grouped_packages[$row['provider_key']][] = $row;
}

// Fetch current user's wallet balance for display
$user_id = $_SESSION['user_id'];
$balance_stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$balance_stmt->bind_param("i", $user_id);
$balance_stmt->execute();
$current_balance = $balance_stmt->get_result()->fetch_assoc()['balance'] ?? 0;
$balance_stmt->close();


$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);

require_once 'user/includes/navbar.php';
require_once 'user/includes/sidebar.php';
?>

<!-- Custom CSS for the store page -->
<style>
    .product-card-v2 {
        background-color: #1F2937;
        border: 1px solid #374151;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .product-card-v2:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.2), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        border-color: var(--primary-color);
    }
    .card-body { flex-grow: 1; display: flex; flex-direction: column; }
    .product-title { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
    .feature-list { list-style: none; padding: 0; margin: 1.5rem 0; flex-grow: 1; }
    .feature-list li { display: flex; align-items: center; margin-bottom: 0.75rem; color: var(--text-secondary); }
    .feature-list i { color: var(--primary-color); margin-right: 1rem; width: 20px; text-align: center; }
    .product-price-v2 { font-size: 2.5rem; font-weight: 800; color: var(--text-primary); }
    .product-price-v2 .price-period { font-size: 1rem; font-weight: 500; color: var(--text-secondary); }
    .balance-highlight { background-color: rgba(0, 169, 255, 0.1); border-left: 4px solid var(--primary-color); }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1><?php echo $page_title; ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <div class="alert balance-highlight p-3 rounded-lg mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Your Wallet Balance: <strong>₹<?php echo number_format($current_balance, 2); ?></strong></h5>
                    <small>Ensure you have sufficient balance before purchasing.</small>
                </div>
                <a href="user/add_fund.php" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle me-1"></i> Add Funds</a>
            </div>

            <?php foreach ($providers as $key => $provider): ?>
                <?php if (!empty($grouped_packages[$key])): ?>
                    <h3 class="mt-4 mb-3"><?php echo htmlspecialchars($provider['name']); ?> Plans</h3>
                    <div class="row">
                        <?php foreach ($grouped_packages[$key] as $package): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="product-card-v2">
                                    <div class="card-body">
                                        <div>
                                            <h4 class="product-title"><?php echo htmlspecialchars($package['name']); ?></h4>
                                            <p class="text-secondary"><?php echo htmlspecialchars($package['description']); ?></p>
                                        </div>
                                        <ul class="feature-list">
                                            <li><i class="fas fa-microchip"></i> <?php echo htmlspecialchars($package['cpu']); ?> CPU</li>
                                            <li><i class="fas fa-memory"></i> <?php echo htmlspecialchars($package['ram']); ?> RAM</li>
                                            <li><i class="fas fa-hdd"></i> <?php echo htmlspecialchars($package['disk']); ?> Storage</li>
                                            <?php if (!empty($package['bandwidth'])): ?>
                                                <li><i class="fas fa-exchange-alt"></i> <?php echo htmlspecialchars($package['bandwidth']); ?> Bandwidth</li>
                                            <?php endif; ?>
                                        </ul>
                                        <div class="mt-auto text-center">
                                            <div class="product-price-v2">₹<?php echo number_format($package['price'], 0); ?><span class="price-period">/month</span></div>
                                            <button class="btn btn-primary btn-lg w-100 mt-3 purchase-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#purchaseModal"
                                                    data-package-id="<?php echo $package['id']; ?>"
                                                    data-package-name="<?php echo htmlspecialchars($package['name']); ?>"
                                                    data-package-price="<?php echo number_format($package['price'], 2); ?>">
                                                <i class="fas fa-shopping-cart me-2"></i> Buy Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<!-- Purchase Confirmation Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="vps_store.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Your Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="package_id" id="modal_package_id">
                    
                    <p>You are about to purchase the following package:</p>
                    <h4 class="text-center" id="modal_package_name"></h4>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Price:</span> <strong id="modal_package_price"></strong></div>
                    <div class="d-flex justify-content-between"><span>Your Balance:</span> <span>₹<?php echo number_format($current_balance, 2); ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between text-success"><strong>New Balance After Purchase:</strong> <strong id="modal_new_balance"></strong></div>
                    <div id="insufficient-funds-alert" class="alert alert-danger mt-3" style="display: none;">
                        <strong>Insufficient Funds!</strong> Please add more funds to your wallet to complete this purchase.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="confirmPurchaseBtn">
                        <i class="fas fa-check-circle me-2"></i>Confirm & Pay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'user/includes/footer.php'; ?>
<script>
$(function() {
    // Handle modal data population when a purchase button is clicked
    $('#purchaseModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var packageId = button.data('package-id');
        var packageName = button.data('package-name');
        var packagePrice = parseFloat(button.data('package-price'));
        var userBalance = parseFloat(<?php echo $current_balance; ?>);

        var modal = $(this);
        modal.find('#modal_package_id').val(packageId);
        modal.find('#modal_package_name').text(packageName);
        modal.find('#modal_package_price').text('₹' + packagePrice.toFixed(2));
        
        var newBalance = userBalance - packagePrice;
        modal.find('#modal_new_balance').text('₹' + newBalance.toFixed(2));

        // Show/hide insufficient funds warning and disable button
        if (newBalance < 0) {
            $('#insufficient-funds-alert').show();
            $('#confirmPurchaseBtn').prop('disabled', true);
        } else {
            $('#insufficient-funds-alert').hide();
            $('#confirmPurchaseBtn').prop('disabled', false);
        }
    });

    // Toast notifications
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>