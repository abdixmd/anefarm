<?php
require_once 'includes/auth.php';
require_once '../includes/config.php';

// Handle Add/Edit/Delete operations
$message = '';
$messageType = '';

// Delete product
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image filename before deleting
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if($product && $product['image'] && file_exists("../assets/images/products/" . $product['image'])) {
        unlink("../assets/images/products/" . $product['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if($stmt->execute([$id])) {
        $message = "Product deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting product.";
        $messageType = "error";
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        $name_en = $_POST['name_en'];
        $name_om = $_POST['name_om'];
        $name_am = $_POST['name_am'];
        $description_en = $_POST['description_en'];
        $description_om = $_POST['description_om'];
        $description_am = $_POST['description_am'];
        $price = $_POST['price'];
        $unit = $_POST['unit'];
        $in_stock = isset($_POST['in_stock']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $sort_order = $_POST['sort_order'];
        
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if(in_array($ext, $allowed)) {
                $newFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $name_en) . '.' . $ext;
                $uploadPath = "../assets/images/products/" . $newFilename;
                
                if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $image = $newFilename;
                }
            }
        }
        
        if($_POST['action'] === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (name_en, name_om, name_am, description_en, description_om, description_am, price, unit, image, in_stock, is_featured, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if($stmt->execute([$name_en, $name_om, $name_am, $description_en, $description_om, $description_am, $price, $unit, $image, $in_stock, $is_featured, $sort_order])) {
                $message = "Product added successfully!";
                $messageType = "success";
            } else {
                $message = "Error adding product.";
                $messageType = "error";
            }
        } elseif($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            
            // Get current image
            $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $current = $stmt->fetch();
            $currentImage = $current['image'];
            
            // Use new image if uploaded, otherwise keep existing
            $finalImage = $image ?: $currentImage;
            
            $stmt = $pdo->prepare("UPDATE products SET name_en=?, name_om=?, name_am=?, description_en=?, description_om=?, description_am=?, price=?, unit=?, image=?, in_stock=?, is_featured=?, sort_order=? WHERE id=?");
            if($stmt->execute([$name_en, $name_om, $name_am, $description_en, $description_om, $description_am, $price, $unit, $finalImage, $in_stock, $is_featured, $sort_order, $id])) {
                // Delete old image if new one uploaded
                if($image && $currentImage && file_exists("../assets/images/products/" . $currentImage)) {
                    unlink("../assets/images/products/" . $currentImage);
                }
                $message = "Product updated successfully!";
                $messageType = "success";
            } else {
                $message = "Error updating product.";
                $messageType = "error";
            }
        }
    }
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY sort_order");
$products = $stmt->fetchAll();

// Get product for editing if ID is provided
$editProduct = null;
if(isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editProduct = $stmt->fetch();
}
?>
<?php include 'includes/header.php'; ?>

<div class="products-management">
    <div class="page-header">
        <h1>Manage Products</h1>
        <p>Add and manage your dairy products in all three languages.</p>
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
            <h2><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h2>
            
            <form method="POST" enctype="multipart/form-data" class="product-form">
                <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                <?php if($editProduct): ?>
                    <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Name (English) *</label>
                        <input type="text" name="name_en" required value="<?php echo $editProduct['name_en'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Name (Oromo) *</label>
                        <input type="text" name="name_om" required value="<?php echo $editProduct['name_om'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Name (Amharic) *</label>
                        <input type="text" name="name_am" required value="<?php echo $editProduct['name_am'] ?? ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Description (English)</label>
                        <textarea name="description_en" rows="3"><?php echo $editProduct['description_en'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Description (Oromo)</label>
                        <textarea name="description_om" rows="3"><?php echo $editProduct['description_om'] ?? ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Description (Amharic)</label>
                        <textarea name="description_am" rows="3"><?php echo $editProduct['description_am'] ?? ''; ?></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Price (ETB) *</label>
                        <input type="number" name="price" step="0.01" required value="<?php echo $editProduct['price'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Unit *</label>
                        <select name="unit" required>
                            <option value="L" <?php echo ($editProduct['unit'] ?? '') == 'L' ? 'selected' : ''; ?>>Liter (L)</option>
                            <option value="500ml" <?php echo ($editProduct['unit'] ?? '') == '500ml' ? 'selected' : ''; ?>>500ml</option>
                            <option value="250g" <?php echo ($editProduct['unit'] ?? '') == '250g' ? 'selected' : ''; ?>>250g</option>
                            <option value="500g" <?php echo ($editProduct['unit'] ?? '') == '500g' ? 'selected' : ''; ?>>500g</option>
                            <option value="1kg" <?php echo ($editProduct['unit'] ?? '') == '1kg' ? 'selected' : ''; ?>>1kg</option>
                            <option value="piece" <?php echo ($editProduct['unit'] ?? '') == 'piece' ? 'selected' : ''; ?>>Piece</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo $editProduct['sort_order'] ?? '0'; ?>" min="0">
                    </div>
                </div>
                
                <div class="form-row checkbox-row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="in_stock" <?php echo !isset($editProduct) || $editProduct['in_stock'] ? 'checked' : ''; ?>>
                        <span>In Stock</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" <?php echo $editProduct['is_featured'] ?? false ? 'checked' : ''; ?>>
                        <span>Featured Product</span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label>Product Image</label>
                    <?php if($editProduct && $editProduct['image']): ?>
                        <div class="current-image">
                            <img src="../assets/images/products/<?php echo $editProduct['image']; ?>" alt="Current image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; margin-bottom: 10px;">
                            <p>Current image. Upload new to replace.</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                    <small>Recommended: Square image, at least 500x500px</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    <?php if($editProduct): ?>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Products List -->
        <div class="list-card">
            <h2>Current Products (<?php echo count($products); ?>)</h2>
            
            <div class="products-list">
                <?php foreach($products as $product): ?>
                <div class="product-item">
                    <div class="product-info">
                        <div class="product-thumb">
                            <?php if($product['image']): ?>
                                <img src="../assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name_en']; ?>">
                            <?php else: ?>
                                <i class="fas fa-box"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3><?php echo $product['name_en']; ?></h3>
                            <p class="product-price"><?php echo $product['price']; ?> ETB / <?php echo $product['unit']; ?></p>
                            <div class="product-badges">
                                <?php if($product['in_stock']): ?>
                                    <span class="badge success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge error">Out of Stock</span>
                                <?php endif; ?>
                                <?php if($product['is_featured']): ?>
                                    <span class="badge warning">Featured</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="product-actions">
                        <a href="?edit=<?php echo $product['id']; ?>" class="btn-icon" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="?delete=<?php echo $product['id']; ?>" class="btn-icon delete" onclick="return confirm('Are you sure you want to delete this product?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(count($products) == 0): ?>
                <p class="no-data">No products added yet. Add your first product above.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.products-management {
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

.product-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.checkbox-row {
    display: flex;
    gap: 30px;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    color: var(--text-primary);
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
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

.products-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
    max-height: 600px;
    overflow-y: auto;
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: var(--bg-tertiary);
    border-radius: 12px;
    transition: all 0.3s;
}

.product-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px var(--shadow-hover);
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.product-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
    background: var(--border-light);
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-thumb i {
    font-size: 30px;
    color: var(--text-secondary);
}

.product-details h3 {
    font-size: 16px;
    color: var(--text-primary);
    margin-bottom: 3px;
}

.product-price {
    font-size: 14px;
    color: var(--accent-green);
    font-weight: 500;
    margin-bottom: 5px;
}

.product-badges {
    display: flex;
    gap: 5px;
}

.badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.badge.success {
    background: var(--accent-green-light);
    color: var(--accent-green);
}

.badge.error {
    background: #ffebee;
    color: #c62828;
}

.badge.warning {
    background: #fff3e0;
    color: #f57c00;
}

.product-actions {
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

@media (max-width: 1024px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .products-management {
        padding: 20px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .checkbox-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .product-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .product-actions {
        align-self: flex-end;
    }
}
</style>

<?php include 'includes/footer.php'; ?>