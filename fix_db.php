<?php
require_once 'config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )");
    echo "Table 'settings' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
