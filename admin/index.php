<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.0 (Modernized & Optimized)
 * Author: SKYRHRG Technologies Systems
 *
 * Admin Dashboard - Clean, optimized, and accurate.
 */

$page_title = 'Admin Dashboard';
require_once 'includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// --- [OPTIMIZED] DATA FETCHING ---

// Fetch all user counts in a single query for efficiency
$user_counts_result = $conn->query("SELECT role, COUNT(id) as count FROM users GROUP BY role");
$user_counts = ['admin' => 0, 'super_seller' => 0, 'seller' => 0, 'user' => 0];
$total_users = 0;
while ($row = $user_counts_result->fetch_assoc()) {
    if (array_key_exists($row['role'], $user_counts)) {
        $user_counts[$row['role']] = $row['count'];
    }
    $total_users += $row['count'];
}
$total_super_sellers = $user_counts['super_seller'];
$total_sellers = $user_counts['seller'];
$total_customers = $user_counts['user']; // Correctly counts users with role 'user'

// Fetch recent users
$recent_users_result = $conn->query("SELECT username, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

// --- [OPTIMIZED] CHART DATA: Monthly User Registrations ---
$chart_labels = [];
$chart_data = [];
$six_months_ago = date('Y-m-01', strtotime("-5 months")); // Start of the month, 6 months ago

// Initialize months array
for ($i = 5; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i month"));
    $month_name = date('M Y', strtotime("-$i month"));
    $chart_labels[] = $month_name;
    $monthly_counts[$month_key] = 0; // Initialize with 0
}

// Single query for all 6 months
$chart_query_result = $conn->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(id) as count 
    FROM users 
    WHERE created_at >= '$six_months_ago'
    GROUP BY month 
    ORDER BY month ASC
");
while($row = $chart_query_result->fetch_assoc()){
    $monthly_counts[$row['month']] = $row['count'];
}
$chart_data = array_values($monthly_counts);

?>

<?php require_once 'includes/navbar.php'; ?>
<?php require_once 'includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Admin Dashboard</h1></div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary"><i class="fas fa-file-download me-2"></i>Generate Report</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-icon" data-bs-toggle="dropdown"></button>
                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                <a class="dropdown-item" href="#">Download as PDF</a>
                                <a class="dropdown-item" href="#">Export to CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo $total_super_sellers; ?></h3><p>Super Sellers</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="manage_super_sellers.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-dark">
                        <div class="inner">
                            <h3><?php echo $total_sellers; ?></h3><p>Sellers</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-tag"></i></div>
                        <a href="manage_sellers.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-teal">
                        <div class="inner">
                            <h3><?php echo $total_customers; ?></h3><p>Customers</p>
                        </div>
                        <div class="icon"><i class="fas fa-user"></i></div>
                        <a href="manage_customers.php" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                 <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $total_users; ?></h3><p>Total Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="manage_customers.php" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar me-1"></i>Monthly User Registrations</h3></div>
                        <div class="card-body"><canvas id="registrationsChart" style="min-height: 280px; height: 280px;"></canvas></div>
                    </div>
                </div>
                <div class="col-lg-5">
                     <div class="card shadow-sm">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-pie me-1"></i>User Role Distribution</h3></div>
                        <div class="card-body"><canvas id="rolesChart" style="min-height: 280px; height: 280px;"></canvas></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                     <div class="card card-primary card-outline shadow-sm">
                        <div class="card-header"><h3 class="card-title">Recently Registered Users</h3></div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card px-2">
                                <?php while ($user = $recent_users_result->fetch_assoc()) : ?>
                                    <li class="item">
                                        <div class="product-img"><i class="fa fa-user-circle fa-2x text-muted"></i></div>
                                        <div class="product-info">
                                            <span class="product-title"><?php echo htmlspecialchars($user['username']); ?>
                                                <span class="badge badge-info float-right"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))); ?></span>
                                            </span>
                                            <span class="product-description">Joined on: <?php echo date('d M Y, h:i A', strtotime($user['created_at'])); ?></span>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="manage_customers.php" class="uppercase">View All Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- REGISTRATIONS BAR CHART ---
    new Chart(document.getElementById('registrationsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{
                label: 'New Users',
                data: <?php echo json_encode($chart_data); ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.8)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    // --- ROLES DOUGHNUT CHART ---
    new Chart(document.getElementById('rolesChart'), {
        type: 'doughnut',
        data: {
            labels: ['Customers', 'Sellers', 'Super Sellers', 'Admins'],
            datasets: [{
                data: [<?php echo $user_counts['user']; ?>, <?php echo $user_counts['seller']; ?>, <?php echo $user_counts['super_seller']; ?>, <?php echo $user_counts['admin']; ?>],
                backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>