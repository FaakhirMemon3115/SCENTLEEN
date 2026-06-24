<?php
// wishlist.php
require_once 'includes/header.php';

// MVP Placeholder data
$wishlist_items = [
    [
        'id' => 3,
        'name' => 'Golden Elixir',
        'price' => 280.00,
        'category_name' => 'Luxury Collection',
        'image' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=400&auto=format&fit=crop'
    ]
];
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    <div class="container py-5" style="padding: 60px 20px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center;">My Wishlist</h1>

        <?php if(empty($wishlist_items)): ?>
            <div text-align: center; padding: 50px 0;">
                <i class="far fa-heart" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3>Your wishlist is empty</h3>
                <p style="color: #666;">Explore our collections and save your favorite items.</p>
                <a href="shop.php" class="btn-primary" style="margin-top: 20px;">Explore Fragrances</a>
            </div>
        <?php else: ?>
            <div class="shop-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                <?php foreach($wishlist_items as $product): ?>
                    <div class="shop-card" data-aos="fade-up" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: relative;">
                        <div class="wishlist-btn active" onclick="removeFromWishlist(this, <?php echo $product['id']; ?>)" style="position: absolute; top: 15px; right: 15px; z-index: 10; cursor: pointer; color: #e74c3c; font-size: 1.2rem;">
                            <i class="fas fa-times"></i>
                        </div>
                        
                        <a href="product.php?slug=<?php echo urlencode(strtolower(str_replace(' ', '-', $product['name']))); ?>" style="display: block; overflow: hidden; border-radius: 5px; margin-bottom: 15px; height: 250px;">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" class="hover-zoom">
                        </a>
                        
                        <h4 style="font-size: 1.2rem; margin-bottom: 5px;"><a href="#"><?php echo htmlspecialchars($product['name']); ?></a></h4>
                        <p style="font-size: 0.8rem; color: #999; margin-bottom: 10px; text-transform: uppercase;"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        
                        <div class="price" style="color: var(--gold-color); font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                            $<?php echo number_format($product['price'], 2); ?>
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
function removeFromWishlist(btn, id) {
    const card = btn.closest('.shop-card');
    card.style.opacity = '0';
    card.style.transform = 'scale(0.8)';
    setTimeout(() => {
        card.remove();
        // Check if grid is empty and show empty state (MVP)
        if(document.querySelectorAll('.shop-card').length === 0) {
            location.reload(); 
        }
    }, 300);
}

function addToCart(productId) {
    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Added';
        btn.style.backgroundColor = 'var(--text-color)';
        btn.style.color = '#fff';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.backgroundColor = 'transparent';
            btn.style.color = 'var(--text-color)';
        }, 2000);
    }, 800);
}
</script>

<?php require_once 'includes/footer.php'; ?>
