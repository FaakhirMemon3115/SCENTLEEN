<?php
// admin/settings.php
require_once '../config/database.php';
require_once 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hero_title    = $_POST['hero_title']    ?? '';
    $hero_subtitle = $_POST['hero_subtitle'] ?? '';

    // Update text settings
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_title', :val)")->execute([':val' => $hero_title]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_subtitle', :val)")->execute([':val' => $hero_subtitle]);

    // Handle Hero Image Upload
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename    = time() . '_hero.' . pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['hero_image']['tmp_name'], $upload_dir . $filename)) {
            $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('hero_image', :val)")->execute([':val' => 'uploads/' . $filename]);
        }
    }

    // Handle About Image Upload
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename    = time() . '_about.' . pathinfo($_FILES['about_image']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['about_image']['tmp_name'], $upload_dir . $filename)) {
            $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('about_image', :val)")->execute([':val' => 'uploads/' . $filename]);
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

$current_title    = $settings['hero_title']    ?? '';
$current_subtitle = $settings['hero_subtitle'] ?? '';
$current_image    = $settings['hero_image']    ?? '';
$current_about    = $settings['about_image']   ?? '';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h3 style="font-size: 1.2rem; font-weight: 500;">Homepage Settings</h3>
    </div>

    <?php if($message): ?>
        <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <!-- Hero Section -->
        <h4 style="margin-bottom: 15px; color: #555; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            Hero Section
        </h4>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Title</label>
            <input type="text" name="hero_title" value="<?php echo htmlspecialchars($current_title); ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Subtitle</label>
            <input type="text" name="hero_subtitle" value="<?php echo htmlspecialchars($current_subtitle); ?>" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
        </div>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Hero Background Image</label>
            <?php if($current_image): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo htmlspecialchars($current_image); ?>" alt="Current Hero"
                        style="max-width: 300px; max-height: 150px; border-radius: 5px; border: 1px solid #ddd; object-fit: cover;">
                </div>
            <?php endif; ?>
            <input type="file" name="hero_image" accept="image/*"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            <small style="color: #999;">Leave blank to keep current image.</small>
        </div>

        <!-- About Section -->
        <h4 style="margin-bottom: 15px; color: #555; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: 10px;">
            About Section
        </h4>

        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">About Section Image (right side)</label>
            <?php if($current_about): ?>
                <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 15px;">
                    <img src="../<?php echo htmlspecialchars($current_about); ?>" alt="Current About Image"
                        style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd; object-fit: cover;">
                    <span style="color: #27ae60; font-size: 0.85rem;"><i class="fas fa-check-circle"></i> Image set</span>
                </div>
            <?php else: ?>
                <p style="font-size:0.85rem; color:#999; margin-bottom:10px;">No image set — the logo placeholder will be shown by default.</p>
            <?php endif; ?>
            <input type="file" name="about_image" accept="image/*"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; outline: none;">
            <small style="color: #999;">Upload a perfume/brand image to show on the homepage About section.</small>
        </div>

        <button type="submit"
            style="background: var(--text-color); color: #fff; border: none; padding: 13px 30px; border-radius: 5px; cursor: pointer; transition: 0.3s; font-size: 0.95rem;">
            <i class="fas fa-save" style="margin-right: 8px;"></i> Save Settings
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
