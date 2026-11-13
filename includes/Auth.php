<?php
require_once 'config.php';

class Auth {
    private $pdo;
    private $user = null;
    // Security constants
    const SESSION_TIMEOUT = 1800; // 30 minutes
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutes
    const REMEMBER_ME_DAYS = 30;
    const PASSWORD_MIN_LENGTH = 8;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->startSecureSession();
    }
    
    /**
     * Start a secure session
     */
    private function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_lifetime', 0);
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
        }
    }
    
    /**
     * Attempt to login user
     */
    public function login($username, $password, $remember = false) {
        try {
            // Check if account is locked
            if ($this->isAccountLocked($username)) {
                $this->logLoginAttempt(null, false, 'Account locked');
                return ['success' => false, 'message' => 'Account is temporarily locked. Please try again later.'];
            }
            
            // Get user by username or email
            $stmt = $this->pdo->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $this->incrementLoginAttempts($username);
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->incrementLoginAttempts($user['id']);
                $this->logLoginAttempt($user['id'], false, 'Invalid password');
                return ['success' => false, 'message' => 'Invalid username or password'];
            }
            
            // Check if password needs rehash
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $this->rehashPassword($user['id'], $password);
            }
            
            // Login successful
            $this->createUserSession($user, $remember);
            $this->resetLoginAttempts($user['id']);
            $this->logLoginAttempt($user['id'], true, 'Login successful');
            $this->updateLastLogin($user['id']);
            
            return ['success' => true, 'user' => $user];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
    
    /**
     * Create user session
     */
    private function createUserSession($user, $remember = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        
        session_regenerate_id(true);
        
        // Set remember me cookie
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }
    }
    
    /**
     * Set remember me cookie
     */
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + (self::REMEMBER_ME_DAYS * 24 * 60 * 60));
        
        // Store token in database
        $stmt = $this->pdo->prepare("INSERT INTO admin_remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expires]);
        
        setcookie(
            'remember_token',
            $token,
            [
                'expires' => strtotime($expires),
                'path' => '/anefarm/admin',
                'domain' => '',
                'secure' => false, // Set to true if using HTTPS
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > self::SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }
        
        // Check user agent
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            $this->logout();
            return false;
        }
        
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        $user = $this->getUser();
        if (!$user) return false;
        
        if ($role === 'super_admin' && $user['role'] === 'super_admin') {
            return true;
        }
        
        if ($role === 'admin' && in_array($user['role'], ['super_admin', 'admin'])) {
            return true;
        }
        
        if ($role === 'editor' && in_array($user['role'], ['super_admin', 'admin', 'editor'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get current user
     */
    public function getUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        if ($this->user === null) {
            $stmt = $this->pdo->prepare("SELECT id, username, email, full_name, role, last_login, two_factor_enabled FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $this->user = $stmt->fetch();
        }
        
        return $this->user;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            $this->invalidateRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/anefarm/admin');
        }
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Change user password
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $stmt = $this->pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Validate new password
            $passwordValidation = $this->validatePassword($newPassword);
            if (!$passwordValidation['valid']) {
                return ['success' => false, 'message' => $passwordValidation['message']];
            }
            
            // Hash and save new password
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
            
            if ($stmt->execute([$newHash, $userId])) {
                return ['success' => true, 'message' => 'Password changed successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to change password'];
            
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }
    
    /**
     * Validate password strength
     */
    public function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "Password must be at least " . self::PASSWORD_MIN_LENGTH . " characters long";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        if (empty($errors)) {
            return ['valid' => true, 'message' => 'Password is valid'];
        }
        
        return ['valid' => false, 'message' => implode('. ', $errors)];
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Check if account is locked
     */
    private function isAccountLocked($username) {
        $stmt = $this->pdo->prepare("SELECT locked_until FROM admin_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && $user['locked_until']) {
            $lockedUntil = strtotime($user['locked_until']);
            if ($lockedUntil > time()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Increment login attempts
     */
    private function incrementLoginAttempts($identifier) {
        $field = is_numeric($identifier) ? 'id' : 'username';
        
        $stmt = $this->pdo->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1 WHERE $field = ?");
        $stmt->execute([$identifier]);
        
        // Check if we need to lock the account
        $stmt = $this->pdo->prepare("SELECT login_attempts FROM admin_users WHERE $field = ?");
        $stmt->execute([$identifier]);
        $attempts = $stmt->fetchColumn();
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $lockedUntil = date('Y-m-d H:i:s', time() + self::LOCKOUT_TIME);
            $stmt = $this->pdo->prepare("UPDATE admin_users SET locked_until = ? WHERE $field = ?");
            $stmt->execute([$lockedUntil, $identifier]);
        }
    }
    
    /**
     * Reset login attempts
     */
    private function resetLoginAttempts($userId) {
        $stmt = $this->pdo->prepare("UPDATE admin_users SET login_attempts = 0, locked_until = NULL WHERE id = ?");
        $stmt->execute([$userId]);
    }
    
    /**
     * Log login attempt
     */
    private function logLoginAttempt($userId, $status, $notes = '') {
        if (!$userId) return;
        
        $stmt = $this->pdo->prepare("INSERT INTO admin_login_history (user_id, login_time, ip_address, user_agent, status) VALUES (?, NOW(), ?, ?, ?)");
        $stmt->execute([
            $userId,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $status ? 'success' : 'failed'
        ]);
    }
    
    /**
     * Update last login
     */
    private function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("UPDATE admin_users SET last_login = NOW(), last_login_ip = ? WHERE id = ?");
        $stmt->execute([$_SERVER['REMOTE_ADDR'], $userId]);
    }
    
    /**
     * Rehash password
     */
    private function rehashPassword($userId, $password) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHash, $userId]);
    }
    
    /**
     * Invalidate remember token
     */
    private function invalidateRememberToken($token) {
        $stmt = $this->pdo->prepare("DELETE FROM admin_remember_tokens WHERE token = ?");
        $stmt->execute([$token]);
    }
    
    /**
     * Check remember me token
     */
    public function checkRememberMe() {
        if (isset($_COOKIE['remember_token']) && !$this->isLoggedIn()) {
            $token = $_COOKIE['remember_token'];
            
            $stmt = $this->pdo->prepare("SELECT user_id FROM admin_remember_tokens WHERE token = ? AND expires_at > NOW()");
            $stmt->execute([$token]);
            $result = $stmt->fetch();
            
            if ($result) {
                $stmt = $this->pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
                $stmt->execute([$result['user_id']]);
                $user = $stmt->fetch();
                
                if ($user) {
                    $this->createUserSession($user, true);
                    return true;
                }
            }
        }
        return false;
    }
}
?>