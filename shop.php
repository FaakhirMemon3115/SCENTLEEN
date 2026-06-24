<?php
// shop.php
require_once 'includes/header.php';
require_once 'config/database.php';

// Pagination setup
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter setup
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Base query
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";
$count_query = "SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'active'";
$params = [];

if ($category_filter) {
    $query .= " AND c.name = :category";
    $count_query .= " AND c.name = :category";
    $params[':category'] = $category_filter;
}

if ($search_query) {
    $query .= " AND p.name LIKE :search";
    $count_query .= " AND p.name LIKE :search";
    $params[':search'] = '%' . $search_query . '%';
}

$query .= " ORDER BY p.id DESC LIMIT $limit OFFSET $offset";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    $stmt_count = $pdo->prepare($count_query);
    $stmt_count->execute($params);
    $total_rows = $stmt_count->fetch()['total'];
    $total_pages = ceil($total_rows / $limit);
} catch(PDOException $e) {
    $products = [];
    $total_pages = 0;
    // Handle error quietly for the user, maybe log it
}

// Fetch categories for sidebar
try {
    $cat_stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active'");
    $categories = $cat_stmt->fetchAll();
} catch(PDOException $e) {
    $categories = [];
}
?>

<main style="padding-top: 100px; background-color: var(--bg-color);">
    
    <div class="shop-header text-center" style="padding: 60px 0; background: url('https://images.unsplash.com/photo-1615529182904-14819c35db37?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat; position: relative;">
        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.6);"></div>
        <div class="container" style="position: relative; z-index: 1; color: #fff;">
            <h1 class="fade-up" style="font-size: 3rem; margin-bottom: 10px; letter-spacing: 2px;">
                <?php echo $category_filter ? ucfirst($category_filter) . " Collection" : "All Fragrances"; ?>
            </h1>
            <p class="fade-up" style="color: #ddd;">Explore our exclusive range of luxurious scents.</p>
        </div>
    </div>

    <div class="container" style="padding: 60px 20px;">
        <div style="display: flex; gap: 40px; flex-wrap: wrap;">
            
            <!-- Sidebar Filters -->
            <aside class="sidebar" style="flex: 1; min-width: 250px; max-width: 300px;">
                <div class="filter-group mb-4">
                    <h4 style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Categories</h4>
                    <ul style="list-style: none; display: flex; flex-direction: column; gap: 10px;">
                        <li><a href="shop.php" style="color: <?php echo empty($category_filter) ? 'var(--gold-color)' : 'var(--text-color)'; ?>">All Collections</a></li>
                        <?php foreach($categories as $cat): ?>
                        <li>
                            <a href="shop.php?category=<?php echo urlencode($cat['name']); ?>" style="color: <?php echo ($category_filter === $cat['name']) ? 'var(--gold-color)' : 'var(--text-color)'; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="filter-group mb-4">
                    <h4 style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Price Range</h4>
                    <input type="range" min="0" max="1000" value="500" style="width: 100%; accent-color: var(--text-color);">
                    <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-top: 5px;">
                        <span>$0</span>
                        <span>$1000+</span>
                    </div>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="products-area" style="flex: 3;">
                
                <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 1px solid #ddd; padding-bottom: 15px;">
                    <p style="font-size: 0.9rem; color: #666;">Showing <?php echo count($products); ?> of <?php echo $total_rows; ?> results</p>
                    <select style="padding: 8px; border: 1px solid #ddd; font-family: var(--font-body); background: transparent;">
                        <option>Sort by Latest</option>
                        <option>Sort by Price: Low to High</option>
                        <option>Sort by Price: High to Low</option>
                    </select>
                </div>

                <div class="shop-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                    <?php if (empty($products)): ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 50px 0;">
                            <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 20px;"></i>
                            <h3>No products found</h3>
                            <p style="color: #666;">Try adjusting your filters or <a href="shop.php" style="color: var(--gold-color);">clear them</a>.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                            <div class="shop-card" data-aos="fade-up" style="background: #fff; padding: 20px; border-radius: 10px; text-align: center; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: relative;">
                                <div class="wishlist-btn" onclick="toggleWishlist(<?php echo $product['id']; ?>)" style="position: absolute; top: 15px; right: 15px; z-index: 10; cursor: pointer; color: #ccc; font-size: 1.2rem; transition: color 0.3s;">
                                    <i class="fas fa-heart"></i>
                                </div>
                                
                                <a href="product.php?slug=<?php echo urlencode($product['slug']); ?>" style="display: block; overflow: hidden; border-radius: 5px; margin-bottom: 15px; height: 250px;">
                                    <?php $img = $product['image'] ? 'uploads/'.$product['image'] : 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=400&auto=format&fit=crop'; ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;" class="hover-zoom">
                                </a>
                                
                                <h4 style="font-size: 1.2rem; margin-bottom: 5px;"><a href="product.php?slug=<?php echo urlencode($product['slug']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h4>
                                <p style="font-size: 0.8rem; color: #999; margin-bottom: 10px; text-transform: uppercase;"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                
                                <div class="price" style="color: var(--gold-color); font-weight: 600; margin-bottom: 15px; font-size: 1.1rem;">
                                    <?php if($product['sale_price']): ?>
                                        <span style="text-decoration: line-through; color: #999; font-size: 0.9rem; margin-right: 5px;">$<?php echo number_format($product['price'], 2); ?></span>
                                        $<?php echo number_format($product['sale_price'], 2); ?>
                                    <?php else: ?>
                                        $<?php echo number_format($product['price'], 2); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn-outline" style="width: 100%; font-size: 0.8rem; padding: 10px;">Add to Cart</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 50px;">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="shop.php?page=<?php echo $i; ?><?php echo $category_filter ? '&category='.urlencode($category_filter) : ''; ?>" 
                           style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--text-color); border-radius: 50%; <?php echo $page == $i ? 'background: var(--text-color); color: #fff;' : ''; ?>">
                           <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<style>
.shop-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }
.shop-card:hover .hover-zoom { transform: scale(1.1); }
.wishlist-btn.active { color: #e74c3c !important; animation: heartbeat 0.5s ease; }
@keyframes heartbeat {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}
</style>

<script>
function addToCart(productId) {
    // Basic AJAX cart simulation for MVP
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

function toggleWishlist(productId) {
    const icon = event.currentTarget;
    icon.classList.toggle('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>
