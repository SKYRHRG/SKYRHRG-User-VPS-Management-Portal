<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Manage Deposit Requests
 */

$page_title = 'Manage Deposits';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- HANDLE ACTIONS (APPROVE / REJECT) ---
$approve_id = isset($_GET['approve_id']) ? (int)$_GET['approve_id'] : 0;
$reject_id = isset($_GET['reject_id']) ? (int)$_GET['reject_id'] : 0;

if ($approve_id > 0) {
    // Fetch request details
    $req_stmt = $conn->prepare("SELECT user_id, amount FROM deposit_requests WHERE id = ? AND status = 'pending'");
    $req_stmt->bind_param("i", $approve_id);
    $req_stmt->execute();
    $request = $req_stmt->get_result()->fetch_assoc();
    $req_stmt->close();

    if ($request) {
        $user_id = $request['user_id'];
        $amount = $request['amount'];

        $conn->begin_transaction();
        try {
            // 1. Update wallet balance
            $conn->query("INSERT INTO wallets (user_id, balance) VALUES ($user_id, $amount) ON DUPLICATE KEY UPDATE balance = balance + $amount");
            // 2. Log transaction
            $desc = "Deposit approved (Request ID: $approve_id)";
            $tran_stmt = $conn->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'credit', ?, ?)");
            $tran_stmt->bind_param("ids", $user_id, $amount, $desc);
            $tran_stmt->execute();
            $tran_stmt->close();
            // 3. Update request status
            $conn->query("UPDATE deposit_requests SET status = 'approved' WHERE id = $approve_id");
            
            $conn->commit();
            $_SESSION['success_message'] = 'Deposit request approved successfully!';
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = 'Error approving request: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = 'Invalid or already processed request.';
    }
    redirect('manage_deposits.php');
}

if ($reject_id > 0) {
    $stmt = $conn->prepare("UPDATE deposit_requests SET status = 'rejected' WHERE id = ? AND status = 'pending'");
    $stmt->bind_param("i", $reject_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success_message'] = 'Deposit request has been rejected.';
    } else {
        $_SESSION['error_message'] = 'Failed to reject request or it was already processed.';
    }
    $stmt->close();
    redirect('manage_deposits.php');
}


// --- DATA FETCHING ---
$requests_result = $conn->query("SELECT dr.*, u.username FROM deposit_requests dr JOIN users u ON dr.user_id = u.id WHERE dr.status = 'pending' ORDER BY dr.created_at ASC");

// Session messages
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
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
            <div class="card">
                <div class="card-header"><h3 class="card-title">Pending Deposit Requests</h3></div>
                <div class="card-body">
                    <table id="depositsTable" class="table table-bordered table-hover">
                        <thead>
                            <tr><th>ID</th><th>User</th><th>Amount</th><th>UTR Number</th><th>Date</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = $requests_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['utr_number']); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <a href="manage_deposits.php?approve_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success"
                                       data-confirm="Are you sure?" data-confirm-title="Confirm Approval" data-confirm-text="This will add ₹<?php echo number_format($row['amount'], 2); ?> to the user's wallet.">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <a href="manage_deposits.php?reject_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger"
                                       data-confirm="Are you sure?" data-confirm-title="Confirm Rejection">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
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

<?php require_once 'includes/footer.php'; ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#depositsTable').DataTable({"order": [[0, "desc"]]});
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo $success_message; ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo $error_message; ?>' }); <?php endif; ?>
});
</script>