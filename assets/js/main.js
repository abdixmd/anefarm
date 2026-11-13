// ============================================
// ANEFARM - Premium Interactive Features
// Apple-Inspired Smooth Experience
// Auto Theme Detection | Advanced Animations
// ============================================

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', () => {
    initLoader();
    initNavigation();
    initScrollEffects();
    initParallax();
    initCounters();
    initSmoothScroll();
    initGallery();
    initFormValidation();
    initLazyLoading();
    initThemeDetection();
    initTooltips();
    initBackToTop();
});

// Page Loader
function initLoader() {
    const loader = document.querySelector('.page-loader');
    if(loader) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 500);
        });
    }
}

// Navigation with active state and smooth scroll
function initNavigation() {
    const navbar = document.querySelector('.navbar');
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a');
    const body = document.body;
    
    // Create mobile menu backdrop
    const menuBackdrop = document.createElement('div');
    menuBackdrop.className = 'menu-backdrop';
    body.appendChild(menuBackdrop);
    
    // Mobile menu toggle
    if(menuToggle) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            menuBackdrop.classList.toggle('active');
            body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });
    }
    
    // Close menu when clicking backdrop
    menuBackdrop.addEventListener('click', () => {
        menuToggle?.classList.remove('active');
        navMenu?.classList.remove('active');
        menuBackdrop.classList.remove('active');
        body.style.overflow = '';
    });
    
    // Close menu when clicking a link
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            menuToggle?.classList.remove('active');
            navMenu?.classList.remove('active');
            menuBackdrop.classList.remove('active');
            body.style.overflow = '';
        });
    });
    
    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        if(window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        // Active link highlighting
        let current = '';
        const sections = document.querySelectorAll('section[id]');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.clientHeight;
            if(window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if(href === `#${current}` || (href === 'index.php' && current === 'home')) {
                link.classList.add('active');
            }
        });
    });
}

// Scroll effects with Intersection Observer
function initScrollEffects() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
                // Add staggered animation to children
                const children = entry.target.children;
                Array.from(children).forEach((child, index) => {
                    child.style.animationDelay = `${index * 0.1}s`;
                });
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements
    document.querySelectorAll('.feature-card, .product-card, .owner-card, .gallery-item, .stat-item, .testimonial-card')
        .forEach(el => observer.observe(el));
}

// Parallax effect for hero section
function initParallax() {
    const hero = document.querySelector('.hero');
    const heroContent = document.querySelector('.hero-content');
    const heroVideo = document.querySelector('.hero-video');
    
    if(hero && heroContent) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * 0.3;
            
            if(heroVideo) {
                heroVideo.style.transform = `translate3d(0, ${rate * 0.2}px, 0) scale(1.1)`;
            }
            heroContent.style.transform = `translate3d(0, ${rate * 0.5}px, 0)`;
        });
    }
}

// Animated counters
function initCounters() {
    const counters = document.querySelectorAll('.stat-number');
    
    const observerOptions = {
        threshold: 0.5
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const counter = entry.target;
                const target = parseInt(counter.dataset.target || counter.textContent.replace(/[^0-9]/g, ''));
                const suffix = counter.dataset.suffix || '';
                let current = 0;
                const increment = target / 50; // Smooth animation
                const duration = 1500; // 1.5 seconds
                const stepTime = duration / 50;
                
                const updateCounter = () => {
                    if(current < target) {
                        current += increment;
                        counter.innerHTML = Math.ceil(current) + suffix;
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerHTML = target + suffix;
                    }
                };
                
                updateCounter();
                observer.unobserve(counter);
            }
        });
    }, observerOptions);
    
    counters.forEach(counter => observer.observe(counter));
}

// Smooth scroll for anchor links
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if(targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if(target) {
                const navHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = target.offsetTop - navHeight;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// Advanced gallery with lightbox and filtering
function initGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    
    galleryItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            const img = item.querySelector('img');
            const imgSrc = img.src;
            const title = img.alt || 'Gallery Image';
            
            lightbox.innerHTML = `
                <div class="lightbox-content">
                    <img src="${imgSrc}" alt="${title}">
                    <span class="close-lightbox">&times;</span>
                    <div class="lightbox-caption">${title}</div>
                </div>
            `;
            
            document.body.appendChild(lightbox);
            document.body.style.overflow = 'hidden';
            
            // Close handlers
            const closeBtn = lightbox.querySelector('.close-lightbox');
            closeBtn.addEventListener('click', closeLightbox);
            lightbox.addEventListener('click', (e) => {
                if(e.target === lightbox) closeLightbox();
            });
            
            // Keyboard close
            document.addEventListener('keydown', function escHandler(e) {
                if(e.key === 'Escape') {
                    closeLightbox();
                    document.removeEventListener('keydown', escHandler);
                }
            });
        });
    });
    
    function closeLightbox() {
        lightbox.remove();
        document.body.style.overflow = '';
    }
}

// Advanced form validation with real-time feedback
function initFormValidation() {
    const form = document.querySelector('#contact-form');
    if(!form) return;
    
    const inputs = form.querySelectorAll('input, textarea');
    
    // Real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', () => {
            validateField(input);
        });
        
        input.addEventListener('input', () => {
            if(input.classList.contains('error')) {
                validateField(input);
            } else if(input.classList.contains('success')) {
                // Keep success styling
            }
        });
    });
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        let isValid = true;
        
        // Validate all fields
        inputs.forEach(input => {
            if(!validateField(input)) {
                isValid = false;
            }
        });
        
        if(!isValid) {
            showNotification('Please fill all required fields correctly', 'error');
            return;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;
        
        try {
            // Collect form data
            const formData = new FormData(form);
            
            // Send AJAX request
            const response = await fetch('send-message.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if(data.success) {
                showNotification(data.message || 'Message sent successfully!', 'success');
                form.reset();
                
                // Remove success classes
                inputs.forEach(input => {
                    input.classList.remove('success');
                });
            } else {
                showNotification(data.message || 'Error sending message. Please try again.', 'error');
            }
        } catch(error) {
            console.error('Error:', error);
            showNotification('Network error. Please check your connection.', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        if(field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        } else if(field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email';
            }
        } else if(field.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            if(!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }
        
        // Update UI
        field.classList.toggle('error', !isValid && value !== '');
        field.classList.toggle('success', isValid && value !== '');
        
        // Show/hide error message
        let errorElement = field.parentElement.querySelector('.error-message');
        if(!isValid && value !== '') {
            if(!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                field.parentElement.appendChild(errorElement);
            }
            errorElement.textContent = errorMessage;
        } else if(errorElement) {
            errorElement.remove();
        }
        
        return isValid;
    }
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        // Style the notification
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 16px 24px;
            background: ${type === 'success' ? 'var(--accent-green)' : 'var(--error)'};
            color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease;
            font-size: 14px;
            max-width: 350px;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }
}

// Lazy loading images
function initLazyLoading() {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const img = entry.target;
                if(img.dataset.src) {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                }
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px'
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Theme detection
function initThemeDetection() {
    // Check system preference
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    const themeToggle = document.querySelector('.theme-toggle');
    
    // Function to apply theme
    const setTheme = (theme) => {
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
            document.body.classList.remove('light-theme');
            if(themeToggle) themeToggle.querySelector('i').className = 'fas fa-sun';
        } else if (theme === 'light') {
            document.body.classList.add('light-theme');
            document.body.classList.remove('dark-theme');
            if(themeToggle) themeToggle.querySelector('i').className = 'fas fa-moon';
        } else {
            // Auto / System Default
            document.body.classList.remove('dark-theme', 'light-theme');
            const isDark = prefersDark.matches;
            if(themeToggle) themeToggle.querySelector('i').className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }
    };

    // Initialize icon on load based on PHP-rendered body class
    if (document.body.classList.contains('dark-theme')) {
        if(themeToggle) themeToggle.querySelector('i').className = 'fas fa-sun';
    } else if (document.body.classList.contains('light-theme')) {
        if(themeToggle) themeToggle.querySelector('i').className = 'fas fa-moon';
    } else {
        // Auto / System Default
        const isDark = prefersDark.matches;
        if(themeToggle) themeToggle.querySelector('i').className = isDark ? 'fas fa-sun' : 'fas fa-moon';
    }

    // Handle Toggle Click
    if(themeToggle) {
        themeToggle.addEventListener('click', () => {
            // Check current effective theme from body class
            let currentTheme = 'auto';
            if (document.body.classList.contains('dark-theme')) currentTheme = 'dark';
            else if (document.body.classList.contains('light-theme')) currentTheme = 'light';
            
            let newTheme;
            
            if (currentTheme !== 'auto') {
                newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            } else {
                // If currently auto, switch to the opposite of system
                newTheme = prefersDark.matches ? 'light' : 'dark';
            }
            
            setTheme(newTheme);
            
            // Persist via PHP Session
            fetch('set-theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'theme=' + newTheme
            });
        });
    }
    
    // Listen for system theme changes
    prefersDark.addEventListener('change', (e) => {
        // Only update if no manual override is active
        if(!document.body.classList.contains('dark-theme') && !document.body.classList.contains('light-theme')) {
            setTheme(null);
        }
    });
}

// Tooltips
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', (e) => {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = element.dataset.tooltip;
            tooltip.style.cssText = `
                position: absolute;
                background: var(--text-primary);
                color: var(--bg-primary);
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                z-index: 1000;
                pointer-events: none;
                white-space: nowrap;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            
            element.addEventListener('mouseleave', () => {
                tooltip.remove();
            }, { once: true });
        });
    });
}

// Back to top button
function initBackToTop() {
    const backToTop = document.createElement('button');
    backToTop.className = 'back-to-top';
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: var(--accent-green);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 12px var(--accent-green-soft);
        z-index: 998;
        transition: all 0.3s ease;
        opacity: 0;
        transform: translateY(20px);
    `;
    
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', () => {
        if(window.scrollY > 500) {
            backToTop.style.display = 'flex';
            setTimeout(() => {
                backToTop.style.opacity = '1';
                backToTop.style.transform = 'translateY(0)';
            }, 10);
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.transform = 'translateY(20px)';
            setTimeout(() => {
                if(window.scrollY <= 500) {
                    backToTop.style.display = 'none';
                }
            }, 300);
        }
    });
    
    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    backToTop.addEventListener('mouseenter', () => {
        backToTop.style.transform = 'scale(1.1)';
    });
    
    backToTop.addEventListener('mouseleave', () => {
        backToTop.style.transform = 'scale(1)';
    });
}

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    .fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    .error {
        border-color: var(--error) !important;
    }
    
    .success {
        border-color: var(--success) !important;
    }
    
    .error-message {
        color: var(--error);
        font-size: 12px;
        margin-top: 4px;
        padding-left: 16px;
        animation: fadeIn 0.3s ease;
    }
    
    .notification {
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .menu-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .menu-backdrop.active {
        opacity: 1;
        visibility: visible;
    }
    
    img[data-src] {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    img[data-src].loaded {
        opacity: 1;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .fa-spin {
        animation: spin 1s linear infinite;
    }
    
    .back-to-top:hover {
        background: var(--accent-green-dark);
        box-shadow: 0 8px 20px var(--accent-green-soft);
    }
`;

document.head.appendChild(style);

// Gallery category filtering
function initGalleryFilters() {
    const categoryPills = document.querySelectorAll('.category-pill');
    const galleryItems = document.querySelectorAll('.gallery-item-enhanced');
    
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function() {
            // Update active pill
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            
            // Filter items with animation
            galleryItems.forEach((item, index) => {
                if(category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, index * 50);
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// Social sharing functions
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareOnTelegram() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out Anefarm Gallery - Pure, Fresh, Local!');
    window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent('Check out Anefarm Gallery - Pure, Fresh, Local!');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if(document.querySelector('.gallery-categories')) {
        initGalleryFilters();
    }
});