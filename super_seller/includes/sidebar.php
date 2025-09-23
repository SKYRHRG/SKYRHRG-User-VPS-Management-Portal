<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.2
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller Panel Sidebar (Updated with Reseller Features)
 */

$current_page = basename($_SERVER['PHP_SELF']);

// Define page groups for active menu state
$user_management_pages = ['manage_sellers.php', 'manage_users.php', 'create_user.php'];

?>
<aside class="main-sidebar sidebar-light-primary elevation-2">
    <a href="index.php" class="brand-link">
        <img src="https://placehold.co/40x40/007bff/ffffff?text=S" alt="SKYRHRG Logo" class="brand-image img-circle elevation-1" style="opacity: .8">
        <span class="brand-text font-weight-light">HIGH DATA CENTER</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="https://placehold.co/160x160/3c8dbc/ffffff?text=S" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <small class="text-muted">Super Seller</small>
            </div>
        </div>

        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search Menu" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar"><i class="fas fa-search fa-fw"></i></button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MANAGEMENT</li>
                <li class="nav-item <?php echo in_array($current_page, $user_management_pages) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo in_array($current_page, $user_management_pages) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users-cog"></i><p>User Management<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="manage_sellers.php" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Manage Sellers</p></a></li>
                        <li class="nav-item"><a href="manage_users.php" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Manage Users</p></a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        </div>
    <div class="sidebar-footer p-3">
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="theme-switch-checkbox">
            <label class="custom-control-label" for="theme-switch-checkbox">
                <i class="fas fa-moon" id="theme-icon"></i>
                <span id="theme-text">Dark Mode</span>
            </label>
        </div>
    </div>
</aside>