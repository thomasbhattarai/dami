<?php
require_once('connection.php');
session_start();

$step = 1;
$message = '';
$error = '';

// Step 1: Verify Email and License Number
if (isset($_POST['verify'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $license = mysqli_real_escape_string($con, $_POST['license']);
    
    if (empty($email) || empty($license)) {
        $error = 'Please fill in all fields';
    } else {
        $query = "SELECT * FROM users WHERE EMAIL='$email' AND LIC_NUM='$license'";
        $result = mysqli_query($con, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['reset_email'] = $email;
            $step = 2;
            $message = 'Verification successful! Please enter your new password.';
        } else {
            $error = 'Email or License Number does not match our records';
        }
    }
}

// Step 2: Reset Password
if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['reset_email'])) {
        header("location: forgotpassword.php");
        exit();
    }
    
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
        $step = 2;
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long';
        $step = 2;
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
        $step = 2;
    } else {
        $email = $_SESSION['reset_email'];
        $hashed_password = md5($new_password);
        
        $update_query = "UPDATE users SET PASSWORD='$hashed_password' WHERE EMAIL='$email'";
        
        if (mysqli_query($con, $update_query)) {
            unset($_SESSION['reset_email']);
            $step = 3; // Success step
        } else {
            $error = 'Error updating password. Please try again.';
            $step = 2;
        }
    }
}

// Check if already verified
if (isset($_SESSION['reset_email']) && !isset($_POST['verify'])) {
    $step = 2;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - VeloRent</title>
    <script src="main.js" defer></script>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #fff;
            color: linear-gradient(135deg, #0c0c0c, #1a1a2e);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Back to Login Link */
        .back-link {
            position: fixed;
            top: 30px;
            left: 30px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: #8db5dcff;
        }

        .back-link::before {
            content: '←';
            font-size: 1.5rem;
        }

        /* Container */
        .container {
            max-width: 500px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .header p {
            color: #333131ff;
            font-size: 0.95rem;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .step.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(255, 123, 0, 0.5);
        }

        .step-line {
            width: 50px;
            height: 2px;
            background: rgba(255, 255, 255, 0.2);
        }

        /* Messages */
        .message {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid #4CAF50;
            color: #4CAF50;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .error {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid #f44336;
            color: #ff6b6b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #3c3c3cff;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            border-color: #b9cad8ff;
            color: #100f0fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #7867e8ff;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(255, 123, 0, 0.3);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Button */
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(25, 110, 248, 0.77);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(17, 132, 255, 0.73);
        }

        .btn:active {
            transform: translateY(-1px);
        }

        /* Info Box */
        .info-box {
            background: rgba(33, 150, 243, 0.1);
            border: 1px solid rgba(33, 150, 243, 0.3);
            color: #64B5F6;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Success Box */
        .success-box {
            text-align: center;
            padding: 30px 20px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease;
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .success-box h2 {
            color: #4CAF50;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .success-box p {
            color: #aaa;
            font-size: 1rem;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .back-link {
                position: static;
                margin-bottom: 20px;
            }

            .container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .step-line {
                width: 30px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .container {
                padding: 25px 15px;
            }

            .step {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">Back to Login</a>

    <div class="container">
        <div class="header">
            <h1>Reset Password</h1>
            <p>Verify your identity to reset your password</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step <?php echo ($step == 1) ? 'active' : ''; ?>">1</div>
            <div class="step-line"></div>
            <div class="step <?php echo ($step == 2) ? 'active' : ''; ?>">2</div>
            <div class="step-line"></div>
            <div class="step <?php echo ($step == 3) ? 'active' : ''; ?>">✓</div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <!-- Step 1: Verify Email and License -->
            <div class="info-box">
                <strong>Step 1:</strong> Enter your email and license number to verify your identity.
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                </div>

                <div class="form-group">
                    <label for="license">License Number</label>
                    <input type="text" id="license" name="license" placeholder="Enter your license number" required>
                </div>

                <button type="submit" name="verify" class="btn">Verify Identity</button>
            </form>

        <?php else: ?>
            <!-- Step 2: Reset Password -->
            <div class="info-box">
                <strong>Step 2:</strong> Create a new password for your account. Password must be at least 6 characters long.
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                </div>

                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>

        <?php if ($step == 3): ?>
            <!-- Step 3: Success Message -->
            <div class="success-box">
                <div class="success-icon">✓</div>
                <h2>Password Reset Successful!</h2>
                <p>Your password has been updated successfully.</p>
                <p>You can now login with your new password.</p>
                <a href="index.php" class="btn" style="display: inline-block; text-decoration: none; margin-top: 20px;">Go to Login</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form validation for password matching
        <?php if ($step == 2): ?>
        const form = document.querySelector('form');
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('confirm_password');

        form.addEventListener('submit', function(e) {
            if (newPassword.value !== confirmPassword.value) {
                e.preventDefault();
                showDialog('Passwords do not match!');
                confirmPassword.focus();
            } else if (newPassword.value.length < 6) {
                e.preventDefault();
                showDialog('Password must be at least 6 characters long!');
                newPassword.focus();
            }
        });

        // Real-time password match indicator
        confirmPassword.addEventListener('input', function() {
            if (newPassword.value !== '' && confirmPassword.value !== '') {
                if (newPassword.value === confirmPassword.value) {
                    confirmPassword.style.borderColor = '#4CAF50';
                } else {
                    confirmPassword.style.borderColor = '#ff6b6b';
                }
            } else {
                confirmPassword.style.borderColor = 'rgba(255, 255, 255, 0.2)';
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
