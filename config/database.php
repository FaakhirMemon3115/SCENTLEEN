<?php
// config/database.php

$host = 'localhost';
$db_name = 'SCENTLEEN_DB';
$username = 'SCENTLEEN_DB';
$password = 'Scentleen_DB1';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db_name};charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
    exit;
}
?>
