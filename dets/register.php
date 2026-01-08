<?php
// Start PHP session and include database connection
session_start();

// Initialize variables
$msg = '';
$success = false;

// Check if form is submitted
if(isset($_POST['submit'])) {
    // Database connection
    $servername = "localhost";
    $username = "root"; // default XAMPP username
    $password = ""; // default XAMPP password
    $dbname = "expense_tracker"; // your database name
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobilenumber = $_POST['mobilenumber'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $msg = "Email already exists. Please use a different email.";
        $success = false;
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, mobilenumber, password, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $mobilenumber, $password);
        
        if($stmt->execute()) {
            $msg = "Registration successful! Redirecting to login page...";
            $success = true;
            
            // Store user data in session
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
        } else {
            $msg = "Error: " . $conn->error;
            $success = false;
        }
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Signup</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Your custom CSS -->
    <link rel="stylesheet" href="./css/signup-style.css">
    
    <script type="text/javascript">
    function checkpass() {
        var password = document.signup.password.value;
        var repeatPassword = document.signup.repeatpassword.value;
        
        // Check password length
        if(password.length < 8) {
            alert('Password must be at least 8 characters long');
            document.signup.password.focus();
            return false;
        }
        
        // Check if passwords match
        if(password != repeatPassword) {
            alert('Password and Repeat Password field does not match');
            document.signup.repeatpassword.focus();
            return false;
        }
        
        return true;
    }
    
    // Real-time password match validation
    function validatePassword() {
        var password = document.getElementById('password').value;
        var repeatPassword = document.getElementById('repeatpassword').value;
        var errorMsg = document.getElementById('passwordError');
        
        if(password !== repeatPassword && repeatPassword !== '') {
            errorMsg.textContent = 'Passwords do not match';
            errorMsg.style.color = '#f72585';
            return false;
        } else {
            errorMsg.textContent = '';
            return true;
        }
    }
    
    // Password strength indicator
    function checkPasswordStrength() {
        var password = document.getElementById('password').value;
        var strengthText = document.getElementById('passwordStrength');
        
        if(!strengthText) {
            // Create strength indicator if it doesn't exist
            var parent = document.getElementById('password').parentNode;
            strengthText = document.createElement('small');
            strengthText.id = 'passwordStrength';
            strengthText.style.display = 'block';
            strengthText.style.marginTop = '5px';
            strengthText.style.fontSize = '0.85rem';
            parent.appendChild(strengthText);
        }
        
        var strength = 0;
        
        if(password.length >= 8) strength++;
        if(/[A-Z]/.test(password)) strength++;
        if(/[a-z]/.test(password)) strength++;
        if(/[0-9]/.test(password)) strength++;
        if(/[^A-Za-z0-9]/.test(password)) strength++;
        
        switch(strength) {
            case 0:
            case 1:
            case 2:
                strengthText.textContent = 'Password strength: Weak';
                strengthText.style.color = '#f72585';
                break;
            case 3:
            case 4:
                strengthText.textContent = 'Password strength: Good';
                strengthText.style.color = '#ff9e00';
                break;
            case 5:
                strengthText.textContent = 'Password strength: Strong';
                strengthText.style.color = '#2ec4b6';
                break;
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Daily Expense Tracker</h1>
            <p>Track your expenses, maximize your savings</p>
        </div>
        
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <div class="signup-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-plus"></i> Sign Up</h2>
                    </div>
                    
                    <div class="card-body">
                        <?php if(!empty($msg)): ?>
                            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                                <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                                <?php echo htmlspecialchars($msg); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form role="form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" name="signup" onsubmit="return checkpass();">
                            <div class="form-group input-icon">
                                <i class="fas fa-user"></i>
                                <input class="form-control" placeholder="Full Name" name="name" type="text" required 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            
                            <div class="form-group input-icon">
                                <i class="fas fa-envelope"></i>
                                <input class="form-control" placeholder="E-mail" name="email" type="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="form-group input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" 
                                       placeholder="Mobile Number" maxlength="10" pattern="[0-9]{10}" required
                                       value="<?php echo isset($_POST['mobilenumber']) ? htmlspecialchars($_POST['mobilenumber']) : ''; ?>">
                            </div>
                            
                            <div class="form-group input-icon">
                                <i class="fas fa-lock"></i>
                                <input class="form-control" placeholder="Password" id="password" name="password" 
                                       type="password" required onkeyup="validatePassword(); checkPasswordStrength();">
                            </div>
                            
                            <div class="form-group input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" class="form-control" id="repeatpassword" name="repeatpassword" 
                                       placeholder="Repeat Password" required onkeyup="validatePassword()">
                                <small id="passwordError" class="error-message"></small>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" value="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus"></i> Register
                                </button>
                                
                                <div class="mt-20">
                                    <p>Already have an account?</p>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-sign-in-alt"></i> Login Here
                                    </a>
                                </div>
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
    // Auto-redirect if registration was successful
    <?php if($success): ?>
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 2000);
    <?php endif; ?>
    
    // Form persistence on page refresh
    document.addEventListener('DOMContentLoaded', function() {
        // Validate password on page load if there's content
        validatePassword();
        
        // Check password strength if password field has content
        var passwordField = document.getElementById('password');
        if(passwordField.value) {
            checkPasswordStrength();
        }
    });
    </script>
</body>
</html>
