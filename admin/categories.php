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
    $name   = trim($_POST['name']);
    $slug   = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    $status = $_POST['status'];
    $image_url = '';

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        // Handle image upload
        if (isset($_FILES['cat_image']) && $_FILES['cat_image']['error'] == 0) {
            $upload_dir = '../uploads/categories/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext      = pathinfo($_FILES['cat_image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_cat.' . $ext;
            if (move_uploaded_file($_FILES['cat_image']['tmp_name'], $upload_dir . $filename)) {
                $image_url = 'uploads/categories/' . $filename;
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, image, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $image_url, $status]);
            $message = "Category added successfully.";
        } catch(PDOException $e) {
            // Try without slug column in case it doesn't exist
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, image, status) VALUES (?, ?, ?)");
                $stmt->execute([$name, $image_url, $status]);
                $message = "Category added successfully.";
            } catch(PDOException $e2) {
                $error = "Error adding category: " . $e2->getMessage();
            }
        }
    }
}

// Handle Edit Category Image
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $edit_id     = (int)$_POST['edit_id'];
    $edit_name   = trim($_POST['edit_name']);
    $edit_status = $_POST['edit_status'];

    if (isset($_FILES['edit_cat_image']) && $_FILES['edit_cat_image']['error'] == 0) {
        $upload_dir = '../uploads/categories/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $ext      = pathinfo($_FILES['edit_cat_image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_cat.' . $ext;
        if (move_uploaded_file($_FILES['edit_cat_image']['tmp_name'], $upload_dir . $filename)) {
            $image_url = 'uploads/categories/' . $filename;
            $pdo->prepare("UPDATE categories SET name=?, image=?, status=? WHERE id=?")->execute([$edit_name, $image_url, $edit_status, $edit_id]);
        }
    } else {
        $pdo->prepare("UPDATE categories SET name=?, status=? WHERE id=?")->execute([$edit_name, $edit_status, $edit_id]);
    }
    $message = "Category updated successfully.";
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
    <small style="color:#999;">Categories appear as collection cards on the homepage.</small>
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
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="add_category" value="1">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Category Name *</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Collection Card Image</label>
                <input type="file" name="cat_image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
                <small style="color:#999;">This image shows on the homepage collection card.</small>
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
                    <th>Image</th>
                    <th>Name</th>
                    <th>Products</th>
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
                        <td>
                            <?php if($c['image']): ?>
                                <img src="../<?php echo htmlspecialchars($c['image']); ?>" alt="" style="width: 60px; height: 45px; object-fit: cover; border-radius: 5px; border: 1px solid #eee;">
                            <?php else: ?>
                                <span style="color:#ccc; font-size:0.8rem;">No image</span>
                            <?php endif; ?>
                        </td>
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
                            <button onclick="openEdit(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars(addslashes($c['name'])); ?>', '<?php echo $c['status']; ?>')"
                                style="background: none; border: none; color: #3498db; cursor: pointer; margin-right: 8px;" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="categories.php?delete=<?php echo $c['id']; ?>" style="color: #e74c3c;" title="Delete" onclick="return confirm('Delete this category?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:10px; padding:35px; width:450px; max-width:95%;">
        <h3 style="margin-bottom:20px; font-size:1.1rem;">Edit Category</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update_category" value="1">
            <input type="hidden" name="edit_id" id="edit_id">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:8px; font-size:0.9rem; color:#666;">Category Name *</label>
                <input type="text" name="edit_name" id="edit_name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; outline:none;">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:8px; font-size:0.9rem; color:#666;">Replace Collection Image</label>
                <input type="file" name="edit_cat_image" accept="image/*" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                <small style="color:#999;">Leave blank to keep current image.</small>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:8px; font-size:0.9rem; color:#666;">Status</label>
                <select name="edit_status" id="edit_status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-primary" style="flex:1;">Update</button>
                <button type="button" onclick="closeEdit()" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:5px; background:#fff; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, status) {
    document.getElementById('edit_id').value   = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_status').value = status;
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}
function closeEdit() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<?php require_once 'includes/footer.php'; ?>
