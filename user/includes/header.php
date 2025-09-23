<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 2.1 (Modern UI)
 * Author: SKYRHRG Technologies Systems
 *
 * User Panel Header (Updated Design)
 */

// Go up two directories to access the main includes folder
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// Authorize access
authorize_access(['user', 'seller', 'super_seller', 'admin']);

$page_title = isset($page_title) ? $page_title : 'User Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HighDataCenter.Com | <?php echo htmlspecialchars($page_title); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #00A9FF;
            --bg-dark: #111827;      /* Main background */
            --bg-light: #1F2937;     /* Cards, Navbar, Sidebar */
            --border-color: #374151; /* Borders and dividers */
            --text-primary: #F9FAFB;   /* Primary text color */
            --text-secondary: #9CA3AF; /* Muted text color */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
        }

        .content-wrapper {
            background-color: var(--bg-dark);
        }
        
        .main-header.navbar {
            background-color: var(--bg-light);
            border-bottom: 1px solid var(--border-color);
        }
        .navbar-dark .navbar-nav .nav-link {
            color: var(--text-secondary);
        }
        .navbar-dark .navbar-nav .nav-link:hover {
            color: var(--text-primary);
        }

        .main-sidebar {
            background-color: var(--bg-light);
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active,
        .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .brand-link {
            border-bottom: 1px solid var(--border-color);
        }
        .brand-text-strong {
            color: var(--primary-color) !important;
            font-weight: 700 !important;
        }

        .card {
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-color);
        }

        .info-box {
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
        }
        .info-box .info-box-number, .info-box .info-box-text {
            color: var(--text-primary);
        }
        
        /* Custom styles from your previous code for consistency */
        .transaction-item {
            display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .transaction-item:last-child { border-bottom: none; }
        .transaction-amount-credit { font-weight: bold; color: #28a745; }
        .transaction-amount-debit { font-weight: bold; color: #dc3545; }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">