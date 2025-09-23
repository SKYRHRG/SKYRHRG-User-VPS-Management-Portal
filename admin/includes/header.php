<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Admin Panel Header
 */

// Go up two directories to access the main includes folder
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// Authorize access: only 'admin' role is allowed
authorize_access(['admin']);

// A dynamic page title can be set before including this header
$page_title = isset($page_title) ? $page_title : 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HighDataCenter.Com | <?php echo htmlspecialchars($page_title); ?></title>

    <link rel="icon" href="https://placehold.co/32x32/007bff/ffffff?text=HDC" type="image/x-icon">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">