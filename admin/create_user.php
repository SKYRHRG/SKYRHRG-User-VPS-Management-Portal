<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Create User Page (with Manual/Random Generation)
 */

$page_title = 'Create User';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php'; // DB connection

$errors = [];
$user_created_successfully = false;
$new_user_details = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation would be here in a real app
    // if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) { ... }

    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password']; // Don't trim passwords
    $role = $_POST['role'];
    $allowed_roles = ['super_seller', 'seller', 'user'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $errors[] = 'All fields with * are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!in_array($role, $allowed_roles)) {
        $errors[] = 'Invalid role selected.';
    }

    // If no validation errors, check for existing user
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Username or email already exists.';
        }
        $stmt->close();
    }

    // If still no errors, insert into database
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // The 'created_by' field should be set from the logged-in user's session ID
        $created_by = $_SESSION['user_id'] ?? null;
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $email, $hashed_password, $full_name, $role, $created_by);
        
        if ($stmt->execute()) {
            // Success! Prepare to show the details instead of redirecting
            $user_created_successfully = true;
            $new_user_details = [
                'username' => $username,
                'password' => $password, // The plain-text password
                'email' => $email,
                'role' => ucfirst(str_replace('_', ' ', $role))
            ];
        } else {
            $errors[] = 'Failed to create user. Please try again. Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

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
                        <li class="breadcrumb-item"><a href="manage_users.php">Users</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 mx-auto">

                    <?php if ($user_created_successfully): ?>
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">User Created Successfully!</h3>
                        </div>
                        <div class="card-body text-center">
                            <p class="lead">The user account for <strong><?php echo htmlspecialchars($new_user_details['username']); ?></strong> has been created.</p>
                            <p>Please copy and save the credentials below. The password will not be shown again.</p>
                            
                            <div class="credentials-box bg-light p-3 rounded border mb-3" id="credentials-box">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Username:</strong> <?php echo htmlspecialchars($new_user_details['username']); ?></span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><strong>Password:</strong> <?php echo htmlspecialchars($new_user_details['password']); ?></span>
                                </div>
                            </div>
                            
                            <button id="copy-btn" class="btn btn-primary mb-3"><i class="fas fa-copy mr-2"></i>Copy Credentials</button>
                            
                            <div class="mt-4">
                                <a href="create_user.php" class="btn btn-info"><i class="fas fa-plus mr-2"></i>Create Another User</a>
                                <a href="manage_users.php" class="btn btn-secondary"><i class="fas fa-users mr-2"></i>View All Users</a>
                            </div>
                        </div>
                    </div>

                    <?php else: ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New User Details</h3>
                        </div>
                        
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger m-3">
                            <?php foreach ($errors as $error): ?>
                                <p class="mb-0"><?php echo $error; ?></p>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <form action="create_user.php" method="post" id="create-user-form">
                            <div class="card-body">
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label">Creation Mode</label>
                                    <div class="col-sm-9">
                                        <div class="custom-control custom-switch custom-switch-lg">
                                          <input type="checkbox" class="custom-control-input" id="creation-mode-switch">
                                          <label class="custom-control-label" for="creation-mode-switch" id="creation-mode-label">Manual</label>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="form-group">
                                    <label for="username">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name">
                                </div>
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="password" name="password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="generate-pass-btn" title="Generate Strong Password"><i class="fas fa-cogs"></i></button>
                                            <button class="btn btn-outline-secondary" type="button" id="toggle-pass-btn" title="Show/Hide Password"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="role">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="user">User</option>
                                        <option value="seller">Seller</option>
                                        <option value="super_seller">Super Seller</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create User</button>
                                <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- FORM GENERATION LOGIC ---
    const modeSwitch = document.getElementById('creation-mode-switch');
    const modeLabel = document.getElementById('creation-mode-label');
    const form = document.getElementById('create-user-form');
    if (!form) return; // Guard clause for success page

    const usernameEl = document.getElementById('username');
    const emailEl = document.getElementById('email');
    const fullNameEl = document.getElementById('full_name');
    const passwordEl = document.getElementById('password');
    const generateBtn = document.getElementById('generate-pass-btn');
    const togglePassBtn = document.getElementById('toggle-pass-btn');

    const generateRandomString = (length) => {
        const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    };

    const generateStrongPassword = () => {
        const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lower = 'abcdefghijklmnopqrstuvwxyz';
        const nums = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        const all = upper + lower + nums + symbols;
        let password = '';
        password += upper[Math.floor(Math.random() * upper.length)];
        password += lower[Math.floor(Math.random() * lower.length)];
        password += nums[Math.floor(Math.random() * nums.length)];
        password += symbols[Math.floor(Math.random() * symbols.length)];
        for (let i = 0; i < 8; i++) { // Total length of 12
            password += all[Math.floor(Math.random() * all.length)];
        }
        return password.split('').sort(() => 0.5 - Math.random()).join('');
    };

    const populateRandomData = () => {
        const randomUser = 'hdc_' + generateRandomString(8);
        const randomPassword = generateStrongPassword();
        usernameEl.value = randomUser;
        emailEl.value = randomUser + '@highdatacenter.com';
        fullNameEl.value = 'Random User ' + generateRandomString(4);
        passwordEl.value = randomPassword;
        setFieldsReadOnly(true);
    };
    
    const clearFields = () => {
        form.reset();
        setFieldsReadOnly(false);
    };

    const setFieldsReadOnly = (isReadOnly) => {
        usernameEl.readOnly = isReadOnly;
        emailEl.readOnly = isReadOnly;
        fullNameEl.readOnly = isReadOnly;
    };

    modeSwitch.addEventListener('change', function () {
        if (this.checked) {
            modeLabel.textContent = 'Random';
            populateRandomData();
        } else {
            modeLabel.textContent = 'Manual';
            clearFields();
        }
    });

    generateBtn.addEventListener('click', () => {
        passwordEl.value = generateStrongPassword();
        passwordEl.type = 'text';
        togglePassBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    });
    
    togglePassBtn.addEventListener('click', () => {
        const isText = passwordEl.type === 'text';
        passwordEl.type = isText ? 'password' : 'text';
        togglePassBtn.innerHTML = isText ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    // --- SUCCESS PAGE LOGIC ---
    const copyBtn = document.getElementById('copy-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            const credsBox = document.getElementById('credentials-box');
            const textToCopy = credsBox.innerText.replace(/\n\s*\n/g, '\n'); // Clean up extra newlines
            navigator.clipboard.writeText(textToCopy).then(() => {
                copyBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                setTimeout(() => {
                    copyBtn.innerHTML = '<i class="fas fa-copy mr-2"></i>Copy Credentials';
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>