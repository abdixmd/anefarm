<?php
use Auth;
require_once 'includes/auth.php';
require_once '../includes/Auth.php';

$auth = new Auth($pdo);
$user = $auth->getUser();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if ($newPassword !== $confirmPassword) {
        $message = 'New passwords do not match';
        $messageType = 'error';
    } else {
        $result = $auth->changePassword($user['id'], $currentPassword, $newPassword);
        
        if ($result['success']) {
            $message = $result['message'];
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'error';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="password-change" style="padding: 30px; max-width: 600px; margin: 0 auto;">
    <h1><i class="fas fa-key"></i> Change Password</h1>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required class="form-control">
        </div>
        
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required class="form-control">
            <small>Must be at least 8 characters with uppercase, lowercase, number, and special character</small>
        </div>
        
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Change Password</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>