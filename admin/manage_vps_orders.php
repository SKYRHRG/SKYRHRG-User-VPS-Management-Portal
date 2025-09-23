<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Manage VPS Orders
 */

$page_title = 'Manage VPS Orders';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Load provider configs to display names
$providers = require __DIR__ . '/../includes/vps_providers_config.php';

// --- HANDLE FORM SUBMISSIONS FOR ORDER ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }

    $action = $_POST['action'] ?? '';
    $order_id = (int)($_POST['order_id'] ?? 0);

    if ($order_id > 0) {
        $new_status = '';
        $success_msg = '';
        $error_msg = 'Invalid action or order state.';

        try {
            switch ($action) {
                case 'approve_order':
                    $vps_id = (int)($_POST['vps_id'] ?? 0);
                    if ($vps_id > 0) {
                        $stmt = $conn->prepare("UPDATE vps_orders SET status = 'active', vps_id = ? WHERE id = ? AND status = 'pending'");
                        $stmt->bind_param("ii", $vps_id, $order_id);
                        if ($stmt->execute() && $stmt->affected_rows > 0) {
                            $success_msg = "Order #$order_id has been approved and activated!";
                        } else {
                            $error_msg = "Could not approve order #$order_id. It might have been already processed.";
                        }
                        $stmt->close();
                    } else {
                        $error_msg = "A valid VPS ID is required to approve an order.";
                    }
                    break;

                case 'cancel_order':
                    $new_status = 'canceled';
                    $success_msg = "Order #$order_id has been canceled.";
                    break;
                case 'suspend_order':
                    $new_status = 'suspended';
                    $success_msg = "Order #$order_id has been suspended.";
                    break;
                case 'unsuspend_order':
                    $new_status = 'active';
                    $success_msg = "Order #$order_id has been unsuspended and is now active.";
                    break;
            }

            if (!empty($new_status)) {
                $stmt = $conn->prepare("UPDATE vps_orders SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $new_status, $order_id);
                if ($stmt->execute()) {
                    // Success is handled below
                } else {
                     $error_msg = "Failed to update order status.";
                }
                $stmt->close();
            }

            if (!empty($success_msg)) {
                $_SESSION['success_message'] = $success_msg;
            } else {
                $_SESSION['error_message'] = $error_msg;
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = "An unexpected error occurred.";
            log_api_error('Database', 'VPS Order management failed.', ['error' => $e->getMessage()]);
        }
    }
    redirect('manage_vps_orders.php');
}


// --- DATA FETCHING WITH FILTERING ---
$status_filter = $_GET['status_filter'] ?? 'all';
$sql_where = "";
if ($status_filter !== 'all' && in_array($status_filter, ['pending', 'active', 'suspended', 'canceled'])) {
    $sql_where = " WHERE o.status = ?";
}

$sql = "
    SELECT 
        o.id, o.user_id, o.vps_id, o.status, o.created_at,
        u.username,
        p.name as package_name, p.provider_key
    FROM vps_orders o
    JOIN users u ON o.user_id = u.id
    JOIN vps_packages p ON o.package_id = p.id
    $sql_where
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($sql);
if ($sql_where) {
    $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$orders_result = $stmt->get_result();

// Helper for status badges
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-warning',
        'active' => 'bg-success',
        'suspended' => 'bg-danger',
        'canceled' => 'bg-secondary'
    ];
    return $badges[$status] ?? 'bg-light';
}

$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title"><i class="fas fa-tasks me-2"></i>All VPS Orders</h3>
                    <form method="get" class="form-inline">
                        <label class="me-2">Filter by Status:</label>
                        <select name="status_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="all" <?php if($status_filter == 'all') echo 'selected'; ?>>All</option>
                            <option value="pending" <?php if($status_filter == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="active" <?php if($status_filter == 'active') echo 'selected'; ?>>Active</option>
                            <option value="suspended" <?php if($status_filter == 'suspended') echo 'selected'; ?>>Suspended</option>
                            <option value="canceled" <?php if($status_filter == 'canceled') echo 'selected'; ?>>Canceled</option>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <table id="ordersTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Provider</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td><?php echo htmlspecialchars($order['package_name']); ?></td>
                                    <td><?php echo htmlspecialchars($providers[$order['provider_key']]['name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                                    <td><span class="badge <?php echo getStatusBadge($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($order['status'] == 'pending'): ?>
                                                <button class="btn btn-sm btn-success approve-btn" data-bs-toggle="modal" data-bs-target="#approveModal" data-id="<?php echo $order['id']; ?>" data-info="<?php echo htmlspecialchars($order['username'] . ' - ' . $order['package_name']); ?>">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                                <button class="btn btn-sm btn-danger action-btn" data-action="cancel_order" data-id="<?php echo $order['id']; ?>" data-text="This will permanently cancel the order.">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            <?php elseif ($order['status'] == 'active'): ?>
                                                <button class="btn btn-sm btn-warning action-btn" data-action="suspend_order" data-id="<?php echo $order['id']; ?>" data-text="This will suspend the user's service.">
                                                    <i class="fas fa-pause"></i> Suspend
                                                </button>
                                            <?php elseif ($order['status'] == 'suspended'): ?>
                                                <button class="btn btn-sm btn-success action-btn" data-action="unsuspend_order" data-id="<?php echo $order['id']; ?>" data-text="This will reactivate the user's service.">
                                                    <i class="fas fa-play"></i> Unsuspend
                                                </button>
                                            <?php endif; ?>
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

<!-- Approve Order Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="manage_vps_orders.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Approve VPS Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>You are approving order for: <strong id="approve-info"></strong></p>
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="action" value="approve_order">
                    <input type="hidden" name="order_id" id="approve_order_id">
                    <div class="mb-3">
                        <label for="vps_id" class="form-label">Virtualizor VPS ID <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="vps_id" id="vps_id" placeholder="Enter the numeric ID from the provider" required>
                        <small class="form-text text-muted">This ID is required to enable automated user controls.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm & Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Action Form -->
<form id="actionForm" action="manage_vps_orders.php" method="post" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="action" id="form_action">
    <input type="hidden" name="order_id" id="form_order_id">
</form>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    $('#ordersTable').DataTable({ "responsive": true, "autoWidth": false, "order": [[4, 'desc']] });

    // Handle Approve Modal
    $('.approve-btn').on('click', function() {
        var orderId = $(this).data('id');
        var orderInfo = $(this).data('info');
        $('#approve_order_id').val(orderId);
        $('#approve-info').text(orderInfo);
    });

    // Handle other actions with SweetAlert confirmation
    $('#ordersTable').on('click', '.action-btn', function() {
        var action = $(this).data('action');
        var orderId = $(this).data('id');
        var confirmationText = $(this).data('text');
        var actionName = action.split('_')[0].charAt(0).toUpperCase() + action.split('_')[0].slice(1);

        Swal.fire({
            title: 'Confirm ' + actionName,
            text: confirmationText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ' + actionName + ' it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#form_action').val(action);
                $('#form_order_id').val(orderId);
                $('#actionForm').submit();
            }
        });
    });

    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>