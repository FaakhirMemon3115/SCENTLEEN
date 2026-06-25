<?php
// index.php (Login Page)
require_once 'config/database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Already logged in? Redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: home.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND status = 'active'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: home.php");
            }
            exit;
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    } catch (PDOException $e) {
        $error = "Database error. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SCENTLEEN</title>
    <meta name="description" content="Sign in to your Scentleen account to manage orders and wishlist.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #C9A96E;
            --dark: #2D2D2D;
            --bg: #FAF8F4;
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            color: var(--dark);
        }

        /* Left Panel */
        .left-panel {
            flex: 1;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url('https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=1000&auto=format&fit=crop') center/cover no-repeat;
            opacity: 0.25;
        }

        .left-panel-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: #fff;
        }

        .brand-logo {
            border: 2px solid var(--gold);
            padding: 25px 35px;
            display: inline-block;
            border-radius: 8px;
            margin-bottom: 40px;
            text-decoration: none;
        }

        .brand-logo span.symbol {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            line-height: 1;
            display: block;
            color: var(--gold);
        }

        .brand-logo span.name {
            font-family: var(--font-heading);
            font-size: 1.6rem;
            letter-spacing: 6px;
            color: #fff;
        }

        .left-panel-content h2 {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 400;
            font-style: italic;
            color: var(--gold);
        }

        .left-panel-content p {
            color: #ccc;
            font-size: 0.95rem;
            line-height: 1.8;
            max-width: 320px;
        }

        .left-panel-content .tagline {
            margin-top: 40px;
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #888;
        }

        /* Right Panel (Form) */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            background: #fff;
        }

        .form-box {
            width: 100%;
            max-width: 420px;
        }

        .form-box h1 {
            font-family: var(--font-heading);
            font-size: 2.2rem;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .form-box .subtitle {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e5e5e5;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 0.95rem;
            color: var(--dark);
            background: #fafafa;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            border-color: var(--gold);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.12);
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #666;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            accent-color: var(--dark);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.85rem;
            color: var(--gold);
            text-decoration: none;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--dark);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-login:hover {
            background: var(--gold);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #ccc;
            font-size: 0.85rem;
        }

        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #eee;
        }

        .divider::before { left: 0; }
        .divider::after  { right: 0; }

        .register-link {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }

        .register-link a {
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #999;
            text-decoration: none;
            margin-bottom: 35px;
            transition: color 0.3s;
        }

        .back-home:hover { color: var(--dark); }

        .alert-error {
            background: #fdeaea;
            color: #c0392b;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            border-left: 4px solid #e74c3c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 25px; }
        }
    </style>
</head>
<body>

    <!-- Left Branding Panel -->
    <div class="left-panel">
        <div class="left-panel-content">
            <a href="home.php" class="brand-logo">
                <span class="symbol">$</span>
                <span class="name">SCENTLEEN</span>
            </a>
            <h2>"Wear your story."</h2>
            <p>Discover an exclusive collection of premium perfumes, authentic Arabian Oud, and imported masterpieces crafted for the modern connoisseur.</p>
            <p class="tagline">Paris · Est. 1980</p>
        </div>
    </div>

    <!-- Right Form Panel -->
    <div class="right-panel">
        <div class="form-box">

            <a href="home.php" class="back-home">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>

            <h1>Welcome Back</h1>
            <p class="subtitle">Sign in to your Scentleen account</p>

            <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <div class="form-row">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i> Sign In
                </button>
            </form>

            <div class="divider">or</div>

            <div class="register-link">
                Don't have an account? <a href="register.php">Create one now</a>
            </div>

        </div>
    </div>

</body>
</html>