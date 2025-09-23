<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * Unified VPS Control Panel
 * This page dynamically handles control and display for any user-owned VPS.
 */

// --- CORE INCLUDES ---
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/VirtualizorAPI.php';

// --- INITIAL SETUP & SECURITY CHECK ---
$order_id = (int)($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];
$vps_details = null;
$order = null;

if ($order_id <= 0) {
    die("Invalid Order ID specified.");
}

// Security Check: Fetch the order and ensure it belongs to the logged-in user.
$order_stmt = $conn->prepare(
    "SELECT o.*, p.name as package_name, p.provider_key 
     FROM vps_orders o 
     JOIN vps_packages p ON o.package_id = p.id 
     WHERE o.id = ? AND o.user_id = ?"
);
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

if (!$order) {
    // If no order is found, the user either doesn't own it or it doesn't exist.
    die("Access Denied: You do not have permission to view this service.");
}

// Load provider configurations
$providers = require __DIR__ . '/../includes/vps_providers_config.php';
$provider_config = $providers[$order['provider_key']] ?? null;

// Only proceed with API calls if the service is active and has a VPS ID
if ($order['status'] === 'active' && !empty($order['vps_id']) && $provider_config) {
    $api = new VirtualizorAPI($provider_config);
    $vps_details = $api->getVpsDetails($order['vps_id']);
}

$page_title = 'Manage: ' . htmlspecialchars($order['package_name']);


// --- AJAX ACTION HANDLER ---
// This block will process API requests sent from the page's JavaScript.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'An unknown error occurred.'];

    // Re-verify ownership and CSRF token for the API action
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $response['message'] = 'Invalid security token.';
        echo json_encode($response);
        exit();
    }
    
    // Only proceed if the API object was successfully created
    if (isset($api)) {
        $action = $_POST['action'];
        $vpsId = $order['vps_id'];

        switch ($action) {
            case 'start':
                if ($api->startVps($vpsId)) {
                    $response = ['success' => true, 'message' => 'VPS start command issued successfully!'];
                } else {
                    $response['message'] = 'Failed to start VPS. Please check the logs or contact support.';
                }
                break;
            case 'stop':
                if ($api->stopVps($vpsId)) {
                    $response = ['success' => true, 'message' => 'VPS stop command issued successfully!'];
                } else {
                    $response['message'] = 'Failed to stop VPS. Please check the logs or contact support.';
                }
                break;
            case 'reboot':
                if ($api->rebootVps($vpsId)) {
                    $response = ['success' => true, 'message' => 'VPS reboot command issued successfully!'];
                } else {
                    $response['message'] = 'Failed to reboot VPS. Please check the logs or contact support.';
                }
                break;
            // Add cases for other actions like reinstall OS, change password etc.
        }
    } else {
        $response['message'] = 'API could not be initialized for this service.';
    }

    echo json_encode($response);
    exit(); // Stop script execution after handling AJAX request
}

// --- STANDARD PAGE INCLUDES ---
require_once 'includes/navbar.php';
require_once 'includes/sidebar.php';
?>

<!-- Custom CSS for this page -->
<style>
    .status-badge { font-size: 1.2rem; font-weight: bold; padding: 0.5rem 1rem; }
    .control-btn { width: 120px; }
    .info-label { font-weight: 600; color: var(--text-secondary); }
    .info-value { font-weight: 500; color: var(--text-primary); }
    .copy-btn { cursor: pointer; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1><?php echo $page_title; ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php if ($order['status'] !== 'active' || !$vps_details): ?>
                <div class="alert alert-warning text-center">
                    <h4>Service Not Active</h4>
                    <p>
                        <?php
                            if ($order['status'] === 'pending') echo 'Your service is currently pending approval by an administrator.';
                            elseif ($order['status'] === 'suspended') echo 'This service has been suspended. Please contact support.';
                            elseif (!$provider_config) echo 'Provider configuration is missing for this service.';
                            elseif (empty($order['vps_id'])) echo 'The VPS ID has not been assigned by the administrator yet.';
                            else echo 'Could not retrieve VPS details. The server may be offline or undergoing maintenance. Please try again later.';
                        ?>
                    </p>
                </div>
            <?php else: // Main control panel content ?>
                <div class="row">
                    <!-- Server Controls -->
                    <div class="col-lg-4">
                        <div class="card card-primary card-outline">
                            <div class="card-header"><h3 class="card-title">Server Status & Controls</h3></div>
                            <div class="card-body text-center">
                                <?php $is_online = ($vps_details['status'] ?? 0) == 1; ?>
                                <p>
                                    <span class="badge <?php echo $is_online ? 'bg-success' : 'bg-danger'; ?> status-badge">
                                        <?php echo $is_online ? 'Online' : 'Offline'; ?>
                                    </span>
                                </p>
                                <div class="btn-group-vertical w-100 mt-3" role="group">
                                    <button class="btn btn-lg btn-success control-btn" data-action="start" <?php if ($is_online) echo 'disabled'; ?>>
                                        <i class="fas fa-play me-2"></i> Start
                                    </button>
                                    <button class="btn btn-lg btn-danger control-btn" data-action="stop" <?php if (!$is_online) echo 'disabled'; ?>>
                                        <i class="fas fa-stop me-2"></i> Stop
                                    </button>
                                    <button class="btn btn-lg btn-warning control-btn" data-action="reboot" <?php if (!$is_online) echo 'disabled'; ?>>
                                        <i class="fas fa-sync-alt me-2"></i> Reboot
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Server Information -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Server Information</h3></div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="info-label">Hostname:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($vps_details['hostname']); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="info-label">Main IP Address:</span>
                                        <span class="info-value">
                                            <span id="ip-address"><?php echo htmlspecialchars($vps_details['ips'][0]); ?></span>
                                            <i class="far fa-copy ms-2 copy-btn" data-copy-target="#ip-address" title="Copy IP"></i>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="info-label">Operating System:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($vps_details['os_name']); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="info-label">RAM:</span>
                                        <span class="info-value"><?php echo round($vps_details['ram'] / 1024, 2); ?> MB</span>
                                    </li>
                                     <li class="list-group-item d-flex justify-content-between">
                                        <span class="info-label">Disk Space:</span>
                                        <span class="info-value"><?php echo round($vps_details['space'] / 1024, 2); ?> GB</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
$(function() {
    // --- AJAX Handler for Server Controls ---
    $('.control-btn').on('click', function() {
        var button = $(this);
        var action = button.data('action');
        var actionText = action.charAt(0).toUpperCase() + action.slice(1);

        Swal.fire({
            title: 'Confirm ' + actionText,
            text: "Are you sure you want to " + action + " your server?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, ' + action + ' it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.ajax({
                    type: 'POST',
                    url: 'vps_control.php?order_id=<?php echo $order_id; ?>',
                    data: {
                        action: action,
                        csrf_token: '<?php echo generate_csrf_token(); ?>'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Request Failed!', 'Could not connect to the server. Please try again.', 'error');
                    },
                    complete: function() {
                        // Restore button state after a delay to show result
                        setTimeout(function() {
                             button.prop('disabled', false).html('<i class="fas fa-' + (action === 'start' ? 'play' : (action === 'stop' ? 'stop' : 'sync-alt')) + ' me-2"></i> ' + actionText);
                        }, 2000);
                    }
                });
            }
        });
    });

    // --- Clipboard Copy Functionality ---
    $('.copy-btn').on('click', function() {
        var targetSelector = $(this).data('copy-target');
        var textToCopy = $(targetSelector).text();
        
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Use your global toast function if available, or this simple one
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            Toast.fire({ icon: 'success', title: 'IP address copied!' });
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    });
});
</script>