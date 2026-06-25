<?php
// admin/coupons.php
require_once '../config/database.php';
require_once 'includes/header.php';

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM coupons WHERE id = :id")->execute([':id' => $del_id]);
        $message = "Coupon deleted successfully.";
    } catch(PDOException $e) {
        $error = "Error deleting coupon.";
    }
}

// Handle Add Coupon
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $discount = (float)$_POST['discount'];
    $expiry_date = $_POST['expiry_date'];
    $status = $_POST['status'];
    
    if (empty($code) || empty($discount) || empty($expiry_date)) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO coupons (code, discount, expiry_date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $discount, $expiry_date, $status]);
            $message = "Coupon added successfully.";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "A coupon with this code already exists.";
            } else {
                $error = "Error adding coupon.";
            }
        }
    }
}

// Fetch coupons
try {
    $stmt = $pdo->query("SELECT * FROM coupons ORDER BY id DESC");
    $coupons = $stmt->fetchAll();
} catch(PDOException $e) {
    $coupons = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 1.5rem; font-weight: 500;">Coupon Management</h2>
</div>

<?php if($message): ?>
    <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div style="background: #fdeaea; color: #e74c3c; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #e74c3c;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- Add Coupon Form -->
    <div class="card">
        <h3 style="font-size: 1.1rem; margin-bottom: 20px;">Add New Coupon</h3>
        <form method="POST" action="">
            <input type="hidden" name="add_coupon" value="1">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Coupon Code *</label>
                <input type="text" name="code" required placeholder="e.g. SUMMER20" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none; text-transform: uppercase;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Discount Percentage (%) *</label>
                <input type="number" step="0.01" name="discount" required placeholder="e.g. 15" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Expiry Date *</label>
                <input type="date" name="expiry_date" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Status</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Save Coupon</button>
        </form>
    </div>

    <!-- Coupons List -->
    <div class="card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($coupons)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #999;">No coupons found.</td></tr>
                <?php else: ?>
                    <?php foreach($coupons as $c): ?>
                    <?php 
                        $is_expired = strtotime($c['expiry_date']) < strtotime('today');
                    ?>
                    <tr>
                        <td><strong style="letter-spacing: 1px;"><?php echo htmlspecialchars($c['code']); ?></strong></td>
                        <td><span style="color: #27ae60; font-weight: 600;"><?php echo $c['discount']; ?>% OFF</span></td>
                        <td>
                            <?php echo date('M d, Y', strtotime($c['expiry_date'])); ?>
                            <?php if($is_expired): ?>
                                <br><span style="font-size: 0.75rem; color: #e74c3c;">Expired</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($c['status'] == 'active' && !$is_expired): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="coupons.php?delete=<?php echo $c['id']; ?>" style="color: #e74c3c;" title="Delete" onclick="return confirm('Delete this coupon?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
