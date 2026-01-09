<?php
    require_once('connection.php');
    session_start();
    
    if(isset($_POST['adlog'])){
        $id=$_POST['adid'];
        $pass=$_POST['adpass'];
        
        if(empty($id)|| empty($pass))
        {
            $error_message = "Please fill all the blanks";
        }
        else {
            $query="select *from admin where ADMIN_ID='$id'";
            $res=mysqli_query($con,$query);
            if($row=mysqli_fetch_assoc($res)){
                $db_password = $row['ADMIN_PASSWORD'];
                if($pass  == $db_password)
                {
                    $_SESSION['admin_id'] = $id;
                    header("location: adminusers.php");
                    exit();
                }
                else {
                    $error_message = "Enter a proper password";
                }
            }
            else {
                $error_message = "Enter a proper admin ID";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - VeloRent</title>
    
    <!-- Embedded CSS -->
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


       
        @keyframes spin {
            100% { transform: rotate(360deg); }
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
        /* Main Container */
        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Admin Login Content */
        .admin-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 120px 20px 80px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Admin Welcome */
        .helloadmin {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .helloadmin h1 {
            font-size: 2.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 0 15px rgba(255, 123, 0, 0.3);
            position: relative;
            display: inline-block;
        }

        .helloadmin h1::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            bottom: -10px;
            left: 0;
            border-radius: 2px;
        }

        /* Admin Login Form */
        .form {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .form:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .form h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #667eea;
            font-weight: 600;
        }

        .form input.h {
            width: 100%;
            padding: 16px 20px;
            margin-bottom: 25px;
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .form input.h:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(255, 123, 0, 0.3);
        }

        .form input.h::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form .btnn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #764ba2, #667eea);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .form .btnn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        /* Admin Security Note */
        .admin-note {
            margin-top: 30px;
            text-align: center;
            max-width: 500px;
            padding: 20px;
            background: rgba(255, 0, 0, 0.1);
            border-radius: 10px;
            border-left: 4px solid #764ba2;
        }

        .admin-note p {
            color: #da2d2dff;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Admin Features */
        .admin-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
            max-width: 1000px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            width: 280px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: block;
        }

        .feature-card h3 {
            color: #764ba2;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .feature-card p {
            color: #6f4cd8ff;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Error Message */
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #ff0000;
            display: <?php echo isset($error_message) ? 'block' : 'none'; ?>;
        }

        /* Success Message */
        .success-message {
            background: rgba(0, 255, 0, 0.1);
            color: #6bff6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #00ff00;
        }
 .form input[type="text"],
        .form input[type="password"] {
            width: 100%;
            padding: 16px;
            margin-bottom: 18px;
            border: 2px solid #2762a4ff;
            border-radius: 12px;
            background: #f8f9fa;
            color: #0c2238ff;
            outline: none;
            transition: 0.3s;
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


        /* Responsive Design */
        @media (max-width: 992px) {
            .helloadmin h1 {
                font-size: 2.5rem;
            }
            
            .admin-features {
                gap: 20px;
            }
            
            .feature-card {
                width: 100%;
                max-width: 350px;
            }
        }

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
                width: 250px;
                height: 100vh;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                transition: right 0.5s ease;
                z-index: 1000;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.5);
            }

            .menu.active {
                right: 0;
            }

            .menu ul {
                flex-direction: column;
                align-items: center;
            }

            .menu li {
                margin: 20px 0;
            }

            .helloadmin h1 {
                font-size: 2rem;
            }
            
            .form {
                padding: 40px 30px;
            }
            
            .form h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .admin-content {
                padding: 100px 15px 40px;
            }
            
            .helloadmin h1 {
                font-size: 1.8rem;
            }
            
            .form {
                padding: 30px 20px;
            }
            
            .form h2 {
                font-size: 1.5rem;
            }
            
            .form input.h {
                padding: 14px 16px;
            }
        }
    </style>
    
    <!-- Prevent back button -->
    <script type="text/javascript">
        function preventBack() {
            window.history.forward(); 
        }
        setTimeout("preventBack()", 0);
        window.onunload = function () { null };
    </script>
</head>
<body onload="noBack();" onpageshow="if(event.persisted) noBack();" onunload="">

    <!-- Loading Animation -->
    <div class="loading">
        <div class="loader"></div>
    </div>

    <div class="container">
        <!-- Navigation Bar -->
        <nav class="navbar">
            <a href="index.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
            
            <!-- Hamburger Menu -->
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
                    <li><a href="adminlogin.php" style="color: #2c3e50; font-weight: 600;">Admin</a></li>
                </ul>
            </div>
        </nav>

        <!-- Admin Login Content -->
        <div class="admin-content">
            <div class="helloadmin">
                <h1>HELLO ADMIN!</h1>
            </div>

            <!-- Admin Login Form -->
            <form class="form" method="POST">
                <h2>Admin Login</h2>
                
                <!-- Error Message Display -->
                <?php if(isset($error_message)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <input class="h" type="text" name="adid" placeholder="Enter admin user id" required>
                <input class="h" type="password" name="adpass" placeholder="Enter admin password" required>
                <input type="submit" class="btnn" value="LOGIN" name="adlog">
            </form>

            <!-- Security Note -->
            <div class="admin-note">
                <p>⚠️ <strong>Security Notice:</strong> This page is restricted to authorized personnel only. Unauthorized access attempts will be logged and may result in legal action.</p>
            </div>

            <!-- Admin Features -->
            <div class="admin-features">
                <div class="feature-card">
                    <span class="feature-icon">👥</span>
                    <h3>User Management</h3>
                    <p>Manage all user accounts, view profiles, and handle user-related operations.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">🚗</span>
                    <h3>Vehicle Control</h3>
                    <p>Add, edit, or remove vehicles from the rental fleet and manage availability.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-icon">📊</span>
                    <h3>Analytics Dashboard</h3>
                    <p>Access detailed reports and analytics on bookings, revenue, and performance.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
            <div class="socials">
                <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
                <a href="https://x.com/" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
                <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
            </div>
        </footer>
    </div>

    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
    
    <!-- Embedded JavaScript -->
    <script>
        // Loading animation
        window.addEventListener('load', function() {
            const loading = document.querySelector('.loading');
            setTimeout(() => {
                loading.classList.add('hidden');
            }, 1000);
        });

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

        // Form validation
        const adminForm = document.querySelector('.form');
        const adminIdInput = document.querySelector('input[name="adid"]');
        const adminPassInput = document.querySelector('input[name="adpass"]');

        adminForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous error styles
            adminIdInput.style.borderColor = '';
            adminPassInput.style.borderColor = '';
            
            // Validate Admin ID
            if (adminIdInput.value.trim() === '') {
                adminIdInput.style.borderColor = '#ff0000';
                adminIdInput.focus();
                isValid = false;
            }
            
            // Validate Password
            if (adminPassInput.value.trim() === '') {
                adminPassInput.style.borderColor = '#ff0000';
                if (isValid) adminPassInput.focus();
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Prevent back button
        function noBack() {
            window.history.forward();
        }
    </script>
</body>
</html>