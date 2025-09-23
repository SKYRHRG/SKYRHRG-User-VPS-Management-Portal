<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.2
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Commission Reports
 */

$page_title = 'Commission Reports';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- DATE FILTERING LOGIC ---
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// --- DATA FETCHING & STATISTICS ---
$date_condition = "WHERE t.type = 'credit' AND t.description LIKE 'Commission from sale%' AND DATE(t.created_at) BETWEEN '$start_date' AND '$end_date'";

// 1. Fetch all commission transactions for the selected period
$sql = "
    SELECT 
        t.id, t.amount, t.created_at, t.description,
        u.username as recipient_username
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    $date_condition
    ORDER BY t.created_at DESC
";
$transactions_result = $conn->query($sql);

// 2. Calculate Statistics
$stats_sql = "
    SELECT
        COUNT(id) as total_transactions,
        SUM(amount) as total_commission
    FROM transactions t
    $date_condition
";
$stats_result = $conn->query($stats_sql)->fetch_assoc();
$total_commission = $stats_result['total_commission'] ?? 0;
$total_transactions = $stats_result['total_transactions'] ?? 0;

// 3. Find Top Earner
$top_earner_sql = "
    SELECT SUM(t.amount) as total_earned, u.username
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    $date_condition
    GROUP BY t.user_id
    ORDER BY total_earned DESC
    LIMIT 1
";
$top_earner_result = $conn->query($top_earner_sql)->fetch_assoc();
$top_earner_name = $top_earner_result['username'] ?? 'N/A';
$top_earner_amount = $top_earner_result['total_earned'] ?? 0;

// Helper to get buyer username from description to avoid N+1 queries
$all_users = [];
$users_res = $conn->query("SELECT id, username FROM users");
while($row = $users_res->fetch_assoc()) {
    $all_users[$row['id']] = $row['username'];
}
?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Row -->
            <div class="row">
                <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-hand-holding-usd"></i></span><div class="info-box-content"><span class="info-box-text">Total Commission Paid</span><span class="info-box-number">₹ <?php echo number_format($total_commission, 2); ?></span></div></div></div>
                <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-exchange-alt"></i></span><div class="info-box-content"><span class="info-box-text">Total Transactions</span><span class="info-box-number"><?php echo $total_transactions; ?></span></div></div></div>
                <div class="col-md-4"><div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-trophy"></i></span><div class="info-box-content"><span class="info-box-text">Top Earner</span><span class="info-box-number"><?php echo htmlspecialchars($top_earner_name); ?> (₹ <?php echo number_format($top_earner_amount, 2); ?>)</span></div></div></div>
            </div>

            <!-- Main Report Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Commission Log</h3>
                    <div class="card-tools">
                        <form method="get" class="form-inline">
                            <label for="start_date" class="mr-2">From:</label>
                            <input type="date" name="start_date" class="form-control mr-2" value="<?php echo $start_date; ?>">
                            <label for="end_date" class="mr-2">To:</label>
                            <input type="date" name="end_date" class="form-control mr-2" value="<?php echo $end_date; ?>">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <table id="commissionTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Txn ID</th>
                                <th>Recipient</th>
                                <th>Amount</th>
                                <th>Original Order ID</th>
                                <th>Original Buyer</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $transactions_result->fetch_assoc()): 
                                // Parse details from description string
                                $buyer_id = 'N/A';
                                $order_id = 'N/A';
                                sscanf($row['description'], "Commission from sale to User ID #%d (Order #%d)", $buyer_id, $order_id);
                                $buyer_username = $all_users[$buyer_id] ?? 'Unknown';
                            ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['recipient_username']); ?></td>
                                <td><strong>₹ <?php echo number_format($row['amount'], 2); ?></strong></td>
                                <td><a href="view_order.php?id=<?php echo $order_id; ?>">#<?php echo $order_id; ?></a></td>
                                <td><?php echo htmlspecialchars($buyer_username); ?></td>
                                <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
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
<!-- DataTables & Buttons JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(function () {
    $("#commissionTable").DataTable({
        "responsive": true, 
        "lengthChange": false, 
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "order": [[ 5, "desc" ]] // Sort by date descending
    }).buttons().container().appendTo('#commissionTable_wrapper .col-md-6:eq(0)');
});
</script>