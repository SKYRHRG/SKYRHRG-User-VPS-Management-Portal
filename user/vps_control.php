<?php
/**
 * Unified VPS Control Panel (improved)
 */

require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/VirtualizorAPI.php';

// --- Setup & Security ---
$order_id = (int)($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;
$vps_details = null;
$order = null;

if ($order_id <= 0) {
    die("Invalid Order ID.");
}

// Fetch order (include optional stored credentials and main_ip)
$order_stmt = $conn->prepare("
    SELECT o.*, p.name as package_name, p.provider_key, o.main_ip, o.vps_username, o.vps_password
    FROM vps_orders o
    JOIN vps_packages p ON o.package_id = p.id
    WHERE o.id = ? AND o.user_id = ?
");
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

if (!$order) {
    die("Access Denied: You do not have permission to view this service.");
}

$providers = require __DIR__ . '/../includes/vps_providers_config.php';
$provider_config = $providers[$order['provider_key']] ?? null;

if ($order['status'] === 'active' && !empty($order['vps_id']) && $provider_config) {
    $api = new VirtualizorAPI($provider_config);
    // Use vpsmanage to get a single VPS' detail (docs show act=vpsmanage&svs=VID). :contentReference[oaicite:6]{index=6}
    $vps_details = $api->getVpsDetails($order['vps_id']);
}

// OS Templates for reinstall (if API available)
$os_templates = [];
if (isset($api) && $vps_details) {
    $os_templates = $api->getOsTemplates() ?? [];
}

$page_title = 'Manage: ' . htmlspecialchars($order['package_name']);

// --- AJAX POST handler ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $resp = ['success' => false, 'message' => 'Unknown error'];

    try {
        // CSRF check
        if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
            $resp['message'] = 'Invalid security token.';
            echo json_encode($resp);
            exit;
        }

        // API object must exist
        if (!isset($api) || !$vps_details) {
            $resp['message'] = 'Service unavailable or API not configured.';
            echo json_encode($resp);
            exit;
        }

        $action = $_POST['action'];
        $vpsId = (int)$order['vps_id'];

        switch ($action) {
            case 'start':
                if ($api->startVps($vpsId)) {
                    $resp = ['success' => true, 'message' => 'Start command accepted.'];
                } else {
                    $resp['message'] = 'Failed to start VPS.';
                }
                break;

            case 'stop':
                if ($api->stopVps($vpsId)) {
                    $resp = ['success' => true, 'message' => 'Stop command accepted.'];
                } else {
                    $resp['message'] = 'Failed to stop VPS.';
                }
                break;

            case 'reboot':
                if ($api->rebootVps($vpsId)) {
                    $resp = ['success' => true, 'message' => 'Reboot command accepted.'];
                } else {
                    $resp['message'] = 'Failed to reboot VPS.';
                }
                break;

            case 'reinstall':
                $newos = (int)($_POST['osid'] ?? 0);
                $newpass = trim($_POST['newpass'] ?? '');
                $conf = trim($_POST['conf'] ?? '');
                if ($newos > 0 && $newpass && $newpass === $conf) {
                    if ($api->reinstallOs($vpsId, $newos, $newpass)) {
                        $resp = ['success' => true, 'message' => 'Reinstall command sent.'];
                    } else {
                        $resp['message'] = 'Failed to send reinstall command.';
                    }
                } else {
                    $resp['message'] = 'Select OS and matching password.';
                }
                break;

            default:
                $resp['message'] = 'Invalid action.';
        }
    } catch (Exception $e) {
        // Ensure we always return JSON (and also log)
        if (function_exists('log_api_error')) {
            log_api_error('VPS Control', 'Exception in vps_control.php: ' . $e->getMessage(), ['post' => $_POST, 'order_id' => $order_id]);
        } else {
            error_log("VPS Control Exception: " . $e->getMessage());
        }
        $resp['message'] = 'Server error processing request.';
    }

    echo json_encode($resp);
    exit;
}

// If we have a vps_details and order doesn't have main_ip stored, attempt to persist it (best-effort)
if (!empty($vps_details) && empty($order['main_ip'])) {
    // attempt to extract primary IP from returned structure
    $primaryIp = '';
    if (!empty($vps_details['ips'])) {
        // ips might be array of strings or array of arrays
        $first = reset($vps_details['ips']);
        if (is_array($first)) {
            $primaryIp = $first['ip'] ?? ($first[0] ?? '');
        } else {
            $primaryIp = $first;
        }
    } elseif (!empty($vps_details['ip'])) {
        $primaryIp = $vps_details['ip'];
    }

    if ($primaryIp) {
        // Try to update DB; this is optional, ignore DB errors
        $tryStmt = $conn->prepare("UPDATE vps_orders SET main_ip = ? WHERE id = ?");
        if ($tryStmt) {
            $tryStmt->bind_param("si", $primaryIp, $order_id);
            @$tryStmt->execute();
            $tryStmt->close();
            // update local copy so UI shows it
            $order['main_ip'] = $primaryIp;
        }
    }
}

require_once 'includes/navbar.php';
require_once 'includes/sidebar.php';
?>

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
/* Colorful, compact styles & small animations */
.status-badge { font-size: 1.15rem; font-weight: 700; padding: 0.45rem 0.9rem; border-radius: 999px; display:inline-block; }
.bg-online { background: linear-gradient(90deg,#28a745,#45d17d); color: #fff; box-shadow: 0 6px 20px rgba(37,201,122,0.12); transform: translateY(0); transition: transform .18s ease; }
.bg-offline { background: linear-gradient(90deg,#dc3545,#ff6b6b); color:#fff; box-shadow: 0 6px 20px rgba(255,82,82,0.12); }
.control-btn { width: 100%; padding: .9rem; font-weight:600; border-radius:12px; transition:transform .12s ease, box-shadow .12s ease; }
.control-btn:hover { transform: translateY(-3px); box-shadow: 0 6px 14px rgba(0,0,0,0.08); }
.btn-reinstall { background: linear-gradient(90deg,#ff8c00,#ffb84d); color:#fff; border:none; }
.info-label { font-weight:700; color:#6b7280; }
.info-value { font-weight:600; color:#111827; }
.copy-btn { cursor:pointer; font-size:1.05rem; margin-left:8px; opacity:0.85; transition: opacity .12s ease; }
.copy-btn:hover { opacity:1; transform: scale(1.05); }
.small-muted { font-size:0.9rem; color:#6b7280; }
.card { border-radius:12px; box-shadow: 0 6px 18px rgba(20,23,28,0.04); }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid"><h1 class="m-0"><?php echo $page_title; ?></h1></div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if ($order['status'] !== 'active' || !$vps_details): ?>
        <div class="alert alert-warning text-center">
          <h4>Service Unavailable</h4>
          <p>
            <?php
              if ($order['status'] === 'pending') echo 'Your service is pending approval.';
              elseif ($order['status'] === 'suspended') echo 'This service has been suspended. Contact support.';
              elseif (!$provider_config) echo 'Provider configuration missing for this service.';
              elseif (empty($order['vps_id'])) echo 'The VPS ID has not been assigned yet.';
              else echo 'Could not fetch VPS details. Server might be offline or credentials are invalid.';
            ?>
          </p>
        </div>
      <?php else: ?>

        <div class="row">
          <!-- Controls -->
          <div class="col-lg-4 mb-3">
            <div class="card card-body text-center">
              <h5 class="mb-3">Server Status & Controls</h5>
              <?php
                $statusVal = $vps_details['status'] ?? $vps_details['power'] ?? '';
                $is_online = ($statusVal === '1' || $statusVal === 1 || strtolower((string)$statusVal) === 'running' || strtolower((string)$statusVal) === 'on' || strtolower((string)$statusVal) === 'online');
              ?>
              <p>
                <span class="status-badge <?php echo $is_online ? 'bg-online' : 'bg-offline'; ?>">
                  <?php echo $is_online ? 'Online' : 'Offline'; ?>
                </span>
              </p>

              <div class="d-grid gap-2 mt-4">
                <button class="btn btn-lg control-btn btn-success" data-action="start" <?php if($is_online) echo 'disabled'; ?>>
                  <i class="fas fa-play me-2"></i> Start
                </button>
                <button class="btn btn-lg control-btn btn-danger" data-action="stop" <?php if(!$is_online) echo 'disabled'; ?>>
                  <i class="fas fa-stop me-2"></i> Stop
                </button>
                <button class="btn btn-lg control-btn btn-warning" data-action="reboot" <?php if(!$is_online) echo 'disabled'; ?>>
                  <i class="fas fa-sync-alt me-2"></i> Reboot
                </button>
                <button class="btn btn-lg btn-reinstall control-btn" data-bs-toggle="modal" data-bs-target="#reinstallModal">
                  <i class="fas fa-redo-alt me-2"></i> Reinstall OS
                </button>
              </div>
            </div>
          </div>

          <!-- Info -->
          <div class="col-lg-8 mb-3">
            <div class="card">
              <div class="card-header"><h5 class="card-title">Server Information</h5></div>
              <div class="card-body">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="info-label">Hostname</div>
                      <div class="small-muted"><?php echo htmlspecialchars($vps_details['hostname'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($vps_details['hostname'] ?? 'N/A'); ?></div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="info-label">Main IP Address</div>
                      <div class="small-muted">Primary IP assigned to VPS</div>
                    </div>
                    <div class="info-value">
                      <?php
                        $ip = $order['main_ip'] ?? '';
                        if (!$ip) {
                            if (!empty($vps_details['ips'])) {
                                $first = reset($vps_details['ips']);
                                if (is_array($first)) {
                                    $ip = $first['ip'] ?? ($first[0] ?? '');
                                } else {
                                    $ip = $first;
                                }
                            } elseif (!empty($vps_details['ip'])) {
                                $ip = $vps_details['ip'];
                            }
                        }
                        $ip = $ip ?: 'N/A';
                      ?>
                      <span id="ip-address"><?php echo htmlspecialchars($ip); ?></span>
                      <i class="far fa-copy copy-btn ms-2" data-copy-target="#ip-address" title="Copy IP"></i>
                    </div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="info-label">Operating System</div>
                    <div class="info-value"><?php echo htmlspecialchars($vps_details['os_name'] ?? $vps_details['ostemplate'] ?? 'N/A'); ?></div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="info-label">RAM</div>
                    <div class="info-value"><?php
                        if (isset($vps_details['ram'])) {
                            // API sometimes returns MB or integer; try to present reasonably
                            $r = (float)$vps_details['ram'];
                            // if large value (MB) show MB else attempt kb -> MB
                            echo ($r > 1024 ? round($r/1024,2) . ' MB' : round($r,2) . ' MB');
                        } else {
                            echo 'N/A';
                        }
                      ?></div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="info-label">Disk Space</div>
                    <div class="info-value"><?php
                        if (isset($vps_details['space'])) {
                            $s = (float)$vps_details['space'];
                            echo ( $s > 10 ? round($s,2) . ' GB' : round($s,2) . ' GB');
                        } else echo 'N/A';
                      ?></div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="info-label">Username</div>
                      <div class="small-muted">May be stored when the admin approves the order</div>
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($order['vps_username'] ?: ($vps_details['user'] ?? $vps_details['username'] ?? 'N/A')); ?></div>
                  </li>

                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="info-label">Password</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['vps_password'] ?: ($vps_details['password'] ?? $vps_details['pass'] ?? 'N/A')); ?></div>
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

<!-- Reinstall Modal -->
<div class="modal fade" id="reinstallModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="reinstallForm" method="post">
        <div class="modal-header">
          <h5 class="modal-title">Reinstall Operating System</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
          <input type="hidden" name="action" value="reinstall">
          <div class="mb-3">
            <label for="osid" class="form-label">OS</label>
            <select class="form-select" name="osid" id="osid" required>
              <option value="">-- Choose OS --</option>
              <?php
                if (!empty($os_templates) && is_array($os_templates)) {
                    // os_templates might be nested e.g. ['kvm' => [osid => ['name'=>..]]]
                    $flat = [];
                    // flatten somewhat:
                    foreach ($os_templates as $k=>$v) {
                        if (is_array($v)) {
                            foreach ($v as $osid=>$meta) {
                                $name = is_array($meta) ? ($meta['name'] ?? json_encode($meta)) : (string)$meta;
                                $flat[$osid] = $name;
                            }
                        } else {
                            // fallback
                            $flat[$k] = is_string($v) ? $v : json_encode($v);
                        }
                    }
                    foreach ($flat as $osid => $name) {
                        echo '<option value="'.htmlspecialchars($osid).'">'.htmlspecialchars($name).'</option>';
                    }
                } else {
                    echo '<option value="">(No OS templates available)</option>';
                }
              ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Root Password</label>
            <input type="password" class="form-control" name="newpass" id="newpass" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="conf" id="conf" required>
          </div>
          <div class="alert alert-warning small">
            <strong>Warning:</strong> Reinstalling will wipe the VPS. Make sure you have backups.
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-reinstall">Reinstall OS</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {
    // Copy IP
    $('.copy-btn').on('click', function(){
        var target = $(this).data('copy-target');
        var text = $(target).text();
        navigator.clipboard.writeText(text).then(function(){
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            Toast.fire({ icon: 'success', title: 'Copied!' });
        });
    });

    // Control buttons
    $('.control-btn').on('click', function(){
        var btn = $(this);
        var action = btn.data('action');
        if (!action) return;

        Swal.fire({
            title: action.charAt(0).toUpperCase() + action.slice(1),
            text: "Are you sure you want to " + action + " the VPS?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "Yes, " + action + " it!"
        }).then(function(result){
            if (!result.isConfirmed) return;

            var originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

            $.ajax({
                type: 'POST',
                url: 'vps_control.php?order_id=<?php echo $order_id; ?>',
                data: {
                    action: action,
                    csrf_token: '<?php echo generate_csrf_token(); ?>'
                },
                dataType: 'json',
                success: function(res){
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success').then(()=> location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'Operation failed', 'error');
                    }
                },
                error: function(xhr, status, err){
                    // Try to show server returned message if possible
                    var text = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error, could not send request.';
                    Swal.fire('Error', text, 'error');
                },
                complete: function(){
                    setTimeout(function(){
                        btn.prop('disabled', false).html(originalHtml);
                    }, 1200);
                }
            });
        });
    });

    // Reinstall form
    $('#reinstallForm').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        Swal.fire({
            title: 'Reinstall OS?',
            text: "This will erase all data.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Reinstall!'
        }).then(function(result){
            if (!result.isConfirmed) return;
            $.ajax({
                type: 'POST',
                url: 'vps_control.php?order_id=<?php echo $order_id; ?>',
                data: form.serialize(),
                dataType: 'json',
                success: function(res){
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success').then(()=> location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'Failed to send reinstall', 'error');
                    }
                },
                error: function(xhr){
                    var text = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error, could not send request.';
                    Swal.fire('Error', text, 'error');
                }
            });
        });
    });
});
</script>
