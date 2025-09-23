<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller - Wallet Dashboard
 */

$page_title = 'My Wallet';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];

// Fetch current wallet balance
$stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$wallet = $result->fetch_assoc();
$current_balance = $wallet['balance'] ?? 0;
$stmt->close();
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
                <!-- Balance Card -->
                <div class="col-md-12">
                    <div class="card wallet-balance-card p-3 mb-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">CURRENT BALANCE</h5>
                                <span class="balance-amount">₹ <?php echo number_format($current_balance, 2); ?></span>
                            </div>
                            <div>
                                <a href="add_fund.php" class="btn btn-light"><i class="fas fa-plus-circle"></i> Add Funds</a>
                                <a href="transfer_balance.php" class="btn btn-outline-light"><i class="fas fa-exchange-alt"></i> Transfer Funds</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Transaction History -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Transaction History</h3>
                        </div>
                        <div class="card-body">
                            <div id="transaction-list-container" class="transaction-list">
                                <div class="text-center p-4"><span class="spinner-border spinner-border-sm"></span> Loading transactions...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
$(function() {
    // AJAX call to fetch and display transactions
    $.ajax({
        url: '../ajax/get_transactions.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const container = $('#transaction-list-container');
            container.empty();

            if (response.success && response.transactions.length > 0) {
                response.transactions.forEach(tx => {
                    const amountClass = tx.type === 'credit' ? 'transaction-amount-credit' : 'transaction-amount-debit';
                    const amountSign = tx.type === 'credit' ? '+' : '-';
                    const itemHtml = `
                        <div class="transaction-item">
                            <div>
                                <strong>${tx.description}</strong>
                                <div class="text-muted small">${tx.date}</div>
                            </div>
                            <div class="${amountClass}">${amountSign} ₹ ${tx.amount}</div>
                        </div>
                    `;
                    container.append(itemHtml);
                });
            } else {
                container.html('<div class="text-center p-4 text-muted">No transactions found.</div>');
            }
        },
        error: function() {
            $('#transaction-list-container').html('<div class="text-center p-4 text-danger">Failed to load transaction history.</div>');
        }
    });
});
</script>