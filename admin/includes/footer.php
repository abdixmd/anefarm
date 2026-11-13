<?php
// Get current year for copyright
$currentYear = date('Y');
?>
</main> <!-- Close main-content from header -->
</div> <!-- Close admin-container from header -->

<!-- Admin Footer -->
<footer class="admin-footer">
    <div class="footer-container">
        <div class="footer-row">
            <!-- Copyright Section -->
            <div class="footer-col">
                <div class="copyright">
                    <i class="fas fa-copyright"></i>
                    <span><?php echo $currentYear; ?> AneDiaryFarm. All rights reserved.</span>
                </div>
            </div>
            
            <!-- Version Info -->
            <div class="footer-col">
                <div class="version-info">
                    <i class="fas fa-code-branch"></i>
                    <span>Version 2.5.0</span>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="footer-col">
                <div class="quick-actions">
                    <button class="footer-btn" onclick="window.open('https://anediaryfarm.com', '_blank')" title="View Website">
                        <i class="fas fa-external-link-alt"></i>
                    </button>
                    <button class="footer-btn" onclick="window.location.reload()" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="footer-btn" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Back to Top">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom with System Info -->
        <div class="footer-bottom">
            <div class="system-info">
                <span><i class="fas fa-database"></i> MySQL</span>
                <span class="separator">•</span>
                <span><i class="fab fa-php"></i> PHP 8.0+</span>
                <span class="separator">•</span>
                <span><i class="fas fa-clock"></i> 
                    <?php 
                        date_default_timezone_set('Africa/Addis_Ababa');
                        echo date('H:i A'); 
                    ?> EAT
                </span>
            </div>
            
            <div class="logout-timer" id="sessionTimer">
                <i class="fas fa-hourglass-half"></i>
                <span>Session expires in: <span id="timer">30:00</span></span>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button (Mobile) -->
<button class="scroll-top-mobile" id="scrollTopMobile" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Admin Footer Styles -->
<style>
/* Admin Footer Styles */
.admin-footer {
    background: var(--bg-primary, #ffffff);
    border-top: 2px solid var(--border-light, #f0f0f0);
    padding: 20px 30px;
    margin-top: 30px;
    box-shadow: 0 -5px 20px var(--shadow-color, rgba(0,0,0,0.02));
    transition: all 0.3s ease;
}

.footer-container {
    max-width: 100%;
    margin: 0 auto;
}

.footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 15px;
}

.footer-col {
    flex: 1;
    min-width: 200px;
}

/* Copyright Section */
.copyright {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary, #666);
    font-size: 14px;
}

.copyright i {
    color: var(--accent-green, #00a400);
    font-size: 14px;
}

/* Version Info */
.version-info {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--text-secondary, #666);
    font-size: 13px;
    background: var(--bg-secondary, #f5f5f7);
    padding: 6px 15px;
    border-radius: 20px;
    width: fit-content;
    margin: 0 auto;
}

.version-info i {
    color: var(--accent-green, #00a400);
    font-size: 14px;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.footer-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: var(--bg-secondary, #f5f5f7);
    color: var(--text-secondary, #666);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    position: relative;
    overflow: hidden;
}

.footer-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: var(--accent-green, #00a400);
    transform: translate(-50%, -50%);
    transition: width 0.3s, height 0.3s;
    z-index: 0;
    opacity: 0.2;
}

.footer-btn:hover::before {
    width: 100px;
    height: 100px;
}

.footer-btn:hover {
    color: var(--accent-green, #00a400);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px var(--accent-green-soft, rgba(0,164,0,0.2));
}

.footer-btn i {
    position: relative;
    z-index: 1;
}

.footer-btn:active {
    transform: translateY(0);
}

/* Footer Bottom */
.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    padding-top: 15px;
    border-top: 1px dashed var(--border-light, #e0e0e0);
    font-size: 12px;
}

.system-info {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary, #999);
    flex-wrap: wrap;
}

.system-info span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.system-info i {
    color: var(--accent-green, #00a400);
    font-size: 12px;
}

.separator {
    color: var(--border-light, #ddd);
    font-weight: bold;
}

/* Session Timer */
.logout-timer {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--bg-secondary, #f8f9fa);
    padding: 6px 15px;
    border-radius: 20px;
    color: var(--text-secondary, #666);
}

.logout-timer i {
    color: var(--accent-green, #00a400);
    font-size: 14px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.logout-timer.warning {
    background: #fff3e0;
    color: #f57c00;
}

.logout-timer.warning i {
    color: #f57c00;
}

.logout-timer.danger {
    background: #ffebee;
    color: #f44336;
}

.logout-timer.danger i {
    color: #f44336;
    animation: blink 1s infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

/* Scroll to Top Mobile */
.scroll-top-mobile {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    border: none;
    background: var(--accent-green, #00a400);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 5px 20px var(--accent-green-soft, rgba(0,164,0,0.3));
    transition: all 0.3s ease;
    z-index: 999;
}

.scroll-top-mobile:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px var(--accent-green-soft, rgba(0,164,0,0.4));
}

.scroll-top-mobile:active {
    transform: scale(0.95);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .footer-row {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-col {
        width: 100%;
    }
    
    .version-info {
        margin: 0 auto;
    }
    
    .quick-actions {
        justify-content: center;
    }
    
    .footer-bottom {
        flex-direction: column;
        text-align: center;
    }
    
    .system-info {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .admin-footer {
        padding: 15px 20px;
        margin-bottom: 70px; /* Space for mobile menu */
    }
    
    .scroll-top-mobile {
        display: flex;
    }
    
    .logout-timer {
        width: 100%;
        justify-content: center;
    }
    
    .system-info {
        flex-wrap: wrap;
    }
    
    .separator {
        display: none;
    }
    
    .system-info span {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .admin-footer {
        padding: 15px;
    }
    
    .copyright {
        justify-content: center;
        font-size: 13px;
    }
    
    .version-info {
        font-size: 12px;
        padding: 4px 12px;
    }
    
    .footer-btn {
        width: 40px;
        height: 40px;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .admin-footer {
        --bg-primary: #1c1c1e;
        --bg-secondary: #2c2c2e;
        --text-secondary: #98989e;
        --border-light: #38383a;
        --shadow-color: rgba(0,0,0,0.3);
    }
    
    .version-info,
    .logout-timer,
    .footer-btn {
        background: #2c2c2e;
    }
}

/* Print Styles */
@media print {
    .admin-footer {
        display: none;
    }
}

/* Loading Animation for Refresh */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.fa-spin-custom {
    animation: spin 1s linear infinite;
}

/* Tooltip Styles */
.footer-btn {
    position: relative;
}

.footer-btn:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: var(--text-primary, #333);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    margin-bottom: 8px;
    z-index: 1000;
    pointer-events: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.footer-btn:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: var(--text-primary, #333);
    margin-bottom: -2px;
    z-index: 1000;
}
</style>

<!-- Session Timer Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Session timeout (30 minutes = 1800 seconds)
    let timeLeft = 1800; // 30 minutes in seconds
    const timerElement = document.getElementById('timer');
    const sessionTimer = document.querySelector('.logout-timer');
    
    function updateTimer() {
        if(timeLeft <= 0) {
            // Session expired - redirect to logout
            window.location.href = 'logout.php?timeout=1';
            return;
        }
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Add warning classes
        if(timeLeft < 300) { // Less than 5 minutes
            sessionTimer.classList.add('danger');
            sessionTimer.classList.remove('warning');
        } else if(timeLeft < 600) { // Less than 10 minutes
            sessionTimer.classList.add('warning');
            sessionTimer.classList.remove('danger');
        } else {
            sessionTimer.classList.remove('warning', 'danger');
        }
        
        timeLeft--;
    }
    
    // Update timer every second
    updateTimer(); // Initial call
    setInterval(updateTimer, 1000);
    
    // Reset timer on user activity
    function resetTimer() {
        timeLeft = 1800;
        sessionTimer.classList.remove('warning', 'danger');
    }
    
    // Listen for user activity
    ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
        document.addEventListener(event, resetTimer);
    });
});

// Scroll to top button visibility
window.addEventListener('scroll', function() {
    const scrollTop = document.getElementById('scrollTopMobile');
    if(window.scrollY > 300) {
        scrollTop.style.display = 'flex';
    } else {
        scrollTop.style.display = 'none';
    }
});

// Refresh button with loading state
document.querySelectorAll('.footer-btn[onclick*="reload"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        const icon = this.querySelector('i');
        icon.classList.add('fa-spin-custom');
        
        // Remove spin after page loads (if page doesn't reload)
        setTimeout(() => {
            icon.classList.remove('fa-spin-custom');
        }, 1000);
    });
});

// External link confirmation
document.querySelectorAll('.footer-btn[onclick*="_blank"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
        if(!confirm('Leave admin panel and visit website?')) {
            e.preventDefault();
            return false;
        }
    });
});

// Show quick stats on hover
const versionInfo = document.querySelector('.version-info');
if(versionInfo) {
    versionInfo.addEventListener('mouseenter', function() {
        const tooltip = document.createElement('div');
        tooltip.className = 'version-tooltip';
        tooltip.innerHTML = `
            <strong>System Info</strong><br>
            PHP: 8.0.28<br>
            MySQL: 5.7.39<br>
            Server: Apache/2.4.54<br>
            Memory: 128MB
        `;
        tooltip.style.cssText = `
            position: absolute;
            background: var(--text-primary, #333);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            line-height: 1.6;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 1000;
            pointer-events: none;
            margin-top: 5px;
        `;
        
        this.style.position = 'relative';
        this.appendChild(tooltip);
        
        this.addEventListener('mouseleave', function() {
            tooltip.remove();
        }, { once: true });
    });
}
</script>

<!-- Additional Admin Footer Features -->
<div style="display: none;">
    <!-- Hidden elements for dynamic updates -->
    <span id="server-time" data-time="<?php echo time(); ?>"></span>
</div>

<!-- Database Status Indicator (Optional) -->
<div class="db-status" style="position: fixed; bottom: 10px; left: 10px; z-index: 9999;">
    <span class="status-dot" style="
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #00a400;
        border-radius: 50%;
        box-shadow: 0 0 10px #00a400;
        animation: pulse 2s infinite;
    "></span>
</div>

<!-- Memory Usage Info (Development Only) -->
<?php if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost'): ?>
<div style="position: fixed; bottom: 10px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 3px 10px; border-radius: 20px; font-size: 10px; z-index: 9999;">
    Memory: <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?>MB
</div>
<?php endif; ?>

</body>
</html>