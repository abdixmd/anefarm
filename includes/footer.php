<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>AneDiaryFarm</h3>
                <p><?php echo $lang['story_text3']; ?></p>
                <div class="social-links">
                    <a href="<?php echo $settings['facebook']; ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo $settings['telegram']; ?>" target="_blank"><i class="fab fa-telegram-plane"></i></a>
                    <a href="<?php echo $settings['tiktok']; ?>" target="_blank"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3><?php echo $lang['quick_links']; ?></h3>
                <ul>
                    <li><a href="index.php#home"><i class="fas fa-chevron-right"></i> <?php echo $lang['home']; ?></a></li>
                    <li><a href="index.php#owners"><i class="fas fa-chevron-right"></i> <?php echo $lang['our_team']; ?></a></li>
                    <li><a href="products.php"><i class="fas fa-chevron-right"></i> <?php echo $lang['products']; ?></a></li>
                    <li><a href="gallery.php"><i class="fas fa-chevron-right"></i> <?php echo $lang['gallery']; ?></a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> <?php echo $lang['contact']; ?></a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php echo $lang['products']; ?></h3>
                <ul>
                    <li><a href="products.php?category=milk"><i class="fas fa-chevron-right"></i> Fresh Milk</a></li>
                    <li><a href="products.php?category=a2"><i class="fas fa-chevron-right"></i> A2 Milk</a></li>
                    <li><a href="products.php?category=paneer"><i class="fas fa-chevron-right"></i> Farm Paneer</a></li>
                    <li><a href="products.php?category=butter"><i class="fas fa-chevron-right"></i> Fresh Butter</a></li>
                    <li><a href="products.php?category=yogurt"><i class="fas fa-chevron-right"></i> Natural Yogurt</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php echo $lang['contact_info']; ?></h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo $settings['address']; ?></li>
                    <li><i class="fas fa-phone"></i> <?php echo $settings['phone']; ?></li>
                    <li><i class="fas fa-envelope"></i> <?php echo $settings['email']; ?></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> AneDiaryFarm. <?php echo $lang['rights_reserved']; ?> | 
               <?php echo $lang['designed_with']; ?> <i class="fas fa-heart"></i> <?php echo $lang['in_ethiopia']; ?></p>
        </div>
    </div>
</footer>