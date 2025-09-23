 
<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Seller Panel Navbar
 */
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- User Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-user-circle"></i>
                <?php echo htmlspecialchars($_SESSION['username']); ?>
                <i class="fas fa-caret-down ml-1"></i>
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
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->