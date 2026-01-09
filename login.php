<?php
require_once('connection.php');
session_start();

// Unified login handler - Auto-detect Admin or User
if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $pass = $_POST['pass'] ?? '';

    if ($username === '' || $pass === '') {
        $error = "All fields are required";
    } else {
        // First check if username is an admin ID
        $stmt = mysqli_prepare($con, "SELECT ADMIN_PASSWORD FROM admin WHERE ADMIN_ID = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($res)) {
            // Found in admin table
            $stored = $row['ADMIN_PASSWORD'];
            $ok = false;
            
            // Accept legacy formats and bcrypt
            if (strlen($stored) === 32 && ctype_xdigit($stored)) {
                $ok = (md5($pass) === strtolower($stored));
            } elseif (preg_match('/^\$2y\$\d{2}\$/', $stored)) {
                $ok = password_verify($pass, $stored);
            } else {
                $ok = ($pass === $stored);
            }

            if ($ok) {
                // Migrate to bcrypt if not already
                if (!preg_match('/^\$2y\$\d{2}\$/', $stored)) {
                    $newHash = password_hash($pass, PASSWORD_BCRYPT);
                    $u = mysqli_prepare($con, "UPDATE admin SET ADMIN_PASSWORD = ? WHERE ADMIN_ID = ?");
                    mysqli_stmt_bind_param($u, 'ss', $newHash, $username);
                    mysqli_stmt_execute($u);
                    mysqli_stmt_close($u);
                }
                $_SESSION['admin_id'] = $username;
                header("Location: adminusers.php");
                exit();
            } else {
                $error = "Invalid password";
            }
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            
            // Check if username is an email in users table
            $stmt = mysqli_prepare($con, "SELECT PASSWORD FROM users WHERE EMAIL = ?");
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($res)) {
                // Found in users table
                $stored = $row['PASSWORD'];
                $ok = false;
                
                // Legacy MD5 support with migration
                if (strlen($stored) === 32 && ctype_xdigit($stored)) {
                    if (md5($pass) === strtolower($stored)) {
                        $ok = true;
                        $newHash = password_hash($pass, PASSWORD_BCRYPT);
                        $u = mysqli_prepare($con, "UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
                        mysqli_stmt_bind_param($u, 'ss', $newHash, $username);
                        mysqli_stmt_execute($u);
                        mysqli_stmt_close($u);
                    }
                } else {
                    $ok = password_verify($pass, $stored);
                }

                if ($ok) {
                    $_SESSION['email'] = $username;
                    header("Location: vehiclesdetails.php");
                    exit();
                } else {
                    $error = "Invalid password";
                }
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);
                $error = "Username/Email not found";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login | VeloRent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8f9fa;
            font-family: Poppins, sans-serif;
            color: #2c3e50;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main, .form-container {
            flex: 1;
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(255, 255, 255, 0.98);
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
        }

        .menu li {
            margin-left: 40px;
        }

        .menu a {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s, border-bottom 0.3s;
            padding-bottom: 5px;
            position: relative;
        }

        .menu a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }

        .menu a:hover {
            color: #667eea;
        }

        .menu a:hover::after {
            width: 100%;
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
            background: #2c3e50;
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

        @media (max-width: 768px) {
            .hamburger {
                display: flex;
            }

            .menu {
                position: absolute;
                top: 70px;
                right: 20px;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                display: none;
                z-index: 1000;
            }

            .menu.active {
                display: block;
            }

            .menu ul {
                flex-direction: column;
                gap: 15px;
            }

            .menu li {
                margin-left: 0;
            }
        }

        .form {
            background: #fff;
            max-width: 520px;
            margin: 120px auto;
            padding: 45px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .form input, .form select {
            width: 100%;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form input:focus, .form select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btnn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .btnn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .error {
            background: #fadbd8;
            color: #c0392b;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
        }

        a {
            text-decoration: none;
            color: #667eea;
        }

        .form p a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.05);
            padding: 30px 5%;
            text-align: center;
            margin-top: 80px;
            border-top: 1px solid #e0e6ed;
        }

        footer p {
            margin-bottom: 15px;
            color: #524f4f;
            font-weight: 500;
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
            transform: scale(1.2);
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="index.php">
            <img src="images/icon.png" alt="VeloRent Logo">
        </a>
        
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="menu" id="menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="aboutus.html">About Us</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </nav>  
    <div class="form">
        <h2>Login</h2>

        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" id="loginForm">
            <input type="text" name="username" placeholder="Email Address or Admin ID" autocomplete="username" required>
            <input type="password" name="pass" placeholder="Password" required autocomplete="current-password">
            <input type="submit" name="login" value="Login" class="btnn">
        </form>

        <p style="text-align:center;margin-top:15px;">
            New user? <a href="register.php">Create Account</a>
        </p>
    </div>

    <footer>
        <p>&copy; 2025 VeloRent. All Rights Reserved.</p>
        <div class="socials">
            <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://x.com/" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            menu.classList.toggle('active');
            if(menu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        const menuLinks = document.querySelectorAll('.menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

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