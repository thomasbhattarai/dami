<?php
    require_once('connection.php');
    session_start();
    
    // Check if admin is logged in
    if(!isset($_SESSION['admin_id'])) {
        header("location: adminlogin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - VeloRent Admin</title>
    <script src="main.js" defer></script>
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
            background: #fff;
            color: linear-gradient(135deg, #0c0c0c, #1a1a2e);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Main Wrapper */
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styles - Fixed at top */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 80px;
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

        .menu a {
            color: #0e0e0eff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu a:not(.button)::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #667eea;
            transition: width 0.3s;
        }

        .menu a:not(.button):hover {
            color: #667eea;
        }

        .menu a:not(.button):hover::after {
            width: 100%;
        }

        .menu a.button {
            background: linear-gradient(45deg, #764ba2, #667eea);
            color: white;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .menu a.button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(73, 105, 211, 0.71);
        }

        /* Back Button */
        #back {
            position: fixed;
            top: 100px;
            left: 5%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            z-index: 100;
            box-shadow: 0 5px 15px rgba(63, 86, 239, 0.82);
        }

        #back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(43, 98, 226, 0.67);
        }

        #back a {
            color: white;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        }

        /* Main Content */
        .main {
            padding: 120px 5% 40px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 180px);
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Register Container */
        .register {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 600px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .register:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .register h2 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #667eea;
            font-weight: 600;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .register h2::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 4px;
            background: #667eea;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Image Preview */
        #image-preview {
            display: none;
            width: 100%;
            max-width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin: 0 auto 25px;
            border: 2px dashed rgba(255, 255, 255, 0.2);
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Form Styles */
        #register {
            display: flex;
            flex-direction: column;
        }

        #register label {
            margin-bottom: 8px;
            font-weight: 500;
            color: #403e3eff;
            font-size: 1rem;
        }

               
        #register input[type="text"],
        #register input[type="number"],
        #register input[type="file"] {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            background-color: #bdc9fcff;
            color: #151414ff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        #register input[type="text"]:focus,
        #register input[type="number"]:focus,
        #register input[type="file"]:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #7474d6ff;
            box-shadow: 0 0 15px rgba(59, 141, 223, 0.71);
        }

        #register input::placeholder {
            color: rgba(18, 17, 17, 0.6);
        }

        /* File Input Customization */
        #register input[type="file"] {
            padding: 12px 20px;
            cursor: pointer;
        }

        #register input[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            margin-right: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #register input[type="file"]::-webkit-file-upload-button:hover {
            background: linear-gradient(45deg, #764ba2, #667eea);
        }

        /* Submit Button */
        .btnn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #00ff00, #00cc00);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .btnn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 0, 0.4);
            background: linear-gradient(45deg, #00cc00, #00ff00);
        }

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
            .navbar {
                padding: 15px 5%;
            }

            .menu ul {
                gap: 20px;
            }

            .register {
                padding: 40px;
            }

            .register h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px 5%;
                height: 70px;
            }
            
            .main {
                padding: 100px 20px 40px;
            }

            .hamburger {
                display: flex;
            }

        }

         
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Navigation Bar - Fixed at top -->
        <nav class="navbar">
            <img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo">
            
            <!-- Hamburger Menu -->
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="menu" id="menu">
                <ul>
                    <li><a href="adminvehicle.php">VEHICLE MANAGEMENT</a></li>
                    <li><a href="adminusers.php">USERS</a></li>
                    <li><a href="adminbook.php">BOOKING REQUEST</a></li>
                    <li><a href="index.php" class="button">LOGOUT</a></li>
                </ul>
            </div>
        </nav>

        <!-- Back Button -->
        <button id="back"><a href="adminoverview.php">CANCEL</a></button>

        <!-- Main Content -->
        <div class="main">
            <div class="register">
                <h2>Enter Details Of New Vehicle</h2>
                <img id="image-preview" alt="Selected Vehicle Image Preview">
                <form id="register" action="upload.php" method="POST" enctype="multipart/form-data">
                    <label>Vehicle Name:</label>
                    <input type="text" name="vehiclename" placeholder="Enter Vehicle Name" required>
                    
                    <label>Vehicle Type:</label>
                    <input type="text" name="vehicletype" placeholder="Enter Vehicle Type (e.g., Car, Bike, Scooter)" required>
                    
                    <label>Fuel Type:</label>
                    <input type="text" name="ftype" placeholder="Enter Fuel Type" required>
                    
                    <label>Capacity:</label>
                    <input type="number" name="capacity" min="1" placeholder="Enter Capacity Of Vehicle" required>
                    
                    <label>Price:</label>
                    <input type="number" name="price" min="1" placeholder="Enter Price Of Vehicle for One Day (in rupees)" required>
                    
                    <label>Vehicle Image:</label>
                    <input type="file" name="image" accept="image/*" required>
                    
                    <input type="submit" class="btnn" value="ADD VEHICLE" name="addvehicle">
                </form>
            </div>
        </div>

        <!-- Footer - Sticky at bottom -->
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
        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        if (hamburger && menu) {
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
        }

        // Image preview functionality
        const imageInput = document.querySelector('input[name="image"]');
        const imagePreview = document.getElementById('image-preview');

        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.src = '';
                    imagePreview.style.display = 'none';
                }
            });
        }

        // Form validation
        const form = document.getElementById('register');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Get all required inputs
                const inputs = form.querySelectorAll('input[required]');
                inputs.forEach(input => {
                    // Clear previous error styles
                    input.style.borderColor = '';
                    
                    // Validate each field
                    if (!input.value.trim()) {
                        input.style.borderColor = '#ff0000';
                        if (isValid) input.focus();
                        isValid = false;
                    }
                    
                    // Validate number fields
                    if (input.type === 'number' && input.value <= 0) {
                        input.style.borderColor = '#ff0000';
                        if (isValid) input.focus();
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showDialog('Please fill in all required fields with valid values.');
                }
            });
        }

        // Simple page load animation
        document.addEventListener('DOMContentLoaded', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.5s ease';
            
            setTimeout(function() {
                document.body.style.opacity = '1';
            }, 100);
        });
    </script>
</body>
</html>