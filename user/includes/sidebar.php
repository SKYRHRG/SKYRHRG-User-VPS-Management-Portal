<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0
 * Author: SKYRHRG Technologies Systems
 *
 * User Panel Sidebar (Dynamically generates VPS control links)
 * This sidebar automatically lists a user's active VPS services.
 */

// This file is included by pages that have already established a database connection.

// Get the current page's filename to highlight the active menu item.
$current_page = basename($_SERVER['PHP_SELF']);
// Get the specific order ID if we are on the control page to highlight the correct server.
$current_order_id = (int)($_GET['order_id'] ?? 0);

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link text-center">
        <span class="brand-text-strong">HighDataCenter</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="https://placehold.co/160x160/00A9FF/ffffff?text=<?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="profile.php" class="d-block"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                <!-- Static Menu Items -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../vps_store.php" class="nav-link <?php echo ($current_page == 'vps_store.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>VPS Store</p>
                    </a>
                </li>

                <li class="nav-header">MY ACCOUNT</li>
                <li class="nav-item">
                    <a href="add_fund.php" class="nav-link <?php echo ($current_page == 'add_fund.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Add Funds / Wallet</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>My Profile</p>
                    </a>
                </li>

                <?php
                // --- DYNAMICALLY GENERATE VPS SERVICE LINKS ---
                
                global $conn; // Ensure the global DB connection is accessible
                $user_id_sidebar = $_SESSION['user_id'];
                
                // Prepare a secure SQL statement to fetch active services for this user
                $vps_query = "SELECT o.id, p.name 
                              FROM vps_orders o 
                              JOIN vps_packages p ON o.package_id = p.id 
                              WHERE o.user_id = ? AND o.status = 'active'
                              ORDER BY p.name ASC";

                $stmt = $conn->prepare($vps_query);

                if ($stmt) {
                    $stmt->bind_param("i", $user_id_sidebar);
                    $stmt->execute();
                    $vps_result = $stmt->get_result();

                    if ($vps_result->num_rows > 0) {
                        // Only show the header if the user has active services
                        echo '<li class="nav-header">MY SERVERS</li>';

                        while ($vps = $vps_result->fetch_assoc()) {
                            // Check if the current link is the one being viewed
                            $is_active_link = ($current_page == 'vps_control.php' && $current_order_id == $vps['id']);
                            $active_class = $is_active_link ? 'active' : '';

                            // Each server gets its own link pointing to the SAME control page
                            echo '<li class="nav-item">
                                    <a href="vps_control.php?order_id=' . $vps['id'] . '" class="nav-link ' . $active_class . '">
                                        <i class="nav-icon fas fa-server"></i>
                                        <p>' . htmlspecialchars($vps['name']) . '</p>
                                    </a>
                                  </li>';
                        }
                    }
                    $stmt->close();
                } else {
                    // If the database query fails, log the error for debugging.
                    log_api_error('Sidebar Generation', 'Failed to prepare SQL statement to fetch user VPS services.', ['error' => $conn->error]);
                }
                ?>
                
                <li class="nav-header"></li>
                 <li class="nav-item">
                    <a href="../logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text">Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>