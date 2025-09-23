<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.2 (Refined Modern UI)
 * Author: SKYRHRG Technologies Systems
 *
 * Login Page
 */

// Include core files (Unchanged)
require_once 'includes/session.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// If user is already logged in, redirect to their dashboard (Unchanged)
if (is_logged_in()) {
    $role = $_SESSION['role'];
    redirect($role . '/index.php');
}

$error_message = ''; // This will be set by PHP logic if login fails

// Handle form submission (Unchanged PHP logic)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $username = sanitize_input($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error_message = 'Username and password are required.';
        } else {
            $sql = "SELECT id, username, password, role, status FROM users WHERE username = ? OR email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    if ($user['status'] === 'active') {
                        regenerate_session();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        // For successful login, redirect. SweetAlert will not fire here directly,
                        // but can be triggered on the dashboard page if needed.
                        redirect($user['role'] . '/index.php');
                    } else {
                        $error_message = 'Your account is currently ' . $user['status'] . '. Please contact an administrator.';
                    }
                } else {
                    $error_message = 'Invalid username or password.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HighDataCenter.Com  | Log in</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
  /*
 * ===================================================================
 * Modern UI Styles for HighDataCenter.Com  Login Page (Refined)
 * Features: Dark Theme, Gradient Background, Glassmorphism, Animations
 * ===================================================================
 */

:root {
    --primary-color: #00A9FF;
    --primary-glow: rgba(0, 169, 255, 0.4);
    --bg-gradient-start: #111827; /* Dark Gray Blue */
    --bg-gradient-end: #0c121e;   /* Deeper Navy */
    --glass-bg: rgba(31, 41, 55, 0.6); /* Slightly less transparent */
    --glass-border: rgba(255, 255, 255, 0.15); /* Slightly more visible border */
    --text-primary: #F9FAFB;
    --text-secondary: #CBD5E1; /* Lighter secondary text */
    --input-bg: rgba(17, 24, 39, 0.9); /* Slightly darker input background */
    --input-border-focus: var(--primary-color);
    --button-hover-bg: #0095e0;
}

/* Base and Body Styling */
body.login-page-modern {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
    background-attachment: fixed;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
    margin: 0; /* Ensure no default body margin */
}

/* Animation */
@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Login Box with Glass Effect */
.login-container {
    max-width: 400px; /* Slightly narrower */
    width: 100%;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.35); /* More pronounced shadow */
    backdrop-filter: blur(15px); /* More blur */
    -webkit-backdrop-filter: blur(15px);
    padding: 3rem 2.5rem; /* More padding */
    animation: fadeInScale 0.7s ease-out forwards;
    display: flex; /* Use flex for internal layout */
    flex-direction: column;
    align-items: center;
}

.login-logo {
    text-align: center;
    margin-bottom: 2rem; /* Increased margin */
}

.login-logo a {
    text-decoration: none;
    font-size: 2.2rem; /* Slightly larger logo */
    font-weight: 300;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px; /* Space between text parts */
}

.login-logo .brand-text-strong {
    font-weight: 800; /* Bolder primary text */
    color: var(--primary-color);
    letter-spacing: 0.5px;
}

.login-message {
    text-align: center;
    color: var(--text-secondary);
    margin-bottom: 2.5rem; /* Increased margin */
    font-size: 1.05rem;
    line-height: 1.5;
}

/* Form Styling */
.form-group-modern { /* New wrapper for input + icon */
    position: relative;
    margin-bottom: 1.5rem; /* Consistent spacing */
    width: 100%; /* Ensure it takes full width of container */
}

.form-control-modern {
    background-color: var(--input-bg);
    border: 1px solid var(--glass-border);
    border-radius: 10px; /* Slightly more rounded */
    padding: 14px 14px 14px 50px; /* More padding and space for icon */
    color: var(--text-primary);
    width: 100%;
    transition: border-color 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    font-size: 1rem;
}

.form-control-modern::placeholder {
    color: var(--text-secondary);
    opacity: 0.7; /* Make placeholder a bit lighter */
}

.form-control-modern:focus {
    outline: none;
    border-color: var(--input-border-focus);
    background-color: rgba(17, 24, 39, 0.95); /* Slightly lighten on focus */
    box-shadow: 0 0 0 3px var(--primary-glow); /* Subtle glow */
}

.input-icon {
    position: absolute;
    left: 18px; /* Adjusted icon position */
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    font-size: 1.1rem; /* Slightly larger icon */
}

/* Button */
.btn-primary-modern {
    width: 100%;
    background-color: var(--primary-color);
    border: none;
    font-weight: 700; /* Bolder button text */
    padding: 14px 20px; /* More padding */
    border-radius: 10px; /* Matching input border-radius */
    transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 1px; /* More prominent text */
    font-size: 1.1rem; /* Slightly larger font */
    cursor: pointer;
}

.btn-primary-modern:hover {
    background-color: var(--button-hover-bg);
    box-shadow: 0 0 25px var(--primary-glow);
    transform: translateY(-2px); /* Subtle lift effect */
}

.btn-primary-modern:active {
    transform: translateY(0);
    box-shadow: 0 0 10px var(--primary-glow);
}

/* SweetAlert Dark Theme */
.swal2-popup {
    background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end)) !important;
    border: 1px solid var(--glass-border) !important;
    color: var(--text-primary) !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4) !important;
}
.swal2-title {
    color: var(--text-primary) !important;
}
.swal2-html-container {
    color: var(--text-secondary) !important;
}
.swal2-icon.swal2-error [class^="swal2-x-mark-line"] {
    background-color: var(--primary-color) !important; /* Adjust error mark color */
}
.swal2-icon.swal2-success [class^="swal2-success-line"] {
    background-color: var(--primary-color) !important; /* Adjust success mark color */
}
.swal2-confirm {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    font-weight: 600 !important;
}
.swal2-cancel {
    background-color: #6B7280 !important;
    border-color: #6B7280 !important;
    color: var(--text-primary) !important;
}
    </style>
</head>
<body class="login-page-modern">

<div class="login-container">
    <div class="login-logo">
        <a href="index.php">
            <span class="brand-text-strong">HighDataCenter</span>
            <span class="brand-text">Panel</span>
        </a>
    </div>

    <p class="login-message">Sign in to HighDataCenter Beta V1.0</p>

    <form action="login.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

        <div class="form-group-modern">
            <span class="input-icon"><i class="fas fa-user"></i></span>
            <input type="text" class="form-control-modern" name="username" placeholder="Username or Email" required autocomplete="username">
        </div>

        <div class="form-group-modern">
            <span class="input-icon"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control-modern" name="password" placeholder="Password" required autocomplete="current-password">
        </div>

        <div class="mt-4" style="width: 100%;"> <button type="submit" class="btn btn-primary-modern">Sign In</button>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {
        // Display login error message using SweetAlert2
        <?php if (!empty($error_message)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            html: '<?php echo $error_message; ?>', // Use html for potential bold/styling if needed
            customClass: {
                popup: 'swal2-popup', // Use swal2-popup for base styling
                title: 'swal2-title',
                htmlContainer: 'swal2-html-container'
            }
        });
        <?php endif; ?>

        // Display message from URL query (e.g., after logout) using SweetAlert2
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('message')) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                html: urlParams.get('message'), // Use html for potential bold/styling if needed
                timer: 3000, // Shortened timer for success messages
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-popup',
                    title: 'swal2-title',
                    htmlContainer: 'swal2-html-container'
                }
            });
        }
    });
</script>
</body>
</html>