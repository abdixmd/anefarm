<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Get gallery images from database - EXCLUDE products category
$stmt = $pdo->query("SELECT * FROM gallery WHERE category != 'products' ORDER BY featured DESC, sort_order ASC, created_at DESC");
$dbImages = $stmt->fetchAll();

// Check if we have database images
$hasDbImages = !empty($dbImages);

// If no database images, use hardcoded fallback images (excluding products)
if(!$hasDbImages) {
    // Hardcoded fallback gallery images (no products category)
    $fallbackImages = [
        [
            'id' => 1,
            'title_en' => 'Green Pastures',
            'title_om' => 'Lafa Marga Qullqulluu',
            'title_am' => 'አረንጓዴ ግጦሽ',
            'image' => 'farm-1.jpg',
            'category' => 'farm',
            'featured' => 1,
            'description' => 'Our cows graze on fresh, green grass daily'
        ],
        [
            'id' => 2,
            'title_en' => 'Happy Cows',
            'title_om' => 'Horii Galeeffatan',
            'title_am' => 'ደስተኛ ላሞች',
            'image' => 'cows-1.jpg',
            'category' => 'cows',
            'featured' => 0,
            'description' => 'Our cows are treated with love and care'
        ],
        [
            'id' => 3,
            'title_en' => 'Morning Milking',
            'title_om' => 'Aannan Haadhoo',
            'title_am' => 'የጠዋት ማልሚያ',
            'image' => 'milking-1.jpg',
            'category' => 'process',
            'featured' => 0,
            'description' => 'Fresh milk collected at dawn'
        ],
        [
            'id' => 5,
            'title_en' => 'Our Dedicated Team',
            'title_om' => 'Garee Keenya',
            'title_am' => 'ቡድናችን',
            'image' => 'team-1.jpg',
            'category' => 'team',
            'featured' => 0,
            'description' => 'The 5 owners working together'
        ],
        [
            'id' => 6,
            'title_en' => 'Sunrise at Farm',
            'title_om' => 'Bariisaa Qonnaa',
            'title_am' => 'ንጋት በእርሻ',
            'image' => 'farm-2.jpg',
            'category' => 'farm',
            'featured' => 0,
            'description' => 'Beautiful mornings on the farm'
        ],
        [
            'id' => 7,
            'title_en' => 'Desi Cows',
            'title_om' => 'Horii Dacha"a',
            'title_am' => 'ደሲ ላሞች',
            'image' => 'cows-2.jpg',
            'category' => 'cows',
            'featured' => 0,
            'description' => 'Our purebred indigenous cows'
        ],
        [
            'id' => 8,
            'title_en' => 'Clean Packaging',
            'title_om' => 'Qulqullina Guftuu',
            'title_am' => 'ንጹህ ማሸጊያ',
            'image' => 'packaging.jpg',
            'category' => 'process',
            'featured' => 0,
            'description' => 'Hygienic packaging process'
        ],
        [
            'id' => 10,
            'title_en' => 'Doorstep Delivery',
            'title_om' => 'Geessituu Balbalaa',
            'title_am' => 'የቤት አቅርቦት',
            'image' => 'delivery.jpg',
            'category' => 'delivery',
            'featured' => 0,
            'description' => 'Fresh milk delivered to your home'
        ],
        [
            'id' => 11,
            'title_en' => 'Farm Sunset',
            'title_om' => 'Dhiilaa Qonnaa',
            'title_am' => 'የእርሻ መጥለቅ',
            'image' => 'farm-3.jpg',
            'category' => 'farm',
            'featured' => 0,
            'description' => 'Peaceful evenings at the farm'
        ],
        [
            'id' => 12,
            'title_en' => 'Team Meeting',
            'title_om' => 'Walga\'ii Garee',
            'title_am' => 'የቡድን ስብሰባ',
            'image' => 'team-2.jpg',
            'category' => 'team',
            'featured' => 0,
            'description' => 'Planning for quality milk delivery'
        ]
    ];
    
    // Use fallback images
    $galleryImages = $fallbackImages;
    $totalImages = count($fallbackImages);
    $sourceType = 'fallback';
} else {
    // Use database images
    $galleryImages = $dbImages;
    $totalImages = count($dbImages);
    $sourceType = 'database';
}

// Get unique categories from images (excluding products)
$categories = [];
foreach($galleryImages as $img) {
    $cat = $img['category'] ?? 'farm';
    if($cat === 'products') continue; // Skip products category
    if(!in_array($cat, $categories)) {
        $categories[] = $cat;
    }
}

// Define all possible valid categories (excluding products)
$validCategories = ['farm', 'cows', 'process', 'team', 'delivery'];

// Make sure categories array only contains valid categories
$categories = array_intersect($categories, $validCategories);
$categories = array_values($categories); // Reindex array

// Category icons mapping
$categoryIcons = [
    'farm' => '🏡',
    'cows' => '🐄',
    'process' => '⚙️',
    'team' => '👥',
    'delivery' => '🚚'
];

// Category names in current language
$categoryNames = [
    'farm' => $lang['farm_life'] ?? 'Farm Life',
    'cows' => $lang['our_cows'] ?? 'Our Cows',
    'process' => $lang['milking_process'] ?? 'Milking Process',
    'team' => $lang['our_team'] ?? 'Our Team',
    'delivery' => $lang['delivery'] ?? 'Delivery'
];

// Category icons for font-awesome
$categoryFontIcons = [
    'farm' => 'tractor',
    'cows' => 'paw',
    'process' => 'cogs',
    'team' => 'users',
    'delivery' => 'truck'
];
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $settings['farm_name']; ?> - <?php echo $lang['gallery'] ?? 'Gallery'; ?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="Explore life at AneDiaryFarm - see our happy cows, farming process, and fresh dairy products in action.">
    <meta name="theme-color" content="#00a400" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#30d158" media="(prefers-color-scheme: dark)">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lightgallery.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-zoom.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/css/lg-thumbnail.min.css">
    
    <style>
        /* ============================================
           GALLERY PAGE - Apple Inspired Design
           ============================================ */
        
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

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to top, var(--bg-primary), transparent);
        }

        @keyframes slide {
            from { background-position: 0 0; }
            to { background-position: 100% 100%; }
        }

        .page-header-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }

        .page-header h1 {
            font-size: clamp(40px, 8vw, 64px);
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: fadeInUp 1s ease;
        }

        .page-header p {
            font-size: clamp(16px, 3vw, 20px);
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .page-header .header-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .header-stat {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 14px;
        }

        .header-stat i {
            font-size: 16px;
        }

        /* Gallery Controls */
        .gallery-controls {
            padding: 40px 0 20px;
            background: var(--bg-primary);
            position: sticky;
            top: 60px;
            z-index: 10;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-light);
        }

        .controls-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        /* Category Pills */
        .category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .category-pill {
            padding: 10px 24px;
            border: 2px solid var(--border-light);
            background: transparent;
            color: var(--text-secondary);
            border-radius: 40px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-pill:hover {
            border-color: var(--accent-green);
            color: var(--accent-green);
            transform: translateY(-2px);
        }

        .category-pill.active {
            background: var(--accent-green);
            color: white;
            border-color: var(--accent-green);
            box-shadow: 0 10px 20px var(--accent-green-soft);
        }

        .category-pill i {
            font-size: 16px;
        }

        /* View Options */
        .view-options {
            display: flex;
            gap: 10px;
            background: var(--bg-secondary);
            padding: 5px;
            border-radius: 40px;
            border: 1px solid var(--border-light);
        }

        .view-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .view-btn:hover {
            color: var(--accent-green);
        }

        .view-btn.active {
            background: var(--accent-green);
            color: white;
        }

        /* Search Bar */
        .search-bar {
            position: relative;
            width: 250px;
        }

        .search-bar input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid var(--border-light);
            border-radius: 40px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 4px var(--accent-green-soft);
        }

        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 16px;
        }

        .search-bar .clear-search {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            display: none;
        }

        .search-bar.has-value .clear-search {
            display: block;
        }

        /* Gallery Grid */
        .gallery-page {
            padding: 40px 0 80px;
            background: var(--bg-primary);
            min-height: 60vh;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 30px;
            transition: all 0.3s ease;
        }

        .gallery-grid.grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .gallery-grid.grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .gallery-grid.grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        /* Gallery Item */
        .gallery-item {
            position: relative;
            border-radius: 24px;
            overflow: hidden;
            cursor: pointer;
            aspect-ratio: 1;
            box-shadow: 0 10px 30px var(--shadow-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .gallery-item.featured {
            grid-column: span 2;
            grid-row: span 2;
            aspect-ratio: 16/9;
        }

        .gallery-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 30px 60px var(--shadow-hover);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        /* Gallery Badges */
        .gallery-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-green);
            color: white;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
            box-shadow: 0 5px 15px rgba(0,164,0,0.3);
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .gallery-item:hover .gallery-badge {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery-category {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            color: white;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .gallery-item:hover .gallery-category {
            opacity: 1;
            transform: translateX(0);
        }

        /* Gallery Overlay */
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 25px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-overlay h3 {
            color: white;
            font-size: 20px;
            margin-bottom: 5px;
            transform: translateY(20px);
            transition: transform 0.3s ease 0.1s;
        }

        .gallery-overlay p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            transform: translateY(20px);
            transition: transform 0.3s ease 0.2s;
        }

        .gallery-item:hover .gallery-overlay h3,
        .gallery-item:hover .gallery-overlay p {
            transform: translateY(0);
        }

        /* Loading Animation */
        .gallery-item {
            animation-delay: calc(var(--item-index) * 0.05s);
        }

        /* Empty State */
        .empty-gallery {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            background: var(--card-bg);
            border-radius: 40px;
            border: 2px dashed var(--border-light);
        }

        .empty-gallery i {
            font-size: 80px;
            color: var(--border-light);
            margin-bottom: 20px;
        }

        .empty-gallery h3 {
            font-size: 28px;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .empty-gallery p {
            color: var(--text-secondary);
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .empty-gallery .suggestions {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .suggestion-tag {
            padding: 8px 20px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 30px;
            color: var(--text-secondary);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .suggestion-tag:hover {
            background: var(--accent-green);
            color: white;
            border-color: var(--accent-green);
        }

        /* Gallery Stats */
        .gallery-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 60px 0 40px;
            padding: 30px;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border-radius: 60px;
            border: 1px solid var(--border-light);
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item.highlight {
            transform: scale(1.1);
            color: var(--accent-green);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--accent-green);
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Load More */
        .load-more {
            text-align: center;
            margin-top: 50px;
        }

        .load-more-btn {
            padding: 16px 50px;
            background: transparent;
            border: 2px solid var(--border-light);
            color: var(--text-primary);
            border-radius: 50px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .load-more-btn:hover:not(:disabled) {
            border-color: var(--accent-green);
            color: var(--accent-green);
            transform: translateY(-2px);
        }

        .load-more-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .load-more-btn i {
            transition: transform 0.3s;
        }

        .load-more-btn:hover:not(:disabled) i {
            transform: rotate(180deg);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
            
            .gallery-grid.grid-4 {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .gallery-item.featured {
                grid-column: span 1;
                grid-row: span 1;
                aspect-ratio: 1;
            }
        }

        @media (max-width: 768px) {
            .controls-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .category-pills {
                justify-content: center;
                order: 2;
            }
            
            .view-options {
                align-self: center;
                order: 1;
            }
            
            .search-bar {
                width: 100%;
                order: 3;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
            
            .gallery-grid.grid-2,
            .gallery-grid.grid-3,
            .gallery-grid.grid-4 {
                grid-template-columns: 1fr;
            }
            
            .gallery-stats {
                flex-direction: column;
                gap: 20px;
                border-radius: 30px;
            }
            
            .header-stats {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .category-pills {
                gap: 8px;
            }
            
            .category-pill {
                padding: 8px 16px;
                font-size: 13px;
            }
            
            .gallery-item {
                aspect-ratio: 4/3;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .gallery-controls {
                background: rgba(0,0,0,0.8);
            }
            
            .search-bar input {
                background: #1c1c1e;
                border-color: #38383a;
            }
            
            .view-options {
                background: #1c1c1e;
            }
        }

        /* Print Styles */
        @media print {
            .gallery-controls,
            .load-more {
                display: none;
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
        <div class="page-header-content">
            <h1><?php echo $lang['our_gallery'] ?? 'Our Gallery'; ?></h1>
            <p>A glimpse into life at AneDiaryFarm - where happy cows produce the freshest milk</p>
            
            <div class="header-stats">
                <div class="header-stat">
                    <i class="fas fa-images"></i>
                    <span id="totalPhotosHeader"><?php echo $totalImages; ?> Photos</span>
                </div>
                <div class="header-stat">
                    <i class="fas fa-tags"></i>
                    <span><?php echo count($categories); ?> Categories</span>
                </div>
                <div class="header-stat">
                    <i class="fas fa-star"></i>
                    <span id="featuredCount"><?php echo count(array_filter($galleryImages, function($img) { return ($img['featured'] ?? 0) == 1; })); ?> Featured</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Controls -->
    <section class="gallery-controls">
        <div class="container">
            <div class="controls-container">
                <!-- Category Pills -->
                <div class="category-pills" id="categoryPills">
                    <button class="category-pill active" data-category="all">
                        <i class="fas fa-th-large"></i>
                        All Photos
                    </button>
                    <?php foreach($categories as $category): ?>
                    <?php if(in_array($category, $validCategories)): ?>
                    <button class="category-pill" data-category="<?php echo $category; ?>">
                        <i class="fas fa-<?php echo $categoryFontIcons[$category] ?? 'image'; ?>"></i>
                        <?php echo $categoryNames[$category] ?? ucfirst($category); ?>
                    </button>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- View Options -->
                <div class="view-options">
                    <button class="view-btn active" data-view="4" title="Grid View">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn" data-view="2" title="Large Grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="view-btn" data-view="1" title="List View">
                        <i class="fas fa-list"></i>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search images...">
                    <i class="fas fa-times clear-search" onclick="clearSearch()"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-page">
        <div class="container">
            <!-- Source Indicator (only shown in development) -->
            <?php if(isset($_GET['debug']) || $sourceType == 'fallback'): ?>
            <div style="text-align: center; margin-bottom: 20px; padding: 10px; background: var(--accent-green-light); border-radius: 40px; color: var(--accent-green); font-size: 13px;">
                <i class="fas fa-<?php echo $sourceType == 'database' ? 'database' : 'image'; ?>"></i>
                Currently showing: <strong><?php echo $sourceType == 'database' ? 'Database Images' : 'Sample Gallery Images'; ?></strong>
                <?php if($sourceType == 'fallback'): ?>
                <span style="margin-left: 10px;">(Add images in admin panel to customize)</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="gallery-grid grid-4" id="galleryGrid">
                <?php foreach($galleryImages as $index => $image): 
                    $category = $image['category'] ?? 'farm';
                    // Skip products category entirely
                    if($category === 'products') continue;
                    
                    $title = $image['title_' . $_SESSION['lang']] ?? $image['title_en'];
                    $featured = $image['featured'] ?? 0;
                ?>
                <div class="gallery-item <?php echo $featured ? 'featured' : ''; ?>" 
                     style="--item-index: <?php echo $index; ?>"
                     data-category="<?php echo $category; ?>"
                     data-title="<?php echo strtolower($title); ?>"
                     data-src="assets/images/gallery/<?php echo $image['image']; ?>"
                     data-sub-html=".caption-<?php echo $index; ?>">
                    
                    <img src="assets/images/gallery/<?php echo $image['image']; ?>" 
                         alt="<?php echo $title; ?>"
                         loading="lazy"
                         onerror="this.src='assets/images/placeholder.jpg'; this.onerror=null;">
                    
                    <?php if($featured): ?>
                        <span class="gallery-badge"><i class="fas fa-star"></i> Featured</span>
                    <?php endif; ?>
                    
                    <span class="gallery-category">
                        <i class="fas fa-<?php echo $categoryFontIcons[$category] ?? 'image'; ?>"></i>
                        <?php echo $categoryNames[$category] ?? ucfirst($category); ?>
                    </span>
                    
                    <div class="gallery-overlay">
                        <h3><?php echo $title; ?></h3>
                        <p><?php echo $image['description'] ?? 'Click to enlarge'; ?></p>
                    </div>
                    
                    <!-- Hidden caption for lightbox -->
                    <div style="display:none" class="caption-<?php echo $index; ?>">
                        <h4><?php echo $title; ?></h4>
                        <p><?php echo $image['description'] ?? 'Anefarm - Pure, Fresh, Local'; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button (hidden by default, shown via JS if needed) -->
            <div class="load-more" id="loadMoreContainer" style="display: none;">
                <button class="load-more-btn" id="loadMoreBtn">
                    <i class="fas fa-sync-alt"></i>
                    Load More Photos
                </button>
            </div>

            <!-- Empty State (hidden by default) -->
            <div class="empty-gallery" id="emptyGallery" style="display: none;">
                <i class="fas fa-images"></i>
                <h3>No Images Found</h3>
                <p>We couldn't find any images matching your search. Try different keywords or browse our categories.</p>
                <div class="suggestions">
                    <span class="suggestion-tag" onclick="setSearch('cows')">🐄 Cows</span>
                    <span class="suggestion-tag" onclick="setSearch('farm')">🏡 Farm</span>
                    <span class="suggestion-tag" onclick="setSearch('milk')">🥛 Milk</span>
                    <span class="suggestion-tag" onclick="setSearch('team')">👥 Team</span>
                </div>
            </div>

            <!-- Gallery Stats -->
            <div class="gallery-stats">
                <div class="stat-item" data-stat="all">
                    <div class="stat-number" id="totalPhotos"><?php echo $totalImages; ?></div>
                    <div class="stat-label">Total Photos</div>
                </div>
                <div class="stat-item" data-stat="visible">
                    <div class="stat-number" id="visiblePhotos"><?php echo $totalImages; ?></div>
                    <div class="stat-label">Currently Showing</div>
                </div>
                <?php foreach($categories as $category): 
                    if(!in_array($category, $validCategories)) continue;
                    $count = count(array_filter($galleryImages, function($img) use ($category) {
                        return ($img['category'] ?? 'farm') == $category;
                    }));
                ?>
                <div class="stat-item category-stat" data-category="<?php echo $category; ?>">
                    <div class="stat-number"><?php echo $count; ?></div>
                    <div class="stat-label"><?php echo $categoryNames[$category] ?? ucfirst($category); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/251912345678?text=<?php echo urlencode($lang['whatsapp_text'] ?? 'Hi Anefarm, I love your gallery!'); ?>" 
       class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/lightgallery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/zoom/lg-zoom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    
    <script>
        // ============================================
        // GALLERY INTERACTIVITY - FIXED VERSION
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Gallery initialized');
            initLightGallery();
            initCategoryFilters();
            initSearch();
            initViewOptions();
            initLoadMore();
            initKeyboardShortcuts();
            updateVisibleCount();
        });

        // Initialize LightGallery
        function initLightGallery() {
            const galleryGrid = document.getElementById('galleryGrid');
            if(galleryGrid && typeof lightGallery !== 'undefined') {
                try {
                    lightGallery(galleryGrid, {
                        plugins: [lgZoom, lgThumbnail],
                        speed: 500,
                        backdropDuration: 300,
                        thumbnail: true,
                        animateThumb: true,
                        zoomFromOrigin: true,
                        allowMediaOverlap: true,
                        toggleThumb: true,
                        selector: '.gallery-item'
                    });
                    console.log('LightGallery initialized');
                } catch(e) {
                    console.error('LightGallery error:', e);
                }
            }
        }

        // Category Filtering - FIXED VERSION
        function initCategoryFilters() {
            const categoryPills = document.querySelectorAll('.category-pill');
            const galleryItems = document.querySelectorAll('.gallery-item');
            const categoryStats = document.querySelectorAll('.category-stat');
            const allStat = document.querySelector('[data-stat="all"]');
            const visibleStat = document.getElementById('visiblePhotos');
            
            if(!categoryPills.length) {
                console.error('No category pills found');
                return;
            }
            
            console.log('Found', categoryPills.length, 'category pills');
            
            categoryPills.forEach(pill => {
                pill.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Category clicked:', this.dataset.category);
                    
                    // Update active pill
                    categoryPills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    
                    const category = this.dataset.category;
                    
                    // Filter items
                    let visibleCount = 0;
                    
                    galleryItems.forEach((item, index) => {
                        const itemCategory = item.dataset.category;
                        
                        if(category === 'all' || itemCategory === category) {
                            // Show item with animation
                            item.style.display = 'block';
                            setTimeout(() => {
                                item.style.opacity = '1';
                                item.style.transform = 'translateY(0) scale(1)';
                            }, index * 30);
                            visibleCount++;
                        } else {
                            // Hide item
                            item.style.opacity = '0';
                            item.style.transform = 'translateY(20px) scale(0.8)';
                            setTimeout(() => {
                                item.style.display = 'none';
                            }, 300);
                        }
                    });
                    
                    // Update visible count
                    if(visibleStat) {
                        visibleStat.textContent = visibleCount;
                    }
                    
                    // Update header total if needed
                    const headerTotal = document.getElementById('totalPhotosHeader');
                    if(headerTotal) {
                        headerTotal.innerHTML = visibleCount + ' Photo' + (visibleCount !== 1 ? 's' : '');
                    }
                    
                    // Highlight category in stats
                    if(allStat) {
                        if(category === 'all') {
                            allStat.classList.add('highlight');
                        } else {
                            allStat.classList.remove('highlight');
                        }
                    }
                    
                    categoryStats.forEach(stat => {
                        if(stat.dataset.category === category) {
                            stat.classList.add('highlight');
                        } else {
                            stat.classList.remove('highlight');
                        }
                    });
                    
                    // Show/hide empty state
                    const emptyGallery = document.getElementById('emptyGallery');
                    if(emptyGallery) {
                        if(visibleCount === 0) {
                            emptyGallery.style.display = 'block';
                        } else {
                            emptyGallery.style.display = 'none';
                        }
                    }
                    
                    console.log('Visible items:', visibleCount);
                });
            });
        }

        // Search functionality - FIXED VERSION
        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            const galleryItems = document.querySelectorAll('.gallery-item');
            const clearBtn = document.querySelector('.clear-search');
            const searchBar = document.querySelector('.search-bar');
            const visibleStat = document.getElementById('visiblePhotos');
            const emptyGallery = document.getElementById('emptyGallery');
            
            if(!searchInput) {
                console.error('Search input not found');
                return;
            }
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                console.log('Search term:', searchTerm);
                
                // Toggle clear button
                if(searchTerm) {
                    searchBar?.classList.add('has-value');
                } else {
                    searchBar?.classList.remove('has-value');
                }
                
                let visibleCount = 0;
                
                galleryItems.forEach(item => {
                    const title = item.dataset.title || '';
                    
                    if(title.includes(searchTerm) || searchTerm === '') {
                        item.style.display = 'block';
                        visibleCount++;
                        
                        // Highlight matching text if search term exists
                        if(searchTerm) {
                            const titleEl = item.querySelector('.gallery-overlay h3');
                            if(titleEl) {
                                const originalText = titleEl.textContent;
                                const regex = new RegExp(`(${searchTerm})`, 'gi');
                                titleEl.innerHTML = originalText.replace(regex, '<mark style="background: var(--accent-green); color: white; padding: 2px 4px; border-radius: 4px;">$1</mark>');
                            }
                        } else {
                            // Restore original text
                            const titleEl = item.querySelector('.gallery-overlay h3');
                            if(titleEl) {
                                titleEl.innerHTML = titleEl.textContent;
                            }
                        }
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Update visible count
                if(visibleStat) {
                    visibleStat.textContent = visibleCount;
                }
                
                // Show/hide empty state
                if(emptyGallery) {
                    if(visibleCount === 0) {
                        emptyGallery.style.display = 'block';
                    } else {
                        emptyGallery.style.display = 'none';
                    }
                }
                
                console.log('Search results:', visibleCount);
            });
            
            // Clear search on button click
            if(clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input'));
                });
            }
        }

        // Clear search (global function)
        window.clearSearch = function() {
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
            }
        };

        // Set search from suggestion
        window.setSearch = function(term) {
            const searchInput = document.getElementById('searchInput');
            if(searchInput) {
                searchInput.value = term;
                searchInput.dispatchEvent(new Event('input'));
                
                // Switch to all category
                const allPill = document.querySelector('[data-category="all"]');
                if(allPill) {
                    allPill.click();
                }
            }
        };

        // View options (grid size)
        function initViewOptions() {
            const viewBtns = document.querySelectorAll('.view-btn');
            const galleryGrid = document.getElementById('galleryGrid');
            
            if(!viewBtns.length || !galleryGrid) {
                console.error('View options not found');
                return;
            }
            
            viewBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    viewBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const viewSize = this.dataset.view;
                    galleryGrid.className = `gallery-grid grid-${viewSize}`;
                    
                    // Save preference
                    try {
                        localStorage.setItem('galleryView', viewSize);
                    } catch(e) {
                        console.error('Could not save preference:', e);
                    }
                    
                    console.log('View changed to:', viewSize);
                });
            });
            
            // Load saved preference
            try {
                const savedView = localStorage.getItem('galleryView');
                if(savedView) {
                    const btn = document.querySelector(`[data-view="${savedView}"]`);
                    if(btn) btn.click();
                }
            } catch(e) {
                console.error('Could not load saved preference:', e);
            }
        }

        // Load more functionality
        function initLoadMore() {
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadMoreContainer = document.getElementById('loadMoreContainer');
            const galleryItems = document.querySelectorAll('.gallery-item');
            
            if(!loadMoreBtn || !loadMoreContainer) return;
            
            // Show load more if more than 12 items
            if(galleryItems.length > 12) {
                loadMoreContainer.style.display = 'block';
                
                // Hide items beyond 12 initially
                galleryItems.forEach((item, index) => {
                    if(index >= 12) {
                        item.style.display = 'none';
                    }
                });
                
                let currentItems = 12;
                
                loadMoreBtn.addEventListener('click', function() {
                    const itemsToShow = 8; // Show 8 more each time
                    
                    for(let i = currentItems; i < currentItems + itemsToShow && i < galleryItems.length; i++) {
                        galleryItems[i].style.display = 'block';
                    }
                    
                    currentItems += itemsToShow;
                    
                    if(currentItems >= galleryItems.length) {
                        this.disabled = true;
                        this.innerHTML = '<i class="fas fa-check"></i> All Photos Loaded';
                    }
                    
                    updateVisibleCount();
                });
            }
        }

        // Update visible count
        function updateVisibleCount() {
            const galleryItems = document.querySelectorAll('.gallery-item[style="display: block;"]');
            const visibleStat = document.getElementById('visiblePhotos');
            if(visibleStat) {
                visibleStat.textContent = galleryItems.length;
            }
        }

        // Keyboard shortcuts
        function initKeyboardShortcuts() {
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + F to focus search
                if((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    const searchInput = document.getElementById('searchInput');
                    if(searchInput) {
                        searchInput.focus();
                    }
                }
                
                // Escape to clear search
                if(e.key === 'Escape') {
                    clearSearch();
                }
                
                // Number keys 1-5 to switch categories
                if(e.key >= '1' && e.key <= '5') {
                    const index = parseInt(e.key) - 1;
                    const pills = document.querySelectorAll('.category-pill');
                    if(pills[index]) {
                        pills[index].click();
                    }
                }
            });
        }

        // Image lazy loading with blur effect
        const images = document.querySelectorAll('.gallery-item img');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    const img = entry.target;
                    img.style.opacity = '1';
                    observer.unobserve(img);
                }
            });
        }, { threshold: 0.1 });

        images.forEach(img => {
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s';
            imageObserver.observe(img);
        });

        // Image error handling
        images.forEach(img => {
            img.onerror = function() {
                this.src = 'assets/images/placeholder.jpg';
                this.onerror = null;
            };
        });

        // Animate stats on scroll
        const stats = document.querySelectorAll('.stat-number');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    entry.target.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        entry.target.style.transform = 'scale(1)';
                    }, 200);
                }
            });
        });

        stats.forEach(stat => statsObserver.observe(stat));

        console.log('Gallery scripts loaded');
    </script>
</body>
</html>