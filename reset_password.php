<?php
// reset_password.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';
$success = '';
$email = '';
$valid_token = false;

// Validate token from URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        // Check if token exists and is valid (e.g. less than 24 hours old)
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = :token AND created_at >= NOW() - INTERVAL 1 DAY");
        $stmt->execute([':token' => $token]);
        $reset_record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reset_record) {
            $valid_token = true;
            $email = $reset_record['email'];
        } else {
            $error = "This password reset link is invalid or has expired.";
        }
    } catch (PDOException $e) {
        $error = "System error validating token.";
    }
} else {
    $error = "No reset token provided.";
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            // Update the user's password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = :pass WHERE email = :email");
            $stmt->execute([':pass' => $hashed_password, ':email' => $email]);
            
            // Delete the token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            $success = "Your password has been successfully reset. You can now login.";
            $valid_token = false; // Hide form
        } catch (PDOException $e) {
            $error = "An error occurred while resetting your password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Scentleen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reuse styles from forgot_password.php */
        :root {
            --bg: #FAF8F4;
            --dark: #2D2D2D;
            --gold: #C9A96E;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-body); background-color: var(--bg); color: var(--dark); display: flex; min-height: 100vh; }
        .left-panel { flex: 1; background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=1200&auto=format&fit=crop') center/cover; display: flex; flex-direction: column; justify-content: center; padding: 60px; color: #fff; }
        .right-panel { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; background: #fff; }
        .form-box { width: 100%; max-width: 450px; }
        h1 { font-family: var(--font-heading); font-size: 2.5rem; margin-bottom: 10px; color: var(--dark); }
        .subtitle { color: #666; margin-bottom: 35px; line-height: 1.6; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-size: 0.9rem; font-weight: 500; color: #444; }
        input[type="password"] { width: 100%; padding: 14px 18px; border: 1px solid #ddd; border-radius: 8px; font-family: var(--font-body); font-size: 1rem; outline: none; transition: all 0.3s; background: #fcfcfc; }
        input[type="password"]:focus { border-color: var(--gold); background: #fff; box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1); }
        .btn-submit { width: 100%; background: var(--dark); color: #fff; border: none; padding: 16px; border-radius: 8px; font-family: var(--font-body); font-size: 1rem; font-weight: 500; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .btn-submit:hover { background: #1a1a1a; transform: translateY(-2px); }
        .alert-error { background: #fdeaea; color: #c0392b; padding: 14px 18px; border-radius: 8px; margin-bottom: 22px; font-size: 0.9rem; border-left: 4px solid #e74c3c; }
        .alert-success { background: #eafaf1; color: #27ae60; padding: 14px 18px; border-radius: 8px; margin-bottom: 22px; font-size: 0.9rem; border-left: 4px solid #27ae60; }
        .back-link { display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; color: #999; text-decoration: none; margin-bottom: 35px; transition: color 0.3s; }
        .back-link:hover { color: var(--dark); }
        @media (max-width: 768px) { .left-panel { display: none; } .right-panel { padding: 40px 25px; } }
    </style>
</head>
<body>

    <div class="left-panel">
        <div style="max-width: 400px;">
            <a href="home.php" style="text-decoration:none;">
                <h1 style="color:#C9A96E; font-size:3rem; letter-spacing:4px; margin-bottom:20px;">SCENTLEEN</h1>
            </a>
            <p style="color:#ccc; line-height:1.8;">Set a new password to regain access to your Scentleen account.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-box">

            <h1>Create New Password</h1>
            <p class="subtitle">Please enter your new password below.</p>

            <?php if ($error): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
                <?php if (!$valid_token): ?>
                    <a href="forgot_password.php" class="back-link" style="margin-top:20px;"><i class="fas fa-redo"></i> Request New Link</a>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
                <a href="index.php" class="btn-submit" style="text-decoration:none; text-align:center;">Return to Login</a>
            <?php elseif ($valid_token): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                    </div>

                    <button type="submit" class="btn-submit">Reset Password</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
