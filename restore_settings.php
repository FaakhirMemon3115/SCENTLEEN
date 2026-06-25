<?php
require_once 'config/database.php';

try {
    $defaults = [
        'hero_title' => 'SCENTLEEN',
        'hero_subtitle' => 'The Art of Luxury Fragrance',
        'hero_image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=2000&auto=format&fit=crop'
    ];

    foreach ($defaults as $key => $val) {
        $stmt = $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
    }
    echo "Settings restored.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
