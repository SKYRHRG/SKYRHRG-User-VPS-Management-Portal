<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * User - My Profile
 */

$page_title = 'My Profile';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$current_user_id = $_SESSION['user_id'];
$errors = [];

// --- HANDLE PASSWORD CHANGE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid request token.';
    } else {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $errors[] = 'All password fields are required.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'New password and confirmation do not match.';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result && password_verify($current_password, $result['password'])) {
                // Update to new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $current_user_id);
                if ($update_stmt->execute()) {
                    $_SESSION['success_message'] = 'Your password has been updated successfully!';
                } else {
                    $errors[] = 'Failed to update password. Please try again.';
                }
                $update_stmt->close();
            } else {
                $errors[] = 'The current password you entered is incorrect.';
            }
        }
    }
}

// --- DATA FETCHING ---
$stmt = $conn->prepare("SELECT username, email, full_name, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Session messages
$success_message = $_SESSION['success_message'] ?? null; unset($_SESSION['success_message']);
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
                <div class="col-md-5">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <h3 class="profile-username text-center"><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></h3>
                            <p class="text-muted text-center"><?php echo htmlspecialchars($user['username']); ?></p>
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item"><b>Email</b> <a class="float-right"><?php echo htmlspecialchars($user['email']); ?></a></li>
                                <li class="list-group-item"><b>Member Since</b> <a class="float-right"><?php echo date('d M Y', strtotime($user['created_at'])); ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Change Password</h3></div>
                        <form action="profile.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="card-body">
                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger"><?php foreach ($errors as $error) echo "<p class='mb-0'>$error</p>"; ?></div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
<script>
$(function() {
    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    <?php if ($success_message): ?> Toast.fire({ icon: 'success', title: '<?php echo $success_message; ?>' }); <?php endif; ?>
});
</script>