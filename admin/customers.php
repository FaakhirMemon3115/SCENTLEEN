<?php
// admin/customers.php
require_once '../config/database.php';
require_once 'includes/header.php';

$message = '';

// Handle Ban/Unban toggle
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $target_id = (int)$_GET['id'];
    $new_status = $_GET['toggle_status'] === 'active' ? 'active' : 'inactive';
    
    // Prevent banning the currently logged-in admin
    if ($target_id !== $_SESSION['user_id']) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $new_status, ':id' => $target_id]);
            $message = "User status updated successfully.";
        } catch (PDOException $e) {
            $message = "Error updating user status.";
        }
    } else {
        $message = "You cannot ban your own active session.";
    }
}

// Fetch all users
try {
    $stmt = $pdo->query("SELECT id, name, email, password, role, status, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 1.5rem; font-weight: 500;">Customer Management</h2>
</div>

<?php if($message): ?>
    <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #27ae60;">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card" style="overflow-x: auto;">
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email / Password Hash</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($users)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 30px; color: #999;">No users found.</td></tr>
            <?php else: ?>
                <?php foreach($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['id']; ?></td>
                    <td><strong style="color: var(--text-color);"><?php echo htmlspecialchars($u['name']); ?></strong></td>
                    <td>
                        <span style="color: #333;"><?php echo htmlspecialchars($u['email']); ?></span><br>
                        <span style="font-size: 0.75rem; color: #999; font-family: monospace;">[Encrypted] <?php echo substr($u['password'], 0, 15); ?>...</span>
                    </td>
                    <td>
                        <?php if($u['role'] == 'admin'): ?>
                            <span class="badge badge-warning" style="background:#fef5e7; color:#f39c12;"><i class="fas fa-shield-alt"></i> Admin</span>
                        <?php else: ?>
                            <span class="badge" style="background:#eef2f5; color:#555;">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 0.85rem; color: #666;"><?php echo date('M d, Y g:i A', strtotime($u['created_at'])); ?></td>
                    <td>
                        <?php if($u['status'] == 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Banned</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <?php if($u['id'] !== $_SESSION['user_id']): ?>
                            <?php if($u['status'] == 'active'): ?>
                                <a href="customers.php?toggle_status=inactive&id=<?php echo $u['id']; ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; background: #e74c3c; border-color: #e74c3c;">Ban User</a>
                            <?php else: ?>
                                <a href="customers.php?toggle_status=active&id=<?php echo $u['id']; ?>" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; background: #27ae60; border-color: #27ae60;">Unban</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: #999;">Current Session</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
