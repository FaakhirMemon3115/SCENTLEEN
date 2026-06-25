<?php
// shipping.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px; max-width: 800px; margin: 0 auto;">
        
        <h1 style="font-size: 2.5rem; margin-bottom: 20px; text-align: center; color: var(--gold-color);">Shipping Policy</h1>
        <p style="text-align: center; color: #666; margin-bottom: 50px;">Everything you need to know about our delivery process.</p>

        <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
            
            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Delivery Times</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">Standard delivery takes 3-5 business days depending on your location. Orders placed before 2 PM are typically processed the same day.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Shipping Costs</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">We offer a flat shipping rate of Rs. 150 nationwide. Free shipping is automatically applied to orders over Rs. 10,000.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Order Tracking</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">Once your order is dispatched, you will receive a tracking number via email so you can monitor your package's journey.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Damaged Packages</h3>
            <p style="color: #666; line-height: 1.6;">If your fragrance arrives damaged, please do not open the sealed box. Take a photo and contact our support team within 24 hours of delivery for a replacement.</p>

        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
