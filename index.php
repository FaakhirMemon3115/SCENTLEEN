<?php
// index.php (Login Page as Landing)
require_once 'config/database.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: home.php");
    }
    exit;
}

require_once 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND status = 'active'");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: home.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error. Please try again later.";
    }
}
?>

<main
    style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div
        style="background: #fff; padding: 50px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); width: 100%; max-width: 500px; margin: 40px 20px;">

        <div class="text-center mb-4">
            <div
                style="display: inline-block; padding: 20px; border: 2px solid var(--gold-color); border-radius: 10px; margin-bottom: 30px;">
                <span
                    style="font-size: 3rem; line-height: 0.8; display: block; font-family: var(--font-heading); margin-bottom: 5px; color: var(--text-color);">$</span>
                <span
                    style="font-size: 1.5rem; letter-spacing: 3px; font-family: var(--font-heading); color: var(--text-color);">SCENTLEEN</span>
            </div>
            <h2 style="font-size: 1.8rem; margin-bottom: 10px;">Welcome Back</h2>
            <p style="color: #666; font-size: 0.9rem;">Please sign in to your account</p>
        </div>

        <?php if ($error): ?>
            <div
                style="background: #fdeaea; color: #e74c3c; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; border-left: 4px solid #e74c3c;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #666;">Email Address</label>
                <input type="email" name="email" required
                    style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); transition: border-color 0.3s;"
                    onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
            </div>

            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <label style="font-size: 0.9rem; color: #666;">Password</label>
                    <a href="#" style="font-size: 0.8rem; color: var(--gold-color);">Forgot Password?</a>
                </div>
                <input type="password" name="password" required
                    style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 5px; outline: none; font-family: var(--font-body); transition: border-color 0.3s;"
                    onfocus="this.style.borderColor='var(--gold-color)'" onblur="this.style.borderColor='#ddd'">
            </div>

            <label
                style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px; font-size: 0.9rem; color: #666; cursor: pointer;">
                <input type="checkbox" style="accent-color: var(--text-color); width: 16px; height: 16px;"> Remember me
            </label>

            <button type="submit" class="btn-primary" style="width: 100%; font-size: 1.1rem; padding: 15px;">Sign
                In</button>
        </form>

        <div style="text-align: center; margin-top: 30px; font-size: 0.9rem; color: #666;">
            Don't have an account? <a href="register.php" style="color: var(--gold-color); font-weight: 600;">Register
                Here</a>
        </div>
    </div>

</main>

<?php require_once 'includes/footer.php'; ?>