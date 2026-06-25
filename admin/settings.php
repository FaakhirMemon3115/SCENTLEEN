<?php
// admin/settings.php
require_once '../config/database.php';
require_once 'includes/header.php';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hero_title = $_POST['hero_title'] ?? '';
    $hero_subtitle = $_POST['hero_subtitle'] ?? '';
    
    // Update text settings
    $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_title', :val)");
    $stmt->execute([':val' => $hero_title]);
    
    $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_subtitle', :val)");
    $stmt->execute([':val' => $hero_subtitle]);

    // Handle Image Upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['hero_image']['name']);
        $target_file = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $target_file)) {
            // Only save the relative URL part for src attribute
            $image_url = 'uploads/' . $filename;
            $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_image', :val)");
            $stmt->execute([':val' => $image_url]);
        }
    }
    
    $message = "Settings updated successfully!";
}

// Fetch current settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch(PDOException $e) {}

$current_title = $settings['hero_title'] ?? '';
$current_subtitle = $settings['hero_subtitle'] ?? '';
$current_image = $settings['hero_image'] ?? '';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="font-size: 1.2rem; font-weight: 500;">Homepage Settings</h3>
    </div>
    
    <?php if($message): ?>
        <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Title</label>
            <input type="text" name="hero_title" value="<?php echo htmlspecialchars($current_title); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Subtitle</label>
            <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($current_subtitle); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Background Image</label>
            <?php if($current_image): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo htmlspecialchars($current_image); ?>" alt="Current Hero" style="max-width: 300px; max-height: 150px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover;">
                </div>
            <?php endif; ?>
            <input type="file" name="hero_image" accept="image/*" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            <small style="color: #999;">Leave blank to keep current image.</small>
        </div>

        <button type="submit" style="background: var(--text-color); color: #fff; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; transition: 0.3s;">Save Settings</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
