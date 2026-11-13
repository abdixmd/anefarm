<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Get owners for display
$owners = getOwners($pdo);

// Get farm stats
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
$totalProducts = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM gallery");
$totalImages = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $settings['farm_name']; ?> - <?php echo $lang['about']; ?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="Learn about AneDiaryFarm's story, our 5 dedicated owners, and our commitment to pure, fresh dairy in Ethiopia.">
    <meta name="theme-color" content="#00a400" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#30d158" media="(prefers-color-scheme: dark)">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Additional CSS for about page -->
    <style>
        /* Page Header */
        .page-header {
            height: 50vh;
            min-height: 400px;
            background: linear-gradient(135deg, var(--accent-green-light), var(--accent-green));
            position: relative;
            overflow: hidden;
            margin-top: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/pattern.svg') repeat;
            opacity: 0.1;
            animation: slide 20s linear infinite;
        }

        @keyframes slide {
            from { background-position: 0 0; }
            to { background-position: 100% 100%; }
        }

        .page-header h1 {
            font-size: clamp(40px, 8vw, 64px);
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .page-header p {
            font-size: clamp(16px, 3vw, 20px);
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            opacity: 0.9;
        }

        /* Story Section */
        .story-section {
            padding: 100px 0;
            background: var(--bg-primary);
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .story-content h2 {
            font-size: clamp(32px, 5vw, 42px);
            margin-bottom: 30px;
            color: var(--text-primary);
        }

        .story-content h2 span {
            color: var(--accent-green);
            position: relative;
            display: inline-block;
        }

        .story-content h2 span::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--accent-green);
            border-radius: 2px;
            transform: scaleX(0.3);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .story-content:hover h2 span::after {
            transform: scaleX(0.8);
        }

        .story-content p {
            color: var(--text-secondary);
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .story-highlight {
            background: var(--accent-green-light);
            padding: 30px;
            border-radius: 20px;
            margin-top: 30px;
            border-left: 4px solid var(--accent-green);
        }

        .story-highlight p {
            margin-bottom: 0;
            font-style: italic;
            color: var(--text-primary);
        }

        .story-highlight i {
            color: var(--accent-green);
            font-size: 24px;
            margin-right: 10px;
        }

        .story-image {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 60px var(--shadow-color);
        }

        .story-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .story-image:hover img {
            transform: scale(1.05);
        }

        .story-image::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 20px;
            z-index: 2;
            pointer-events: none;
        }

        /* Values Section */
        .values-section {
            padding: 80px 0;
            background: var(--bg-secondary);
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .value-card {
            text-align: center;
            padding: 40px 30px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid var(--border-light);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px var(--shadow-hover);
        }

        .value-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 25px;
            background: var(--accent-green-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .value-card:hover .value-icon {
            transform: rotateY(180deg);
            background: var(--accent-green);
        }

        .value-icon i {
            font-size: 40px;
            color: var(--accent-green);
            transition: all 0.3s ease;
        }

        .value-card:hover .value-icon i {
            color: white;
            transform: rotateY(180deg);
        }

        .value-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--text-primary);
        }

        .value-card p {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.6;
        }

        /* Timeline Section */
        .timeline-section {
            padding: 100px 0;
            background: var(--bg-primary);
        }

        .timeline {
            position: relative;
            max-width: 800px;
            margin: 60px auto 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background: linear-gradient(to bottom, transparent, var(--accent-green), transparent);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 60px;
        }

        .timeline-item:nth-child(odd) .timeline-content {
            margin-left: auto;
            padding-left: 60px;
        }

        .timeline-item:nth-child(even) .timeline-content {
            margin-right: auto;
            padding-right: 60px;
            text-align: right;
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            background: var(--accent-green);
            border-radius: 50%;
            border: 4px solid var(--bg-primary);
            box-shadow: 0 0 0 4px var(--accent-green-light);
            z-index: 2;
        }

        .timeline-content {
            width: calc(50% - 50px);
            padding: 30px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid var(--border-light);
            position: relative;
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 40px var(--shadow-hover);
        }

        .timeline-year {
            display: inline-block;
            padding: 5px 15px;
            background: var(--accent-green);
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .timeline-content h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .timeline-content p {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.6;
        }

        /* Team Section Enhanced */
        .team-section {
            padding: 100px 0;
            background: var(--bg-secondary);
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .team-card {
            text-align: center;
            padding: 30px 20px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid var(--border-light);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-green), var(--accent-green-dark));
            transform: translateX(-100%);
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .team-card:hover::before {
            transform: translateX(0);
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px var(--shadow-hover);
        }

        .team-image {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            position: relative;
        }

        .team-card:hover .team-image {
            border-color: var(--accent-green);
            transform: scale(1.05);
            box-shadow: 0 15px 30px var(--accent-green-soft);
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.1);
        }

        .team-card h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .team-role {
            color: var(--accent-green);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .team-bio {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            padding: 0 10px;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .team-card:hover .team-social {
            opacity: 1;
            transform: translateY(0);
        }

        .team-social a {
            width: 35px;
            height: 35px;
            background: var(--bg-tertiary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .team-social a:hover {
            background: var(--accent-green);
            color: white;
            transform: translateY(-3px);
        }

        /* Stats Section Enhanced */
        .stats-enhanced {
            padding: 60px 0;
            background: linear-gradient(135deg, var(--accent-green-light), var(--bg-secondary));
        }

        .stats-grid-enhanced {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-item-enhanced {
            text-align: center;
            padding: 40px 20px;
        }

        .stat-number-enhanced {
            font-size: 56px;
            font-weight: 700;
            color: var(--accent-green);
            line-height: 1;
            margin-bottom: 10px;
            font-feature-settings: "tnum";
        }

        .stat-label-enhanced {
            font-size: 16px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .story-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .timeline::before {
                left: 30px;
            }

            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content {
                width: calc(100% - 80px);
                margin-left: 80px;
                padding-left: 20px;
                text-align: left;
            }

            .timeline-dot {
                left: 30px;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .stats-grid-enhanced {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="<?php echo $_SESSION['theme'] ?? ''; ?>">
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader"></div>
    </div>

    <?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><?php echo $lang['our_story']; ?></h1>
            <p><?php echo $lang['story_text1']; ?></p>
        </div>
    </section>

    <!-- Story Section -->
    <section class="story-section">
        <div class="container">
            <div class="story-grid">
                <div class="story-content">
                    <h2><?php echo $lang['our_story']; ?> <span>AneDiaryFarm</span></h2>
                    <p><?php echo $lang['story_text2']; ?></p>
                    <p><?php echo $lang['story_text3']; ?></p>
                    
                    <div class="story-highlight">
                        <i class="fas fa-quote-left"></i>
                        <p>"We don't just sell milk; we deliver trust in every drop. Our mission is to provide pure, unadulterated milk to every family in Ethiopia."</p>
                        <strong style="color: var(--accent-green); margin-top: 15px; display: block;">- Anwar, Founder</strong>
                    </div>
                </div>
                
                <div class="story-image">
                    <img src="assets/images/farm-story.jpg" alt="AneDiaryFarm Story" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Our <span>Values</span></h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <h3>Purity</h3>
                    <p>100% pure milk with no additives, no powder, no water. Just nature's best.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Ethical Farming</h3>
                    <p>Happy cows on green pastures, treated with love and respect.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Community</h3>
                    <p>Serving our local community with fresh, healthy dairy products.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Sustainability</h3>
                    <p>Sustainable farming practices for a better tomorrow.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <div class="container">
            <h2 class="section-title">Our <span>Journey</span></h2>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2018</span>
                        <h3>The Beginning</h3>
                        <p>Anwar starts AneDiaryFarm with just 5 cows, driven by a vision to provide pure milk to his community.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2020</span>
                        <h3>Growth & Expansion</h3>
                        <p>Farm expands to 20 cows. First 5 team member come together to form the core team.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2022</span>
                        <h3>Home Delivery Service</h3>
                        <p>Launch of daily home delivery service in chiro.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <span class="timeline-year">2024</span>
                        <h3>Today</h3>
                        <p>Proudly serving 1500+ happy families with fresh, pure dairy products daily.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section (5 Owners) -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our <span> Owners</span></h2>
            
            <div class="team-grid">
                <?php foreach($owners as $owner): ?>
                <div class="team-card">
                    <div class="team-image">
                        <img src="assets/images/owners/<?php echo $owner['image'] ?: 'default-owner.jpg'; ?>" 
                             alt="<?php echo $owner['name']; ?>"
                             loading="lazy">
                    </div>
                    <h3><?php echo $owner['name']; ?></h3>
                    <p class="team-role"><?php echo $owner['role_' . $_SESSION['lang']]; ?></p>
                    <p class="team-bio"><?php echo $owner['bio_' . $_SESSION['lang']] ?? 'Dedicated to providing the freshest milk for your family.'; ?></p>
                    <div class="team-social">
                        <?php if(!empty($owner['facebook'])): ?>
                        <a href="<?php echo $owner['facebook']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if(!empty($owner['telegram'])): ?>
                        <a href="<?php echo $owner['telegram']; ?>" target="_blank"><i class="fab fa-telegram-plane"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-enhanced">
        <div class="container">
            <div class="stats-grid-enhanced">
                <div class="stat-item-enhanced">
                    <div class="stat-number-enhanced" data-target="5">0</div>
                    <div class="stat-label-enhanced"><?php echo $lang['dedicated_owners']; ?></div>
                </div>
                <div class="stat-item-enhanced">
                    <div class="stat-number-enhanced" data-target="50">0</div>
                    <div class="stat-label-enhanced">Daily Deliveries</div>
                </div>
                <div class="stat-item-enhanced">
                    <div class="stat-number-enhanced" data-target="<?php echo $totalProducts; ?>">0</div>
                    <div class="stat-label-enhanced">Products</div>
                </div>
                <div class="stat-item-enhanced">
                    <div class="stat-number-enhanced" data-target="1500">0</div>
                    <div class="stat-label-enhanced">Happy Customers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" style="padding: 80px 0; text-align: center; background: var(--bg-primary);">
        <div class="container">
            <h2 style="font-size: 36px; margin-bottom: 20px;">Experience the <span class="text-gradient">AneDiaryFarm</span> Difference</h2>
            <p style="color: var(--text-secondary); font-size: 18px; max-width: 600px; margin: 0 auto 30px;">
                Join hundreds of happy families who start their day with fresh, pure AneDiaryFarm milk.
            </p>
            <a href="contact.php" class="btn btn-primary" style="padding: 16px 40px;">
                <i class="fas fa-tint"></i> Order Fresh Milk Today
            </a>
        </div>
    </section>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/251989784673?text=<?php echo urlencode($lang['whatsapp_text']); ?>" 
       class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script>
        // Animate counters
        const counters = document.querySelectorAll('.stat-number-enhanced');
        const speed = 200;

        counters.forEach(counter => {
            const animate = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const increment = target / speed;

                if(count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(animate, 20);
                } else {
                    counter.innerText = target;
                }
            }

            // Start animation when element is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if(entry.isIntersecting) {
                        animate();
                        observer.unobserve(entry.target);
                    }
                });
            });

            observer.observe(counter);
        });
    </script>
</body>
</html>