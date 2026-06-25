<?php
// account.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'config/database.php';
require_once 'includes/header.php';

$user_id = $_SESSION['user_id'];

// Fetch user info
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) { $user = []; }

// Fetch user's orders
try {
    $stmt = $pdo->prepare("SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count FROM orders o WHERE o.user_id = :uid ORDER BY o.created_at DESC");
    $stmt->execute([':uid' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) { $orders = []; }

// Handle profile update
$update_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name  = trim($_POST['name']);
    $new_phone = trim($_POST['phone']);
    try {
        $upd = $pdo->prepare("UPDATE users SET name = :name, phone = :phone WHERE id = :id");
        $upd->execute([':name' => $new_name, ':phone' => $new_phone, ':id' => $user_id]);
        $_SESSION['user_name'] = $new_name;
        $user['name']  = $new_name;
        $user['phone'] = $new_phone;
        $update_msg = 'success';
    } catch(PDOException $e) { $update_msg = 'error'; }
}

// Handle password change
$pwd_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $cur_pass  = $_POST['current_password'];
    $new_pass  = $_POST['new_password'];
    $conf_pass = $_POST['confirm_new_password'];

    if (!password_verify($cur_pass, $user['password'])) {
        $pwd_msg = 'wrong';
    } elseif (strlen($new_pass) < 6) {
        $pwd_msg = 'short';
    } elseif ($new_pass !== $conf_pass) {
        $pwd_msg = 'mismatch';
    } else {
        try {
            $upd = $pdo->prepare("UPDATE users SET password = :pass WHERE id = :id");
            $upd->execute([':pass' => password_hash($new_pass, PASSWORD_DEFAULT), ':id' => $user_id]);
            $pwd_msg = 'success';
        } catch(PDOException $e) { $pwd_msg = 'error'; }
    }
}
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
<div class="container" style="padding: 50px 20px;">

    <!-- Page Title -->
    <div style="margin-bottom: 40px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 5px;">My Account</h1>
        <p style="color: #888; font-size: 0.9rem;">Welcome back, <strong><?php echo htmlspecialchars($user['name']); ?></strong></p>
    </div>

    <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: flex-start;">

        <!-- ── LEFT SIDEBAR ── -->
        <aside style="width: 240px; min-width: 220px;">
            <!-- User Card -->
            <div style="background:#fff; border-radius:12px; padding:25px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,0.05); margin-bottom:20px;">
                <div style="width:70px;height:70px;background:var(--text-color);color:#fff;border-radius:50%;font-size:1.8rem;font-family:var(--font-heading);display:flex;align-items:center;justify-content:center;margin:0 auto 15px;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h3 style="font-size:1.1rem;margin-bottom:4px;"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p style="font-size:0.8rem;color:#999;"><?php echo htmlspecialchars($user['email']); ?></p>
                <span style="display:inline-block;margin-top:10px;background:<?php echo $user['role']==='admin'?'#fff9e6':'#eafaf1';?>;color:<?php echo $user['role']==='admin'?'#f39c12':'#27ae60';?>;padding:4px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;">
                    <?php echo $user['role'] === 'admin' ? '⚡ Administrator' : '✓ Member'; ?>
                </span>
            </div>

            <!-- Nav Links -->
            <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                <a href="#orders" onclick="showTab('orders')" id="tab-orders" style="display:flex;align-items:center;gap:12px;padding:14px 20px;font-size:0.9rem;border-left:3px solid var(--gold-color);color:var(--text-color);background:#fdfaf6;">
                    <i class="fas fa-shopping-bag" style="color:var(--gold-color);width:16px;"></i> My Orders
                </a>
                <a href="#profile" onclick="showTab('profile')" id="tab-profile" style="display:flex;align-items:center;gap:12px;padding:14px 20px;font-size:0.9rem;border-left:3px solid transparent;color:#666;">
                    <i class="fas fa-user" style="width:16px;color:#aaa;"></i> Edit Profile
                </a>
                <a href="#password" onclick="showTab('password')" id="tab-password" style="display:flex;align-items:center;gap:12px;padding:14px 20px;font-size:0.9rem;border-left:3px solid transparent;color:#666;">
                    <i class="fas fa-lock" style="width:16px;color:#aaa;"></i> Change Password
                </a>
                <div style="height:1px;background:#eee;margin:4px 0;"></div>
                <form method="POST" action="logout.php">
                    <button type="submit" style="display:flex;align-items:center;gap:12px;padding:14px 20px;font-size:0.9rem;border:none;background:none;width:100%;cursor:pointer;color:#e74c3c;border-left:3px solid transparent;">
                        <i class="fas fa-sign-out-alt" style="width:16px;"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- ── RIGHT CONTENT ── -->
        <div style="flex:1;min-width:280px;">

            <!-- ─── ORDERS TAB ─── -->
            <div id="content-orders">
                <div style="background:#fff;border-radius:12px;padding:30px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                    <h2 style="font-size:1.3rem;margin-bottom:25px;border-bottom:1px solid #eee;padding-bottom:12px;">
                        <i class="fas fa-shopping-bag" style="color:var(--gold-color);margin-right:10px;"></i>My Orders
                        <span style="font-size:0.8rem;color:#999;font-weight:400;margin-left:10px;">(<?php echo count($orders); ?> total)</span>
                    </h2>

                    <?php if (empty($orders)): ?>
                    <div style="text-align:center;padding:40px 0;">
                        <i class="fas fa-box-open" style="font-size:3rem;color:#ddd;margin-bottom:15px;display:block;"></i>
                        <h3 style="color:#aaa;margin-bottom:10px;">No orders yet</h3>
                        <a href="shop.php" class="btn-primary" style="margin-top:10px;">Start Shopping</a>
                    </div>
                    <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
                            <thead>
                                <tr style="background:#f9f9f9;text-transform:uppercase;font-size:0.75rem;color:#888;">
                                    <th style="padding:12px 15px;text-align:left;">Order #</th>
                                    <th style="padding:12px 15px;text-align:left;">Date</th>
                                    <th style="padding:12px 15px;text-align:center;">Items</th>
                                    <th style="padding:12px 15px;text-align:right;">Total</th>
                                    <th style="padding:12px 15px;text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $order): ?>
                                <?php
                                $badge_color = '#fff9e6'; $badge_text = '#f39c12';
                                if ($order['status'] === 'Delivered')  { $badge_color = '#eafaf1'; $badge_text = '#27ae60'; }
                                if ($order['status'] === 'Processing') { $badge_color = '#eaf4fb'; $badge_text = '#3498db'; }
                                if ($order['status'] === 'Cancelled')  { $badge_color = '#fdeaea'; $badge_text = '#e74c3c'; }
                                ?>
                                <tr style="border-bottom:1px solid #f5f5f5;">
                                    <td style="padding:14px 15px;font-weight:600;color:var(--text-color);"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td style="padding:14px 15px;color:#888;"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td style="padding:14px 15px;text-align:center;color:#666;"><?php echo $order['item_count']; ?></td>
                                    <td style="padding:14px 15px;text-align:right;font-weight:600;color:var(--gold-color);">Rs. <?php echo number_format($order['total'], 2); ?></td>
                                    <td style="padding:14px 15px;text-align:center;">
                                        <span style="background:<?php echo $badge_color;?>;color:<?php echo $badge_text;?>;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ─── PROFILE TAB ─── -->
            <div id="content-profile" style="display:none;">
                <div style="background:#fff;border-radius:12px;padding:30px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                    <h2 style="font-size:1.3rem;margin-bottom:25px;border-bottom:1px solid #eee;padding-bottom:12px;">
                        <i class="fas fa-user" style="color:var(--gold-color);margin-right:10px;"></i>Edit Profile
                    </h2>

                    <?php if ($update_msg === 'success'): ?>
                    <div style="background:#eafaf1;color:#27ae60;padding:12px 18px;border-radius:8px;margin-bottom:20px;border-left:4px solid #27ae60;font-size:0.9rem;">
                        ✓ Profile updated successfully!
                    </div>
                    <?php elseif ($update_msg === 'error'): ?>
                    <div style="background:#fdeaea;color:#e74c3c;padding:12px 18px;border-radius:8px;margin-bottom:20px;border-left:4px solid #e74c3c;font-size:0.9rem;">
                        ✗ Something went wrong. Please try again.
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div style="margin-bottom:18px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;outline:none;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#e5e5e5'">
                        </div>
                        <div style="margin-bottom:18px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">Email Address</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;background:#f9f9f9;color:#aaa;cursor:not-allowed;">
                            <small style="color:#aaa;font-size:0.78rem;">Email cannot be changed.</small>
                        </div>
                        <div style="margin-bottom:25px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="03xx-xxxxxxx" style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;outline:none;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#e5e5e5'">
                        </div>
                        <button type="submit" name="update_profile" style="background:var(--text-color);color:#fff;border:none;padding:13px 30px;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;cursor:pointer;transition:background 0.3s;" onmouseover="this.style.background='var(--gold-color)'" onmouseout="this.style.background='var(--text-color)'">
                            <i class="fas fa-save" style="margin-right:8px;"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- ─── PASSWORD TAB ─── -->
            <div id="content-password" style="display:none;">
                <div style="background:#fff;border-radius:12px;padding:30px;box-shadow:0 4px 20px rgba(0,0,0,0.05);">
                    <h2 style="font-size:1.3rem;margin-bottom:25px;border-bottom:1px solid #eee;padding-bottom:12px;">
                        <i class="fas fa-lock" style="color:var(--gold-color);margin-right:10px;"></i>Change Password
                    </h2>

                    <?php
                    $pwd_messages = [
                        'wrong'    => ['color'=>'#e74c3c','bg'=>'#fdeaea','border'=>'#e74c3c','text'=>'Current password is incorrect.'],
                        'short'    => ['color'=>'#e74c3c','bg'=>'#fdeaea','border'=>'#e74c3c','text'=>'New password must be at least 6 characters.'],
                        'mismatch' => ['color'=>'#e74c3c','bg'=>'#fdeaea','border'=>'#e74c3c','text'=>'New passwords do not match.'],
                        'success'  => ['color'=>'#27ae60','bg'=>'#eafaf1','border'=>'#27ae60','text'=>'✓ Password changed successfully!'],
                        'error'    => ['color'=>'#e74c3c','bg'=>'#fdeaea','border'=>'#e74c3c','text'=>'Something went wrong. Try again.'],
                    ];
                    if ($pwd_msg && isset($pwd_messages[$pwd_msg])):
                        $m = $pwd_messages[$pwd_msg];
                    ?>
                    <div style="background:<?php echo $m['bg'];?>;color:<?php echo $m['color'];?>;padding:12px 18px;border-radius:8px;margin-bottom:20px;border-left:4px solid <?php echo $m['border'];?>;font-size:0.9rem;">
                        <?php echo $m['text']; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div style="margin-bottom:18px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">Current Password</label>
                            <input type="password" name="current_password" required style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;outline:none;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#e5e5e5'">
                        </div>
                        <div style="margin-bottom:18px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">New Password</label>
                            <input type="password" name="new_password" required placeholder="Min. 6 characters" style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;outline:none;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#e5e5e5'">
                        </div>
                        <div style="margin-bottom:25px;">
                            <label style="display:block;font-size:0.85rem;font-weight:500;color:#555;margin-bottom:7px;">Confirm New Password</label>
                            <input type="password" name="confirm_new_password" required style="width:100%;padding:13px 15px;border:1.5px solid #e5e5e5;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;outline:none;" onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#e5e5e5'">
                        </div>
                        <button type="submit" name="change_password" style="background:var(--text-color);color:#fff;border:none;padding:13px 30px;border-radius:8px;font-family:var(--font-body);font-size:0.95rem;cursor:pointer;transition:background 0.3s;" onmouseover="this.style.background='var(--gold-color)'" onmouseout="this.style.background='var(--text-color)'">
                            <i class="fas fa-key" style="margin-right:8px;"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
</main>

<script>
function showTab(tab) {
    // Hide all content
    ['orders','profile','password'].forEach(function(t) {
        document.getElementById('content-' + t).style.display = 'none';
        var link = document.getElementById('tab-' + t);
        if (link) {
            link.style.borderLeftColor = 'transparent';
            link.style.color = '#666';
            link.style.background = '';
        }
    });

    // Show selected
    document.getElementById('content-' + tab).style.display = 'block';
    var active = document.getElementById('tab-' + tab);
    if (active) {
        active.style.borderLeftColor = 'var(--gold-color)';
        active.style.color = 'var(--text-color)';
        active.style.background = '#fdfaf6';
    }
}

// Show profile tab if update was just done
<?php if ($update_msg): ?>showTab('profile');<?php endif; ?>
<?php if ($pwd_msg): ?>showTab('password');<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
