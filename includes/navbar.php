<style>
    /* Language Switcher */
.lang-switcher {
    position: relative;
}

.lang-current {
    background: #1e293b;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
}

.lang-dropdown {
    position: absolute;
    top: 110%;
    right: 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    display: none;
    min-width: 160px;
    overflow: hidden;
    z-index: 999;
}

.lang-switcher:hover .lang-dropdown {
    display: block;
}

.lang-btn {
    width: 100%;
    padding: 10px;
    border: none;
    background: transparent;
    text-align: left;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    transition: background 0.3s;
}

.lang-btn:hover {
    background: #f1f5f9;
}

.lang-btn.active {
    background: #e2e8f0;
    font-weight: bold;
}

/* Menu Toggle Animation */
.menu-toggle {
    width: 30px;
    height: 22px;
    background: none;
    border: none;
    cursor: pointer;
    position: relative;
}

.menu-toggle span {
    display: block;
    height: 3px;
    background: #1e293b;
    margin-bottom: 5px;
    border-radius: 3px;
    transition: 0.3s ease;
}

.menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -5px);
}
</style>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">AneDiaryFarm</a>
        
        <ul class="nav-menu">
            <li><a href="index.php#home" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>"><?php echo $lang['home']; ?></a></li>
            <!-- <li><a href="index.php#owners"><?php echo $lang['our_team']; ?></a></li> -->
            <li><a href="products.php"><?php echo $lang['products']; ?></a></li>
            <li><a href="gallery.php"><?php echo $lang['gallery']; ?></a></li>
            <li><a href="contact.php"><?php echo $lang['contact']; ?></a></li>
            <li><a href="about.php"><?php echo $lang['about']; ?></a></li>
        </ul>
        
    <div class="nav-right">
        <!-- Language Switcher Dropdown -->
        <div class="lang-switcher">
            <button class="lang-current" aria-haspopup="true" aria-expanded="false">
                <?php echo $supported_languages[$_SESSION['lang']]['label']; ?>
                <i class="arrow-down"></i>
            </button>

            <div class="lang-dropdown">
                <?php foreach ($supported_languages as $code => $language_data): ?>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="lang" value="<?php echo htmlspecialchars($code); ?>">
                        <button 
                            type="submit" 
                            class="lang-btn <?php echo $_SESSION['lang'] === $code ? 'active' : ''; ?>"
                            aria-label="Switch to <?php echo htmlspecialchars($language_data['name']); ?>"
                        >
                            <?php echo htmlspecialchars($language_data['label']); ?>
                            <span class="lang-name"><?php echo htmlspecialchars($language_data['name']); ?></span>
                        </button>
                    </form>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modern Menu Toggle -->
        <button class="menu-toggle" aria-label="Toggle Navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
    </div>

</nav>

    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            this.classList.toggle('active');
        });
    </script>