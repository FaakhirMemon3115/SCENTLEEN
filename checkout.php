<?php
// checkout.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
require_once 'config/database.php';

// Redirect to cart if empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Fetch cart items from DB
$cart_items = [];
$subtotal = 0;

$ids = array_keys($_SESSION['cart']);
$placeholders = str_repeat('?,', count($ids) - 1) . '?';

try {
    $stmt = $pdo->prepare("SELECT id, name, slug FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $id = $p['id'];
        $qty   = $_SESSION['cart'][$id]['qty'];
        $price = $_SESSION['cart'][$id]['price'];
        $subtotal += $price * $qty;

        $cart_items[] = [
            'id'    => $id,
            'name'  => $p['name'],
            'slug'  => $p['slug'],
            'qty'   => $qty,
            'price' => $price,
        ];
    }
} catch(PDOException $e) {}

$shipping = 150.00; // Rs. 150 shipping
$total    = $subtotal + $shipping;

// Pre-fill form if user is logged in
$user_name  = $_SESSION['user_name'] ?? '';
$user_email = '';
if (isset($_SESSION['user_id'])) {
    try {
        $u = $pdo->prepare("SELECT email, phone FROM users WHERE id = :id");
        $u->execute([':id' => $_SESSION['user_id']]);
        $u = $u->fetch(PDO::FETCH_ASSOC);
        $user_email = $u['email'] ?? '';
    } catch(PDOException $e) {}
}
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
                            <input type="text" name="first_name" required value="<?php echo htmlspecialchars(explode(' ', $user_name)[0] ?? ''); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Last Name *</label>
                            <?php $last = implode(' ', array_slice(explode(' ', $user_name), 1)); ?>
                            <input type="text" name="last_name" required value="<?php echo htmlspecialchars($last); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Email Address * <small style="color:#999">(Order confirmation will be sent here)</small></label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($user_email); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Phone *</label>
                        <input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Street Address *</label>
                        <input type="text" name="address" placeholder="House # / Street" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); margin-bottom: 10px;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                        <input type="text" name="address2" placeholder="Apartment, suite, unit etc. (optional)" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">City *</label>
                            <input type="text" name="city" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Postcode / ZIP *</label>
                            <input type="text" name="postcode" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-size: 0.9rem; color: #666;">Order Notes (Optional)</label>
                        <textarea name="notes" rows="3" placeholder="Any special instructions for your order..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); resize: vertical;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'"></textarea>
                    </div>
                </div>
            </div>

            <!-- Your Order -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: sticky; top: 100px;">
                    <h3 style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">Your Order</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; color: #888;">
                        <span>Product</span>
                        <span>Subtotal</span>
                    </div>
                    
                    <?php foreach($cart_items as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; color: #666; border-bottom: 1px solid #f5f5f5; padding-bottom: 10px;">
                        <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['qty']; ?></span>
                        <span>Rs. <?php echo number_format($item['price'] * $item['qty'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div style="display: flex; justify-content: space-between; margin: 15px 0; color: #666;">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                        <span>Shipping</span>
                        <span>Rs. <?php echo number_format($shipping, 2); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 1.2rem; font-weight: 600;">
                        <span>Total</span>
                        <span style="color: var(--gold-color);">Rs. <?php echo number_format($total, 2); ?></span>
                    </div>

                    <!-- Hidden inputs to pass cart totals -->
                    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                    <input type="hidden" name="shipping" value="<?php echo $shipping; ?>">
                    <input type="hidden" name="total" value="<?php echo $total; ?>">

                    <div style="margin-bottom: 30px;">
                        <h4 style="margin-bottom: 15px; font-size: 1rem;">Payment Method</h4>
                        
                        <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px; cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                            <input type="radio" name="payment_method" value="Cash on Delivery" checked style="accent-color: var(--text-color);">
                            <div>
                                <strong>Cash on Delivery</strong>
                                <p style="font-size: 0.8rem; color: #999; margin: 0;">Pay with cash upon delivery.</p>
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 15px; border: 1px solid #ddd; border-radius: 5px;">
                            <input type="radio" name="payment_method" value="Bank Transfer" style="accent-color: var(--text-color);">
                            <div>
                                <strong>Bank Transfer</strong>
                                <p style="font-size: 0.8rem; color: #999; margin: 0;">Transfer to our bank account before delivery.</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; text-align: center; font-size: 1.1rem; padding: 15px; cursor: pointer;">
                        <i class="fas fa-lock" style="margin-right: 8px;"></i> Place Order
                    </button>
                    
                    <div style="text-align: center; margin-top: 15px; font-size: 0.8rem; color: #999;">
                        <i class="fas fa-shield-alt" style="margin-right: 5px;"></i> Your information is safe and secure
                    </div>
                </div>
            </div>

        </form>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
