<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.4 (Fixed)
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Custom Pricing (Fixed Modal UI & Reset Logic)
 */

$page_title = 'Custom Pricing';
require_once 'includes/header.php';
// ADDED: Check if it's an AJAX request early to prevent loading the full page
if (isset($_GET['ajax_get_products'])) {
    require_once __DIR__ . '/../includes/db.php'; // DB connection for AJAX
    
    $target_user_id_ajax = (int)$_GET['user_id'];
    
    // Fetch all products and join with any existing custom prices for this user
    $sql = "
        SELECT 
            p.id, p.name, p.price_super_seller,
            upp.price as custom_price
        FROM products p
        LEFT JOIN user_product_prices upp ON p.id = upp.product_id AND upp.user_id = ?
        ORDER BY p.name ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $target_user_id_ajax);
    $stmt->execute();
    $products = $stmt->get_result();

    // Start output buffering to capture HTML
    ob_start();
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Product Name</th>
                    <th class="text-center">Your Base Price</th>
                    <th class="text-center">Current Custom Price</th>
                    <th>Set New Price <small class="fw-normal">(Leave blank or enter 0 to reset)</small></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td class="text-center"><strong>₹<?php echo number_format($product['price_super_seller'], 2); ?></strong></td>
                        <td class="text-center">
                            <?php 
                                // CHANGED: Improved display logic for custom price
                                if ($product['custom_price'] !== null && $product['custom_price'] >= 0) {
                                    echo '₹' . number_format($product['custom_price'], 2);
                                } else {
                                    echo '<span class="text-muted fst-italic">Default</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" 
                                       name="prices[<?php echo $product['id']; ?>]" 
                                       placeholder="<?php echo number_format($product['price_super_seller'], 2); ?>"
                                       value="<?php echo ($product['custom_price'] !== null && $product['custom_price'] > 0) ? htmlspecialchars($product['custom_price']) : ''; ?>"
                                       min="0">
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php
    echo ob_get_clean(); // Send buffered HTML and exit
    exit();
}
// --- END AJAX HANDLER ---

require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];

// --- HANDLE FORM SUBMISSION FROM MODAL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }

    $user_id = (int)$_POST['user_id'];
    $prices = $_POST['prices'] ?? []; // Default to empty array if no prices are submitted
    $error = false;

    // --- REFACTORED & FIXED LOGIC ---
    $conn->begin_transaction(); // Use a transaction for data integrity

    try {
        // Prepare statements outside the loop for efficiency
        $product_stmt = $conn->prepare("SELECT price_super_seller FROM products WHERE id = ?");
        $upsert_stmt = $conn->prepare("INSERT INTO user_product_prices (user_id, product_id, price, set_by_user_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE price = VALUES(price)");
        $delete_stmt = $conn->prepare("DELETE FROM user_product_prices WHERE user_id = ? AND product_id = ?");

        foreach ($prices as $product_id => $price_str) {
            $product_id = (int)$product_id;
            // Treat empty string as 0 for reset purposes
            $price = !empty($price_str) ? (float)$price_str : 0.0;
            
            // If price is 0 or less, it's a reset request.
            if ($price <= 0) {
                $delete_stmt->bind_param("ii", $user_id, $product_id);
                $delete_stmt->execute();
            } else {
                // Otherwise, it's an update/insert request. Validate it.
                $product_stmt->bind_param("i", $product_id);
                $product_stmt->execute();
                $product_result = $product_stmt->get_result();
                
                if ($product_result->num_rows > 0) {
                    $product = $product_result->fetch_assoc();
                    if ($price < $product['price_super_seller']) {
                        throw new Exception("Error: Custom price for a product cannot be lower than your base price.");
                    }
                    // Price is valid, perform upsert
                    $upsert_stmt->bind_param("iidi", $user_id, $product_id, $price, $current_user_id);
                    $upsert_stmt->execute();
                }
            }
        }
        $conn->commit();
        $_SESSION['success_message'] = 'Custom prices updated successfully!';
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    redirect('custom_pricing.php');
}


// --- DATA FETCHING for main user list ---
$users_result = $conn->query("SELECT id, username, email FROM users WHERE role = 'super_seller' ORDER BY username ASC");

$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header"><h3 class="card-title">Set Custom Prices for Super Sellers</h3></div>
                <div class="card-body">
                    <p class="text-muted">Select a Super Seller from the list below to manage their product pricing in a pop-up window.</p>
                    <table id="usersTable" class="table table-bordered table-striped">
                        <thead><tr><th>Username</th><th>Email</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary manage-pricing-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#pricingModal"
                                                data-user-id="<?php echo $user['id']; ?>"
                                                data-user-name="<?php echo htmlspecialchars($user['username']); ?>">
                                            <i class="fas fa-tags"></i> Set Custom Prices
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

<div class="modal fade" id="pricingModal" tabindex="-1"><div class="modal-dialog modal-xl"><div class="modal-content">
    <form action="custom_pricing.php" method="post">
        <div class="modal-header">
            <h5 class="modal-title" id="pricingModalLabel">Custom Prices for...</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"> <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="user_id" id="modal_user_id">
            <div id="pricingTableContainer" class="text-center p-4">
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div></div></div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#usersTable').DataTable({"responsive": true, "lengthChange": false, "autoWidth": false});

    // Event Delegation for the "Manage Pricing" button
    $('#usersTable').on('click', '.manage-pricing-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const container = $('#pricingTableContainer');
        
        $('#pricingModalLabel').text('Custom Prices for ' + userName);
        $('#modal_user_id').val(userId);
        container.html('<div class="d-flex justify-content-center align-items-center p-5"><div class="spinner-border text-primary" role="status"></div><strong class="ms-3">Loading pricing...</strong></div>');

        $.ajax({
            url: 'custom_pricing.php', // It's better to be explicit with the URL
            type: 'GET',
            data: { ajax_get_products: 1, user_id: userId },
            success: responseHtml => container.html(responseHtml),
            error: () => container.html('<div class="alert alert-danger">Failed to load pricing information. Please try again.</div>')
        });
    });

    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>