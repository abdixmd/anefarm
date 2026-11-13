<?php
require_once 'includes/auth.php';
require_once '../includes/config.php';

// Handle Add/Edit/Delete operations
$message = '';
$messageType = '';

// Delete owner
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image filename before deleting
    $stmt = $pdo->prepare("SELECT image FROM owners WHERE id = ?");
    $stmt->execute([$id]);
    $owner = $stmt->fetch();
    
    if($owner && $owner['image'] && file_exists("../assets/images/owners/" . $owner['image'])) {
        unlink("../assets/images/owners/" . $owner['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM owners WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Owner deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting owner.";
        $messageType = "error";
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        $name = $_POST['name'];
        $role_en = $_POST['role_en'];
        $role_om = $_POST['role_om'];
        $role_am = $_POST['role_am'];
        $bio_en = $_POST['bio_en'];
        $bio_om = $_POST['bio_om'];
        $bio_am = $_POST['bio_am'];
        $facebook = $_POST['facebook'];
        $telegram = $_POST['telegram'];
        $sort_order = $_POST['sort_order'];
        
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed)) {
                $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $name) . '.' . $ext;
                $uploadPath = "../assets/images/owners/" . $newFilename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $image = $newFilename;
                }
            }
        }
        
        if($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO owners (name, role_en, role_om, role_am, bio_en, bio_om, bio_am, image, facebook, telegram, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if($stmt->execute([$name, $role_en, $role_om, $role_am, $bio_en, $bio_om, $bio_am, $image, $facebook, $telegram, $sort_order])) {
                $message = "Owner added successfully!";
                $messageType = "success";
            } else {
                $message = "Error adding owner.";
                $messageType = "error";
            }
        } elseif($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            // Get current image
            $stmt = $pdo->prepare("SELECT image FROM owners WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            $currentImage = $current['image'];
            
            // Use new image if uploaded, otherwise keep existing
            $finalImage = $image ?: $currentImage;
            
            $stmt = $pdo->prepare("UPDATE owners SET name=?, role_en=?, role_om=?, role_am=?, bio_en=?, bio_om=?, bio_am=?, image=?, facebook=?, telegram=?, sort_order=? WHERE id=?");
            if($stmt->execute([$name, $role_en, $role_om, $role_am, $bio_en, $bio_om, $bio_am, $finalImage, $facebook, $telegram, $sort_order, $id])) {
                // Delete old image if new one uploaded
                if($image && $currentImage && file_exists("../assets/images/owners/" . $currentImage)) {
                    unlink("../assets/images/owners/" . $currentImage);
                }
                $message = "Owner updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating owner.";
                $messageType = "error";
            }
        }
    }
}

// Get all owners
$stmt = $pdo->query("SELECT * FROM owners ORDER BY sort_order");
$owners = $stmt->fetchAll();

// Get owner for editing if ID is provided
$editOwner = null;
if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM owners WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editOwner = $stmt->fetch();
}
?>
<?php include 'includes/header.php'; ?>

<div class="owners-management">
    <div class="page-header">
        <h1>Manage Owners</h1>
        <p>Manage the 5 farm owners - their photos, roles, and social media links.</p>
    </div>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="content-grid">
        <!-- Add/Edit Form -->
        <div class="form-card">
            <h2><?php echo $editOwner ? 'Edit Owner' : 'Add New Owner'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data" class="owner-form">
                <input type="hidden" name="action" value="<?php echo $editOwner ? 'edit' : 'add'; ?>">
                <?php if($editOwner): ?>
                    <input type="hidden" name="id" value="<?php echo $editOwner['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" name="name" required value="<?php echo $editOwner['name'] ?? ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Role (English) *</label>
                        <input type="text" name="role_en" required value="<?php echo $editOwner['role_en'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Role (Oromo) *</label>
                        <input type="text" name="role_om" required value="<?php echo $editOwner['role_om'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Role (Amharic) *</label>
                        <input type="text" name="role_am" required value="<?php echo $editOwner['role_am'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Bio (English)</label>
                        <textarea name="bio_en" rows="3"><?php echo $editOwner['bio_en'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Bio (Oromo)</label>
                        <textarea name="bio_om" rows="3"><?php echo $editOwner['bio_om'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Bio (Amharic)</label>
                        <textarea name="bio_am" rows="3"><?php echo $editOwner['bio_am'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Facebook URL</label>
                        <input type="url" name="facebook" value="<?php echo $editOwner['facebook'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Telegram URL</label>
                        <input type="url" name="telegram" value="<?php echo $editOwner['telegram'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo $editOwner['sort_order'] ?? '0'; ?>" min="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Photo</label>
                    <?php if($editOwner && $editOwner['image']): ?>
                        <div class="current-image">
                            <img src="../assets/images/owners/<?php echo $editOwner['image']; ?>" alt="Current photo" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin-bottom: 10px;">
                            <p>Current photo. Upload new to replace.</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                    <small>Recommended: Square image, at least 300x300px</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editOwner ? 'Update Owner' : 'Add Owner'; ?>
                    </button>
                    <?php if($editOwner): ?>
                        <a href="owners.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Owners List -->
        <div class="list-card">
            <h2>Current Owners (<?php echo count($owners); ?>/5)</h2>
            
            <div class="owners-list">
                <?php foreach($owners as $index => $owner): ?>
                <div class="owner-item">
                    <div class="owner-info">
                        <div class="owner-avatar">
                            <?php if($owner['image']): ?>
                                <img src="../assets/images/owners/<?php echo $owner['image']; ?>" alt="<?php echo $owner['name']; ?>">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                        </div>
                        <div class="owner-details">
                            <h3><?php echo $owner['name']; ?></h3>
                            <p class="owner-role"><?php echo $owner['role_en']; ?></p>
                            <p class="owner-order">Order: <?php echo $owner['sort_order']; ?></p>
                        </div>
                    </div>
                    <div class="owner-actions">
                        <a href="?edit=<?php echo $owner['id']; ?>" class="btn-icon" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?delete=<?php echo $owner['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this owner?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(count($owners) == 0): ?>
                <p class="no-data">No owners added yet. Add your first owner above.</p>
                <?php endif; ?>
            </div>
            
            <div class="info-note">
                <i class="fas fa-info-circle"></i>
                <p>These 5 owners will appear on the website homepage. Sort order determines their display position.</p>
            </div>
        </div>
    </div>
</div>

<style>
.owners-management {
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

.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: var(--accent-green-light);
    color: var(--accent-green);
    border-left: 4px solid var(--accent-green);
}

.alert-error {
    background: #ffebee;
    color: #c62828;
    border-left: 4px solid #c62828;
}

.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.form-card, .list-card {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 5px 20px var(--shadow-color);
}

.form-card h2, .list-card h2 {
    font-size: 20px;
    color: var(--text-primary);
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-light);
}

.owner-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 12px 15px;
    border: 2px solid var(--border-light);
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--accent-green);
    box-shadow: 0 0 0 3px var(--accent-green-soft);
}

.form-group small {
    font-size: 12px;
    color: #999;
}

.current-image {
    margin-bottom: 10px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.btn {
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
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
    box-shadow: 0 5px 15px var(--accent-green-soft);
}

.btn-secondary {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
}

.btn-secondary:hover {
    background: var(--border-light);
    transform: translateY(-2px);
}

.owners-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-height: 500px;
    overflow-y: auto;
}

.owner-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: var(--bg-tertiary);
    border-radius: 12px;
    transition: all 0.3s;
}

.owner-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-hover);
}

.owner-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.owner-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--border-light);
    display: flex;
    align-items: center;
    justify-content: center;
}

.owner-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.owner-avatar i {
    font-size: 40px;
    color: var(--text-secondary);
}

.owner-details h3 {
    font-size: 16px;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.owner-role {
    font-size: 13px;
    color: var(--accent-green);
    margin-bottom: 2px;
}

.owner-order {
    font-size: 12px;
    color: var(--text-secondary);
}

.owner-actions {
    display: flex;
    gap: 10px;
}

.btn-icon {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.3s;
}

.btn-icon:hover {
    background: var(--accent-green);
    color: white;
    transform: scale(1.1);
}

.btn-icon.delete:hover {
    background: #f44336;
}

.no-data {
    text-align: center;
    color: var(--text-secondary);
    padding: 40px 20px;
}

.info-note {
    margin-top: 20px;
    padding: 15px;
    background: var(--accent-green-light);
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--accent-green);
    font-size: 13px;
}

.info-note i {
    font-size: 20px;
}

@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .owners-management {
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .owner-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .owner-actions {
        align-self: flex-end;
    }
}
</style>

<?php include 'includes/footer.php'; ?>