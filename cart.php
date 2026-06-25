<?php
// cart.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Handle Item Removal
if (isset($_GET['remove']) && isset($_SESSION['cart'][$_GET['remove']])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

// Handle Coupon Removal
if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['coupon']);
    $_SESSION['coupon_msg'] = ['type' => 'success', 'text' => 'Coupon removed.'];
    header("Location: cart.php");
    exit;
}

// Handle Coupon Application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_coupon'])) {
    $code = strtoupper(trim($_POST['coupon_code']));
    try {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURRENT_DATE()");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($coupon) {
            $_SESSION['coupon'] = [
                'id' => $coupon['id'],
                'code' => $coupon['code'],
                'discount' => (float)$coupon['discount']
            ];
            $_SESSION['coupon_msg'] = ['type' => 'success', 'text' => 'Coupon applied successfully!'];
        } else {
            $_SESSION['coupon_msg'] = ['type' => 'error', 'text' => 'Invalid or expired coupon code.'];
        }
    } catch(PDOException $e) {
        $_SESSION['coupon_msg'] = ['type' => 'error', 'text' => 'Error applying coupon.'];
    }
    header("Location: cart.php");
    exit;
}

// Handle Cart Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $quantity) {
            $id = (int)$id;
            $quantity = (int)$quantity;
            if ($quantity > 0 && isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = $quantity;
            } elseif ($quantity <= 0 && isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    header("Location: cart.php");
    exit;
}

require_once 'includes/header.php';

$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, image, price, sale_price FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($products as $p) {
            $id = $p['id'];
            $qty = $_SESSION['cart'][$id]['qty'];
            $price = $_SESSION['cart'][$id]['price']; // Price stored at time of add
            
            $subtotal += $price * $qty;
            
            $p['qty'] = $qty;
            $p['cart_price'] = $price;
            $cart_items[] = $p;
        }
    } catch(PDOException $e) {}
}

$shipping = 15.00;
$discount_amount = 0;

if (isset($_SESSION['coupon']) && $subtotal > 0) {
    $discount_amount = ($subtotal * $_SESSION['coupon']['discount']) / 100;
}

$total = $subtotal > 0 ? ($subtotal - $discount_amount) + $shipping : 0;
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    
    <div class="container py-5" style="padding: 60px 20px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center;">Shopping Cart</h1>

        <?php if(isset($_SESSION['coupon_msg'])): ?>
            <?php 
                $cmsg = $_SESSION['coupon_msg']; 
                $bg = $cmsg['type'] == 'success' ? '#eafaf1' : '#fdeaea';
                $color = $cmsg['type'] == 'success' ? '#27ae60' : '#e74c3c';
            ?>
            <div style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>; padding: 15px; border-radius: 5px; margin-bottom: 30px; border-left: 4px solid <?php echo $color; ?>;">
                <?php echo htmlspecialchars($cmsg['text']); ?>
            </div>
            <?php unset($_SESSION['coupon_msg']); ?>
        <?php endif; ?>

        <?php if(empty($cart_items)): ?>
            <div style="text-align: center; padding: 50px 0;">
                <i class="fas fa-shopping-bag" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3>Your cart is empty</h3>
                <a href="shop.php" class="btn-primary" style="margin-top: 20px;">Return to Shop</a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-wrap: wrap; gap: 40px;">
                
                <!-- Cart Items List (Wrapped in form for updating qty) -->
                <form method="POST" action="cart.php" style="flex: 2; min-width: 300px;">
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
                                            <?php $img = $item['image'] ? 'uploads/'.$item['image'] : 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=400&auto=format&fit=crop'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                                            <div>
                                                <h4 style="font-size: 1rem; margin-bottom: 5px;"><a href="product.php?slug=<?php echo urlencode($item['name']); ?>"><?php echo htmlspecialchars($item['name']); ?></a></h4>
                                                <p style="font-size: 0.8rem; color: #999;">SKU: SCT-<?php echo str_pad($item['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 20px; text-align: center; color: #666;">Rs. <?php echo number_format($item['cart_price'], 2); ?></td>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="display: inline-flex; border: 1px solid #ccc; border-radius: 5px; overflow: hidden; width: 100px; height: 35px;">
                                            <button type="button" style="flex: 1; border: none; background: #f9f9f9; cursor: pointer;" onclick="this.nextElementSibling.stepDown()">-</button>
                                            <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="0" style="flex: 1; border: none; text-align: center; outline: none; width: 30px;">
                                            <button type="button" style="flex: 1; border: none; background: #f9f9f9; cursor: pointer;" onclick="this.previousElementSibling.stepUp()">+</button>
                                        </div>
                                    </td>
                                    <td style="padding: 20px; text-align: right; font-weight: 600; color: var(--gold-color);">Rs. <?php echo number_format($item['cart_price'] * $item['qty'], 2); ?></td>
                                    <td style="padding: 20px; text-align: center;">
                                        <a href="cart.php?remove=<?php echo $item['id']; ?>" style="background: transparent; border: none; color: #e74c3c; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                        <button type="submit" name="update_cart" class="btn-outline" style="padding: 10px 20px;">Update Cart</button>
                    </div>
                </form>

                <!-- Order Summary Sidebar -->
                <div style="flex: 1; min-width: 300px;">
                    <div style="background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: sticky; top: 100px;">
                        <h3 style="margin-bottom: 25px; font-size: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 15px;">Order Summary</h3>
                        
                        <div style="margin-bottom: 25px;">
                            <form method="POST" action="cart.php" style="display: flex; gap: 10px;">
                                <input type="text" name="coupon_code" placeholder="Coupon Code" required style="flex: 1; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; text-transform: uppercase;">
                                <button type="submit" name="apply_coupon" class="btn-outline" style="padding: 10px 20px;">Apply</button>
                            </form>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                            <span>Subtotal</span>
                            <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #666;">
                            <span>Shipping</span>
                            <span>Rs. <?php echo number_format($shipping, 2); ?></span>
                        </div>

                        <?php if(isset($_SESSION['coupon'])): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #27ae60; font-weight: 600;">
                            <span>Discount (<?php echo $_SESSION['coupon']['code']; ?> - <?php echo $_SESSION['coupon']['discount']; ?>%) <a href="cart.php?remove_coupon=1" style="color: #e74c3c; font-size: 0.8rem; margin-left: 5px;"><i class="fas fa-times"></i></a></span>
                            <span>- Rs. <?php echo number_format($discount_amount, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 1.2rem; font-weight: 600;">
                            <span>Total</span>
                            <span style="color: var(--gold-color);">Rs. <?php echo number_format($total, 2); ?></span>
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
