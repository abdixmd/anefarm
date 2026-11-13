<?php
require_once 'config.php';

function getOwners($pdo) {
    $stmt = $pdo->query("SELECT * FROM owners ORDER BY sort_order");
    return $stmt->fetchAll();
}

function getProducts($pdo, $featured_only = false) {
    $sql = "SELECT * FROM products ORDER BY sort_order";
    if($featured_only) {
        $sql = "SELECT * FROM products WHERE is_featured = TRUE ORDER BY sort_order";
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function getGallery($pdo, $limit = null) {
    $sql = "SELECT * FROM gallery ORDER BY sort_order, created_at DESC";
    if($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}
?>