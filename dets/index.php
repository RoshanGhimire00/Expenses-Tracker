<?php 
session_start();
// Don't suppress errors during development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Include database connection
include('includes/dbconnection.php');

// Initialize variables
$msg = '';
$success = false;

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Consider using password_hash() instead
    
    // Using prepared statements to prevent SQL injection
    $query = "SELECT ID FROM tbluser WHERE Email = ? AND Password = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $ret = mysqli_fetch_array($result);
        
        if($ret) {
            $_SESSION['detsuid'] = $ret['ID'];
            header('location: dashboard.php');
            exit();
        } else {
            $msg = "Invalid email or password.";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $msg = "Database error. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Login</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="css/login-style.css" rel="stylesheet">
    
    <script>
    // Client-side form validation
    function validateLoginForm() {
        var email = document.forms["login"]["email"].value;
        var password = document.forms["login"]["password"].value;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }
        
        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }
        
        return true;
    }
    
    // Toggle password visibility
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var toggleIcon = document.getElementById("togglePasswordIcon");
        
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Daily Expense Tracker</h1>
            <p class="tagline">Manage your finances, achieve your goals</p>
        </div>
        
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="login-card">
                    <div class="card-header">
                        <h2><i class="fas fa-sign-in-alt"></i> Login to Your Account</h2>
                    </div>
                    
                    <div class="card-body">
                        <?php if(!empty($msg)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form name="login" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return validateLoginForm();">
                            <div class="form-group input-icon">
                                <i class="fas fa-envelope"></i>
                                <input class="form-control" 
                                       placeholder="Email Address" 
                                       name="email" 
                                       type="email" 
                                       autofocus 
                                       required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="form-group input-icon">
                                <i class="fas fa-lock"></i>
                                <input class="form-control" 
                                       placeholder="Password" 
                                       name="password" 
                                       type="password" 
                                       id="password"
                                       required>
                                <i class="fas fa-eye toggle-password" id="togglePasswordIcon" onclick="togglePassword()"></i>
                            </div>
                            
                            <div class="form-options">
                                <a href="forgot-password.php" class="forgot-password">
                                    <i class="fas fa-key"></i> Forgot Password?
                                </a>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" name="login" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                            </div>
                            
                            <div class="divider">
                                <span>or</span>
                            </div>
                            
                            <div class="form-group text-center register-link">
                                <p>Don't have an account?</p>
                                <a href="register.php" class="btn btn-secondary">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="footer">
                    <p>&copy; <?php echo date('Y'); ?> Daily Expense Tracker. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Focus on email field with animation
    document.addEventListener('DOMContentLoaded', function() {
        var emailField = document.querySelector('input[name="email"]');
        if(emailField) {
            emailField.focus();
        }
        
        // Add animation to login button
        var loginBtn = document.querySelector('button[name="login"]');
        if(loginBtn) {
            loginBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
            });
            
            loginBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        }
    });
    </script>
</body>
</html>
