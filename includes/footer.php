<?php
// includes/footer.php
// Determine base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base_url  = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/SCENTLEEN/';
?>
    <footer style="background-color: var(--text-color); color: var(--secondary-color); padding: 60px 5% 20px;">
        <div class="container" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px; margin-bottom: 40px;">
            <div style="flex: 1; min-width: 250px;">
                <h3 style="color: var(--gold-color); margin-bottom: 20px; font-size: 1.5rem;">SCENTLEEN</h3>
                <p style="font-size: 0.9rem; margin-bottom: 20px;">Since 1980 – The Art of Luxury Fragrance.</p>
                <div style="display: flex; gap: 15px;">
                    <a href="#" style="color: var(--secondary-color); font-size: 1.2rem;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: var(--secondary-color); font-size: 1.2rem;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="color: var(--secondary-color); font-size: 1.2rem;"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <div style="flex: 1; min-width: 150px;">
                <h4 style="color: var(--gold-color); margin-bottom: 20px;">Shop</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="shop.php?category=men">Men's Collection</a></li>
                    <li><a href="shop.php?category=women">Women's Collection</a></li>
                    <li><a href="shop.php?category=oud">Arabian Oud</a></li>
                    <li><a href="shop.php?category=luxury">Luxury Collection</a></li>
                </ul>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <h4 style="color: var(--gold-color); margin-bottom: 20px;">Account</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="account.php">My Account</a></li>
                    <li><a href="wishlist.php">My Wishlist</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                    <li><a href="index.php">Login / Register</a></li>
                </ul>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <h4 style="color: var(--gold-color); margin-bottom: 20px;">Support</h4>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Shipping Policy</a></li>
                    <li><a href="#">Returns &amp; Refunds</a></li>
                </ul>
            </div>
        </div>
        <div style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem;">
            &copy; <?php echo date('Y'); ?> Scentleen Fragrance Store. All Rights Reserved.
        </div>
    </footer>

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Lenis for Smooth Scroll -->
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Set base URL for AJAX calls (works regardless of subdirectory) -->
    <script>
        window.SCENTLEEN_BASE = '<?php echo $base_url; ?>';
    </script>

    <!-- Custom Main JS -->
    <script src="<?php echo $base_url; ?>assets/js/main.js"></script>
</body>
</html>
