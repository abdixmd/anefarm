<?php
session_start();

if (isset($_POST['theme'])) {
    $theme = $_POST['theme'];
    
    // Validate and set session
    if ($theme === 'dark') {
        $_SESSION['theme'] = 'dark-theme';
    } elseif ($theme === 'light') {
        $_SESSION['theme'] = 'light-theme';
    } else {
        unset($_SESSION['theme']); // Auto/System
    }
}
?>