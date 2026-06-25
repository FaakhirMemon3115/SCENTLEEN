<?php
// returns.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px; max-width: 800px; margin: 0 auto;">
        
        <h1 style="font-size: 2.5rem; margin-bottom: 20px; text-align: center; color: var(--gold-color);">Returns &amp; Refunds</h1>
        <p style="text-align: center; color: #666; margin-bottom: 50px;">Our commitment to your satisfaction.</p>

        <div style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
            
            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Return Eligibility</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">Because fragrances are personal care items, we can only accept returns on unopened, sealed products in their original packaging within 7 days of delivery.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">How to Initiate a Return</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">Please email our support team with your Order ID. We will provide you with a return authorization and shipping instructions. Return shipping costs are the responsibility of the customer.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Refund Process</h3>
            <p style="color: #666; line-height: 1.6; margin-bottom: 25px;">Once we receive and inspect the returned item, we will process your refund to the original payment method within 3-5 business days. Please note that original shipping charges are non-refundable.</p>

            <h3 style="font-size: 1.2rem; margin-bottom: 10px; color: var(--text-color);">Exchanges</h3>
            <p style="color: #666; line-height: 1.6;">We currently do not offer direct exchanges. You will need to return the unwanted item for a refund and place a new order.</p>

        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
