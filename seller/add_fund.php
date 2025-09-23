<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller - Add Funds (Updated)
 */

$page_title = 'Add Funds';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];
$upi_id = 'samit7669-4@okhdfcbank'; // Your UPI ID

// --- DATA FETCHING for Deposit History ---
$stmt = $conn->prepare("SELECT amount, utr_number, status, created_at, updated_at FROM deposit_requests WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$deposit_history = $stmt->get_result();
$stmt->close();
?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
                <!-- Add Funds Form -->
                <div class="col-lg-5">
                    <div class="card card-primary h-100">
                        <div class="card-header"><h3 class="card-title">Create Deposit Request</h3></div>
                        <form id="depositForm" action="../ajax/process_deposit.php" method="post">
                            <div class="card-body">
                                <div class="alert alert-info py-2">
                                    <p class="mb-1"><strong>Step 1:</strong> Enter Amount & Generate QR.</p>
                                    <p class="mb-1"><strong>Step 2:</strong> Scan & Pay with any UPI App.</p>
                                    <p class="mb-0"><strong>Step 3:</strong> Submit the UTR/Transaction ID.</p>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount (INR)</label>
                                    <input type="number" step="1" min="1" class="form-control" id="amount" name="amount" required>
                                </div>
                                
                                <div class="text-center my-3">
                                    <div class="qr-code-container">
                                        <img id="qrCodeImage" src="" alt="UPI QR Code" style="display: none; max-width: 250px;">
                                        <p id="qrPlaceholder">Enter an amount to generate QR code.</p>
                                    </div>
                                    <p class="mt-2 mb-0"><strong>Pay to UPI ID:</strong></p>
                                    <p><code><?php echo $upi_id; ?></code></p>
                                </div>

                                <div class="form-group">
                                    <label for="utr_number">UTR / Transaction ID</label>
                                    <input type="text" class="form-control" id="utr_number" name="utr_number" placeholder="Enter the 12-digit number from your app" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary w-100">Submit Deposit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Deposit History -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-header"><h3 class="card-title">Deposit History</h3></div>
                        <div class="card-body">
                            <table id="depositHistoryTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>UTR</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $deposit_history->fetch_assoc()): ?>
                                    <tr>
                                        <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['utr_number']); ?></td>
                                        <td>
                                            <?php
                                                $status = $row['status'];
                                                $badge_class = 'secondary';
                                                if ($status == 'approved') $badge_class = 'success';
                                                if ($status == 'rejected') $badge_class = 'danger';
                                                if ($status == 'pending') $badge_class = 'warning';
                                                echo "<span class='badge bg-$badge_class'>" . ucfirst($status) . "</span>";
                                            ?>
                                        </td>
                                        <td data-sort="<?php echo strtotime($row['created_at']); ?>"><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
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
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    // Initialize DataTable for history
    $('#depositHistoryTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[ 3, "desc" ]] // Sort by date descending
    });

    // Generate QR code when amount is entered
    $('#amount').on('input', function() {
        const amount = $(this).val();
        const upiId = '<?php echo $upi_id; ?>';
        if (amount && amount > 0) {
            $('#qrPlaceholder').hide();
            window.generateUpiQrCode(upiId, amount, 'qrCodeImage');
        } else {
            $('#qrCodeImage').hide();
            $('#qrPlaceholder').show();
        }
    });

    // Handle form submission with our updated AJAX function
    $('#depositForm').on('submit', window.handleAjaxFormSubmit);
});
</script>