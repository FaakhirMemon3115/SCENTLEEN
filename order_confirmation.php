<?php
// order_confirmation.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

// Security check - must come from POST and have a cart
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// Collect billing info
$first_name     = trim($_POST['first_name'] ?? '');
$last_name      = trim($_POST['last_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$address        = trim($_POST['address'] ?? '');
$address2       = trim($_POST['address2'] ?? '');
$city           = trim($_POST['city'] ?? '');
$postcode       = trim($_POST['postcode'] ?? '');
$notes          = trim($_POST['notes'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? 'Cash on Delivery');
$total          = (float)($_POST['total'] ?? 0);
$shipping       = (float)($_POST['shipping'] ?? 0);

$full_name = $first_name . ' ' . $last_name;
$full_address = $address . ($address2 ? ', '.$address2 : '') . ', ' . $city . ', ' . $postcode;

$order_number = 'SCT-' . strtoupper(uniqid());
$order_id = null;

// Determine user_id (guest or logged in)
$user_id = $_SESSION['user_id'] ?? null;

// If guest (no session user), create a guest user record or handle gracefully
if (!$user_id) {
    // Check if email already in DB
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $user_id = $existing['id'];
        } else {
            // Insert a guest user
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (:name, :email, :phone, :pass, 'customer', 'active')");
            $stmt->execute([
                ':name'  => $full_name,
                ':email' => $email,
                ':phone' => $phone,
                ':pass'  => password_hash(uniqid(), PASSWORD_DEFAULT) // random password for guest
            ]);
            $user_id = $pdo->lastInsertId();
        }
    } catch(PDOException $e) {
        // If users table issue, fallback: use 1 (admin) -- should not happen
        $user_id = 1;
    }
}

// Save order to database
try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total, status, payment_method) VALUES (:uid, :onum, :total, 'Pending', :payment)");
    $stmt->execute([
        ':uid'     => $user_id,
        ':onum'    => $order_number,
        ':total'   => $total,
        ':payment' => $payment_method
    ]);
    $order_id = $pdo->lastInsertId();

    // Save order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt2 = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (:oid, :pid, :qty, :price)");
        $stmt2->execute([
            ':oid'   => $order_id,
            ':pid'   => $product_id,
            ':qty'   => $item['qty'],
            ':price' => $item['price']
        ]);
    }

    // Clear the cart
    $cart_snapshot = $_SESSION['cart'];
    unset($_SESSION['cart']);

} catch(PDOException $e) {
    // If DB fails, still show a message but log the error
    error_log("Order save error: " . $e->getMessage());
}

// --- Send Confirmation Email ---
// Build cart summary HTML for email
$cart_rows_html = '';
$cart_rows_text = '';

// Re-fetch product names for the email
$item_ids = array_keys($cart_snapshot ?? []);
$prod_names = [];
if (!empty($item_ids)) {
    $ph = str_repeat('?,', count($item_ids) - 1) . '?';
    try {
        $ps = $pdo->prepare("SELECT id, name FROM products WHERE id IN ($ph)");
        $ps->execute($item_ids);
        foreach ($ps->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $prod_names[$r['id']] = $r['name'];
        }
    } catch(PDOException $e) {}
}

foreach (($cart_snapshot ?? []) as $pid => $item) {
    $name = $prod_names[$pid] ?? "Product #$pid";
    $line_total = number_format($item['price'] * $item['qty'], 2);
    $price_fmt  = number_format($item['price'], 2);
    $cart_rows_html .= "<tr>
        <td style='padding:10px; border-bottom:1px solid #eee;'>{$name}</td>
        <td style='padding:10px; border-bottom:1px solid #eee; text-align:center;'>{$item['qty']}</td>
        <td style='padding:10px; border-bottom:1px solid #eee; text-align:right;'>Rs. {$price_fmt}</td>
        <td style='padding:10px; border-bottom:1px solid #eee; text-align:right;'>Rs. {$line_total}</td>
    </tr>";
    $cart_rows_text .= "  - {$name} x{$item['qty']} — Rs. {$line_total}\n";
}

$subtotal_fmt = number_format($total - $shipping, 2);
$shipping_fmt = number_format($shipping, 2);
$total_fmt    = number_format($total, 2);

$email_subject = "Order Confirmed! #{$order_number} — Scentleen";

$email_body_html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:30px 0;">
<tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        
        <!-- Header -->
        <tr>
            <td style="background:#1a1a1a;padding:40px;text-align:center;">
                <h1 style="color:#C9A96E;margin:0;font-size:28px;letter-spacing:3px;">SCENTLEEN</h1>
                <p style="color:#ccc;margin:8px 0 0;font-size:13px;letter-spacing:1px;">THE ART OF LUXURY FRAGRANCE</p>
            </td>
        </tr>
        
        <!-- Success Banner -->
        <tr>
            <td style="background:#eafaf1;padding:25px;text-align:center;border-bottom:3px solid #27ae60;">
                <p style="font-size:32px;margin:0;">🎉</p>
                <h2 style="color:#27ae60;margin:10px 0 5px;font-size:22px;">Your Order is Confirmed!</h2>
                <p style="color:#555;margin:0;font-size:14px;">Thank you for shopping with Scentleen. We've received your order.</p>
            </td>
        </tr>
        
        <!-- Order Info -->
        <tr>
            <td style="padding:30px;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding:10px;background:#f9f9f9;border-radius:8px;">
                            <p style="margin:0 0 5px;font-size:12px;color:#999;text-transform:uppercase;letter-spacing:1px;">Order Number</p>
                            <p style="margin:0;font-size:18px;font-weight:bold;color:#1a1a1a;">{$order_number}</p>
                        </td>
                        <td width="20"></td>
                        <td style="padding:10px;background:#f9f9f9;border-radius:8px;">
                            <p style="margin:0 0 5px;font-size:12px;color:#999;text-transform:uppercase;letter-spacing:1px;">Payment</p>
                            <p style="margin:0;font-size:16px;font-weight:bold;color:#1a1a1a;">{$payment_method}</p>
                        </td>
                    </tr>
                </table>

                <h3 style="margin:30px 0 15px;font-size:16px;color:#1a1a1a;border-bottom:2px solid #C9A96E;padding-bottom:8px;">Order Summary</h3>
                <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #eee;border-radius:8px;overflow:hidden;">
                    <thead>
                        <tr style="background:#f9f9f9;">
                            <th style="padding:12px;text-align:left;font-size:12px;color:#666;text-transform:uppercase;">Product</th>
                            <th style="padding:12px;text-align:center;font-size:12px;color:#666;text-transform:uppercase;">Qty</th>
                            <th style="padding:12px;text-align:right;font-size:12px;color:#666;text-transform:uppercase;">Price</th>
                            <th style="padding:12px;text-align:right;font-size:12px;color:#666;text-transform:uppercase;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$cart_rows_html}
                        <tr>
                            <td colspan="3" style="padding:10px;text-align:right;color:#666;font-size:14px;">Subtotal</td>
                            <td style="padding:10px;text-align:right;font-size:14px;">Rs. {$subtotal_fmt}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="padding:10px;text-align:right;color:#666;font-size:14px;">Shipping</td>
                            <td style="padding:10px;text-align:right;font-size:14px;">Rs. {$shipping_fmt}</td>
                        </tr>
                        <tr style="background:#1a1a1a;">
                            <td colspan="3" style="padding:15px;text-align:right;color:#C9A96E;font-weight:bold;font-size:15px;">Total</td>
                            <td style="padding:15px;text-align:right;color:#C9A96E;font-weight:bold;font-size:15px;">Rs. {$total_fmt}</td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin:30px 0 15px;font-size:16px;color:#1a1a1a;border-bottom:2px solid #C9A96E;padding-bottom:8px;">Delivery Address</h3>
                <div style="background:#f9f9f9;padding:15px;border-radius:8px;font-size:14px;color:#555;line-height:1.8;">
                    <strong>{$full_name}</strong><br>
                    {$full_address}<br>
                    📞 {$phone}
                </div>

                <?php if($notes): ?>
                <h3 style="margin:25px 0 10px;font-size:15px;color:#1a1a1a;">Order Notes</h3>
                <p style="background:#fffef0;padding:12px;border-radius:8px;font-size:14px;color:#555;border-left:3px solid #C9A96E;"><?php echo htmlspecialchars($notes); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="background:#1a1a1a;padding:30px;text-align:center;">
                <p style="color:#ccc;font-size:13px;margin:0 0 10px;">Questions? Contact us at <a href="mailto:support@scentleen.com" style="color:#C9A96E;">support@scentleen.com</a></p>
                <p style="color:#666;font-size:12px;margin:0;">© <?php echo date('Y'); ?> Scentleen Fragrance Store. All Rights Reserved.</p>
            </td>
        </tr>

    </table>
</td></tr>
</table>
</body>
</html>
HTML;

// Send Email
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "From: Scentleen <noreply@scentleen.com>\r\n";
$headers .= "Reply-To: support@scentleen.com\r\n";

mail($email, $email_subject, $email_body_html, $headers);

// Now require header for the success page display
require_once 'includes/header.php';
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="max-width: 700px; text-align: center; padding: 60px 20px;">
        
        <div style="width: 100px; height: 100px; background: #eafaf1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; animation: popIn 0.6s ease;">
            <i class="fas fa-check" style="font-size: 2.5rem; color: #27ae60;"></i>
        </div>

        <h1 style="font-size: 2.5rem; margin-bottom: 15px;">Order Placed Successfully!</h1>
        <p style="color: #666; font-size: 1.1rem; margin-bottom: 10px;">Thank you, <strong><?php echo htmlspecialchars($full_name); ?></strong>! Your order has been received.</p>
        <p style="color: #999; font-size: 0.95rem; margin-bottom: 30px;">
            A confirmation email has been sent to <strong style="color: var(--gold-color);"><?php echo htmlspecialchars($email); ?></strong>
        </p>

        <div style="background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 40px; text-align: left;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
                <div>
                    <p style="font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Order Number</p>
                    <p style="font-size: 1.3rem; font-weight: 700; color: var(--text-color); font-family: var(--font-heading);"><?php echo $order_number; ?></p>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Payment</p>
                    <p style="font-size: 1rem; font-weight: 600;"><?php echo htmlspecialchars($payment_method); ?></p>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Total</p>
                    <p style="font-size: 1.3rem; font-weight: 700; color: var(--gold-color);">Rs. <?php echo number_format($total, 2); ?></p>
                </div>
                <div>
                    <p style="font-size: 0.8rem; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Status</p>
                    <span style="background: #fff9e6; color: #f39c12; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">⏳ Pending</span>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

            <p style="font-size: 0.9rem; color: #666;">
                <i class="fas fa-map-marker-alt" style="color: var(--gold-color); margin-right: 8px;"></i>
                <strong>Deliver to:</strong> <?php echo htmlspecialchars($full_address); ?>
            </p>
        </div>

        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="shop.php" class="btn-primary">Continue Shopping</a>
            <a href="home.php" class="btn-outline">Back to Home</a>
        </div>
    </div>
</main>

<style>
@keyframes popIn {
    0%   { transform: scale(0); opacity: 0; }
    70%  { transform: scale(1.15); }
    100% { transform: scale(1); opacity: 1; }
}
</style>

<?php require_once 'includes/footer.php'; ?>
