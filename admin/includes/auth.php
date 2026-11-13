<?php
session_start();

require_once '../includes/config.php';
require_once '../includes/Auth.php';

$auth = new Auth($pdo);

// Check remember me
$auth->checkRememberMe();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get current user
$currentUser = $auth->getUser();

// For backward compatibility
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = $currentUser['username'] ?? '';
$_SESSION['admin_role'] = $currentUser['role'] ?? 'editor';
?>