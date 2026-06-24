<?php
// register.php
require_once 'includes/header.php';
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = "Email address is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (:name, :email, :phone, :password, 'customer')");
                $insert->execute([
                    ':name' => $name,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':password' => $hashed_password
                ]);
                $success = "Registration successful! You can now login.";
            }
        } catch(PDOException $e) {
            $error = "Database error. Please try again.";
        }
    }
}
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    
    <div style="background: #fff; padding: 50px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); width: 100%; max-width: 600px; margin: 40px 20px;">
        
        <div class="text-center mb-4">
            <h2 style="font-size: 1.8rem; margin-bottom: 10px;">Create an Account</h2>
            <p style="color: #666; font-size: 0.9rem;">Join Scentleen to manage your orders and wishlist</p>
        </div>

        <?php if($error): ?>
            <div style="background: #fdeaea; color: #e74c3c; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #e74c3c;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div style="background: #eafaf1; color: #27ae60; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #27ae60;">
                <?php echo $success; ?> <a href="index.php" style="font-weight: bold; text-decoration: underline;">Login here</a>.
            </div>
        <?php else: ?>

        <form method="POST" action="">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Full Name *</label>
                    <input type="text" name="name" required style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Phone Number (Optional)</label>
                    <input type="tel" name="phone" style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Email Address *</label>
                <input type="email" name="email" required style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Password *</label>
                    <input type="password" name="password" required style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Confirm Password *</label>
                    <input type="password" name="confirm_password" required style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body);">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 15px;">Register Now</button>
        </form>

        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px; font-size: 0.9rem; color: #666;">
            Already have an account? <a href="index.php" style="color: var(--gold-color); font-weight: 600;">Sign In</a>
        </div>
    </div>

</main>

<?php require_once 'includes/footer.php'; ?>
