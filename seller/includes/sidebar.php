<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.2
 * Author: SKYRHRG Technologies Systems
 *
 * Seller Panel Sidebar (Updated with Reseller Features)
 */

$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link text-center">
        <span class="brand-text-strong font-weight-light">HIGH DATA CENTER</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://placehold.co/160x160/28a745/ffffff?text=<?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="profile.php" class="d-block"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                 <small class="text-muted">Seller</small>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" role="menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MANAGEMENT</li>
                <li class="nav-item">
                    <a href="create_user.php" class="nav-link <?php echo ($current_page == 'create_user.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-plus"></i><p>Create New User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_users.php" class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users-cog"></i><p>View All Users</p>
                    </a>
                </li>

                 <li class="nav-header"></li>
                 <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text">Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        </div>
    </aside>