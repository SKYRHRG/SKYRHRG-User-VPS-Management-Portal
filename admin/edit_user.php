<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Edit User Page
 */

$page_title = 'Edit User';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$errors = [];
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect if no user ID is provided
if (!$user_id) {
    redirect('manage_users.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF Token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        // Sanitize and validate inputs
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $full_name = sanitize_input($_POST['full_name']);
        $role = sanitize_input($_POST['role']);
        $status = sanitize_input($_POST['status']);
        $password = $_POST['password'];
        
        $allowed_roles = ['admin', 'super_seller', 'seller', 'user'];
        $allowed_statuses = ['active', 'suspended', 'banned'];

        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
        if (!in_array($role, $allowed_roles)) $errors[] = 'Invalid role selected.';
        if (!in_array($status, $allowed_statuses)) $errors[] = 'Invalid status selected.';
        if (!empty($password) && strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long.';

        // Check if email already exists for another user
        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = 'Email address is already in use by another account.';
            }
            $stmt->close();
        }
        
        // If no errors, proceed with update
        if (empty($errors)) {
            if (!empty($password)) {
                // Update with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, role = ?, status = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $email, $full_name, $role, $status, $hashed_password, $user_id);
            } else {
                // Update without changing password
                $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, role = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $email, $full_name, $role, $status, $user_id);
            }

            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'User details updated successfully!';
                redirect('manage_users.php');
            } else {
                $errors[] = 'Failed to update user. Please try again.';
            }
            $stmt->close();
        }
    }
}

// Fetch user data for the form
$stmt = $conn->prepare("SELECT username, email, full_name, role, status FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // If user not found, redirect
    $_SESSION['error_message'] = 'User not found.';
    redirect('manage_users.php');
}
$stmt->close();

?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="manage_users.php">Users</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Update User Details</h3>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger m-3">
                                <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="edit_user.php?id=<?php echo $user_id; ?>" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    <small class="form-text text-muted">Username cannot be changed.</small>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    <small class="form-text text-muted">Leave blank to keep the current password.</small>
                                </div>
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
                                        <option value="seller" <?php if($user['role'] == 'seller') echo 'selected'; ?>>Seller</option>
                                        <option value="super_seller" <?php if($user['role'] == 'super_seller') echo 'selected'; ?>>Super Seller</option>
                                        <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active" <?php if($user['status'] == 'active') echo 'selected'; ?>>Active</option>
                                        <option value="suspended" <?php if($user['status'] == 'suspended') echo 'selected'; ?>>Suspended</option>
                                        <option value="banned" <?php if($user['status'] == 'banned') echo 'selected'; ?>>Banned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>