<?php
/**
 * Admin Login Page
 */
session_start();
require_once 'config/auth.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$timeout = isset($_GET['timeout']) && $_GET['timeout'] == 1;

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        $result = authenticateAdmin($username, $password);
        
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LPS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .login-logo i {
            font-size: 3rem;
            color: #00723F;
        }
        
        .login-logo h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: #00723F;
        }
        
        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .login-card {
            background: #ffffff;
            padding: 2.5rem;
            border: 4px solid #FFC107;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 2px solid;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-danger {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #212529;
            font-size: 0.95rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #dee2e6;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #FFC107;
            background: #fff9e6;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .input-group .form-control {
            padding-left: 2.75rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #FFC107;
            border: 3px solid #FFC107;
            color: #000000;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #000000;
            color: #FFC107;
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        
        .login-footer a {
            color: #000000;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            color: #FFC107;
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
            }
            
            .login-logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-school"></i>
                <h1>LPS</h1>
            </div>
            <p class="login-subtitle">Lukman Primary School - Admin Panel</p>
        </div>
        
        <div class="login-card">
            <?php if ($timeout): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-clock"></i>
                    <span>Your session has expired. Please login again.</span>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            placeholder="Enter your username"
                            required 
                            autofocus
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Enter your password"
                            required
                        >
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
