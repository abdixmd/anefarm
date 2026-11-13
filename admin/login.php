<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

require_once '../includes/config.php';
require_once '../includes/Auth.php';

$auth = new Auth($pdo);

// Check remember me
if ($auth->checkRememberMe()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    $rateLimitKey = 'login_attempts_' . $ip;
    $attempts = $_SESSION[$rateLimitKey] ?? 0;
    
    if ($attempts >= 5) {
        $error = 'Too many login attempts. Please try again later.';
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        $result = $auth->login($username, $password, $remember);
        
        if ($result['success']) {
            $_SESSION['admin_logged_in'] = true; // For backward compatibility
            $_SESSION['admin_username'] = $username;
            header('Location: dashboard.php');
            exit();
        } else {
            $error = $result['message'];
            $_SESSION[$rateLimitKey] = ($_SESSION[$rateLimitKey] ?? 0) + 1;
        }
    }
}

// Generate CSRF token for future use (optional)
// $csrf_token = $auth->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AneDiaryFarm Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #00a400 0%, #008000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.03) 0px,
                rgba(255,255,255,0.03) 20px,
                rgba(255,255,255,0.05) 20px,
                rgba(255,255,255,0.05) 40px
            );
            animation: move 20s linear infinite;
        }

        @keyframes move {
            0% { transform: translate(-10%, -10%) rotate(0deg); }
            100% { transform: translate(-30%, -30%) rotate(10deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            font-size: 42px;
            font-weight: 700;
            background: linear-gradient(135deg, #00a400, #008000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 5px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #f0f9f0;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 25px;
            color: #00a400;
            font-size: 13px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 500;
            font-size: 24px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            transition: color 0.3s;
        }

        .input-group .toggle-password {
            left: auto;
            right: 15px;
            cursor: pointer;
        }

        .input-group input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }

        .input-group input:focus {
            outline: none;
            border-color: #00a400;
            box-shadow: 0 0 0 4px rgba(0,164,0,0.1);
        }

        .input-group input:focus + i {
            color: #00a400;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #c62828;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .remember-forgot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #00a400;
        }

        .forgot-link {
            color: #00a400;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #00a400, #008000);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        button:hover::before {
            left: 100%;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,164,0,0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .security-note {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .security-note span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .security-note i {
            color: #00a400;
        }

        .farm-icon {
            text-align: center;
            margin-bottom: 20px;
        }

        .farm-icon i {
            font-size: 60px;
            color: #00a400;
            opacity: 0.8;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="farm-icon">
            <i class="fas fa-tractor"></i>
        </div>
        <div class="logo">
            <h1>AneDiaryFarm</h1>
            <p>Admin Dashboard</p>
        </div>
        
        <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Secure Admin Access • Database Encrypted</span>
        </div>
        
        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="error-message" style="background: #e8f5e8; color: #00a400;">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username or Email" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required autofocus>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
            </div>
            
            <div class="remember-forgot">
                <label class="remember">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </label>
                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
            </div>
            
            <button type="submit" id="submitBtn">
                <i class="fas fa-sign-in-alt"></i>
                Login to Dashboard
            </button>
        </form>
        
        <div class="security-note">
            <span><i class="fas fa-lock"></i> Encrypted</span>
            <span><i class="fas fa-clock"></i> 30 min timeout</span>
            <span><i class="fas fa-shield"></i> 5 attempts max</span>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Add loading state on submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const button = document.getElementById('submitBtn');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
            button.disabled = true;
        });

        // Auto-focus username field
        window.onload = function() {
            document.querySelector('input[name="username"]').focus();
        };
    </script>
</body>
</html>