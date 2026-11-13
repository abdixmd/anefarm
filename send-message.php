<?php
require_once 'includes/config.php';

// Disable error display to prevent JSON breakage
ini_set('display_errors', 0);
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and sanitize input
$name = strip_tags(trim($_POST['name'] ?? ''));
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone = strip_tags(trim($_POST['phone'] ?? ''));
$subject = strip_tags(trim($_POST['subject'] ?? 'General Inquiry'));
$message_content = strip_tags(trim($_POST['message'] ?? ''));

// Combine subject and message
$full_message = "Subject: $subject\n\n$message_content";

// Validate
$errors = [];

if(empty($name)) {
    $errors[] = 'Name is required';
}

if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if(empty($phone)) {
    $errors[] = 'Phone is required';
}

if(empty($message_content)) {
    $errors[] = 'Message is required';
}

if(!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $full_message]);
    
    // Optional: Send email notification
    $to = $settings['email'] ?? '';
    if($to) {
        $mail_subject = "New message from Anefarm: $subject";
        $body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message_content";
        @mail($to, $mail_subject, $body);
    }
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred. Please try again later.']);
}
?>