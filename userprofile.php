<?php
require_once('connection.php');
session_start();
// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("location: index.php");
    exit();
}
$email = $_SESSION['email'];
// Fetch user details
$sql = "SELECT * FROM users WHERE EMAIL='$email'";
$result = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($result);
// Handle password change
$password_message = '';
if (isset($_POST['change_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);
   
    if ($current_password !== $user['PASSWORD']) {
        $password_message = '<div class="error-msg">Current password is incorrect</div>';
    } elseif ($new_password !== $confirm_password) {
        $password_message = '<div class="error-msg">New passwords do not match</div>';
    } else {
        $update_sql = "UPDATE users SET PASSWORD='$new_password' WHERE EMAIL='$email'";
        if (mysqli_query($con, $update_sql)) {
            $password_message = '<div class="success-msg">Password changed successfully</div>';
            $user['PASSWORD'] = $new_password;
        } else {
            $password_message = '<div class="error-msg">Error updating password</div>';
        }
    }
}
// Handle profile update
$profile_message = '';
if (isset($_POST['update_profile'])) {
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $license = mysqli_real_escape_string($con, $_POST['license']);
   
    $update_sql = "UPDATE users SET PHONE_NUMBER='$phone', GENDER='$gender', LIC_NUM='$license' WHERE EMAIL='$email'";
    if (mysqli_query($con, $update_sql)) {
        $profile_message = '<div class="success-msg">Profile updated successfully</div>';
        // Refresh user data
        $result = mysqli_query($con, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $profile_message = '<div class="error-msg">Error updating profile</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - VeloRent</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f8f9fa;
            color: #2c3e50;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: #ffffff;
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
        }
        .navbar img {
            height: 50px;
            transition: transform 0.3s;
        }
        .navbar img:hover {
            transform: scale(1.05);
        }
        .menu ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }
        .menu a, .menu p {
            color: #193959ff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s;
        }
        .menu a:hover {
            color: #3498db;
        }
        .nn {
            background: linear-gradient(45deg, #3498db, #764ba2);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
        }
        .nn a {
            color: #111010ff;
            text-decoration: none;
        }
        .nn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(64, 102, 178, 0.4);
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger span {
            width: 30px;
            height: 3px;
            background: #193959ff;
            margin: 4px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Back Button */
        .back-btn {
            position: fixed;
            top: 30px;
            left: 30px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
            z-index: 100;
            text-decoration: none;
            display: inline-block;
        }
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }
        /* Container */
        .container {
            max-width: 900px;
            margin: 120px auto 40px;
            padding: 20px;
        }
        /* Header */
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .profile-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
        }
        .profile-header p {
            color: #000000ff;
            font-size: 1.1rem;
        }
        /* Cards */
        .profile-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .card-title {
            font-size: 1.8rem;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(66, 104, 200, 0.6);
        }
        /* Profile Info */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #242222ff;
            font-weight: 500;
        }
        .info-value {
            color: #121111ff;
            font-weight: 600;
        }
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #242222ff;
            font-weight: 500;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            border-color: #abb4ddff;
            color: #242222ff;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-group select option {
            background: #fff;
            color: #0c0c0cff;
        }
        /* Buttons */
        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }
        .btn-secondary:hover {
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
        }
        /* Messages */
        .success-msg {
            background: rgba(76, 175, 80, 0.2);
            border: 1px solid #4CAF50;
            color: #4CAF50;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .error-msg {
            background: rgba(244, 67, 54, 0.2);
            border: 1px solid #f44336;
            color: #f44336;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.05);
            padding: 10px 5%;
            text-align: center;
            margin-top: 80px;
        }
        footer p {
            margin-bottom: 20px;
            color: #524f4f;
        }
        .socials {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .socials a {
            color: #333;
            font-size: 1.5rem;
            transition: color 0.3s, transform 0.3s;
        }
        .socials a:hover {
            color: #667eea;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 5%;
            }

            .hamburger {
                display: flex;
            }

            .menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 100%;
                max-width: 300px;
                height: 100vh;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: flex-start;
                padding-top: 80px;
                transition: right 0.4s ease;
                z-index: 1000;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
            }

            .menu.active {
                right: 0;
            }

            .menu ul {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                padding: 0 30px;
                gap: 0;
            }

            .menu li {
                margin: 20px 0;
                width: 100%;
            }

            .menu a, .menu p {
                display: block;
            }
            .container {
                margin-top: 110px;
            }
            .back-btn {
                position: static;
                margin-bottom: 20px;
            }
            .profile-header h1 {
                font-size: 2rem;
            }
            .info-row {
                flex-direction: column;
                gap: 5px;
            }
            .menu a, .menu p {
                font-size: 0.9rem;
            }
        }
        @media (max-width: 480px) {
            .navbar {
                padding: 10px 3%;
            }
            .navbar img {
                height: 40px;
            }
            .menu ul {
                gap: 10px;
            }
            .menu a, .menu p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="icon">
            <a href="vehiclesdetails.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
        </div>
        
        <!-- Hamburger Menu -->
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="menu" id="menu">
            <ul>
                <li><p class="phello"><a id="pname" href="userprofile.php" style="cursor: pointer;"><?php echo htmlspecialchars($user['FNAME'].' '.$user['LNAME']); ?></a></p></li>
                <li><a id="stat" href="bookingstatus.php">BOOKING STATUS</a></li>
                <li><a id="vehicles" href="vehiclesdetails.php">VEHICLES</a></li>
                <li><a href="index.php">LOGOUT</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <div class="profile-header">
            <h1>My Profile</h1>
            <p>Manage your personal information and settings</p>
        </div>
        <!-- Personal Information -->
        <div class="profile-card">
            <h2 class="card-title">Personal Information</h2>
            <div class="info-row">
                <span class="info-label">Full Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['FNAME'] . ' ' . $user['LNAME']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['EMAIL']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone Number:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['PHONE_NUMBER']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">License Number:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['LIC_NUM']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Gender:</span>
                <span class="info-value"><?php echo htmlspecialchars(ucfirst($user['GENDER'])); ?></span>
            </div>
        </div>
        <!-- Update Profile -->
        <div class="profile-card">
            <h2 class="card-title">Update Profile</h2>
            <?php echo $profile_message; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['PHONE_NUMBER']); ?>" required>
                </div>
                <div class="form-group">
                    <label>License Number</label>
                    <input type="text" name="license" value="<?php echo htmlspecialchars($user['LIC_NUM']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="male" <?php echo ($user['GENDER'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($user['GENDER'] == 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo ($user['GENDER'] == 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <button type="submit" name="update_profile" class="btn btn-secondary">Update Profile</button>
            </form>
        </div>
        <!-- Change Password -->
        <div class="profile-card">
            <h2 class="card-title">Change Password</h2>
            <?php echo $password_message; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn">Change Password</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
        <div class="socials">
            <a href="https://www.facebook.com/thomasbhattrai " target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://x.com/thomashbhattarai " target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="https://www.instagram.com/swostimakaju/ " target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js "></script>
    <script>
        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            menu.classList.toggle('active');
            
            // Prevent scrolling when menu is open
            if(menu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking on a link
        const menuLinks = document.querySelectorAll('.menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>