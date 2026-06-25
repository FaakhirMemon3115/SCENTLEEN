<?php
// admin/index.php
require_once '../config/database.php';
require_once 'includes/header.php';

// Real stats from DB
try {
    $total_orders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $total_revenue  = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'Cancelled'")->fetchColumn();
    $total_customers= $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $total_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
} catch(PDOException $e) {
    $total_orders = $total_revenue = $total_customers = $total_products = 0;
}

// Real recent orders
try {
    $recent_orders = $pdo->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
} catch(PDOException $e) {
    $recent_orders = [];
}
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <p style="color: #666; font-size: 0.9rem; text-transform: uppercase;">Total Orders</p>
            <h3 style="font-size: 2rem; margin-top: 5px;"><?php echo $total_orders; ?></h3>
        </div>
        <div style="width: 50px; height: 50px; background: rgba(201, 169, 110, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--gold-color); font-size: 1.5rem;">
            <i class="fas fa-shopping-bag"></i>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <p style="color: #666; font-size: 0.9rem; text-transform: uppercase;">Total Revenue</p>
            <h3 style="font-size: 2rem; margin-top: 5px;">Rs. <?php echo number_format($total_revenue); ?></h3>
        </div>
        <div style="width: 50px; height: 50px; background: rgba(39, 174, 96, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #27ae60; font-size: 1.5rem;">
            <i class="fas fa-rupee-sign"></i>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <p style="color: #666; font-size: 0.9rem; text-transform: uppercase;">Customers</p>
            <h3 style="font-size: 2rem; margin-top: 5px;"><?php echo $total_customers; ?></h3>
        </div>
        <div style="width: 50px; height: 50px; background: rgba(52, 152, 219, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3498db; font-size: 1.5rem;">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="card" style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <p style="color: #666; font-size: 0.9rem; text-transform: uppercase;">Products</p>
            <h3 style="font-size: 2rem; margin-top: 5px;"><?php echo $total_products; ?></h3>
        </div>
        <div style="width: 50px; height: 50px; background: rgba(155, 89, 182, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #9b59b6; font-size: 1.5rem;">
            <i class="fas fa-box"></i>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    
    <!-- Chart -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 15px;">Revenue Overview</h3>
        <canvas id="revenueChart" height="120"></canvas>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem; border-bottom: 1px solid #eee; padding-bottom: 15px;">Recent Orders</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php if(empty($recent_orders)): ?>
                <p style="color: #999; font-size: 0.9rem;">No orders yet.</p>
            <?php else: ?>
                <?php foreach($recent_orders as $ord): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f5f5f5; padding-bottom: 12px;">
                    <div>
                        <h4 style="font-size: 0.9rem; margin-bottom: 3px;"><?php echo htmlspecialchars($ord['order_number']); ?></h4>
                        <p style="font-size: 0.8rem; color: #999;"><?php echo htmlspecialchars($ord['customer_name'] ?? 'Guest'); ?></p>
                    </div>
                    <?php
                        $badge = 'badge-warning';
                        if($ord['status'] == 'Delivered') $badge = 'badge-success';
                        if($ord['status'] == 'Cancelled') $badge = 'badge-danger';
                    ?>
                    <span class="badge <?php echo $badge; ?>"><?php echo $ord['status']; ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="orders.php" style="display: block; text-align: center; font-size: 0.9rem; color: var(--gold-color); margin-top: 10px;">View All</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [12000, 19000, 15000, 22000, 18000, 30000, 45200],
                    borderColor: '#C9A96E',
                    backgroundColor: 'rgba(201, 169, 110, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>
