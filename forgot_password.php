<?php
// forgot_password.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';
$success = '';
$demo_link = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    // Check if email exists
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            
            // Delete any existing tokens for this email
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            
            // Insert new token
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
            $stmt->execute([$email, $token]);
            
            $success = "A password reset link has been sent to your email address.";
            
            // In a real production environment, you would use mail() or a library like PHPMailer here to email the $token to the user.
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $base_url  = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/SCENTLEEN/';
            $reset_link = $base_url . "reset_password.php?token=" . $token;
            
            $subject = "Scentleen - Password Reset";
            $message = "Click here to reset your password: " . $reset_link;
            $headers = "From: noreply@scentleen.com";
            
            @mail($email, $subject, $message, $headers);
            
        } else {
            // For security, do not reveal if an email exists or not. Show same success message.
            $success = "If that email exists in our system, a reset link has been sent.";
        }
    } catch (PDOException $e) {
        $error = "System error. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Scentleen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #FAF8F4;
            --dark: #2D2D2D;
            --gold: #C9A96E;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background-color: var(--bg);
            color: var(--dark);
            display: flex;
            min-height: 100vh;
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=1200&auto=format&fit=crop') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: #fff;
        }

        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #fff;
        }

        .form-box {
            width: 100%;
            max-width: 450px;
        }

        h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .subtitle {
            color: #666;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .form-group { margin-bottom: 25px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #444;
        }

        input[type="email"] {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
            background: #fcfcfc;
        }

        input[type="email"]:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(201, 169, 110, 0.1);
        }

        .btn-submit {
            width: 100%;
            background: var(--dark);
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .btn-submit:hover { background: #1a1a1a; transform: translateY(-2px); }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #999;
            text-decoration: none;
            margin-bottom: 35px;
            transition: color 0.3s;
        }

        .back-link:hover { color: var(--dark); }

        .alert-error {
            background: #fdeaea;
            color: #c0392b;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            border-left: 4px solid #e74c3c;
        }

        .alert-success {
            background: #eafaf1;
            color: #27ae60;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            border-left: 4px solid #27ae60;
        }

        .demo-box {
            background: #fff9e6;
            border: 1px solid #f1c40f;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 25px; }
        }
    </style>
</head>
<body>

    <div class="left-panel">
        <div style="max-width: 400px;">
            <a href="home.php" style="text-decoration:none;">
                <h1 style="color:#C9A96E; font-size:3rem; letter-spacing:4px; margin-bottom:20px;">SCENTLEEN</h1>
            </a>
            <p style="color:#ccc; line-height:1.8;">Securely recover your account and regain access to our exclusive luxury fragrance collection.</p>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-box">
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>

            <h1>Forgot Password</h1>
            <p class="subtitle">Enter your email address and we'll send you a link to reset your password.</p>

            <?php if ($error): ?>
                <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>

                    <button type="submit" class="btn-submit">Send Reset Link</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>
