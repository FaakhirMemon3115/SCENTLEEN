<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCENTLEEN | The Art of Luxury Fragrance</title>
    <meta name="description" content="Since 1980 – The Art of Luxury Fragrance. Discover premium perfumes, oud, and imported collections.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header id="main-header">
    <div class="logo">
        <a href="index.php">
            <span style="font-size: 2.5rem; line-height: 0.8; display: block; margin-bottom: -5px;">$</span>
            <span style="font-size: 1.2rem; letter-spacing: 5px;">SCENTLEEN</span>
        </a>
    </div>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="shop.php?category=men">Men</a></li>
            <li><a href="shop.php?category=women">Women</a></li>
            <li><a href="shop.php?category=oud">Oud</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <div class="nav-icons">
        <a href="#"><i class="fas fa-search"></i></a>
        <a href="wishlist.php"><i class="far fa-heart"></i></a>
        <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
        <a href="login.php"><i class="far fa-user"></i></a>
    </div>
</header>
