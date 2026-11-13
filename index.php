<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Get data for display
$owners = getOwners($pdo);
$products = getProducts($pdo, true); // Get featured products
$gallery = getGallery($pdo, 7); // Get 7 gallery images
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $settings['farm_name']; ?> - <?php echo $lang['hero_title']; ?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="Pure, fresh, and local dairy products from AneDiaryFarm. Farm-fresh milk delivered to your doorstep in chiro.">
    <meta name="theme-color" content="#00a400" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#30d158" media="(prefers-color-scheme: dark)">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body class="<?php echo $_SESSION['theme'] ?? ''; ?>">
    <!-- Page Loader -->
    <div class="page-loader" style="position:fixed; top:0; left:0; width:100%; height:100%; background:var(--bg-primary); display:flex; align-items:center; justify-content:center; z-index:9999;">
        <div class="loader" style="width:48px; height:48px; border:3px solid var(--border-light); border-top-color:var(--accent-green); border-radius:50%; animation:spin 1s linear infinite;"></div>
    </div>

    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Section with Video -->
    <section class="hero" id="home">
        <div class="hero-video-container">
            <video class="hero-video" autoplay muted loop playsinline poster="assets/images/hero-fallback.jpg">
                <source src="assets/videos/hero-background.mp4" type="video/mp4">
                <!-- Fallback image if video doesn't load -->
            </video>
        </div>
        <div class="hero-content">
            <h1 class="text-gradient"><?php echo $lang['hero_title']; ?></h1>
            <p><?php echo $lang['hero_subtitle']; ?></p>
            <div class="hero-cta">
                <a href="#products" class="btn btn-primary"><?php echo $lang['our_products']; ?></a>
                <a href="#owners" class="btn btn-secondary"><?php echo $lang['meet_owners']; ?></a>
            </div>
        </div>
        <div class="scroll-indicator">
            <span><?php echo $lang['hero_cta']; ?></span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title"><?php echo $lang['why_choose']; ?> <span>AneDiaryFarm</span></h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3>100% <?php echo $lang['purity']; ?></h3>
                    <p><?php echo $lang['purity_desc']; ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><?php echo $lang['fresh_daily']; ?></h3>
                    <p><?php echo $lang['fresh_desc']; ?></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3><?php echo $lang['happy_cows']; ?></h3>
                    <p><?php echo $lang['cows_desc']; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Owners Section (Owners) -->
    <section class="owners-section" id="owners">
        <div class="container">
            <h2 class="section-title"><?php echo $lang['meet_owners']; ?></h2>
            <div class="owners-grid">
                <?php foreach($owners as $index => $owner): ?>
                <div class="owner-card">
                    <div class="owner-image">
                        <img src="assets/images/owners/<?php echo $owner['image'] ?: 'default-owner.jpg'; ?>" 
                             alt="<?php echo $owner['name']; ?>"
                             loading="lazy">
                    </div>
                    <h3><?php echo $owner['name']; ?></h3>
                    <p class="owner-role"><?php echo $owner['role_' . $_SESSION['lang']]; ?></p>
                    <div class="owner-social">
                        <?php if(!empty($owner['facebook'])): ?>
                        <a href="<?php echo $owner['facebook']; ?>" target="_blank" data-tooltip="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        <?php if(!empty($owner['telegram'])): ?>
                        <a href="<?php echo $owner['telegram']; ?>" target="_blank" data-tooltip="Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="container">
            <h2 class="section-title"><?php echo $lang['our_products']; ?></h2>
            <div class="product-grid">
                <?php foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php echo $product['image'] ?: 'default-product.jpg'; ?>" 
                             alt="<?php echo $product['name_' . $_SESSION['lang']]; ?>"
                             loading="lazy">
                        <?php if($product['in_stock']): ?>
                            <span class="product-badge"><?php echo $lang['fresh']; ?></span>
                        <?php else: ?>
                            <span class="product-badge" style="background: var(--text-secondary);"><?php echo $lang['out_of_stock']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name_' . $_SESSION['lang']]; ?></h3>
                        <p class="product-description">
                            <?php echo substr($product['description_' . $_SESSION['lang']], 0, 100); ?>...
                        </p>
                        <div class="product-price">
                            <?php echo number_format($product['price'], 2); ?> <small>ETB / <?php echo $product['unit']; ?></small>
                        </div>
                        <a href="https://wa.me/251989784673?text=<?php echo urlencode($lang['whatsapp_text'] . ' - ' . $product['name_en']); ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fab fa-whatsapp"></i> <?php echo $lang['order_now']; ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="products.php" class="btn btn-secondary"><?php echo $lang['view_all']; ?></a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" data-target="1500" data-suffix="+">0</div>
                    <div class="stat-label"><?php echo $lang['happy_customers']; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="50" data-suffix="+">0</div>
                    <div class="stat-label"><?php echo $lang['daily_deliveries']; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="100" data-suffix="%">0</div>
                    <div class="stat-label"><?php echo $lang['purity']; ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" data-target="5" data-suffix="">0</div>
                    <div class="stat-label"><?php echo $lang['dedicated_owners']; ?></div>
                </div>
            </div>
        </div>
    </section>

<!-- Gallery Section - Enhanced Version -->
<section class="gallery-section" id="gallery">
    <div class="container">
        <h2 class="section-title"><?php echo $lang['our_gallery']; ?></h2>
        <p style="text-align: center; color: var(--text-secondary); margin-bottom: 40px;">
            A glimpse into life at Anefarm - where happy cows produce the freshest milk
        </p>
        
        <!-- Category Pills -->
        <div class="gallery-categories">
            <button class="category-pill active" data-category="all">All Photos</button>
            <button class="category-pill" data-category="farm">Farm Life</button>
            <button class="category-pill" data-category="cows">Our Cows</button>
            <button class="category-pill" data-category="process">Milking Process</button>
            <button class="category-pill" data-category="team">Our Team</button>
        </div>
        
        <!-- Gallery Grid -->
        <div class="gallery-grid-enhanced" id="gallery-grid">
            <?php
            $gallery = getGallery($pdo, 8); // Get first 8 images
            foreach($gallery as $index => $image): 
                $category = $image['category'] ?? 'farm';
            ?>
            <div class="gallery-item-enhanced <?php echo $index == 0 ? 'featured' : ''; ?>" 
                 data-category="<?php echo $category; ?>">
                <img src="assets/images/gallery/<?php echo $image['image']; ?>" 
                     alt="<?php echo $image['title_' . $_SESSION['lang']]; ?>"
                     loading="lazy">
                
                <?php if($index == 0): ?>
                    <span class="gallery-badge">Featured</span>
                <?php endif; ?>
                
                <span class="gallery-category-badge">
                    <?php
                    $icons = [
                        'farm' => '🏡',
                        'cows' => '🐄',
                        'process' => '⚙️',
                        'team' => '👥',
                        'delivery' => '🚚'
                    ];
                    echo $icons[$category] ?? '📸';
                    ?>
                </span>
                
                <div class="gallery-overlay">
                    <h3><?php echo $image['title_' . $_SESSION['lang']]; ?></h3>
                    <p>Click to enlarge</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- View All Button -->
        <div class="gallery-footer">
            <a href="gallery.php" class="btn btn-secondary">
                <i class="fas fa-images"></i> View Full Gallery
            </a>
            
            <div class="gallery-social">
                <span>Share:</span>
                <a href="#" onclick="shareOnFacebook()"><i class="fab fa-facebook"></i></a>
                <a href="#" onclick="shareOnTelegram()"><i class="fab fa-telegram"></i></a>
                <a href="#" onclick="shareOnTwitter()"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <h2 class="section-title"><?php echo $lang['customer_say']; ?></h2>
            <div class="testimonials-slider">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "The freshest milk I've ever tasted. You can really taste the difference! Delivery is always on time."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="assets/images/testimonials/avatar1.ico" alt="Sarah T.">
                        </div>
                        <div class="author-info">
                            <h4>Sarah T.</h4>
                            <p>Customer since 2023</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Finally, milk that tastes like it did when I was a kid. Pure and creamy. My family loves it!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="assets/images/testimonials/avatar2.ico" alt="Michael A.">
                        </div>
                        <div class="author-info">
                            <h4>Michael A.</h4>
                            <p>Customer since 2022</p>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "The A2 milk is perfect for my kids. Easy to digest and so fresh. Highly recommend AneDiaryFarm!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="assets/images/testimonials/avatar3.ico" alt="Abebe K.">
                        </div>
                        <div class="author-info">
                            <h4>Abebe K.</h4>
                            <p>Customer since 2024</p>
                        </div>
                    </div>
                </div>
                
                <!-- Duplicate for infinite scroll -->
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "The freshest milk I've ever tasted. You can really taste the difference! Delivery is always on time."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="assets/images/testimonials/avatar1.ico" alt="Sarah T.">
                        </div>
                        <div class="author-info">
                            <h4>Sarah T.</h4>
                            <p>Customer since 2023</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="container">
            <h2 class="section-title"><?php echo $lang['get_in_touch']; ?></h2>
            <div class="contact-grid">
                <div class="contact-info">
                    <h3><?php echo $lang['contact_info']; ?></h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h4><?php echo $lang['address']; ?></h4>
                            <p><?php echo $settings['address']; ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <h4><?php echo $lang['phone']; ?></h4>
                            <p><?php echo $settings['phone']; ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h4><?php echo $lang['email']; ?></h4>
                            <p><?php echo $settings['email']; ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <h4><?php echo $lang['delivery_hours']; ?></h4>
                            <p><?php echo $lang['mon_fri']; ?>: 6:00 AM - 10:00 AM<br>
                               <?php echo $lang['saturday']; ?>: 6:00 AM - 9:00 AM<br>
                               <?php echo $lang['sunday']; ?>: <?php echo $lang['closed']; ?></p>
                        </div>
                    </div>
                    
                    <h3 style="margin-top: 40px;"><?php echo $lang['follow_us']; ?></h3>
                    <div class="social-links">
                        <a href="<?php echo $settings['facebook']; ?>" target="_blank" data-tooltip="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?php echo $settings['telegram']; ?>" target="_blank" data-tooltip="Telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <a href="<?php echo $settings['tiktok']; ?>" target="_blank" data-tooltip="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/251912345678" target="_blank" data-tooltip="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h3><?php echo $lang['send_message']; ?></h3>
                    <form id="contact-form" method="POST" action="send-message.php">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="<?php echo $lang['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="<?php echo $lang['email']; ?>">
                        </div>
                        <div class="form-group">
                            <input type="tel" name="phone" placeholder="<?php echo $lang['phone']; ?>" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="<?php echo $lang['message']; ?>" required rows="5"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $lang['send']; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/251989784673?text=<?php echo urlencode($lang['whatsapp_text']); ?>" 
       class="whatsapp-float" target="_blank" data-tooltip="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <?php include 'includes/footer.php'; ?>
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>