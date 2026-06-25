<?php
// admin/includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Basic Admin Auth Check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scentleen Admin Panel</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; display: flex; }
        .sidebar { width: 250px; background: #2D2D2D; color: #fff; min-height: 100vh; position: fixed; left: 0; top: 0; padding: 20px 0; }
        .sidebar a { display: block; color: #ccc; padding: 15px 25px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #1a1a1a; color: var(--gold-color); border-left: 4px solid var(--gold-color); }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 30px; }
        .admin-header { background: #fff; padding: 15px 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .card { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        table.admin-table { width: 100%; border-collapse: collapse; }
        table.admin-table th, table.admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        table.admin-table th { background: #f9f9f9; color: #666; font-size: 0.9rem; text-transform: uppercase; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
        .badge-success { background: #eafaf1; color: #27ae60; }
        .badge-warning { background: #fef5e7; color: #f39c12; }
        .badge-danger { background: #fdeaea; color: #e74c3c; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="font-size: 2rem; color: var(--gold-color); font-family: 'Playfair Display', serif;">$ SCENTLEEN</span>
        <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">Admin Panel</p>
    </div>

    <nav>
        <a href="index.php"><i class="fas fa-home" style="width: 25px;"></i> Dashboard</a>
        <a href="orders.php"><i class="fas fa-shopping-cart" style="width: 25px;"></i> Orders</a>
        <a href="products.php"><i class="fas fa-box" style="width: 25px;"></i> Products</a>
        <a href="#"><i class="fas fa-users" style="width: 25px;"></i> Customers</a>
        <a href="#"><i class="fas fa-tags" style="width: 25px;"></i> Categories</a>
        <a href="#"><i class="fas fa-ticket-alt" style="width: 25px;"></i> Coupons</a>
        <a href="settings.php"><i class="fas fa-cog" style="width: 25px;"></i> Settings</a>
    </nav>
</aside>

<div class="main-content">
    <header class="admin-header">
        <h2 style="font-size: 1.5rem; font-weight: 500;">Dashboard</h2>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="position: relative;">
                <i class="fas fa-bell" style="font-size: 1.2rem; color: #666;"></i>
                <span style="position: absolute; top: -5px; right: -5px; background: #e74c3c; color: #fff; border-radius: 50%; width: 15px; height: 15px; font-size: 0.6rem; display: flex; align-items: center; justify-content: center;">3</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="https://ui-avatars.com/api/?name=Admin&background=C9A96E&color=fff" style="width: 35px; height: 35px; border-radius: 50%;">
                <span style="font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
            </div>
            <a href="../index.php" style="color: #e74c3c;"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>
