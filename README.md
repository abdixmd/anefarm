# Anefarm (AneDairyFarm) 🥛🌱

Anefarm is a modern, responsive web application and management system for a dairy farm based in Asebe Teferi, Ethiopia. It features a public-facing website with multilingual support (English, Afaan Oromo, Amharic), interactive gallery, product catalog, customer contact messaging, and a comprehensive admin management dashboard.

## 🚀 Features

- **Public Website:**
  - Modern, mobile-responsive design with dark/light theme support
  - Multilingual support: English (EN), Afaan Oromo (OM), Amharic (AM)
  - Hero section with video background
  - Product showcase with stock and featured indicators
  - Rich photo gallery with categorization
  - Farm story and owners/team profiles
  - Contact form with direct message delivery

- **Admin Dashboard:**
  - Secure authentication and session management
  - Product management (CRUD operations, featured items, stock status)
  - Gallery management (upload, organize, categorize photos)
  - Farm owners/team management
  - General settings and contact message inbox
  - Password management and security settings

## 🛠️ Tech Stack

- **Backend:** PHP 7.4+ / 8.x (PDO MySQL)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript, FontAwesome
- **Server:** Apache (XAMPP / WampServer / LAMP)

## 📦 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL or MariaDB
- Apache Web Server (e.g. XAMPP)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/<username>/anefarm.git
   ```

2. **Move to web server directory:**
   Place the project folder into your web root (e.g., `C:/xampp/htdocs/anefarm`).

3. **Database Setup:**
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a new database named `anefarm`.
   - Import `database.sql` into the `anefarm` database.

4. **Configuration:**
   - Check `includes/config.php` and update database credentials if necessary:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'anefarm');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

5. **Run the Application:**
   - Start Apache and MySQL in XAMPP.
   - Visit `http://localhost/anefarm` in your browser.
   - Access the admin panel at `http://localhost/anefarm/admin`.

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).
