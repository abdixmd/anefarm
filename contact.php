 <?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo $settings['farm_name']; ?> - <?php echo $lang['contact']; ?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="Contact Anefarm for fresh milk delivery in chiro. Reach us by phone, email, or visit our farm.">
    <meta name="theme-color" content="#00a400" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#30d158" media="(prefers-color-scheme: dark)">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Leaflet for maps (lightweight alternative to Google Maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        /* Page Header */
        .page-header {
            height: 40vh;
            min-height: 300px;
            background: linear-gradient(135deg, var(--accent-green-light), var(--accent-green));
            position: relative;
            overflow: hidden;
            margin-top: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }

        .page-header h1 {
            font-size: clamp(40px, 8vw, 56px);
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .page-header p {
            font-size: clamp(16px, 3vw, 18px);
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            opacity: 0.9;
        }

        /* Contact Section */
        .contact-page {
            padding: 80px 0;
            background: var(--bg-primary);
        }

        .contact-grid-enhanced {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 50px;
            margin-bottom: 60px;
        }

        /* Contact Info Cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px var(--shadow-hover);
        }

        .info-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            background: var(--accent-green-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .info-card:hover .info-icon {
            background: var(--accent-green);
            transform: scale(1.1) rotate(5deg);
        }

        .info-icon i {
            font-size: 30px;
            color: var(--accent-green);
            transition: all 0.3s ease;
        }

        .info-card:hover .info-icon i {
            color: white;
        }

        .info-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .info-card p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }

        .info-card .click-hint {
            margin-top: 15px;
            font-size: 12px;
            color: var(--accent-green);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .info-card:hover .click-hint {
            opacity: 1;
        }

        /* Business Hours */
        .hours-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 30px;
            grid-column: span 2;
        }

        .hours-card h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hours-card h3 i {
            color: var(--accent-green);
        }

        .hours-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .hour-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border-light);
        }

        .hour-item .day {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .hour-item .time {
            color: var(--text-primary);
            font-weight: 600;
        }

        .hour-item.closed .time {
            color: #f44336;
        }

        /* Contact Form Enhanced */
        .contact-form-enhanced {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-light);
            border-radius: 32px;
            padding: 40px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: var(--text-primary);
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
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
            padding: 15px 18px;
            background: var(--bg-primary);
            border: 2px solid var(--border-light);
            border-radius: 16px;
            font-size: 15px;
            color: var(--text-primary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 4px var(--accent-green-soft);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-checkbox label {
            color: var(--text-secondary);
            font-size: 14px;
            cursor: pointer;
        }

        .form-checkbox a {
            color: var(--accent-green);
            text-decoration: none;
        }

        .form-checkbox a:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--accent-green), var(--accent-green-dark));
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px var(--accent-green-soft);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn i {
            font-size: 18px;
        }

        /* Map Section */
        .map-section {
            padding: 40px 0 80px;
        }

        .map-container {
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px var(--shadow-hover);
            height: 400px;
            position: relative;
        }

        #farm-map {
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        .map-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 50px;
            border: 1px solid var(--border-light);
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px var(--shadow-color);
        }

        .map-overlay i {
            color: var(--accent-green);
            font-size: 20px;
        }

        .map-overlay span {
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 500;
        }

        /* FAQ Section */
        .faq-section {
            padding: 60px 0;
            background: var(--bg-secondary);
        }

        .faq-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }

        .faq-item {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-light);
            border-radius: 20px;
            padding: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px var(--shadow-hover);
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text-primary);
        }

        .faq-question i {
            color: var(--accent-green);
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            margin-top: 15px;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
            display: none;
        }

        .faq-item.active .faq-answer {
            display: block;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .contact-grid-enhanced {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .info-cards {
                grid-template-columns: 1fr;
            }
            
            .hours-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .faq-grid {
                grid-template-columns: 1fr;
            }
            
            .map-overlay {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .contact-form-enhanced {
                padding: 25px;
            }
        }
    </style>
</head>
<body class="<?php echo $_SESSION['theme'] ?? ''; ?>">
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader"></div>
    </div>

    <?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><?php echo $lang['get_in_touch']; ?></h1>
            <p>We'd love to hear from you. Reach out anytime!</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-page">
        <div class="container">
            <div class="contact-grid-enhanced">
                <!-- Left Column - Contact Info -->
                <div>
                    <div class="info-cards">
                        <div class="info-card" onclick="window.location.href='tel:<?php echo $settings['phone']; ?>'">
                            <div class="info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h3><?php echo $lang['phone']; ?></h3>
                            <p><?php echo $settings['phone']; ?></p>
                            <div class="click-hint">Click to call</div>
                        </div>
                        
                        <div class="info-card" onclick="window.location.href='mailto:<?php echo $settings['email']; ?>'">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3><?php echo $lang['email']; ?></h3>
                            <p><?php echo $settings['email']; ?></p>
                            <div class="click-hint">Click to email</div>
                        </div>
                        
                        <div class="info-card" onclick="window.open('https://maps.google.com/?q=Addis+Ababa+Ethiopia', '_blank')">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h3><?php echo $lang['address']; ?></h3>
                            <p><?php echo $settings['address']; ?></p>
                            <div class="click-hint">Get directions</div>
                        </div>
                        
                        <div class="info-card" onclick="window.open('https://wa.me/251989784673', '_blank')">
                            <div class="info-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <h3>WhatsApp</h3>
                            <p>Chat with us instantly</p>
                            <div class="click-hint">Open WhatsApp</div>
                        </div>
                    </div>
                    
                    <div class="hours-card">
                        <h3><i class="fas fa-clock"></i> <?php echo $lang['delivery_hours']; ?></h3>
                        <div class="hours-grid">
                            <div class="hour-item">
                                <span class="day">Monday - Friday</span>
                                <span class="time">6:00 AM - 10:00 AM</span>
                            </div>
                            <div class="hour-item">
                                <span class="day">Saturday</span>
                                <span class="time">6:00 AM - 9:00 AM</span>
                            </div>
                            <div class="hour-item">
                                <span class="day">Sunday</span>
                                <span class="time">7:00 AM - 9:00 AM</span>
                            </div>
                            <div class="hour-item">
                                <span class="day">Public Holidays</span>
                                <span class="time">7:00 AM - 9:00 AM</span>
                            </div>
                        </div>
                        <p style="margin-top: 20px; color: var(--text-secondary); font-size: 13px;">
                            <i class="fas fa-info-circle"></i> Orders placed after 8 PM are delivered the next morning
                        </p>
                    </div>
                </div>
                
                <!-- Right Column - Contact Form -->
                <div class="contact-form-enhanced">
                    <div class="form-header">
                        <h2>Send us a Message</h2>
                        <p>We'll get back to you within 24 hours</p>
                    </div>
                    
                    <form id="contact-form" method="POST" action="send-message.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><?php echo $lang['name']; ?> *</label>
                                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><?php echo $lang['phone']; ?> *</label>
                                <input type="tel" id="phone" name="phone" placeholder="+251 91 234 5678" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><?php echo $lang['email']; ?></label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject">
                                <option value="general">General Inquiry</option>
                                <option value="delivery">Delivery Question</option>
                                <option value="order">Place an Order</option>
                                <option value="feedback">Feedback</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message"><?php echo $lang['message']; ?> *</label>
                            <textarea id="message" name="message" placeholder="How can we help you?" required></textarea>
                        </div>
                        
                        <div class="form-checkbox">
                            <input type="checkbox" id="consent" name="consent" required>
                            <label for="consent">I agree to the <a href="#">privacy policy</a> and consent to being contacted *</label>
                        </div>
                        
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> <?php echo $lang['send_message']; ?>
                        </button>
                        
                        <div id="form-status" style="margin-top: 20px;"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="map-container">
            
            <iframe 
                src="https://www.google.com/maps?q=9.11000,40.85000&output=embed"
                width="100%" 
                height="400" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>

            <div class="map-overlay">
                <i class="fas fa-map-pin"></i>
                <span>📍 Anefarm, Chiro (Asebe Teferi), Ethiopia</span>
            </div>

        </div>
    </div>
</section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
            
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What areas do you deliver to?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We currently deliver to all areas within chiro city limits. For specific locations, please contact us.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Is your milk really pure?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! Our milk is 100% pure with no additives, no powder, and no water. Straight from our farm to your home.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>How do I place an order?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        You can order via WhatsApp, phone call, or through our contact form. We'll confirm your order and delivery time.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        We accept cash on delivery, Telebirr, and bank transfer. Monthly subscription payments are also available.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Do you offer subscription plans?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! We offer daily, alternate day, and weekly subscription plans with special discounts for regular customers.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I visit the farm?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Absolutely! We welcome visitors. Please contact us in advance to schedule a tour of our farm.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" style="padding: 60px 0; text-align: center; background: var(--bg-primary);">
        <div class="container">
            <h2 style="font-size: 32px; margin-bottom: 20px;">Ready to try <span class="text-gradient">fresh, pure milk</span>?</h2>
            <p style="color: var(--text-secondary); margin-bottom: 30px;">Join our happy customers today!</p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="https://wa.me/251989784673" class="btn btn-primary" target="_blank">
                    <i class="fab fa-whatsapp"></i> Order via WhatsApp
                </a>
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-box"></i> View Products
                </a>
            </div>
        </div>
    </section>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/251989784673?text=<?php echo urlencode($lang['whatsapp_text']); ?>" 
       class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script src="assets/js/main.js"></script>
    <script>
        // Initialize map
        const map = L.map('farm-map').setView([9.0320, 38.7469], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add custom marker
        const marker = L.marker([9.0320, 38.7469]).addTo(map);
        marker.bindPopup('<b>Anefarm</b><br>Addis Ababa, Ethiopia').openPopup();
        
        // Form submission
        document.getElementById('contact-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            const formStatus = document.getElementById('form-status');
            
            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('send-message.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if(data.success) {
                    formStatus.innerHTML = '<div class="alert alert-success">✓ ' + data.message + '</div>';
                    this.reset();
                } else {
                    formStatus.innerHTML = '<div class="alert alert-error">✗ ' + data.message + '</div>';
                }
            } catch(error) {
                formStatus.innerHTML = '<div class="alert alert-error">✗ Network error. Please try again.</div>';
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Auto hide status after 5 seconds
                setTimeout(() => {
                    formStatus.innerHTML = '';
                }, 5000);
            }
        });
        
        // FAQ Accordion
        document.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        });
    </script>
</body>
</html>