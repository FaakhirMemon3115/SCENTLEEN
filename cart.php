<?php
// cart.php
require_once 'includes/header.php';

// In a real application, cart items would be fetched from session or database.
// For the MVP UI, we'll use placeholder data.
$cart_items = [
    [
        'id' => 1,
        'name' => 'Royal Majesty',
        'price' => 240.00,
        'qty' => 1,
        'image' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?q=80&w=400&auto=format&fit=crop'
    ],
    [
        'id' => 2,
        'name' => 'Midnight Oud',
        'price' => 310.00,
        'qty' => 2,
        'image' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=400&auto=format&fit=crop'
    ]
];

$subtotal = 0;
foreach($cart_items as $item) {
    $subtotal += $item['price'] * $item['qty'];
}
$shipping = 15.00;
$total = $subtotal + $shipping;
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    
    <div class="container py-5" style="padding: 60px 20px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center;">Shopping Cart</h1>

        <?php if(empty($cart_items)): ?>
            <div text-align: center; padding: 50px 0;">
                <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3>Your cart is empty</h3>
                <a href="shop.php" class="btn-primary" style="margin-top: 20px;">Return to Shop</a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-wrap: wrap; gap: 40px;">
                
                <!-- Cart Items List -->
                <div style="flex: 2; min-width: 300px;">
                    <div style="background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9f9f9; text-transform: uppercase; font-size: 0.8rem; color: #666;">
                                    <th style="padding: 20px; text-align: left;">Product</th>
                                    <th style="padding: 20px; text-align: center;">Price</th>
                                    <th style="padding: 20px; text-align: center;">Quantity</th>
                                    <th style="padding: 20px; text-align: right;">Total</th>
                                    <th style="padding: 20px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cart_items as $item): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 20px;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                                            <div>
                                                <h4 style="font-size: 1rem; margin-bottom: 5px;"><a href="#"><?php echo htmlspecialchars($item['name']); ?></a></h4>
                                                <p style="font-size: 0.8rem; color: #999;">SKU: SCT-<?php echo str_pad($item['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 20px; text-align: center; color: #666;">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="display: inline-flex; border: 1px solid #ccc; border-radius: 5px; overflow: hidden; width: 100px; height: 35px;">
                                            <button style="flex: 1; border: none; background: #f9f9f9; cursor: pointer;">-</button>
                                            <input type="number" value="<?php echo $item['qty']; ?>" style="flex: 1; border: none; text-align: center; outline: none; width: 30px;">
                                            <button style="flex: 1; border: none; background: #f9f9f9; cursor: pointer;">+</button>
                                        </div>
                                    </td>
                                    <td style="padding: 20px; text-align: right; font-weight: 600; color: var(--gold-color);">$<?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                                    <td style="padding: 20px; text-align: center;">
                                        <button style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 30px; display: flex; justify-content: space-between;">
                        <div style="display: flex; gap: 10px;">
                            <input type="text" placeholder="Coupon Code" style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
                            <button class="btn-outline" style="padding: 10px 20px;">Apply</button>
                        </div>
                        <button class="btn-outline" style="padding: 10px 20px;">Update Cart</button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div style="flex: 1; min-width: 300px;">
                    <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                        <h3 style="margin-bottom: 25px; font-size: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 15px;">Order Summary</h3>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                            <span>Shipping</span>
                            <span>$<?php echo number_format($shipping, 2); ?></span>
                        </div>
                        
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 1.2rem; font-weight: 600;">
                            <span>Total</span>
                            <span style="color: var(--gold-color);">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn-primary" style="display: block; text-align: center; width: 100%;">Proceed to Checkout</a>
                        
                        <div style="text-align: center; margin-top: 20px; font-size: 0.8rem; color: #999;">
                            <i class="fas fa-lock" style="margin-right: 5px;"></i> Secure Checkout
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</main>

<style>
input[type="number"]::-webkit-inner-spin-button, 
input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<?php require_once 'includes/footer.php'; ?>
