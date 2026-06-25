<?php
// ajax_toggle_wishlist.php
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$is_active = false;
if (in_array($product_id, $_SESSION['wishlist'])) {
    // Remove from wishlist
    $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$product_id]);
} else {
    // Add to wishlist
    $_SESSION['wishlist'][] = $product_id;
    $is_active = true;
}

// Re-index array
$_SESSION['wishlist'] = array_values($_SESSION['wishlist']);

echo json_encode(['success' => true, 'is_active' => $is_active, 'wishlist_count' => count($_SESSION['wishlist'])]);
