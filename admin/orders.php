<?php
// admin/orders.php
require_once '../config/database.php';
// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id  = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $allowed = ['Pending', 'Processing', 'Delivered', 'Cancelled'];
    if (in_array($new_status, $allowed)) {
        try {
            $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id")
                ->execute([':status' => $new_status, ':id' => $order_id]);
        } catch(PDOException $e) {}
    }
    header("Location: orders.php?updated=1");
    exit;
}

require_once 'includes/header.php';

// Fetch orders with customer name and item count
try {
    $orders = $pdo->query(
        "SELECT o.*, u.name AS customer_name, u.email AS customer_email,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) AS item_count
         FROM orders o
         LEFT JOIN users u ON o.user_id = u.id
         ORDER BY o.id DESC"
    )->fetchAll();
} catch(PDOException $e) {
    $orders = [];
}
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <h2 style="font-size:1.5rem; font-weight:500;">Order Management
        <span style="font-size:0.85rem; font-weight:400; color:#999; margin-left:10px;">(<?php echo count($orders); ?> total)</span>
    </h2>
</div>

<?php if(isset($_GET['updated'])): ?>
<div style="background:#eafaf1;color:#27ae60;padding:12px 20px;border-radius:5px;margin-bottom:20px;border-left:4px solid #27ae60;font-size:0.9rem;">
    ✓ Order status updated successfully.
</div>
<?php endif; ?>

<div class="card" style="overflow-x:auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th style="text-align:right; min-width:160px;">Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($orders)): ?>
                <tr>
                    <td colspan="8" style="text-align:center; padding:40px; color:#999;">
                        <i class="fas fa-box-open" style="font-size:2rem; display:block; margin-bottom:10px;"></i>
                        No orders found yet.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($orders as $o):
                    $badge = 'badge-warning';
                    if($o['status'] === 'Delivered')  $badge = 'badge-success';
                    if($o['status'] === 'Cancelled')  $badge = 'badge-danger';
                    if($o['status'] === 'Processing') $badge = 'badge-warning';
                ?>
                <tr>
                    <td><strong style="color:var(--gold-color);"><?php echo htmlspecialchars($o['order_number']); ?></strong></td>
                    <td>
                        <strong style="font-size:0.9rem;"><?php echo htmlspecialchars($o['customer_name'] ?? 'Guest'); ?></strong><br>
                        <span style="font-size:0.78rem;color:#999;"><?php echo htmlspecialchars($o['customer_email'] ?? ''); ?></span>
                    </td>
                    <td style="color:#666; font-size:0.9rem;"><?php echo date('d M Y, h:i A', strtotime($o['created_at'])); ?></td>
                    <td style="text-align:center;"><?php echo $o['item_count']; ?> item(s)</td>
                    <td style="color:var(--gold-color); font-weight:600;">Rs. <?php echo number_format($o['total'], 2); ?></td>
                    <td style="font-size:0.85rem;"><?php echo htmlspecialchars($o['payment_method']); ?></td>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo $o['status']; ?></span></td>
                    <td style="text-align:right;">
                        <form method="POST" action="" style="display:flex; gap:6px; justify-content:flex-end; align-items:center;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="status" style="padding:6px 10px; border:1px solid #ddd; border-radius:5px; font-family:inherit; font-size:0.82rem; outline:none; background:#fff; cursor:pointer;">
                                <?php foreach(['Pending','Processing','Delivered','Cancelled'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $o['status']===$s ? 'selected' : ''; ?>><?php echo $s; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" style="background:var(--text-color);color:#fff;border:none;padding:6px 12px;border-radius:5px;cursor:pointer;font-size:0.82rem;transition:background 0.2s;" onmouseover="this.style.background='#C9A96E'" onmouseout="this.style.background='var(--text-color)'">
                                Save
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
