<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * Admin - Manage VPS Packages
 */

$page_title = 'Manage VPS Packages';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Load the provider configurations to populate the dropdown menu
$providers = require __DIR__ . '/../includes/vps_providers_config.php';

// --- HANDLE FORM SUBMISSIONS (CREATE/EDIT/DELETE) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token for security
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        die('Invalid CSRF token.');
    }

    $action = $_POST['action'] ?? '';

    // --- LOGIC TO ADD OR EDIT A PACKAGE ---
    if ($action === 'save_package') {
        $package_id = (int)($_POST['package_id'] ?? 0);
        $name = sanitize_input($_POST['name']);
        $provider_key = sanitize_input($_POST['provider_key']);
        $price = (float)$_POST['price'];
        $cpu = sanitize_input($_POST['cpu']);
        $ram = sanitize_input($_POST['ram']);
        $disk = sanitize_input($_POST['disk']);
        $bandwidth = sanitize_input($_POST['bandwidth']);
        $description = sanitize_input($_POST['description']);

        // --- Validation ---
        if (empty($name) || empty($provider_key) || $price <= 0 || empty($cpu) || empty($ram) || empty($disk)) {
            $_SESSION['error_message'] = 'Please fill in all required fields.';
        } elseif (!array_key_exists($provider_key, $providers)) {
            $_SESSION['error_message'] = 'Invalid provider selected.';
        } else {
            if ($package_id > 0) {
                // --- Update existing package ---
                $stmt = $conn->prepare("UPDATE vps_packages SET name=?, provider_key=?, price=?, cpu=?, ram=?, disk=?, bandwidth=?, description=? WHERE id=?");
                $stmt->bind_param("ssisssssi", $name, $provider_key, $price, $cpu, $ram, $disk, $bandwidth, $description, $package_id);
                $success_msg = 'Package updated successfully!';
            } else {
                // --- Insert new package ---
                $stmt = $conn->prepare("INSERT INTO vps_packages (name, provider_key, price, cpu, ram, disk, bandwidth, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssisssss", $name, $provider_key, $price, $cpu, $ram, $disk, $bandwidth, $description);
                $success_msg = 'Package created successfully!';
            }

            if ($stmt->execute()) {
                $_SESSION['success_message'] = $success_msg;
            } else {
                $_SESSION['error_message'] = 'Database error: Could not save the package.';
                log_api_error('Database', 'Failed to save VPS package.', ['error' => $stmt->error]);
            }
            $stmt->close();
        }
        redirect('manage_vps_packages.php');
    }

    // --- LOGIC TO DELETE A PACKAGE ---
    if ($action === 'delete_package') {
        $package_id = (int)($_POST['delete_id'] ?? 0);
        if ($package_id > 0) {
            $stmt = $conn->prepare("DELETE FROM vps_packages WHERE id = ?");
            $stmt->bind_param("i", $package_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Package deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete package.';
            }
            $stmt->close();
        }
        redirect('manage_vps_packages.php');
    }
}

// --- DATA FETCHING for the main list ---
$packages_result = $conn->query("SELECT * FROM vps_packages ORDER BY provider_key, price ASC");

// Session messages for toast notifications
$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null; unset($_SESSION['error_message']);
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?php echo $page_title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-sm-right" data-bs-toggle="modal" data-bs-target="#packageModal">
                        <i class="fas fa-plus me-1"></i> Create New Package
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-server me-2"></i>All Available VPS Packages</h3>
                </div>
                <div class="card-body">
                    <table id="packagesTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Package Name</th>
                                <th>Provider</th>
                                <th>Price</th>
                                <th>CPU</th>
                                <th>RAM</th>
                                <th>Disk</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($package = $packages_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($package['name']); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($providers[$package['provider_key']]['name'] ?? 'Unknown'); ?>
                                        </span>
                                    </td>
                                    <td><strong>₹<?php echo number_format($package['price'], 2); ?></strong></td>
                                    <td><?php echo htmlspecialchars($package['cpu']); ?></td>
                                    <td><?php echo htmlspecialchars($package['ram']); ?></td>
                                    <td><?php echo htmlspecialchars($package['disk']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#packageModal"
                                                data-package='<?php echo json_encode($package); ?>'>
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn"
                                                data-id="<?php echo $package['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($package['name']); ?>">
                                            <i class="fas fa-trash"></i> Delete
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

<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="manage_vps_packages.php" method="post" id="packageForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="action" value="save_package">
                    <input type="hidden" name="package_id" id="package_id" value="0">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Package Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="price" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Provider</label>
                        <select class="form-select" name="provider_key" id="provider_key" required>
                            <option value="">-- Select a Provider --</option>
                            <?php foreach($providers as $key => $provider): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($provider['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3"><label>CPU</label><input type="text" class="form-control" name="cpu" id="cpu" required placeholder="e.g., 2 Cores"></div>
                        <div class="col-md-3 mb-3"><label>RAM</label><input type="text" class="form-control" name="ram" id="ram" required placeholder="e.g., 4 GB"></div>
                        <div class="col-md-3 mb-3"><label>Disk</label><input type="text" class="form-control" name="disk" id="disk" required placeholder="e.g., 50 GB SSD"></div>
                        <div class="col-md-3 mb-3"><label>Bandwidth</label><input type="text" class="form-control" name="bandwidth" id="bandwidth" placeholder="e.g., 1 TB"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="deleteForm" action="manage_vps_packages.php" method="post" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="action" value="delete_package">
    <input type="hidden" name="delete_id" id="delete_id">
</form>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    // Initialize DataTable
    $('#packagesTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "order": [[1, 'asc']]
    });

    // Handle modal opening for ADD
    $('#packageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var packageData = button.data('package'); // Extract info from data-* attributes
        var modal = $(this);

        if (packageData) { // If editing
            modal.find('#modalTitle').text('Edit Package: ' + packageData.name);
            modal.find('#package_id').val(packageData.id);
            modal.find('#name').val(packageData.name);
            modal.find('#provider_key').val(packageData.provider_key);
            modal.find('#price').val(packageData.price);
            modal.find('#cpu').val(packageData.cpu);
            modal.find('#ram').val(packageData.ram);
            modal.find('#disk').val(packageData.disk);
            modal.find('#bandwidth').val(packageData.bandwidth);
            modal.find('#description').val(packageData.description);
        } else { // If adding new
            modal.find('#modalTitle').text('Create New Package');
            $('#packageForm')[0].reset(); // Reset form fields
            modal.find('#package_id').val('0');
        }
    });

    // Handle Delete button click
    $('#packagesTable').on('click', '.delete-btn', function() {
        var packageId = $(this).data('id');
        var packageName = $(this).data('name');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete the package '" + packageName + "'. This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete_id').val(packageId);
                $('#deleteForm').submit();
            }
        });
    });

    // Toast notifications for success/error messages
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo addslashes($success_message); ?>' }); <?php endif; ?>
    <?php if ($error_message): ?> Toast.fire({ icon: 'error', title: '<?php echo addslashes($error_message); ?>' }); <?php endif; ?>
});
</script>