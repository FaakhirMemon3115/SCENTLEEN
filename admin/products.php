<?php
// admin/products.php
require_once '../config/database.php';
require_once 'includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id' => $del_id]);
    } catch(PDOException $e) {}
    header("Location: products.php?msg=deleted");
    exit;
}

// Fetch products
try {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    $products = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 1.5rem; font-weight: 500;">Product Management</h2>
    <a href="product_add.php" style="background: var(--text-color); color: #fff; padding: 10px 20px; border-radius: 5px; font-size: 0.9rem; text-decoration: none;"><i class="fas fa-plus"></i> Add New Product</a>
</div>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<div style="background:#fdeaea;color:#e74c3c;padding:12px 20px;border-radius:5px;margin-bottom:20px;border-left:4px solid #e74c3c;">Product deleted successfully.</div>
<?php endif; ?>

<div class="card" style="overflow-x: auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th width="60">Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th width="150" style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($products)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">No products found.</td>
                </tr>
            <?php else: ?>
                <?php foreach($products as $p): ?>
                <tr>
                    <td>
                        <img src="<?php echo $p['image'] ? '../uploads/'.$p['image'] : 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=100&auto=format&fit=crop'; ?>" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">
                    </td>
                    <td>
                        <strong style="color: var(--text-color);"><?php echo htmlspecialchars($p['name']); ?></strong><br>
                        <span style="font-size: 0.8rem; color: #999;">SKU: SCT-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                    <td>
                        <?php if($p['sale_price']): ?>
                            Rs. <?php echo number_format($p['sale_price'], 2); ?> <span style="text-decoration: line-through; color: #999; font-size: 0.8rem;">Rs. <?php echo number_format($p['price'], 2); ?></span>
                        <?php else: ?>
                            Rs. <?php echo number_format($p['price'], 2); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="color: <?php echo $p['stock'] > 10 ? '#27ae60' : '#e74c3c'; ?>"><?php echo $p['stock']; ?></span>
                    </td>
                    <td>
                        <?php if($p['status'] == 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <a href="product_edit.php?id=<?php echo $p['id']; ?>" style="color: #3498db; margin-right: 10px;" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="products.php?delete=<?php echo $p['id']; ?>" style="color: #e74c3c;" title="Delete" onclick="return confirm('Are you sure you want to delete this product? This cannot be undone.');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
