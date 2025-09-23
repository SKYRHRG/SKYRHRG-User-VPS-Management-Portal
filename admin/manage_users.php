<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Manage Users Page (Modern Design)
 */

$page_title = 'Manage Users';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- HELPER FUNCTION FOR BADGES ---
function get_badge_class($type, $value) {
    if ($type == 'role') {
        switch (strtolower($value)) {
            case 'admin': return 'badge-purple';
            case 'super_seller': return 'badge-teal';
            case 'seller': return 'badge-info';
            default: return 'badge-secondary';
        }
    }
    if ($type == 'status') {
        switch (strtolower($value)) {
            case 'active': return 'badge-success';
            case 'suspended': return 'badge-warning';
            case 'banned': return 'badge-danger';
            default: return 'badge-light';
        }
    }
    return 'badge-light';
}

// Fetch all users from the database
$result = $conn->query("SELECT id, username, email, full_name, role, status, created_at FROM users ORDER BY id DESC");

// Check for success/error message from session
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<style>
/* Custom Badge Styles */
.badge-purple { background-color: #6f42c1; color: white; }
.badge-teal { background-color: #20c997; color: white; }
.badge { font-size: 0.8rem; padding: 0.4em 0.7em; border-radius: 12px; }
.user-avatar { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
.user-info { margin-left: 12px; }
.user-info .user-name { font-weight: 600; }
.user-info .user-email { color: #6c757d; font-size: 0.9em; }
</style>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $page_title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">All System Users</h3>
                            <a href="create_user.php" class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>Add New User
                            </a>
                        </div>
                        <div class="card-body">
                            <table id="usersTable" class="table table-hover responsive" width="100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://placehold.co/40x40/007bff/ffffff?text=<?php echo strtoupper(substr($row['username'], 0, 1)); ?>" alt="User Avatar" class="user-avatar">
                                                <div class="user-info">
                                                    <div class="user-name"><?php echo htmlspecialchars($row['username']); ?></div>
                                                    <div class="user-email"><?php echo htmlspecialchars($row['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo get_badge_class('role', $row['role']); ?>">
                                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $row['role']))); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo get_badge_class('status', $row['status']); ?>">
                                                <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn" 
                                                   data-username="<?php echo htmlspecialchars($row['username']); ?>" 
                                                   title="Delete">
                                                   <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
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
$conn->close();
require_once 'includes/footer.php'; 
?>

<!-- DataTables & Plugins -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

<script>
$(function () {
    // Initialize DataTables
    var table = $("#usersTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "order": [[0, 'desc']] // Order by ID descending by default
    });
    
    table.buttons().container().appendTo('#usersTable_wrapper .col-md-6:eq(0)');

    // Display Toast Notifications
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    <?php if ($success_message): ?>
        Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' });
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' });
    <?php endif; ?>

    // Handle Delete Confirmation
    $('#usersTable').on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const username = $(this).data('username');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the user "${username}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
});
</script>