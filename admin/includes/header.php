<?php
// Get current page for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AneDiaryFarm Admin - <?php echo ucfirst(str_replace('.php', '', $current_page)); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .nav-badge {
            background: #00a400;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Theme Variables */
        body.dark-theme {
            --bg-primary: #1c1c1e;
            --bg-secondary: #2c2c2e;
            --bg-tertiary: #3a3a3c;
            --text-primary: #f5f5f7;
            --text-secondary: #98989e;
            --border-light: #38383a;
            --card-bg: #2c2c2e;
            --shadow-color: rgba(255, 255, 255, 0.04);
            --shadow-hover: rgba(255, 255, 255, 0.08);
        }

        body.light-theme {
            --bg-primary: #f0f2f5;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f8f9fa;
            --text-primary: #333;
            --text-secondary: #666;
            --border-light: #e0e0e0;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.05);
            --shadow-hover: rgba(0, 0, 0, 0.1);
        }

        /* Theme Toggle Button in Sidebar */
        .sidebar-nav .theme-toggle {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 16px;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 8px;
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 15px;
            font-family: inherit;
        }
        .sidebar-nav li:has(> .theme-toggle):hover {
             background: var(--bg-tertiary);
        }
        .sidebar-nav .theme-toggle i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body class="<?php echo $_SESSION['theme'] ?? ''; ?>">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-tractor"></i>
                    <h2>AneDiaryFarm</h2>
                </div>
                <p class="admin-badge">Admin Panel</p>
            </div>
            
        <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                            <i class="fas fa-chart-pie"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="owners.php" class="<?php echo $current_page == 'owners.php' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i>
                            <span>Manage Owners</span>
                        </a>
                    </li>
                    <li>
                        <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            <span>Manage Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="gallery.php" class="<?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>">
                            <i class="fas fa-images"></i>
                            <span>Gallery Manager</span>
                            <?php
                            // Optional: Show count of new images
                            require_once '../includes/config.php';
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM gallery WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                            $newImages = $stmt->fetch()['count'];
                            if($newImages > 0):
                            ?>
                            <span class="nav-badge"><?php echo $newImages; ?> new</span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                         <button class="theme-toggle">
                            <i class="fas fa-moon"></i>
                            <span>Toggle Theme</span>
                        </button>
                    </li>
                    <li class="logout">
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <p class="user-name"><?php echo $_SESSION['admin_username'] ?? 'Admin'; ?></p>
                        <p class="user-role">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">