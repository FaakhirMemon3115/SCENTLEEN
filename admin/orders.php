<?php
// admin/orders.php
require_once '../config/database.php';
require_once 'includes/header.php';

// Fetch orders
try {
    $stmt = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $orders = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 1.5rem; font-weight: 500;">Order Management</h2>
</div>

<div class="card" style="overflow-x: auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($orders)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">No orders found.</td>
                </tr>
            <?php else: ?>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><strong>#<?php echo htmlspecialchars($o['order_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($o['customer_name']); ?></td>
                    <td style="color: #666;"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                    <td style="color: var(--gold-color); font-weight: 600;">$<?php echo number_format($o['total'], 2); ?></td>
                    <td><?php echo strtoupper($o['payment_method']); ?></td>
                    <td>
                        <?php 
                            $status_class = '';
                            if($o['status'] == 'Pending') $status_class = 'badge-warning';
                            if($o['status'] == 'Processing') $status_class = 'badge-warning';
                            if($o['status'] == 'Delivered') $status_class = 'badge-success';
                            if($o['status'] == 'Cancelled') $status_class = 'badge-danger';
                        ?>
                        <span class="badge <?php echo $status_class; ?>"><?php echo $o['status']; ?></span>
                    </td>
                    <td style="text-align: right;">
                        <a href="#" style="color: #3498db; font-size: 0.9rem;">View Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
