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
            background: #fff;
            color: #linear-gradient(135deg, #0c0c0c, #1a1a2e);
            min-height: 100vh;
            padding: 20px;
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
            margin: 80px auto 40px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin-top: 100px;
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
        }
    </style>
</head>
<body>
    <a href="vehiclesdetails.php" class="back-btn">← Back to Home</a>

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
</body>
</html>
