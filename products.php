<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

$products = getProducts($pdo);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['farm_name']; ?> - <?php echo $lang['products']; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?php echo $_SESSION['theme'] ?? ''; ?>">
    <?php include 'includes/navbar.php'; ?>

    <section class="products-section" style="padding-top: 120px;">
        <div class="container">
            <h1 class="section-title"><?php echo $lang['our_products']; ?></h1>
            <div class="product-grid">
                <?php foreach($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php echo $product['image'] ?: 'default-product.jpg'; ?>" 
                             alt="<?php echo $product['name_' . $_SESSION['lang']]; ?>">
                        <?php if($product['in_stock']): ?>
                            <span class="product-badge">Fresh</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo $product['name_' . $_SESSION['lang']]; ?></h3>
                        <p class="product-description"><?php echo $product['description_' . $_SESSION['lang']]; ?></p>
                        <div class="product-price">
                            <?php echo number_format($product['price'], 2); ?> ETB / <?php echo $product['unit']; ?>
                        </div>
                        <a href="https://wa.me/251989784673?text=<?php echo urlencode($lang['whatsapp_text'] . ' - ' . $product['name_en']); ?>" 
                           class="btn btn-primary" target="_blank">
                            <i class="fab fa-whatsapp"></i> <?php echo $lang['order_now']; ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>