<?php
require_once 'config/database.php';

try {
    $email = 'admin@scentleen.com';
    $password = 'admin@access.com';
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $upd = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', status = 'active' WHERE id = ?");
        $upd->execute([$hashed, $user['id']]);
        echo "Admin updated successfully in active database.";
    } else {
        $ins = $pdo->prepare("INSERT INTO users (name, email, password, role, status) VALUES ('Admin', ?, ?, 'admin', 'active')");
        $ins->execute([$email, $hashed]);
        echo "Admin inserted successfully into active database.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
