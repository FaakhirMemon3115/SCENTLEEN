<?php
// checkout.php
require_once 'includes/header.php';

// MVP Placeholder Data
$subtotal = 550.00;
$shipping = 15.00;
$total = $subtotal + $shipping;
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center;">Checkout</h1>

        <form action="order_confirmation.php" method="POST" style="display: flex; flex-wrap: wrap; gap: 40px;">
            
            <!-- Billing & Shipping Info -->
            <div style="flex: 2; min-width: 300px;">
                <div style="background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); margin-bottom: 30px;">
                    <h3 style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">Billing Details</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">First Name *</label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Last Name *</label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Email Address *</label>
                        <input type="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Phone *</label>
                        <input type="tel" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Address *</label>
                        <input type="text" placeholder="Street address" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); margin-bottom: 10px;">
                        <input type="text" placeholder="Apartment, suite, unit etc. (optional)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">City *</label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Postcode / ZIP *</label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Your Order -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">Your Order</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: 600;">
                        <span>Product</span>
                        <span>Subtotal</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #666;">
                        <span>Royal Majesty × 1</span>
                        <span>$240.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.9rem; color: #666; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <span>Midnight Oud × 1</span>
                        <span>$310.00</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <span>Shipping</span>
                        <span>$<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 1.2rem; font-weight: 600;">
                        <span>Total</span>
                        <span style="color: var(--gold-color);">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <h4 style="margin-bottom: 15px; font-size: 1rem;">Payment Method</h4>
                        
                        <label style="display: block; margin-bottom: 10px; cursor: pointer;">
                            <input type="radio" name="payment" value="cod" checked style="margin-right: 10px; accent-color: var(--text-color);">
                            Cash on Delivery
                        </label>
                        <div style="background: var(--bg-color); padding: 15px; border-radius: 5px; font-size: 0.8rem; color: #666; margin-bottom: 15px;">
                            Pay with cash upon delivery.
                        </div>

                        <label style="display: block; margin-bottom: 10px; cursor: pointer;">
                            <input type="radio" name="payment" value="card" style="margin-right: 10px; accent-color: var(--text-color);">
                            Credit Card / Debit Card
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; text-align: center; font-size: 1.1rem; padding: 15px;">Place Order</button>
                    
                </div>
            </div>

        </form>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
