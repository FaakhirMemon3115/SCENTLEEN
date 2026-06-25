<?php
// faq.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px; max-width: 800px; margin: 0 auto;">
        
        <h1 style="font-size: 2.5rem; margin-bottom: 20px; text-align: center; color: var(--gold-color);">Frequently Asked Questions</h1>
        <p style="text-align: center; color: #666; margin-bottom: 50px;">Find answers to our most common questions below.</p>

        <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
            
            <div style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Are your perfumes authentic?</h3>
                <p style="color: #666; line-height: 1.6;">Yes, all our fragrances are 100% authentic and sourced directly from authorized distributors or imported from our Paris partners. We guarantee the authenticity of every bottle.</p>
            </div>

            <div style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Do you ship internationally?</h3>
                <p style="color: #666; line-height: 1.6;">Currently, we only ship nationwide. We are working on expanding our delivery network to international locations soon.</p>
            </div>

            <div style="margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">How long does the scent last?</h3>
                <p style="color: #666; line-height: 1.6;">Longevity depends on the type of fragrance (Eau de Toilette vs Eau de Parfum) and individual skin chemistry. Generally, our Eau de Parfums last between 8 to 12 hours.</p>
            </div>

            <div style="margin-bottom: 10px;">
                <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Can I cancel my order?</h3>
                <p style="color: #666; line-height: 1.6;">You can cancel your order within 24 hours of placing it, provided it has not already been dispatched. Please contact support immediately if you wish to cancel.</p>
            </div>

        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
