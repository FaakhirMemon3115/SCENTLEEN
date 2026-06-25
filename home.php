<?php
// home.php
require_once 'config/database.php';
require_once 'includes/header.php';

// Fetch settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
}

$hero_title    = $settings['hero_title']    ?? 'SCENTLEEN';
$hero_subtitle = $settings['hero_subtitle'] ?? 'The Art of Luxury Fragrance';
$hero_image    = $settings['hero_image']    ?? 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=2000&auto=format&fit=crop';
$about_image   = $settings['about_image']   ?? '';

// Fetch active categories for collections section
$collections = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY id ASC");
    $collections = $stmt->fetchAll();
} catch (PDOException $e) {
}

// Default fallback collection cards (used only if no categories in DB)
$default_collections = [
    ['name' => 'Men',            'slug' => 'men',    'image' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=600&auto=format&fit=crop'],
    ['name' => 'Women',          'slug' => 'women',  'image' => 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?q=80&w=600&auto=format&fit=crop'],
    ['name' => 'Arabian Oud',    'slug' => 'oud',    'image' => 'https://images.unsplash.com/photo-1615529182904-14819c35db37?q=80&w=600&auto=format&fit=crop'],
    ['name' => 'Luxury Collection','slug' => 'luxury','image' => 'https://images.unsplash.com/photo-1622618991746-fe6004db3a47?q=80&w=600&auto=format&fit=crop'],
];

// Fetch signature products
$signature_products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 3");
    $signature_products = $stmt->fetchAll();
} catch (PDOException $e) {
}
?>

<!-- Main Content -->
<main>
    <!-- Premium Hero Section -->
    <section class="hero-section"
        style="position: relative; height: 100vh; overflow: hidden; display: flex; align-items: center; justify-content: center; color: #fff; padding: 0;">
        <!-- Video Background Placeholder (since we don't have a real video asset yet) -->
        <div class="video-bg"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: #111;">
            <!-- Real video tag should go here when asset is available -->
            <img src="<?php echo htmlspecialchars($hero_image); ?>"
                style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6; filter: brightness(0.7);"
                alt="Hero Perfume Background">
        </div>

        <div class="container hero-content text-center" style="z-index: 1;">
            <p class="hero-subtitle fade-up"
                style="font-family: var(--font-body); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 20px; color: var(--gold-color);">
                <?php echo htmlspecialchars($hero_subtitle); ?></p>
            <h1 class="hero-title fade-up" style="font-size: 5rem; margin-bottom: 30px; letter-spacing: 2px;">
                <?php echo htmlspecialchars($hero_title); ?></h1>
            <p class="hero-text fade-up" style="max-width: 600px; margin: 0 auto 40px; font-weight: 300;">Discover an
                exclusive collection of premium perfumes, authentic Arabian Oud, and imported masterpieces tailored for
                the modern connoisseur.</p>
            <div class="fade-up" style="transition-delay: 0.3s;">
                <a href="shop.php" class="btn-primary"
                    style="background-color: var(--gold-color); color: #fff; border-color: var(--gold-color);">Explore
                    Collection</a>
            </div>
        </div>

        <div class="scroll-indicator"
            style="position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); text-align: center; color: #fff; opacity: 0.8; animation: bounce 2s infinite;">
            <span
                style="font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; display: block; margin-bottom: 10px;">Scroll
                to Discover</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Collection Showcase -->
    <section class="collections py-5" style="background-color: var(--bg-color);">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Our Collections</h2>
            <div class="collection-grid"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 30px;">

                <?php
                $display_collections = !empty($collections) ? $collections : $default_collections;
                $delays = [100, 200, 300, 400, 500, 600];
                foreach ($display_collections as $i => $col):
                    // Build the card image src
                    if (!empty($col['image'])) {
                        // DB category — image stored as relative path OR full URL
                        $col_img = (strpos($col['image'], 'http') === 0) ? $col['image'] : $col['image'];
                    } else {
                        $col_img = 'https://images.unsplash.com/photo-1594035910387-fea47794261f?q=80&w=600&auto=format&fit=crop';
                    }
                    // Build shop link
                    $col_slug = isset($col['slug']) ? $col['slug'] : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $col['name']));
                    $delay = $delays[$i] ?? (($i + 1) * 100);
                ?>
                <a href="shop.php?category=<?php echo urlencode($col_slug); ?>" class="collection-card" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <img src="<?php echo htmlspecialchars($col_img); ?>"
                        alt="<?php echo htmlspecialchars($col['name']); ?> Collection">
                    <div class="collection-overlay">
                        <div class="collection-info">
                            <h3><?php echo htmlspecialchars($col['name']); ?></h3>
                            <span class="collection-cta">Explore <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- Signature Collection (Glassmorphism) -->
    <section class="signature-collection"
        style="background: url('https://images.unsplash.com/photo-1557170334-a9632e77c6e4?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat; padding: 120px 0; position: relative;">
        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.6);"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <div class="text-center mb-5">
                <h2 style="color: #fff; font-size: 2.5rem; margin-bottom: 20px;">The Signature Collection</h2>
                <p style="color: #ddd; max-width: 600px; margin: 0 auto;">Experience the pinnacle of perfumery with our
                    highly sought-after signature creations.</p>
            </div>

            <div
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-top: 50px;">
                <?php if (!empty($signature_products)): ?>
                    <?php foreach ($signature_products as $p): ?>
                        <!-- Product Card -->
                        <div class="glass product-card"
                            style="padding: 30px; border-radius: 15px; text-align: center; color: #fff; transition: transform 0.4s ease, box-shadow 0.4s ease;">
                            <?php $p_img = $p['image'] ? 'uploads/' . $p['image'] : 'https://images.unsplash.com/photo-1588405748880-12d1d2a59f75?q=80&w=400&auto=format&fit=crop'; ?>
                            <img src="<?php echo htmlspecialchars($p_img); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>"
                                style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px; margin-bottom: 20px;">
                            <h4 style="font-size: 1.5rem; margin-bottom: 10px;"><a
                                    href="product.php?slug=<?php echo urlencode($p['slug']); ?>"
                                    style="color: #fff;"><?php echo htmlspecialchars($p['name']); ?></a></h4>
                            <p style="color: var(--gold-color); font-size: 1.2rem; margin-bottom: 15px;">Rs.
                                <?php echo number_format($p['price'], 2); ?></p>
                            <button onclick="addToCart(<?php echo $p['id']; ?>)" class="btn-outline"
                                style="color: #fff; border-color: #fff; width: 100%;">Add to Cart</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #fff; text-align: center; width: 100%;">No signature products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section" style="background-color: var(--bg-color); padding: 100px 0;">
        <div class="container" style="display: flex; align-items: center; gap: 60px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;" data-aos="fade-right">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Since 1980 <br><span class="text-gold">The Art of
                        Luxury</span></h2>
                <p style="margin-bottom: 20px; color: #555;">Scentleen has been at the forefront of the luxury fragrance
                    industry for over four decades. Our commitment to sourcing the finest ingredients globally ensures
                    that every bottle tells a story of elegance and passion.</p>

                <div style="display: flex; gap: 40px; margin-top: 40px;">
                    <div class="counter-box">
                        <h3 class="counter" data-target="45"
                            style="font-size: 2.5rem; color: var(--gold-color); margin-bottom: 5px;">0</h3>
                        <p style="font-size: 0.9rem; text-transform: uppercase;">Years</p>
                    </div>
                    <div class="counter-box">
                        <h3 class="counter" data-target="10000"
                            style="font-size: 2.5rem; color: var(--gold-color); margin-bottom: 5px;">0</h3>
                        <p style="font-size: 0.9rem; text-transform: uppercase;">Customers</p>
                    </div>
                    <div class="counter-box">
                        <h3 class="counter" data-target="500"
                            style="font-size: 2.5rem; color: var(--gold-color); margin-bottom: 5px;">0</h3>
                        <p style="font-size: 0.9rem; text-transform: uppercase;">Perfumes</p>
                    </div>
                </div>
            </div>

            <!-- About Image: loaded from admin settings, or a beautiful default -->
            <div style="flex: 1; min-width: 300px; text-align: center;" data-aos="fade-left">
                <?php if (!empty($about_image)): ?>
                    <div class="about-img-wrap">
                        <img src="<?php echo htmlspecialchars($about_image); ?>"
                            alt="Scentleen - The Art of Luxury"
                            style="width: 100%; max-width: 420px; height: 360px; object-fit: cover; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                    </div>
                <?php else: ?>
                    <!-- Default: branded perfume image -->
                    <div class="about-img-wrap">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?q=80&w=600&auto=format&fit=crop"
                            alt="Scentleen Luxury Fragrance"
                            style="width: 100%; max-width: 420px; height: 360px; object-fit: cover; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
                        <div style="margin-top: 20px; display: inline-flex; align-items: center; gap: 10px; color: var(--gold-color); font-size: 0.85rem; letter-spacing: 2px; text-transform: uppercase;">
                            <span style="width: 30px; height: 1px; background: var(--gold-color); display: inline-block;"></span>
                            Paris · Est. 1980
                            <span style="width: 30px; height: 1px; background: var(--gold-color); display: inline-block;"></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Fragrance Finder -->
    <section class="fragrance-finder"
        style="background-color: var(--secondary-color); padding: 100px 0; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Find Your Signature Scent</h2>
            <p style="margin-bottom: 50px; max-width: 600px; margin-inline: auto;">Not sure what to choose? Answer a few
                questions and our AI-style logic will recommend the perfect fragrance for you.</p>

            <div class="finder-form"
                style="max-width: 600px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <form id="ai-finder">
                    <div class="step" id="step-1">
                        <h4 style="margin-bottom: 20px;">Who is this fragrance for?</h4>
                        <div style="display: flex; justify-content: center; gap: 20px;">
                            <label class="radio-btn"><input type="radio" name="gender" value="men">
                                <span>Men</span></label>
                            <label class="radio-btn"><input type="radio" name="gender" value="women">
                                <span>Women</span></label>
                        </div>
                        <button type="button" class="btn-primary mt-4" style="margin-top: 30px;"
                            onclick="nextStep(2)">Next</button>
                    </div>
                    <div class="step" id="step-2" style="display: none;">
                        <h4 style="margin-bottom: 20px;">What's the occasion?</h4>
                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 15px;">
                            <label class="radio-btn"><input type="radio" name="occasion" value="daily"> <span>Daily
                                    Wear</span></label>
                            <label class="radio-btn"><input type="radio" name="occasion" value="evening"> <span>Evening
                                    / Party</span></label>
                            <label class="radio-btn"><input type="radio" name="occasion" value="office">
                                <span>Office</span></label>
                            <label class="radio-btn"><input type="radio" name="occasion" value="romantic">
                                <span>Romantic Date</span></label>
                        </div>
                        <div style="margin-top: 30px;">
                            <button type="button" class="btn-outline" style="margin-right: 10px;"
                                onclick="nextStep(1)">Back</button>
                            <button type="button" class="btn-primary" onclick="showRecommendation()">Find My
                                Scent</button>
                        </div>
                    </div>

                    <div id="finder-result" style="display: none; margin-top: 20px;">
                        <i class="fas fa-magic text-gold" style="font-size: 2rem; margin-bottom: 15px;"></i>
                        <h4>We Recommend: <strong>Golden Elixir</strong></h4>
                        <p style="font-size: 0.9rem; color: #666; margin-top: 10px;">Based on your choices, this warm
                            and inviting scent is perfect for your needs.</p>
                        <a href="shop.php?product=golden-elixir" class="btn-primary" style="margin-top: 20px;">View
                            Details</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter"
        style="background-color: var(--text-color); color: #fff; padding: 80px 0; text-align: center;">
        <div class="container">
            <h2 style="font-size: 2rem; margin-bottom: 15px;">Join the Scentleen Club</h2>
            <p style="margin-bottom: 30px; color: #ccc;">Subscribe to receive updates, access to exclusive deals, and
                more.</p>
            <form style="max-width: 500px; margin: 0 auto; display: flex; position: relative;">
                <input type="email" placeholder="Enter your email address" required
                    style="width: 100%; padding: 15px 20px; border: none; border-radius: 30px; font-family: var(--font-body); outline: none;">
                <button type="submit" class="btn-primary"
                    style="position: absolute; right: 5px; top: 5px; bottom: 5px; border-radius: 25px; padding: 0 25px;">Subscribe</button>
            </form>
        </div>
    </section>
</main>

<style>
    /* Home Page Specific Styles */
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
        40% { transform: translateY(-15px) translateX(-50%); }
        60% { transform: translateY(-7px) translateX(-50%); }
    }

    /* ============================
       COLLECTION CARD HOVER EFFECTS
       ============================ */
    .collection-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        height: 360px;
        display: block;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transition: box-shadow 0.4s ease, transform 0.4s ease;
    }

    .collection-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: block;
    }

    .collection-card:hover {
        box-shadow: 0 16px 50px rgba(0,0,0,0.28);
        transform: translateY(-6px);
    }

    .collection-card:hover img {
        transform: scale(1.12);
    }

    .collection-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.82) 0%, rgba(0,0,0,0.15) 55%, transparent 100%);
        display: flex;
        align-items: flex-end;
        padding: 28px 30px;
        transition: background 0.4s ease;
    }

    .collection-card:hover .collection-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.3) 60%, transparent 100%);
    }

    .collection-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .collection-info h3 {
        color: #fff;
        font-size: 1.5rem;
        letter-spacing: 1px;
        margin: 0;
        font-family: var(--font-heading);
        transition: transform 0.3s ease;
    }

    .collection-card:hover .collection-info h3 {
        transform: translateY(-4px);
    }

    .collection-cta {
        color: var(--gold-color);
        font-size: 0.85rem;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity 0.35s ease, transform 0.35s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .collection-card:hover .collection-cta {
        opacity: 1;
        transform: translateY(0);
    }

    /* Gold shimmer border on hover */
    .collection-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 12px;
        border: 2px solid transparent;
        transition: border-color 0.4s ease;
        pointer-events: none;
    }

    .collection-card:hover::after {
        border-color: var(--gold-color);
    }

    /* ============================
       ABOUT IMAGE
       ============================ */
    .about-img-wrap {
        position: relative;
        display: inline-block;
    }

    .about-img-wrap::before {
        content: '';
        position: absolute;
        top: 16px;
        left: 16px;
        right: -16px;
        bottom: -16px;
        border: 2px solid var(--gold-color);
        border-radius: 12px;
        z-index: 0;
        opacity: 0.45;
    }

    .about-img-wrap img {
        position: relative;
        z-index: 1;
    }

    /* ============================
       PRODUCT CARD
       ============================ */
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
        border-color: var(--gold-color);
    }

    /* ============================
       FINDER RADIO BUTTONS
       ============================ */
    .radio-btn { cursor: pointer; }
    .radio-btn input { display: none; }
    .radio-btn span {
        display: inline-block;
        padding: 10px 20px;
        border: 1px solid var(--text-color);
        border-radius: 30px;
        transition: all 0.3s;
    }
    .radio-btn input:checked+span {
        background-color: var(--text-color);
        color: #fff;
    }
</style>

<script>
    function nextStep(step) {
        document.querySelectorAll('.step').forEach(el => el.style.display = 'none');
        document.getElementById('step-' + step).style.display = 'block';
    }

    function showRecommendation() {
        document.getElementById('step-2').style.display = 'none';
        document.getElementById('finder-result').style.display = 'block';
    }
</script>

<?php require_once 'includes/footer.php'; ?>