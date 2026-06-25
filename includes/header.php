<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $is_logged_in ? $_SESSION['user_name'] : '';
$user_role    = $is_logged_in ? $_SESSION['user_role'] : '';
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
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">

    <style>
    /* ── User Dropdown ── */
    .user-menu-wrapper {
        position: relative;
    }

    .user-menu-trigger {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 1.2rem;
        color: inherit;
        background: none;
        border: none;
        padding: 0;
        font-family: inherit;
    }

    .user-menu-trigger .user-initial {
        width: 30px;
        height: 30px;
        background: var(--gold-color, #C9A96E);
        color: #fff;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
        letter-spacing: 0;
        text-transform: uppercase;
    }

    .user-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 12px);
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        min-width: 220px;
        z-index: 999;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.06);
    }

    .user-dropdown.open { display: block; }

    .user-dropdown-header {
        background: #2D2D2D;
        padding: 16px 18px;
        color: #fff;
    }

    .user-dropdown-header .user-name {
        font-weight: 600;
        font-size: 0.95rem;
        font-family: 'Playfair Display', serif;
    }

    .user-dropdown-header .user-role-badge {
        font-size: 0.7rem;
        color: #C9A96E;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 2px;
    }

    .user-dropdown a, .user-dropdown form button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        font-size: 0.88rem;
        color: #444;
        text-decoration: none;
        transition: background 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        font-family: 'Inter', sans-serif;
    }

    .user-dropdown a:hover, .user-dropdown form button:hover {
        background: #f9f6f1;
        color: #C9A96E;
    }

    .user-dropdown a i, .user-dropdown form button i {
        width: 16px;
        color: #C9A96E;
        font-size: 0.85rem;
    }

    .user-dropdown .divider-line {
        height: 1px;
        background: #eee;
        margin: 4px 0;
    }

    .user-dropdown .logout-btn { color: #e74c3c; }
    .user-dropdown .logout-btn i { color: #e74c3c; }
    .user-dropdown .logout-btn:hover { background: #fdeaea; color: #c0392b; }

    /* Cart count badge */
    .cart-icon-wrapper {
        position: relative;
        display: inline-block;
    }

    .cart-count-badge {
        position: absolute;
        top: -7px;
        right: -7px;
        background: #C9A96E;
        color: #fff;
        font-size: 0.6rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    </style>
</head>
<body>

<header id="main-header">
    <div class="logo">
        <a href="home.php">
            <span style="font-size: 2.5rem; line-height: 0.8; display: block; margin-bottom: -5px;">$</span>
            <span style="font-size: 1.2rem; letter-spacing: 5px;">SCENTLEEN</span>
        </a>
    </div>

    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="shop.php?category=men">Men</a></li>
            <li><a href="shop.php?category=women">Women</a></li>
            <li><a href="shop.php?category=oud">Oud</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>

    <div class="nav-icons">
        <a href="wishlist.php"><i class="far fa-heart"></i></a>

        <!-- Cart Icon with count -->
        <a href="cart.php" class="cart-icon-wrapper">
            <i class="fas fa-shopping-bag"></i>
            <?php
            $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
            if ($cart_count > 0): ?>
            <span class="cart-count-badge"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>

        <!-- User Icon: Dropdown if logged in, login link if not -->
        <?php if ($is_logged_in): ?>
        <div class="user-menu-wrapper">
            <button class="user-menu-trigger" id="userMenuBtn" onclick="toggleUserMenu(event)" title="My Account">
                <span class="user-initial"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
            </button>
            <div class="user-dropdown" id="userDropdown">
                <div class="user-dropdown-header">
                    <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-role-badge">
                        <i class="fas fa-circle" style="font-size:6px; vertical-align:middle; margin-right:4px;"></i>
                        <?php echo $user_role === 'admin' ? 'Administrator' : 'Member'; ?>
                    </div>
                </div>

                <?php if ($user_role === 'admin'): ?>
                <a href="admin/index.php"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</a>
                <div class="divider-line"></div>
                <?php endif; ?>

                <a href="account.php"><i class="fas fa-user-circle"></i> My Account</a>
                <a href="wishlist.php"><i class="fas fa-heart"></i> My Wishlist</a>
                <a href="cart.php"><i class="fas fa-shopping-bag"></i> My Cart
                    <?php if ($cart_count > 0): ?>
                    <span style="background:#C9A96E;color:#fff;border-radius:10px;padding:1px 7px;font-size:0.75rem;margin-left:auto;"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="divider-line"></div>
                <form method="POST" action="logout.php">
                    <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <a href="index.php" title="Login / Register">
            <i class="far fa-user"></i>
        </a>
        <?php endif; ?>

        <!-- Hamburger Button (mobile only) -->
        <button class="hamburger" id="hamburgerBtn" onclick="toggleMobileNav()" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav" id="mobileNav">
    <ul>
        <li><a href="home.php" onclick="closeMobileNav()">Home</a></li>
        <li><a href="shop.php" onclick="closeMobileNav()">Shop</a></li>
        <li><a href="shop.php?category=men" onclick="closeMobileNav()">Men</a></li>
        <li><a href="shop.php?category=women" onclick="closeMobileNav()">Women</a></li>
        <li><a href="shop.php?category=oud" onclick="closeMobileNav()">Oud</a></li>
        <li><a href="contact.php" onclick="closeMobileNav()">Contact</a></li>
        <?php if($is_logged_in): ?>
        <li><a href="account.php" onclick="closeMobileNav()">My Account</a></li>
        <?php if($user_role === 'admin'): ?>
        <li><a href="admin/index.php" onclick="closeMobileNav()" style="color:var(--gold-color);">Admin Panel</a></li>
        <?php endif; ?>
        <li><a href="logout.php" style="color:#e74c3c;">Logout</a></li>
        <?php else: ?>
        <li><a href="index.php" onclick="closeMobileNav()">Login / Register</a></li>
        <?php endif; ?>
    </ul>
    <div class="mobile-nav-icons">
        <a href="wishlist.php"><i class="far fa-heart"></i></a>
        <a href="cart.php" style="position:relative;">
            <i class="fas fa-shopping-bag"></i>
            <?php if($cart_count > 0): ?>
            <span style="position:absolute;top:-8px;right:-8px;background:var(--gold-color);color:#fff;font-size:0.6rem;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>

<script>
function toggleUserMenu(e) {
    e.stopPropagation();
    document.getElementById('userDropdown').classList.toggle('open');
}

document.addEventListener('click', function() {
    const dd = document.getElementById('userDropdown');
    if (dd) dd.classList.remove('open');
});

function toggleMobileNav() {
    const nav = document.getElementById('mobileNav');
    const btn = document.getElementById('hamburgerBtn');
    nav.classList.toggle('open');
    btn.classList.toggle('open');
    document.body.style.overflow = nav.classList.contains('open') ? 'hidden' : '';
}

function closeMobileNav() {
    const nav = document.getElementById('mobileNav');
    const btn = document.getElementById('hamburgerBtn');
    nav.classList.remove('open');
    btn.classList.remove('open');
    document.body.style.overflow = '';
}

// Close mobile nav on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMobileNav();
});
</script>
