<?php
// admin/categories.php
require_once '../config/database.php';
require_once 'includes/header.php';

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = :id")->execute([':id' => $del_id]);
        $message = "Category deleted successfully.";
    } catch(PDOException $e) {
        $error = "Cannot delete category because it contains products.";
    }
}

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $status = $_POST['status'];
    
    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, status) VALUES (?, ?)");
            $stmt->execute([$name, $status]);
            $message = "Category added successfully.";
        } catch(PDOException $e) {
            $error = "Error adding category.";
        }
    }
}

// Fetch categories
try {
    $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count FROM categories c ORDER BY c.id DESC");
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    $categories = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 1.5rem; font-weight: 500;">Category Management</h2>
</div>

<?php if($message): ?>
    <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div style="background: #fdeaea; color: #e74c3c; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #e74c3c;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- Add Category Form -->
    <div class="card">
        <h3 style="font-size: 1.1rem; margin-bottom: 20px;">Add New Category</h3>
        <form method="POST" action="">
            <input type="hidden" name="add_category" value="1">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Category Name *</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Status</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%;">Save Category</button>
        </form>
    </div>

    <!-- Categories List -->
    <div class="card" style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Products Count</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($categories)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px; color: #999;">No categories found.</td></tr>
                <?php else: ?>
                    <?php foreach($categories as $c): ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
                        <td><?php echo $c['product_count']; ?> items</td>
                        <td>
                            <?php if($c['status'] == 'active'): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="categories.php?delete=<?php echo $c['id']; ?>" style="color: #e74c3c;" title="Delete" onclick="return confirm('Delete this category?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
