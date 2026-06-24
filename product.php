<?php
// product.php
require_once 'includes/header.php';
require_once 'config/database.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    header("Location: shop.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = :slug AND p.status = 'active'");
    $stmt->execute([':slug' => $slug]);
    $product = $stmt->fetch();

    if (!$product) {
        header("Location: shop.php");
        exit;
    }
} catch(PDOException $e) {
    header("Location: shop.php");
    exit;
}

$img = $product['image'] ? 'uploads/'.$product['image'] : 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=800&auto=format&fit=crop';
?>

<main style="padding-top: 100px; background-color: var(--bg-color); min-height: 100vh;">
    
    <!-- Breadcrumb -->
    <div style="background: #fff; border-bottom: 1px solid #eee; padding: 15px 0; font-size: 0.9rem;">
        <div class="container">
            <a href="index.php" style="color: #999;">Home</a> <span style="margin: 0 10px; color: #ccc;">/</span> 
            <a href="shop.php" style="color: #999;">Shop</a> <span style="margin: 0 10px; color: #ccc;">/</span> 
            <a href="shop.php?category=<?php echo urlencode($product['category_name']); ?>" style="color: #999;"><?php echo htmlspecialchars($product['category_name']); ?></a> <span style="margin: 0 10px; color: #ccc;">/</span> 
            <span style="color: var(--text-color);"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>

    <div class="container py-5" style="padding: 60px 20px;">
        <div style="display: flex; flex-wrap: wrap; gap: 60px;">
            
            <!-- Product Images -->
            <div style="flex: 1; min-width: 300px;" data-aos="fade-right">
                <div style="border-radius: 15px; overflow: hidden; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); height: 600px;">
                    <img id="mainImage" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                
                <!-- Gallery Thumbnails (Placeholder for JSON parsing) -->
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <img src="<?php echo htmlspecialchars($img); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 2px solid var(--gold-color);" onclick="changeImage(this.src)">
                    <!-- Add more thumbnails if $product['gallery'] exists -->
                </div>
            </div>

            <!-- Product Details -->
            <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;" data-aos="fade-left">
                <p style="text-transform: uppercase; letter-spacing: 2px; color: var(--gold-color); font-size: 0.9rem; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </p>
                <h1 style="font-size: 3rem; margin-bottom: 20px; line-height: 1.2;"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                    <div class="stars" style="color: var(--gold-color); font-size: 1.2rem;">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <span style="color: #999; font-size: 0.9rem;">(12 Customer Reviews)</span>
                </div>

                <div class="price" style="font-size: 2rem; color: var(--text-color); font-weight: 600; margin-bottom: 30px; font-family: var(--font-heading);">
                    <?php if($product['sale_price']): ?>
                        <span style="text-decoration: line-through; color: #ccc; font-size: 1.3rem; margin-right: 15px; font-weight: 400;">$<?php echo number_format($product['price'], 2); ?></span>
                        $<?php echo number_format($product['sale_price'], 2); ?>
                    <?php else: ?>
                        $<?php echo number_format($product['price'], 2); ?>
                    <?php endif; ?>
                </div>

                <div style="color: #666; line-height: 1.8; margin-bottom: 40px;">
                    <?php echo nl2br(htmlspecialchars($product['description'] ? $product['description'] : 'A premium fragrance that embodies elegance and sophistication. Expertly crafted for the modern individual who appreciates the finer things in life.')); ?>
                </div>

                <hr style="border: 0; border-top: 1px solid #ddd; margin-bottom: 40px;">

                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 40px;">
                    <div style="display: flex; border: 1px solid #ccc; border-radius: 30px; overflow: hidden; width: 140px; height: 50px;">
                        <button style="flex: 1; border: none; background: #fff; cursor: pointer; font-size: 1.2rem;" onclick="updateQty(-1)">-</button>
                        <input type="number" id="qty" value="1" min="1" max="<?php echo $product['stock'] > 0 ? $product['stock'] : 1; ?>" style="flex: 1; border: none; text-align: center; font-family: var(--font-body); font-size: 1.1rem; outline: none; -moz-appearance: textfield;">
                        <button style="flex: 1; border: none; background: #fff; cursor: pointer; font-size: 1.2rem;" onclick="updateQty(1)">+</button>
                    </div>
                    
                    <button class="btn-primary" style="flex: 1; border-radius: 30px; height: 50px; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px;" onclick="addToCart(<?php echo $product['id']; ?>)">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                    
                    <button class="btn-outline wishlist-btn" style="width: 50px; height: 50px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border-color: #ccc;" onclick="toggleWishlist(this, <?php echo $product['id']; ?>)">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>

                <div style="background: #fff; padding: 25px; border-radius: 10px; border: 1px solid #eee;">
                    <p style="margin-bottom: 10px; font-size: 0.9rem;"><strong>Availability:</strong> <span style="color: <?php echo $product['stock'] > 0 ? '#27ae60' : '#e74c3c'; ?>"><?php echo $product['stock'] > 0 ? $product['stock'].' in stock' : 'Out of stock'; ?></span></p>
                    <p style="margin-bottom: 10px; font-size: 0.9rem;"><strong>SKU:</strong> SCT-<?php echo str_pad($product['id'], 5, '0', STR_PAD_LEFT); ?></p>
                    <p style="font-size: 0.9rem; display: flex; gap: 15px; align-items: center;">
                        <strong>Share:</strong> 
                        <a href="#" style="color: #666;"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" style="color: #666;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: #666;"><i class="fab fa-pinterest-p"></i></a>
                    </p>
                </div>

            </div>
        </div>
        
        <!-- Description / Review Tabs -->
        <div style="margin-top: 100px;">
            <div style="display: flex; gap: 40px; border-bottom: 1px solid #ddd; margin-bottom: 40px;">
                <h3 style="padding-bottom: 15px; border-bottom: 2px solid var(--text-color); cursor: pointer;">Description</h3>
                <h3 style="padding-bottom: 15px; color: #999; font-weight: 400; cursor: pointer;">Reviews (12)</h3>
            </div>
            <div style="color: #666; line-height: 1.8; max-width: 800px;">
                <p>Immerse yourself in the captivating aura of <?php echo htmlspecialchars($product['name']); ?>. This exquisite fragrance opens with fresh top notes that gradually give way to a rich, complex heart, finally settling into a warm and enduring base.</p>
                <p style="margin-top: 20px;">Perfect for any occasion, it leaves a memorable trail that speaks volumes of your refined taste.</p>
            </div>
        </div>

    </div>
</main>

<style>
.wishlist-btn:hover { border-color: #e74c3c; color: #e74c3c; background: transparent; }
.wishlist-btn.active { background: #e74c3c; border-color: #e74c3c; color: #fff; animation: heartbeat 0.5s ease; }
input[type="number"]::-webkit-inner-spin-button, 
input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
</style>

<script>
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}

function updateQty(change) {
    const qtyInput = document.getElementById('qty');
    let current = parseInt(qtyInput.value);
    const max = parseInt(qtyInput.getAttribute('max'));
    
    let newVal = current + change;
    if(newVal >= 1 && newVal <= max) {
        qtyInput.value = newVal;
    }
}

function addToCart(productId) {
    const qty = document.getElementById('qty').value;
    const btn = event.currentTarget;
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> Added to Cart';
        btn.style.backgroundColor = 'var(--gold-color)';
        btn.style.borderColor = 'var(--gold-color)';
        
        setTimeout(() => {
            btn.innerHTML = originalContent;
            btn.style.backgroundColor = 'var(--text-color)';
            btn.style.borderColor = 'var(--text-color)';
        }, 2000);
    }, 800);
}

function toggleWishlist(btn, productId) {
    btn.classList.toggle('active');
    if(btn.classList.contains('active')) {
        btn.innerHTML = '<i class="fas fa-heart"></i>';
    } else {
        btn.innerHTML = '<i class="far fa-heart"></i>';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
