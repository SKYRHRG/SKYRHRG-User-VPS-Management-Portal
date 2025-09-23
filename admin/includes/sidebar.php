<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0 (Modernized & Feature-Rich)
 * Author: SKYRHRG Technologies Systems
 *
 * Admin Panel Sidebar - Clean, non-collapsing layout with all management links.
 */

// Get the current page's filename to set the 'active' class on the correct link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="main-sidebar sidebar-light-primary elevation-2">
    <a href="index.php" class="brand-link">
        <img src="https://placehold.co/40x40/007bff/ffffff?text=S" alt="SKYRHRG Logo" class="brand-image img-circle elevation-1" style="opacity: .8">
        <span class="brand-text font-weight-light">HighDataCenter.Com</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image">
                <img src="https://placehold.co/160x160/343a40/ffffff?text=A" class="img-circle elevation-2" alt="Admin Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                <small class="text-muted">Administrator</small>
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
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" role="menu">

                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">USER TIERS</li>
                <li class="nav-item">
                    <a href="manage_super_sellers.php" class="nav-link <?php echo ($current_page == 'manage_super_sellers.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-shield"></i><p>Super Sellers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_sellers.php" class="nav-link <?php echo ($current_page == 'manage_sellers.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-tag"></i><p>Sellers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_customers.php" class="nav-link <?php echo ($current_page == 'manage_customers.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i><p>Customers</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="create_user.php" class="nav-link <?php echo ($current_page == 'create_user.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-plus"></i><p>Create New User</p>
                    </a>
                </li>

                <li class="nav-header">VPS MANAGEMENT</li>
                <li class="nav-item">
                    <a href="manage_vps_packages.php" class="nav-link <?php echo ($current_page == 'manage_vps_packages.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-box-open"></i><p>Manage Packages</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_vps_orders.php" class="nav-link <?php echo ($current_page == 'manage_vps_orders.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tasks"></i><p>Manage Orders</p>
                    </a>
                </li>

                <li class="nav-header">FINANCIALS</li>
                <li class="nav-item">
                    <a href="manage_wallets.php" class="nav-link <?php echo ($current_page == 'manage_wallets.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-wallet"></i><p>Manage Wallets</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_deposits.php" class="nav-link <?php echo ($current_page == 'manage_deposits.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-money-check-alt"></i><p>Deposit Requests</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="commission_reports.php" class="nav-link <?php echo ($current_page == 'commission_reports.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-percentage"></i><p>Commission Reports</p>
                    </a>
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