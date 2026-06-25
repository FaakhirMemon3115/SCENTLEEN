<?php
// admin/product_add.php
require_once '../config/database.php';
require_once 'includes/header.php';

$error = '';
$success = '';

// Fetch categories for dropdown
try {
    $categories = $pdo->query("SELECT id, name FROM categories WHERE status = 'active'")->fetchAll();
} catch(PDOException $e) {
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock = (int)$_POST['stock'];
    $status = $_POST['status'];
    $description = trim($_POST['description']);
    
    // Image Upload
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $filename; // Just save filename, frontend appends '../uploads/'
        }
    }

    if(empty($name) || empty($slug) || empty($price)) {
        $error = "Name, Slug, and Price are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $name, $slug, $description, $price, $sale_price, $stock, $image_url, $status]);
            $success = "Product added successfully!";
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate slug)
                $error = "A product with this slug already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.2rem; font-weight: 500;">Add New Product</h3>
        <a href="products.php" style="color: var(--text-color); text-decoration: none;"><i class="fas fa-arrow-left"></i> Back to Products</a>
    </div>

    <?php if($error): ?>
        <div style="background: #fdeaea; color: #e74c3c; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #e74c3c;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Product Name *</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">URL Slug *</label>
                <input type="text" name="slug" required placeholder="e.g. coco-mademoiselle" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Category</label>
                <select name="category_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; background: #fff;">
                    <?php if(empty($categories)): ?>
                        <option value="1">Default Category</option>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Regular Price (Rs.) *</label>
                <input type="number" step="0.01" name="price" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Sale Price (Rs.)</label>
                <input type="number" step="0.01" name="sale_price" placeholder="Optional" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Stock Quantity</label>
                <input type="number" name="stock" value="10" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Status</label>
                <select name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; background: #fff;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Product Image</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none; background: #fff;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Description</label>
            <textarea name="description" rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <button type="submit" style="background: var(--text-color); color: #fff; border: none; padding: 12px 30px; border-radius: 5px; cursor: pointer; transition: 0.3s;"><i class="fas fa-save"></i> Save Product</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
