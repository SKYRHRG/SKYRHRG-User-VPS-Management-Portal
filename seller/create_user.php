<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller - Create User Page
 */

$page_title = 'Create New User';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php'; // DB connection

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        // Sanitize inputs
        $username = sanitize_input($_POST['username']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $full_name = sanitize_input($_POST['full_name']);
        $password = $_POST['password'];
        
        // ** HIERARCHY RULE **: Seller can ONLY create 'user' role.
        $role = 'user';

        if (empty($username) || empty($email) || empty($password)) $errors[] = 'Username, Email, and Password are required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters long.';

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) $errors[] = 'Username or email already exists.';
            $stmt->close();
        }

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $created_by = $_SESSION['user_id']; // The logged-in seller is the creator.
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $username, $email, $hashed_password, $full_name, $role, $created_by);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'User "' . htmlspecialchars($username) . '" was created successfully!';
                redirect('manage_users.php');
            } else {
                $errors[] = 'Failed to create user. Please try again.';
            }
            $stmt->close();
        }
    }
}
?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
         <div class="container-fluid">
            <h1 class="m-0"><?php echo $page_title; ?></h1>
         </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New User's Details</h3>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger m-3">
                                <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="create_user.php" method="post">
                             <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="icon fas fa-info"></i> You are creating a standard user account.
                                </div>
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create User Account</button>
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