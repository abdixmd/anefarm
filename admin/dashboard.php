<?php
require_once 'includes/auth.php';
require_once '../includes/config.php';
?>
<?php include 'includes/header.php'; ?>

<div class="dashboard">
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo $_SESSION['admin_username']; ?>! Here's what's happening with AneDiaryFarm today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <?php
        // Get total owners
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM owners");
        $totalOwners = $stmt->fetch()['count'];
        
        // Get total products
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $totalProducts = $stmt->fetch()['count'];
        
        // Get total gallery images
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM gallery");
        $totalGallery = $stmt->fetch()['count'];
        
        // Get featured gallery images
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM gallery WHERE featured = 1");
        $featuredGallery = $stmt->fetch()['count'];
        
        // Get gallery by category
        $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM gallery GROUP BY category");
        $galleryCategories = $stmt->fetchAll();
        
        // Get unread messages
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM messages WHERE is_read = FALSE");
        $unreadMessages = $stmt->fetch()['count'];
        
        // Get recent uploads (last 7 days)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM gallery WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $recentUploads = $stmt->fetch()['count'];
        ?>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #00a400, #008000);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalOwners; ?></h3>
                <p>Total Owners</p>
                <small>5 owners configured</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2196F3, #1976D2);">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalProducts; ?></h3>
                <p>Products</p>
                <small>Available for sale</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalGallery; ?></h3>
                <p>Gallery Images</p>
                <small><?php echo $recentUploads; ?> new this week</small>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f44336, #d32f2f);">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $unreadMessages; ?></h3>
                <p>Unread Messages</p>
                <small>Awaiting response</small>
            </div>
        </div>
    </div>

    <!-- Gallery Stats Section (New) -->
<!-- Gallery Stats Section - Enhanced for unlimited -->
<div class="gallery-stats-section">
    <div class="section-header">
        <h2><i class="fas fa-chart-pie"></i> Gallery Overview</h2>
        <a href="gallery.php" class="view-all">
            <i class="fas fa-images"></i> Manage Gallery
        </a>
    </div>
    
    <div class="gallery-stats-grid">
        <!-- Total Images Card -->
        <div class="gallery-stat-card">
            <div class="gallery-stat-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="gallery-stat-content">
                <h3><?php echo $totalGallery; ?></h3>
                <p>Total Images</p>
                <small>Unlimited uploads</small>
            </div>
        </div>
        
        <!-- Featured Images Card -->
        <div class="gallery-stat-card">
            <div class="gallery-stat-icon" style="background: linear-gradient(135deg, #FFD700, #FFA500);">
                <i class="fas fa-star"></i>
            </div>
            <div class="gallery-stat-content">
                <h3><?php echo $featuredGallery; ?></h3>
                <p>Featured Images</p>
                <small><?php echo $totalGallery > 0 ? round(($featuredGallery / $totalGallery) * 100, 1) : 0; ?>% of gallery</small>
            </div>
        </div>
        
        <!-- Categories Card -->
        <div class="gallery-stat-card">
            <div class="gallery-stat-icon" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2);">
                <i class="fas fa-tags"></i>
            </div>
            <div class="gallery-stat-content">
                <h3><?php echo count($galleryCategories); ?></h3>
                <p>Categories</p>
                <small>
                    <?php 
                    $categoryNames = array_column($galleryCategories, 'category');
                    echo implode(', ', array_slice($categoryNames, 0, 3));
                    if(count($categoryNames) > 3) echo '...';
                    ?>
                </small>
            </div>
        </div>
        
        <!-- Storage Card - Enhanced -->
        <div class="gallery-stat-card">
            <div class="gallery-stat-icon" style="background: linear-gradient(135deg, #00BCD4, #0097A7);">
                <i class="fas fa-database"></i>
            </div>
            <div class="gallery-stat-content">
                <?php
                // Calculate total storage
                $totalSize = 0;
                $fileCount = 0;
                $stmt = $pdo->query("SELECT image FROM gallery");
                while($row = $stmt->fetch()) {
                    $file = "../assets/images/gallery/" . $row['image'];
                    if(file_exists($file)) {
                        $totalSize += filesize($file);
                        $fileCount++;
                    }
                }
                $sizeMB = round($totalSize / (1024 * 1024), 1);
                $sizeGB = round($sizeMB / 1024, 2);
                ?>
                <h3><?php echo $sizeMB >= 1024 ? $sizeGB . ' GB' : $sizeMB . ' MB'; ?></h3>
                <p>Storage Used</p>
                <small><?php echo $fileCount > 0 ? round($sizeMB / $fileCount, 1) : 0; ?> MB avg per image</small>
            </div>
        </div>
    </div>
    
    <!-- Category Distribution Chart -->
    <div class="category-distribution">
        <h3>Images by Category</h3>
        <div class="category-bars">
            <?php
            $categoryColors = [
                'farm' => '#00a400',
                'cows' => '#2196F3',
                'process' => '#FF9800',
                'products' => '#9C27B0',
                'team' => '#f44336',
                'delivery' => '#00BCD4'
            ];
            
            foreach($galleryCategories as $cat):
                $category = $cat['category'] ?: 'uncategorized';
                $count = $cat['count'];
                $percentage = $totalGallery > 0 ? round($count / $totalGallery * 100) : 0;
                $color = $categoryColors[$category] ?? '#999';
            ?>
            <div class="category-bar-item">
                <div class="category-label">
                    <span class="category-name">
                        <i class="fas fa-<?php 
                            echo $category == 'farm' ? 'tractor' : 
                                ($category == 'cows' ? 'paw' : 
                                ($category == 'process' ? 'cogs' : 
                                ($category == 'products' ? 'box' : 
                                ($category == 'team' ? 'users' : 'truck')))); 
                        ?>"></i>
                        <?php echo ucfirst($category); ?>
                    </span>
                    <span class="category-count"><?php echo $count; ?> images (<?php echo $percentage; ?>%)</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?php echo $percentage; ?>%; background-color: <?php echo $color; ?>;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Gallery Uploads -->
<div class="recent-uploads">
    <div class="section-header">
        <h2><i class="fas fa-clock"></i> Recent Gallery Uploads</h2>
        <a href="gallery.php" class="view-all">View All</a>
    </div>
    
    <div class="uploads-grid">
        <?php
        // Updated query to include descriptions
        $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6");
        while($image = $stmt->fetch()):
        ?>
        <div class="upload-item">
            <div class="upload-thumbnail">
                <img src="../assets/images/gallery/<?php echo $image['image']; ?>" alt="<?php echo $image['title_en']; ?>">
                <?php if($image['featured']): ?>
                    <span class="featured-badge"><i class="fas fa-star"></i></span>
                <?php endif; ?>
            </div>
            <div class="upload-info">
                <h4><?php echo substr($image['title_en'], 0, 20) . (strlen($image['title_en']) > 20 ? '...' : ''); ?></h4>
                <?php if(!empty($image['description_en'])): ?>
                <p class="upload-description" style="font-size: 11px; color: #999; margin-bottom: 8px;">
                    <?php echo substr($image['description_en'], 0, 30) . (strlen($image['description_en']) > 30 ? '...' : ''); ?>
                </p>
                <?php endif; ?>
                <div class="upload-meta">
                    <span><i class="fas fa-tag"></i> <?php echo ucfirst($image['category'] ?: 'uncategorized'); ?></span>
                    <span><i class="fas fa-clock"></i> <?php echo date('M d', strtotime($image['created_at'])); ?></span>
                </div>
                <div class="upload-actions">
                    <a href="gallery.php?edit=<?php echo $image['id']; ?>" class="btn-icon" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="gallery.php?delete=<?php echo $image['id']; ?>" class="btn-icon delete" onclick="return confirm('Delete this image?')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if($totalGallery == 0): ?>
        <div class="no-uploads">
            <i class="fas fa-cloud-upload-alt"></i>
            <p>No images yet</p>
            <a href="gallery.php" class="btn btn-primary">Upload First Image</a>
        </div>
        <?php endif; ?>
    </div>
</div>

    <!-- Quick Actions with Gallery Link -->
    <div class="quick-actions">
        <div class="section-header">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
        </div>
        
        <div class="actions-grid">
            <a href="owners.php?action=add" class="action-card">
                <i class="fas fa-user-plus"></i>
                <h3>Add New Owner</h3>
                <p>Add one of the 5 farm owners</p>
            </a>
            
            <a href="products.php?action=add" class="action-card">
                <i class="fas fa-plus-circle"></i>
                <h3>Add Product</h3>
                <p>Add new dairy product</p>
            </a>
            
            <a href="gallery.php" class="action-card highlight">
                <i class="fas fa-images"></i>
                <h3>Manage Gallery</h3>
                <p><?php echo $totalGallery; ?> images • <span style="color: var(--accent-green);">+<?php echo $recentUploads; ?> new</span></p>
            </a>
            
            <a href="#" class="action-card">
                <i class="fas fa-cog"></i>
                <h3>Settings</h3>
                <p>Configure website</p>
            </a>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="recent-section">
        <div class="section-header">
            <h2><i class="fas fa-envelope-open-text"></i> Recent Messages</h2>
            <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="messages-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
                    while($message = $stmt->fetch()) {
                        $date = new DateTime($message['created_at']);
                        $now = new DateTime();
                        $interval = $date->diff($now);
                        
                        if($interval->days == 0) {
                            if($interval->h == 0) {
                                $timeAgo = $interval->i . ' minutes ago';
                            } else {
                                $timeAgo = $interval->h . ' hours ago';
                            }
                        } else if($interval->days == 1) {
                            $timeAgo = 'Yesterday';
                        } else {
                            $timeAgo = $interval->days . ' days ago';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['phone']); ?></td>
                        <td><?php echo htmlspecialchars($message['email'] ?: '-'); ?></td>
                        <td class="message-preview"><?php echo substr(htmlspecialchars($message['message']), 0, 50); ?>...</td>
                        <td><?php echo $timeAgo; ?></td>
                        <td>
                            <?php if($message['is_read']): ?>
                                <span class="badge success">Read</span>
                            <?php else: ?>
                                <span class="badge warning">New</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Dashboard Styles */
.dashboard {
    padding: 30px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.page-header p {
    color: var(--text-secondary);
    font-size: 14px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 20px var(--shadow-color);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--shadow-hover);
}

.stat-icon {
    width: 70px;
    height: 70px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 30px;
}

.stat-content h3 {
    font-size: 28px;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.stat-content p {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 3px;
}

.stat-content small {
    color: var(--text-secondary);
    font-size: 12px;
}

/* Gallery Stats Section */
.gallery-stats-section {
    background: var(--card-bg);
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 5px 20px var(--shadow-color);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-header h2 {
    font-size: 20px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h2 i {
    color: #00a400;
}

.view-all {
    color: var(--accent-green);
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 16px;
    background: var(--accent-green-light);
    border-radius: 20px;
    transition: all 0.3s;
}

.view-all:hover {
    background: var(--accent-green);
    color: white;
}

.gallery-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.gallery-stat-card {
    background: var(--bg-tertiary);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.gallery-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px var(--shadow-hover);
}

.gallery-stat-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #00a400, #008000);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.gallery-stat-content h3 {
    font-size: 24px;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.gallery-stat-content p {
    color: var(--text-secondary);
    font-size: 13px;
    margin-bottom: 5px;
}

.gallery-stat-content small {
    color: var(--text-secondary);
    font-size: 11px;
}

.stat-progress {
    width: 100px;
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: #00a400;
    border-radius: 2px;
}

/* Category Distribution */
.category-distribution {
    background: var(--bg-tertiary);
    border-radius: 16px;
    padding: 20px;
}

.category-distribution h3 {
    font-size: 16px;
    color: var(--text-primary);
    margin-bottom: 20px;
}

.category-bars {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.category-bar-item {
    width: 100%;
}

.category-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 13px;
}

.category-name {
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 5px;
}

.category-name i {
    color: #00a400;
    font-size: 12px;
}

.category-count {
    color: var(--text-primary);
    font-weight: 500;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background: var(--border-light);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Recent Uploads */
.recent-uploads {
    background: var(--card-bg);
    border-radius: 24px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 5px 20px var(--shadow-color);
}

.uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.upload-item {
    background: var(--bg-tertiary);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s;
}

.upload-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px var(--shadow-hover);
}

.upload-thumbnail {
    position: relative;
    height: 140px;
    overflow: hidden;
}

.upload-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.upload-item:hover .upload-thumbnail img {
    transform: scale(1.1);
}

.featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #FFD700;
    color: #333;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.upload-info {
    padding: 15px;
}

.upload-info h4 {
    font-size: 14px;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.upload-meta {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: var(--text-secondary);
    margin-bottom: 12px;
}

.upload-meta span {
    display: flex;
    align-items: center;
    gap: 3px;
}

.upload-actions {
    display: flex;
    gap: 8px;
}

.upload-actions .btn-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s;
}

.upload-actions .btn-icon:hover {
    background: var(--accent-green);
    color: white;
}

.upload-actions .btn-icon.delete:hover {
    background: #f44336;
}

.no-uploads {
    grid-column: 1/-1;
    text-align: center;
    padding: 40px;
    color: var(--text-secondary);
}

.no-uploads i {
    font-size: 48px;
    margin-bottom: 15px;
    color: var(--border-light);
}

.no-uploads p {
    margin-bottom: 20px;
}

/* Quick Actions */
.quick-actions {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 40px;
    box-shadow: 0 5px 20px var(--shadow-color);
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.action-card {
    background: var(--bg-tertiary);
    border-radius: 15px;
    padding: 25px 20px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s;
}

.action-card:hover {
    background: var(--accent-green);
    transform: translateY(-5px);
}

.action-card:hover i,
.action-card:hover h3,
.action-card:hover p {
    color: white;
}

.action-card.highlight {
    background: var(--accent-green-light);
    border: 2px solid var(--accent-green);
}

.action-card.highlight:hover {
    background: var(--accent-green);
}

.action-card i {
    font-size: 30px;
    color: var(--accent-green);
    margin-bottom: 15px;
    transition: color 0.3s;
}

.action-card h3 {
    color: var(--text-primary);
    font-size: 16px;
    margin-bottom: 5px;
    transition: color 0.3s;
}

.action-card p {
    color: var(--text-secondary);
    font-size: 12px;
    transition: color 0.3s;
}

/* Recent Messages */
.recent-section {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 40px;
    box-shadow: 0 5px 20px var(--shadow-color);
}

.messages-table {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    text-align: left;
    padding: 15px 10px;
    color: var(--text-secondary);
    font-weight: 500;
    font-size: 14px;
    border-bottom: 2px solid var(--border-light);
}

td {
    padding: 15px 10px;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-primary);
    font-size: 14px;
}

.message-preview {
    color: var(--text-secondary);
    max-width: 250px;
}

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge.success {
    background: var(--accent-green-light);
    color: var(--accent-green);
}

.badge.warning {
    background: #fff3e0;
    color: #f57c00;
}

/* Responsive */
@media (max-width: 1024px) {
    .gallery-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard {
        padding: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .gallery-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .uploads-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>