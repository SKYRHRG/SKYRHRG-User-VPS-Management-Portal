<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.1 (Modern UI)
 * Author: SKYRHRG Technologies Systems
 *
 * User Panel Navbar (Updated Design)
 */
?>
<nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="wallet.php">
                <i class="fas fa-wallet"></i>
                </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
                <i class="far fa-user-circle"></i>
                <span class="d-none d-sm-inline-block"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user-cog mr-2"></i> My Profile
                </a>
                <div class="dropdown-divider"></div>
                <a href="../../logout.php" class="dropdown-item dropdown-footer bg-danger text-white">
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