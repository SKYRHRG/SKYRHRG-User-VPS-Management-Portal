<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.0
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller Panel Navbar (Modern Design)
 */

// We need a database connection to fetch the wallet balance.
// This path assumes the navbar is in an 'includes' folder within the 'super_seller' directory.
require_once __DIR__ . '/../../includes/db.php';

$wallet_balance = 0.00;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $wallet_balance = $row['balance'];
        }
    }
    $stmt->close();
}
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">

        <li class="nav-item d-none d-sm-inline-block">
            <a href="wallet.php" class="nav-link">
                <i class="fas fa-wallet text-success"></i>
                <strong>Balance: ₹<?php echo number_format($wallet_balance, 2); ?></strong>
            </a>
        </li>
        <li class="nav-item d-sm-none"> <a href="wallet.php" class="nav-link">
                <i class="fas fa-wallet text-success"></i> ₹<?php echo number_format($wallet_balance, 2); ?>
            </a>
        </li>


        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">3 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-money-bill-wave mr-2"></i> Funds received
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown" href="#">
                <img src="https://placehold.co/160x160/007bff/ffffff?text=<?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>" class="img-circle elevation-1" alt="User Image" style="width: 25px; height: 25px; object-fit: cover; margin-right: 8px;">
                <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">User Menu</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user-cog mr-2"></i> Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="../logout.php" class="dropdown-item dropdown-footer bg-danger text-white">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>