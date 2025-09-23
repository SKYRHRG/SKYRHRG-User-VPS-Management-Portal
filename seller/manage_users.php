 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller - Manage Users Page (View-Only)
 */

$page_title = 'View Users';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch users created by THIS seller
$current_user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, username, email, full_name, status, created_at FROM users WHERE role = 'user' AND created_by = ? ORDER BY id DESC");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check for session messages
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0"><?php echo $page_title; ?></h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users You Have Created</h3>
                            <div class="card-tools">
                                <a href="create_user.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New User
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="usersTable" class="table table-bordered table-striped table-hover" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($row['status'] == 'active') ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
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

<?php 
require_once 'includes/footer.php'; 
?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
    $("#usersTable").DataTable({ "responsive": true, "lengthChange": false, "autoWidth": false });

    <?php if ($success_message): ?>
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '<?php echo $success_message; ?>', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
});
</script>