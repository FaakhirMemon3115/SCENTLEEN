<?php
// wishlist.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
require_once 'config/database.php';

$wishlist_items = [];

if (!empty($_SESSION['wishlist'])) {
    $ids = array_values($_SESSION['wishlist']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id IN ($placeholders) AND p.status = 'active'");
        $stmt->execute($ids);
        $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {}
}
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center;">My Wishlist</h1>

        <?php if(empty($wishlist_items)): ?>
            <div style="text-align: center; padding: 50px 0;">
                <i class="far fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 20px; display: block;"></i>
                <h3>Your wishlist is empty</h3>
                <p style="color: #666; margin-top: 10px;">Explore our collections and save your favourite items.</p>
                <a href="shop.php" class="btn-primary" style="margin-top: 20px; display: inline-block;">Explore Fragrances</a>
            </div>
        <?php else: ?>
            <div class="shop-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                <?php foreach($wishlist_items as $product): ?>
                    <div class="shop-card" id="wcard-<?php echo $product['id']; ?>" data-aos="fade-up" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: relative;">
                        <!-- Remove from Wishlist Button -->
                        <button onclick="removeFromWishlist(this, <?php echo $product['id']; ?>)" style="position: absolute; top: 15px; right: 15px; z-index: 10; cursor: pointer; color: #e74c3c; font-size: 1.2rem; background: none; border: none;" title="Remove from Wishlist">
                            <i class="fas fa-heart"></i>
                        </button>
                        
                        <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" style="display: block; overflow: hidden; border-radius: 5px; margin-bottom: 15px; height: 250px;">
                            <?php $img = $product['image'] ? 'uploads/'.$product['image'] : 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=400&auto=format&fit=crop'; ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" class="hover-zoom">
                        </a>
                        
                        <h4 style="font-size: 1.2rem; margin-bottom: 5px;"><a href="product.php?slug=<?php echo urlencode($product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h4>
                        <p style="font-size: 0.8rem; color: #999; margin-bottom: 10px; text-transform: uppercase;"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        
                        <div class="price" style="color: var(--gold-color); font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                            <?php if($product['sale_price']): ?>
                                <span style="text-decoration: line-through; color: #999; font-size: 0.9rem; margin-right: 5px;">Rs. <?php echo number_format($product['price'], 2); ?></span>
                                Rs. <?php echo number_format($product['sale_price'], 2); ?>
                            <?php else: ?>
                                Rs. <?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </div>
                        
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn-outline" style="width: 100%; font-size: 0.8rem; padding: 10px;">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.shop-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
.shop-card:hover .hover-zoom { transform: scale(1.1); }
</style>

<script>
function removeFromWishlist(btn, productId) {
    const card = document.getElementById('wcard-' + productId);
    
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('ajax_toggle_wishlist.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(data.success && !data.is_active) {
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            setTimeout(() => {
                card.remove();
                if(document.querySelectorAll('.shop-card').length === 0) {
                    location.reload();
                }
            }, 300);
        }
    })
    .catch(err => console.error(err));
}
</script>

<?php require_once 'includes/footer.php'; ?>
