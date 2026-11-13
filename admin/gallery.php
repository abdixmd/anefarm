<?php
require_once 'includes/auth.php';
require_once '../includes/config.php';

// Remove the image limit - no MAX_GALLERY_IMAGES constant

// Handle Add/Edit/Delete operations
$message = '';
$messageType = '';

// Delete gallery image
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image filename before deleting
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $gallery = $stmt->fetch();
    
    if($gallery && $gallery['image'] && file_exists("../assets/images/gallery/" . $gallery['image'])) {
        unlink("../assets/images/gallery/" . $gallery['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Gallery image deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting image.";
        $messageType = "error";
    }
}

// Handle bulk delete
if(isset($_POST['bulk_delete']) && isset($_POST['selected_ids'])) {
    $ids = $_POST['selected_ids'];
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // Get images before deleting
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $images = $stmt->fetchAll();
    
    // Delete image files
    foreach($images as $image) {
        if($image['image'] && file_exists("../assets/images/gallery/" . $image['image'])) {
            unlink("../assets/images/gallery/" . $image['image']);
        }
    }
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM gallery WHERE id IN ($placeholders)");
    if($stmt->execute($ids)) {
        $message = count($ids) . " images deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting images.";
        $messageType = "error";
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        $title_en = $_POST['title_en'];
        $title_om = $_POST['title_om'];
        $title_am = $_POST['title_am'];
        $description_en = $_POST['description_en'] ?? '';
        $description_om = $_POST['description_om'] ?? '';
        $description_am = $_POST['description_am'] ?? '';
        $category = $_POST['category'];
        $sort_order = $_POST['sort_order'];
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filesize = $_FILES['image']['size'];
            
            // Validate file type
            if(!in_array($ext, $allowed)) {
                $message = "Invalid file type. Allowed: JPG, PNG, GIF, WEBP";
                $messageType = "error";
            }
            // Validate file size (max 10MB - increased for unlimited uploads)
            else if($filesize > 10 * 1024 * 1024) {
                $message = "File too large. Maximum size: 10MB";
                $messageType = "error";
            }
            else {
                $newFilename = time() . '_' . uniqid() . '.' . $ext;
                $uploadPath = "../assets/images/gallery/" . $newFilename;
                
                // Optimize image before upload
                if($ext == 'jpg' || $ext == 'jpeg') {
                    $imageResource = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    imagejpeg($imageResource, $uploadPath, 85); // Compress JPEG
                    imagedestroy($imageResource);
                } else {
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
                }
                
                $image = $newFilename;
            }
        }
        
        if($_POST['action'] === 'add') {
            // No image limit check - unlimited uploads
            if(!$image) {
                $message = "Please select an image to upload.";
                $messageType = "error";
            } else {
                $stmt = $pdo->prepare("INSERT INTO gallery (title_en, title_om, title_am, description_en, description_om, description_am, image, category, sort_order, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if($stmt->execute([$title_en, $title_om, $title_am, $description_en, $description_om, $description_am, $image, $category, $sort_order, $featured])) {
                    $message = "Gallery image added successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error adding image.";
                    $messageType = "error";
                }
            }
        } elseif($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            // Get current image
            $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            $currentImage = $current['image'];
            
            // Use new image if uploaded, otherwise keep existing
            $finalImage = $image ?: $currentImage;
            
            $stmt = $pdo->prepare("UPDATE gallery SET title_en=?, title_om=?, title_am=?, description_en=?, description_om=?, description_am=?, image=?, category=?, sort_order=?, featured=? WHERE id=?");
            if($stmt->execute([$title_en, $title_om, $title_am, $description_en, $description_om, $description_am, $finalImage, $category, $sort_order, $featured, $id])) {
                // Delete old image if new one uploaded
                if($image && $currentImage && file_exists("../assets/images/gallery/" . $currentImage)) {
                    unlink("../assets/images/gallery/" . $currentImage);
                }
                $message = "Gallery image updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating image.";
                $messageType = "error";
            }
        }
    }
}

// Get all gallery images
$stmt = $pdo->query("SELECT * FROM gallery ORDER BY featured DESC, sort_order ASC, created_at DESC");
$galleryImages = $stmt->fetchAll();

// Get total count for stats
$totalImages = count($galleryImages);

// Get image for editing if ID is provided
$editImage = null;
if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editImage = $stmt->fetch();
}

// Get categories for filter
$categories = ['farm', 'cows', 'process', 'products', 'team', 'delivery'];

// Calculate total storage used
$totalStorage = 0;
$stmt = $pdo->query("SELECT image FROM gallery");
while($row = $stmt->fetch()) {
    $file = "../assets/images/gallery/" . $row['image'];
    if(file_exists($file)) {
        $totalStorage += filesize($file);
    }
}
$totalStorageMB = round($totalStorage / (1024 * 1024), 2);
?>
<?php include 'includes/header.php'; ?>

<style>
/* Gallery Management Specific Styles */
.gallery-management {
    padding: 30px;
}

.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
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

.header-actions {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: var(--accent-green);
    color: white;
}

.btn-primary:hover {
    background: var(--accent-green-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px var(--accent-green-soft);
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-light);
}

.btn-secondary:hover {
    background: var(--bg-tertiary);
    transform: translateY(-2px);
}

.btn-danger {
    background: #f44336;
    color: white;
}

.btn-danger:hover {
    background: #d32f2f;
    transform: translateY(-2px);
}

/* Alert Messages */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    animation: slideDown 0.3s ease;
}

.alert-success {
    background: #e8f5e8;
    color: #00a400;
    border-left: 4px solid #00a400;
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    border-left: 4px solid #c62828;
}

.alert-warning {
    background: #fff3e0;
    color: #f57c00;
    border-left: 4px solid #f57c00;
}

.alert-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: inherit;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.alert-close:hover {
    opacity: 1;
}

/* Gallery Stats - Enhanced for unlimited */
.gallery-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--card-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px var(--shadow-hover);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--accent-green-light);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon i {
    font-size: 24px;
    color: var(--accent-green);
}

.stat-content h3 {
    font-size: 24px;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.stat-content p {
    color: var(--text-secondary);
    font-size: 13px;
}

.stat-content small {
    color: var(--text-secondary);
    font-size: 11px;
    opacity: 0.8;
}

/* Storage Indicator */
.storage-indicator {
    background: linear-gradient(135deg, var(--accent-green-light), var(--accent-green));
    border-radius: 16px;
    padding: 20px 25px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    color: white;
}

.storage-info {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.storage-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.storage-details h4 {
    font-size: 18px;
    margin-bottom: 5px;
    font-weight: 600;
}

.storage-details p {
    font-size: 14px;
    opacity: 0.9;
}

.storage-badge {
    background: rgba(255,255,255,0.2);
    padding: 12px 25px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 10px 30px var(--shadow-color);
    margin-bottom: 30px;
}

.form-card h2 {
    font-size: 20px;
    margin-bottom: 25px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card h2 i {
    color: var(--accent-green);
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-light);
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: var(--bg-primary);
}

.form-group textarea {
    min-height: 80px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--accent-green);
    box-shadow: 0 0 0 4px var(--accent-green-soft);
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-group label {
    color: var(--text-primary);
    font-size: 14px;
    cursor: pointer;
}

.current-image {
    margin-bottom: 15px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    display: inline-block;
}

.current-image img {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    border: 3px solid var(--accent-green);
}

.current-image .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    border-radius: 12px;
}

.current-image:hover .image-overlay {
    opacity: 1;
}

.current-image .image-overlay span {
    color: white;
    font-size: 12px;
    background: rgba(0,0,0,0.7);
    padding: 5px 10px;
    border-radius: 20px;
}

/* Gallery Grid */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.gallery-item {
    background: var(--card-bg);
    border: 1px solid var(--border-light);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px var(--shadow-hover);
}

.gallery-item.featured {
    border: 3px solid var(--accent-green);
}

.gallery-image {
    height: 200px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.gallery-item:hover .gallery-image img {
    transform: scale(1.1);
}

.gallery-image .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-item:hover .image-overlay {
    opacity: 1;
}

.gallery-image .image-overlay i {
    color: white;
    font-size: 30px;
    transform: scale(0);
    transition: transform 0.3s;
}

.gallery-item:hover .image-overlay i {
    transform: scale(1);
}

.gallery-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    z-index: 2;
}

.badge-featured {
    background: var(--accent-green);
    color: white;
}

.badge-category {
    background: rgba(0,0,0,0.7);
    color: white;
    left: 10px;
    right: auto;
}

.gallery-info {
    padding: 20px;
}

.gallery-info h3 {
    font-size: 16px;
    margin-bottom: 8px;
    color: var(--text-primary);
}

.gallery-description {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 12px;
    line-height: 1.5;
    max-height: 60px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.gallery-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 12px;
    color: var(--text-secondary);
}

.gallery-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.gallery-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid var(--border-light);
    padding-top: 15px;
}

.gallery-actions .btn-icon {
    flex: 1;
    padding: 8px;
    border: none;
    border-radius: 8px;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 13px;
}

.gallery-actions .btn-icon:hover {
    background: var(--accent-green);
    color: white;
}

.gallery-actions .btn-icon.delete:hover {
    background: #f44336;
}

/* Bulk Actions */
.bulk-actions-bar {
    background: var(--card-bg);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    animation: slideDown 0.3s ease;
}

.bulk-select-all {
    display: flex;
    align-items: center;
    gap: 10px;
}

.bulk-select-all input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.bulk-actions-buttons {
    display: flex;
    gap: 10px;
}

/* Filter Bar */
.filter-bar {
    background: var(--card-bg);
    border: 1px solid var(--border-light);
    border-radius: 16px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 200px;
}

.filter-group i {
    color: var(--text-secondary);
}

.filter-group select,
.filter-group input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.filter-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    background: var(--accent-green-light);
    color: var(--accent-green);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
}

.filter-badge i {
    cursor: pointer;
}

/* Quick Preview Modal */
.preview-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s;
}

.preview-modal.active {
    opacity: 1;
    visibility: visible;
}

.preview-content {
    max-width: 90%;
    max-height: 90%;
    position: relative;
}

.preview-content img {
    max-width: 100%;
    max-height: 90vh;
    border-radius: 12px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.5);
}

.preview-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 30px;
    cursor: pointer;
    background: none;
    border: none;
}

.preview-info {
    position: absolute;
    bottom: -60px;
    left: 0;
    color: white;
    font-size: 14px;
    max-width: 100%;
}

.preview-description {
    position: absolute;
    bottom: -80px;
    left: 0;
    color: #ccc;
    font-size: 13px;
    max-width: 100%;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive */
@media (max-width: 768px) {
    .gallery-management {
        padding: 20px;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .gallery-grid {
        grid-template-columns: 1fr;
    }
    
    .bulk-actions-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .bulk-actions-buttons {
        width: 100%;
        flex-direction: column;
    }
    
    .filter-bar {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .storage-indicator {
        flex-direction: column;
        text-align: center;
    }
    
    .storage-info {
        justify-content: center;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .form-card,
    .bulk-actions-bar,
    .filter-bar {
        background: #1c1c1e;
        border-color: #38383a;
    }
    
    .gallery-item {
        background: #1c1c1e;
    }
    
    .gallery-actions .btn-icon {
        background: #2c2c2e;
        color: #98989e;
    }
}

/* Loading Skeleton */
.skeleton {
    background: linear-gradient(90deg, var(--bg-secondary) 25%, var(--bg-tertiary) 50%, var(--bg-secondary) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<div class="gallery-management">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1><i class="fas fa-images" style="color: var(--accent-green); margin-right: 10px;"></i> Gallery Management</h1>
            <p>Manage your farm gallery images - unlimited uploads, organize, and showcase your farm life</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="document.getElementById('addForm').scrollIntoView({behavior: 'smooth'})">
                <i class="fas fa-plus-circle"></i> Add New Image
            </button>
            <button class="btn btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" id="alertMessage">
            <div>
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Storage Usage Indicator (New) -->
    <div class="storage-indicator">
        <div class="storage-info">
            <div class="storage-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="storage-details">
                <h4>Storage Usage</h4>
                <p>Total storage used: <?php echo $totalStorageMB; ?> MB across <?php echo $totalImages; ?> images</p>
            </div>
        </div>
        <div class="storage-badge">
            <i class="fas fa-infinity"></i> Unlimited Uploads
        </div>
    </div>

    <!-- Gallery Stats - Updated for unlimited -->
    <div class="gallery-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalImages; ?></h3>
                <p>Total Images</p>
                <small>Unlimited storage</small>
            </div>
        </div>
        
        <?php
        $categoryCounts = [];
        foreach($galleryImages as $img) {
            $cat = $img['category'] ?? 'uncategorized';
            $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo array_sum($categoryCounts); ?></h3>
                <p>In Gallery</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($categoryCounts); ?></h3>
                <p>Categories</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-weight-hanging"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $totalStorageMB; ?> MB</h3>
                <p>Total Size</p>
                <small>Avg: <?php echo $totalImages > 0 ? round($totalStorageMB / $totalImages, 2) : 0; ?> MB/image</small>
            </div>
        </div>
    </div>

    <!-- Add/Edit Form Card - No limit checks -->
    <div class="form-card" id="addForm">
        <h2>
            <i class="fas fa-<?php echo $editImage ? 'edit' : 'plus-circle'; ?>"></i>
            <?php echo $editImage ? 'Edit Gallery Image' : 'Add New Gallery Image'; ?>
        </h2>
        
        <form method="POST" enctype="multipart/form-data" id="galleryForm">
            <input type="hidden" name="action" value="<?php echo $editImage ? 'edit' : 'add'; ?>">
            <?php if($editImage): ?>
                <input type="hidden" name="id" value="<?php echo $editImage['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Title (English) *</label>
                    <input type="text" name="title_en" required value="<?php echo $editImage['title_en'] ?? ''; ?>" placeholder="e.g., Happy Cows Grazing">
                </div>
                
                <div class="form-group">
                    <label>Title (Oromo)</label>
                    <input type="text" name="title_om" value="<?php echo $editImage['title_om'] ?? ''; ?>" placeholder="Afaan Oromoo">
                </div>
                
                <div class="form-group">
                    <label>Title (Amharic)</label>
                    <input type="text" name="title_am" value="<?php echo $editImage['title_am'] ?? ''; ?>" placeholder="አማርኛ">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Description (English)</label>
                    <textarea name="description_en" placeholder="Describe this image..."><?php echo $editImage['description_en'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Description (Oromo)</label>
                    <textarea name="description_om" placeholder="Ibsa Afaan Oromootiin..."><?php echo $editImage['description_om'] ?? ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Description (Amharic)</label>
                    <textarea name="description_am" placeholder="በአማርኛ መግለጫ..."><?php echo $editImage['description_am'] ?? ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category" required>
                        <option value="">Select Category</option>
                        <option value="farm" <?php echo ($editImage['category'] ?? '') == 'farm' ? 'selected' : ''; ?>>🏡 Farm Life</option>
                        <option value="cows" <?php echo ($editImage['category'] ?? '') == 'cows' ? 'selected' : ''; ?>>🐄 Our Cows</option>
                        <option value="process" <?php echo ($editImage['category'] ?? '') == 'process' ? 'selected' : ''; ?>>⚙️ Milking Process</option>
                        <option value="products" <?php echo ($editImage['category'] ?? '') == 'products' ? 'selected' : ''; ?>>🥛 Products</option>
                        <option value="team" <?php echo ($editImage['category'] ?? '') == 'team' ? 'selected' : ''; ?>>👥 Our Team</option>
                        <option value="delivery" <?php echo ($editImage['category'] ?? '') == 'delivery' ? 'selected' : ''; ?>>🚚 Delivery</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="<?php echo $editImage['sort_order'] ?? '0'; ?>" min="0" placeholder="0 = highest priority">
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="featured" id="featured" <?php echo ($editImage['featured'] ?? false) ? 'checked' : ''; ?>>
                    <label for="featured">⭐ Mark as Featured Image</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Image <?php echo $editImage ? '(Upload new to replace)' : '*'; ?></label>
                <?php if($editImage && $editImage['image']): ?>
                    <div class="current-image">
                        <img src="../assets/images/gallery/<?php echo $editImage['image']; ?>" alt="Current image">
                        <div class="image-overlay">
                            <span>Current Image</span>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" <?php echo $editImage ? '' : 'required'; ?> id="imageInput">
                <small style="color: var(--text-secondary); display: block; margin-top: 5px;">
                    <i class="fas fa-info-circle"></i> 
                    Max size: 10MB. Allowed: JPG, PNG, GIF, WEBP. <strong>Unlimited uploads - no image limit!</strong>
                </small>
                <div id="imagePreview" style="margin-top: 15px; display: none;"></div>
            </div>
            
            <div class="form-actions" style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editImage ? 'Update Image' : 'Upload Image'; ?>
                </button>
                <?php if($editImage): ?>
                    <a href="gallery.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel Edit
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search images..." onkeyup="filterGallery()">
        </div>
        
        <div class="filter-group">
            <i class="fas fa-filter"></i>
            <select id="categoryFilter" onchange="filterGallery()">
                <option value="all">All Categories</option>
                <option value="farm">🏡 Farm Life</option>
                <option value="cows">🐄 Our Cows</option>
                <option value="process">⚙️ Milking Process</option>
                <option value="products">🥛 Products</option>
                <option value="team">👥 Our Team</option>
                <option value="delivery">🚚 Delivery</option>
            </select>
        </div>
        
        <div class="filter-group">
            <i class="fas fa-sort"></i>
            <select id="sortFilter" onchange="sortGallery()">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="featured">Featured First</option>
                <option value="alphabetical">Alphabetical</option>
            </select>
        </div>
        
        <?php if(isset($_GET['category'])): ?>
            <div class="filter-badge">
                <span>Filtered: <?php echo htmlspecialchars($_GET['category']); ?></span>
                <i class="fas fa-times" onclick="window.location.href='gallery.php'"></i>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions-bar" id="bulkActions" style="display: none;">
        <div class="bulk-select-all">
            <input type="checkbox" id="selectAll" onclick="toggleAll()">
            <label for="selectAll">Select All</label>
            <span id="selectedCount" style="color: var(--accent-green); font-weight: 500;">0</span>
            <span>items selected</span>
        </div>
        
        <div class="bulk-actions-buttons">
            <button class="btn btn-danger" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
            <button class="btn btn-secondary" onclick="bulkFeatured()">
                <i class="fas fa-star"></i> Mark as Featured
            </button>
            <button class="btn btn-secondary" onclick="clearSelection()">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="gallery-grid" id="galleryGrid">
        <?php foreach($galleryImages as $index => $image): ?>
        <div class="gallery-item <?php echo $image['featured'] ? 'featured' : ''; ?>" 
             data-id="<?php echo $image['id']; ?>"
             data-category="<?php echo $image['category']; ?>"
             data-title="<?php echo strtolower($image['title_en']); ?>"
             data-date="<?php echo strtotime($image['created_at']); ?>">
            
            <div class="gallery-image" onclick="previewImage('<?php echo $image['image']; ?>', '<?php echo addslashes($image['title_en']); ?>', '<?php echo addslashes($image['description_en'] ?? ''); ?>')">
                <img src="../assets/images/gallery/<?php echo $image['image']; ?>" alt="<?php echo $image['title_en']; ?>">
                <div class="image-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
            </div>
            
            <?php if($image['featured']): ?>
                <span class="gallery-badge badge-featured">⭐ Featured</span>
            <?php endif; ?>
            
            <span class="gallery-badge badge-category" style="left: 10px; right: auto;">
                <?php 
                    $categoryIcons = [
                        'farm' => '🏡',
                        'cows' => '🐄',
                        'process' => '⚙️',
                        'products' => '🥛',
                        'team' => '👥',
                        'delivery' => '🚚'
                    ];
                    echo ($categoryIcons[$image['category']] ?? '📸') . ' ' . ucfirst($image['category']);
                ?>
            </span>
            
            <div class="gallery-info">
                <h3><?php echo $image['title_en']; ?></h3>
                
                <?php if(!empty($image['description_en'])): ?>
                <div class="gallery-description">
                    <?php echo substr(htmlspecialchars($image['description_en']), 0, 100) . (strlen($image['description_en']) > 100 ? '...' : ''); ?>
                </div>
                <?php endif; ?>
                
                <div class="gallery-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($image['created_at'])); ?></span>
                    <span><i class="fas fa-sort"></i> Order: <?php echo $image['sort_order']; ?></span>
                </div>
                
                <div class="checkbox-group" style="margin-bottom: 10px;">
                    <input type="checkbox" class="image-select" value="<?php echo $image['id']; ?>" onchange="updateBulkActions()">
                    <label>Select for bulk action</label>
                </div>
                
                <div class="gallery-actions">
                    <button class="btn-icon" onclick="window.location.href='?edit=<?php echo $image['id']; ?>'">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-icon delete" onclick="confirmDelete(<?php echo $image['id']; ?>)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    <button class="btn-icon" onclick="copyImageUrl('<?php echo $image['image']; ?>')">
                        <i class="fas fa-link"></i> Copy
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($galleryImages)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; background: var(--card-bg); border-radius: 20px;">
            <i class="fas fa-images" style="font-size: 60px; color: var(--border-light); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-primary); margin-bottom: 10px;">No Images Yet</h3>
            <p style="color: var(--text-secondary); margin-bottom: 20px;">Start by uploading your first gallery image above. No limits - upload as many as you want!</p>
            <button class="btn btn-primary" onclick="document.getElementById('addForm').scrollIntoView({behavior: 'smooth'})">
                <i class="fas fa-upload"></i> Upload First Image
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Preview Modal -->
<div class="preview-modal" id="previewModal" onclick="closePreview()">
    <div class="preview-content">
        <img src="" alt="Preview" id="previewImage">
        <button class="preview-close" onclick="closePreview()">&times;</button>
        <div class="preview-info" id="previewInfo"></div>
        <div class="preview-description" id="previewDescription"></div>
    </div>
</div>

<script>
// Image preview on file select
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.style.display = 'block';
            preview.innerHTML = `
                <div style="border: 2px solid var(--accent-green); border-radius: 12px; padding: 15px;">
                    <img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-bottom: 10px;">
                    <p style="font-size: 13px; color: var(--text-secondary);">
                        <i class="fas fa-check-circle" style="color: var(--accent-green);"></i> 
                        Ready to upload: ${file.name} (${(file.size/1024).toFixed(1)} KB)
                    </p>
                </div>
            `;
        }
        reader.readAsDataURL(file);
    }
});

// Gallery filter function
function filterGallery() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const items = document.querySelectorAll('.gallery-item');
    
    items.forEach(item => {
        const title = item.dataset.title;
        const itemCategory = item.dataset.category;
        
        const matchesSearch = title.includes(searchTerm);
        const matchesCategory = category === 'all' || itemCategory === category;
        
        if(matchesSearch && matchesCategory) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
    
    updateStats();
}

// Sort gallery
function sortGallery() {
    const sortBy = document.getElementById('sortFilter').value;
    const grid = document.getElementById('galleryGrid');
    const items = Array.from(document.querySelectorAll('.gallery-item'));
    
    items.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                return b.dataset.date - a.dataset.date;
            case 'oldest':
                return a.dataset.date - b.dataset.date;
            case 'featured':
                return (b.classList.contains('featured') ? 1 : 0) - (a.classList.contains('featured') ? 1 : 0);
            case 'alphabetical':
                return a.dataset.title.localeCompare(b.dataset.title);
            default:
                return 0;
        }
    });
    
    items.forEach(item => grid.appendChild(item));
}

// Bulk actions
let selectedIds = new Set();

function toggleAll() {
    const checkboxes = document.querySelectorAll('.image-select');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
        const id = cb.value;
        if(selectAll.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.image-select:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    selectedIds.clear();
    checkboxes.forEach(cb => selectedIds.add(cb.value));
    
    if(selectedIds.size > 0) {
        bulkActions.style.display = 'flex';
        selectedCount.textContent = selectedIds.size;
    } else {
        bulkActions.style.display = 'none';
        document.getElementById('selectAll').checked = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.image-select').forEach(cb => cb.checked = false);
    selectedIds.clear();
    document.getElementById('bulkActions').style.display = 'none';
    document.getElementById('selectAll').checked = false;
}

function bulkDelete() {
    if(selectedIds.size === 0) return;
    
    if(confirm(`Are you sure you want to delete ${selectedIds.size} image(s)? This cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const input = document.createElement('input');
        input.name = 'bulk_delete';
        input.value = '1';
        form.appendChild(input);
        
        selectedIds.forEach(id => {
            const hidden = document.createElement('input');
            hidden.name = 'selected_ids[]';
            hidden.value = id;
            form.appendChild(hidden);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkFeatured() {
    alert('Feature multiple images feature coming soon!');
}

// Delete confirmation
function confirmDelete(id) {
    if(confirm('Are you sure you want to delete this image? This cannot be undone.')) {
        window.location.href = 'gallery.php?delete=' + id;
    }
}

// Image preview
function previewImage(src, title, description = '') {
    const modal = document.getElementById('previewModal');
    const img = document.getElementById('previewImage');
    const info = document.getElementById('previewInfo');
    const desc = document.getElementById('previewDescription');
    
    img.src = '../assets/images/gallery/' + src;
    info.textContent = title;
    desc.textContent = description;
    modal.classList.add('active');
}

function closePreview() {
    document.getElementById('previewModal').classList.remove('active');
}

// Copy image URL
function copyImageUrl(filename) {
    const url = window.location.origin + '/assets/images/gallery/' + filename;
    navigator.clipboard.writeText(url).then(() => {
        alert('Image URL copied to clipboard!');
    });
}

// Update stats
function updateStats() {
    const visible = document.querySelectorAll('.gallery-item[style="display: block;"]').length;
    const total = document.querySelectorAll('.gallery-item').length;
    
    if(visible < total) {
        // You could add a small indicator here
    }
}

// Auto-hide alert after 5 seconds
setTimeout(() => {
    const alert = document.getElementById('alertMessage');
    if(alert) {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }
}, 5000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Escape to close preview
    if(e.key === 'Escape') {
        closePreview();
    }
});

// Drag and drop upload (bonus feature)
const dropZone = document.getElementById('addForm');
if(dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        dropZone.style.border = '3px dashed var(--accent-green)';
        dropZone.style.background = 'var(--accent-green-light)';
    }
    
    function unhighlight(e) {
        dropZone.style.border = 'none';
        dropZone.style.background = 'white';
    }
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if(files.length > 0) {
            document.getElementById('imageInput').files = files;
            // Trigger change event
            const event = new Event('change', { bubbles: true });
            document.getElementById('imageInput').dispatchEvent(event);
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>