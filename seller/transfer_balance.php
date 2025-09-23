<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller - Transfer Balance
 */

$page_title = 'Transfer Balance';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }
    
    $recipient_id = (int)$_POST['recipient_id'];
    $amount = (float)$_POST['amount'];
    $description = sanitize_input($_POST['description']);

    if ($recipient_id > 0 && $amount > 0) {
        $conn->begin_transaction();
        try {
            // 1. Check sender's balance (with locking)
            $sender_wallet_res = $conn->query("SELECT balance FROM wallets WHERE user_id = $current_user_id FOR UPDATE");
            $sender_balance = $sender_wallet_res->fetch_assoc()['balance'] ?? 0;
            if ($sender_balance < $amount) {
                throw new Exception("Insufficient balance for this transfer.");
            }

            // 2. Verify recipient is a user created by this seller
            $verify_stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user' AND created_by = ?");
            $verify_stmt->bind_param("ii", $recipient_id, $current_user_id);
            $verify_stmt->execute();
            if ($verify_stmt->get_result()->num_rows === 0) {
                throw new Exception("Invalid recipient. You can only transfer to users you have created.");
            }
            $verify_stmt->close();
            
            // 3. Perform transfer
            $conn->query("UPDATE wallets SET balance = balance - $amount WHERE user_id = $current_user_id");
            $conn->query("INSERT INTO wallets (user_id, balance) VALUES ($recipient_id, $amount) ON DUPLICATE KEY UPDATE balance = balance + $amount");

            // 4. Log transactions
            $desc_sender = "Transferred to User ID $recipient_id. " . $description;
            $desc_recipient = "Received from Seller ID $current_user_id. " . $description;
            $stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'debit', ?, ?), (?, 'credit', ?, ?)");
            $stmt->bind_param("idsids", $current_user_id, $amount, $desc_sender, $recipient_id, $amount, $desc_recipient);
            $stmt->execute();
            
            $conn->commit();
            $_SESSION['success_message'] = '₹' . number_format($amount, 2) . ' transferred successfully!';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = 'Transfer failed: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = 'Invalid amount or recipient.';
    }
    redirect('transfer_balance.php');
}

// --- DATA FETCHING ---
// Fetch users created by this seller
$users_stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'user' AND created_by = ?");
$users_stmt->bind_param("i", $current_user_id);
$users_stmt->execute();
$users_result = $users_stmt->get_result();

// Session messages
$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">Transfer Funds to a User</h3></div>
                        <form action="transfer_balance.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Recipient User</label>
                                    <select class="form-control" name="recipient_id" required>
                                        <option value="">-- Select a User --</option>
                                        <?php while($user = $users_result->fetch_assoc()): ?>
                                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount to Transfer (INR)</label>
                                    <input type="number" step="0.01" class="form-control" name="amount" required>
                                </div>
                                <div class="form-group">
                                    <label>Description / Note</label>
                                    <input type="text" class="form-control" name="description" placeholder="e.g., Project payment">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Confirm & Transfer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
$(function() {
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo $success_message; ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo $error_message; ?>' }); <?php endif; ?>
});
</script>