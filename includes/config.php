<?php
// session_start();

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'anefarm');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site configuration
define('SITE_URL', 'http://localhost/anefarm');
define('SITE_NAME', 'Anefarm');

// --- Language Setup ---
$supported_languages = [
    'en' => ['label' => 'EN', 'name' => 'English'],
    'om' => ['label' => 'OM', 'name' => 'Afaan Oromo'],
    'am' => ['label' => 'አማ', 'name' => 'Amharic']
];

// Handle language change via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
    $selected_lang = $_POST['lang'];
    if (array_key_exists($selected_lang, $supported_languages)) {
        $_SESSION['lang'] = $selected_lang;
    }
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Set default language if not set or invalid
if (!isset($_SESSION['lang']) || !array_key_exists($_SESSION['lang'], $supported_languages)) {
    $_SESSION['lang'] = 'en';
}

// Include language file
require_once __DIR__ . '/../languages/' . $_SESSION['lang'] . '.php';

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get settings
$settings = [];
$stmt = $pdo->query("SELECT * FROM settings");
while($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['value_' . $_SESSION['lang']];
}

// Get current page for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>