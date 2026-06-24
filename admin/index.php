<?php
// admin/index.php
require_once '../config/database.php';
require_once 'includes/header.php';

// Mock data for Dashboard MVP
$total_orders = 156;
$total_revenue = 45200.00;
$total_customers = 89;
$total_products = 45;

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
            <h3 style="font-size: 2rem; margin-top: 5px;">$<?php echo number_format($total_revenue); ?></h3>
        </div>
        <div style="width: 50px; height: 50px; background: rgba(39, 174, 96, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #27ae60; font-size: 1.5rem;">
            <i class="fas fa-dollar-sign"></i>
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
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="font-size: 0.9rem; margin-bottom: 3px;">#ORD-0012</h4>
                    <p style="font-size: 0.8rem; color: #999;">John Doe</p>
                </div>
                <span class="badge badge-warning">Pending</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="font-size: 0.9rem; margin-bottom: 3px;">#ORD-0011</h4>
                    <p style="font-size: 0.8rem; color: #999;">Sarah Smith</p>
                </div>
                <span class="badge badge-success">Delivered</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 style="font-size: 0.9rem; margin-bottom: 3px;">#ORD-0010</h4>
                    <p style="font-size: 0.8rem; color: #999;">Michael Brown</p>
                </div>
                <span class="badge badge-success">Delivered</span>
            </div>
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
