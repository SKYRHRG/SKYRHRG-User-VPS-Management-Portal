<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 1.0
 * Author: SKYRHRG Technologies Systems
 *
 * Super Seller Panel Header
 */

// Go up two directories to access the main includes folder
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/functions.php';

// Authorize access: 'super_seller' and 'admin' roles are allowed
authorize_access(['super_seller', 'admin']);

// A dynamic page title can be set before including this header
$page_title = isset($page_title) ? $page_title : 'Super Seller Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HighDataCenter.Com  | <?php echo htmlspecialchars($page_title); ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE Theme style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">