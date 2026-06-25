<?php
// register.php
require_once 'config/database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Already logged in?
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name             = trim($_POST['name']);
    $email            = trim($_POST['email']);
    $phone            = trim($_POST['phone'] ?? '');
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (empty($name) || empty($email)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $error = "This email address is already registered.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (:name, :email, :phone, :password, 'customer', 'active')");
                $insert->execute([
                    ':name'     => $name,
                    ':email'    => $email,
                    ':phone'    => $phone,
                    ':password' => $hashed
                ]);
                $success = "Account created successfully!";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — SCENTLEEN</title>
    <meta name="description" content="Create a Scentleen account to manage your orders and wishlist.">

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
            background: url('https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=1000&auto=format&fit=crop') center/cover no-repeat;
            opacity: 0.2;
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

        .perks {
            list-style: none;
            margin-top: 30px;
            text-align: left;
        }

        .perks li {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .perks li i { color: var(--gold); width: 18px; }

        /* Right Panel (Form) */
        .right-panel {
            flex: 1.2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 40px;
            background: #fff;
            overflow-y: auto;
        }

        .form-box {
            width: 100%;
            max-width: 480px;
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
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #555;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
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

        .btn-register {
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
            margin-top: 8px;
        }

        .btn-register:hover {
            background: var(--gold);
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #999;
            text-decoration: none;
            margin-bottom: 30px;
            transition: color 0.3s;
        }

        .back-home:hover { color: var(--dark); }

        .alert-error {
            background: #fdeaea;
            color: #c0392b;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #e74c3c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #eafaf1;
            color: #1e8449;
            padding: 20px 22px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            border-left: 4px solid #27ae60;
        }

        .alert-success h3 {
            font-family: var(--font-heading);
            margin-bottom: 8px;
            font-size: 1.2rem;
        }

        .alert-success a {
            color: var(--dark);
            font-weight: 600;
            text-decoration: underline;
        }

        .terms-note {
            font-size: 0.78rem;
            color: #aaa;
            text-align: center;
            margin-top: 12px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { padding: 40px 25px; }
            .form-grid { grid-template-columns: 1fr; }
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
            <h2>"Join the family."</h2>
            <p>Become a member and enjoy exclusive benefits with every purchase.</p>

            <ul class="perks">
                <li><i class="fas fa-check-circle"></i> Track your orders in real-time</li>
                <li><i class="fas fa-check-circle"></i> Save items to your wishlist</li>
                <li><i class="fas fa-check-circle"></i> Exclusive member-only deals</li>
                <li><i class="fas fa-check-circle"></i> Faster checkout experience</li>
                <li><i class="fas fa-check-circle"></i> Early access to new arrivals</li>
            </ul>
        </div>
    </div>

    <!-- Right Form Panel -->
    <div class="right-panel">
        <div class="form-box">

            <a href="home.php" class="back-home">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>

            <h1>Create Account</h1>
            <p class="subtitle">Join Scentleen — it's free and takes just a moment</p>

            <?php if ($error): ?>
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert-success">
                <h3>🎉 Welcome to Scentleen!</h3>
                <p>Your account has been created successfully. <a href="index.php">Click here to Sign In</a> and start exploring.</p>
            </div>
            <?php else: ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" placeholder="e.g. Ali Hassan" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="03xx-xxxxxxx" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="form-group full">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus" style="margin-right: 8px;"></i> Create My Account
                </button>

                <p class="terms-note">By registering, you agree to our Terms of Service and Privacy Policy.</p>
            </form>

            <?php endif; ?>

            <div class="login-link">
                Already have an account? <a href="index.php">Sign In here</a>
            </div>

        </div>
    </div>

</body>
</html>
