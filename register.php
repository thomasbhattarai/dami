<?php
    session_start();
    require_once('connection.php');
    
    $error_message = '';
    $success_message = '';
    
    if(isset($_POST['regs'])) {
        $fname = mysqli_real_escape_string($con, $_POST['fname']);
        $lname = mysqli_real_escape_string($con, $_POST['lname']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $lic = mysqli_real_escape_string($con, $_POST['lic']);
        $ph = mysqli_real_escape_string($con, $_POST['ph']);
        $pass = mysqli_real_escape_string($con, $_POST['pass']);
        $cpass = mysqli_real_escape_string($con, $_POST['cpass']);
        $gender = mysqli_real_escape_string($con, $_POST['gender']);
        $Pass = md5($pass);

        if(empty($fname) || empty($lname) || empty($email) || empty($lic) || empty($ph) || empty($pass) || empty($gender)) {
            $error_message = "Please fill all fields";
        } else {
            if($pass == $cpass) {
                $sql2 = "SELECT * FROM users WHERE EMAIL='$email'";
                $res = mysqli_query($con, $sql2);
                if(mysqli_num_rows($res) > 0) {
                    $error_message = "Email already exists. Please login instead!";
                } else {
                    $sql = "INSERT INTO users (FNAME, LNAME, EMAIL, LIC_NUM, PHONE_NUMBER, PASSWORD, GENDER) VALUES ('$fname', '$lname', '$email', '$lic', '$ph', '$Pass', '$gender')";
                    $result = mysqli_query($con, $sql);
                    if($result) {
                        $success_message = "Registration successful! Redirecting to login...";
                        echo '<script>
                            setTimeout(function() {
                                window.location.href = "index.php";
                            }, 2000);
                        </script>';
                    } else {
                        $error_message = "Please check the connection";
                    }
                }
            } else {
                $error_message = "Passwords do not match";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - VeloRent</title>
    
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
            color: #000000ff;
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
            background: #fff;
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
            color: #fff;
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

        /* Cancel Button */
        .cancel-btn {
            position: fixed;
            top: 12px;
            right: 12px;
            background: linear-gradient(45deg, #764ba2, #667eea);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .cancel-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
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

        /* Form Styles */
        #register {
            display: flex;
            flex-direction: column;
        }

        #register label {
            margin-bottom: 8px;
            font-weight: 500;
            color: #0b0b0bff;
            font-size: 1rem;
        }

        #register input[type="text"],
        #register input[type="email"],
        #register input[type="tel"],
        #register input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
            border-radius: 12px;
            background: rgba(218, 220, 222, 0.73);
            color: #030303ff;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        #register input[type="text"]:focus,
        #register input[type="email"]:focus,
        #register input[type="tel"]:focus,
        #register input[type="password"]:focus {
            background: rgba(134, 125, 125, 0.15);
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(255, 123, 0, 0.3);
        }

        #register input::placeholder {
            color: rgba(18, 17, 17, 0.6);
        }

        /* Gender Radio Buttons */
        .gender-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            align-items: center;
        }

        .gender-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: normal;
            margin: 0;
        }

        .gender-group input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #667eea;
        }

        /* Error Message */
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #764ba2;
            display: <?php echo !empty($error_message) ? 'block' : 'none'; ?>;
        }

        /* Password Requirement Helper */
        .password-helper {
            background: rgba(33, 150, 243, 0.1);
            border: 1px solid rgba(33, 150, 243, 0.25);
            color: #64b5f6;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 12px;
            display: none;
            font-size: 0.9rem;
        }

        .password-helper.show {
            display: block;
        }

        .password-helper ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .password-helper li {
            position: relative;
            padding-left: 22px;
            margin-bottom: 6px;
        }

        .password-helper li::before {
            content: '✗';
            position: absolute;
            left: 0;
            color: #ff6b6b;
            font-weight: 700;
        }

        .password-helper li.valid::before {
            content: '✓';
            color: #4caf50;
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
            display: <?php echo !empty($success_message) ? 'block' : 'none'; ?>;
        }

        /* Submit Button */
        .btnn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #667eea, #764ba2);
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
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
            background: linear-gradient(45deg, #764ba2, #667eea);
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
    

        <!-- Cancel Button -->
        <a href="index.php" class="cancel-btn">CANCEL</a>

        <!-- Main Content -->
        <div class="main">
            <div class="register">
                <h2>Register Here</h2>
                
                <!-- Error Message Display -->
                <?php if(!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Success Message Display -->
                <?php if(!empty($success_message)): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <form id="register" action="register.php" method="POST">    
                    <label>First Name:</label>
                    <input type="text" name="fname" placeholder="Enter Your First Name" required>

                    <label>Last Name:</label>
                    <input type="text" name="lname" placeholder="Enter Your Last Name" required>

                    <label>Email:</label>
                    <input type="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="ex: example@ex.com" placeholder="Enter Valid Email" required>

                    <label>Your License Number:</label>
                    <input type="text" name="lic" placeholder="Enter Your License Number" required>

                    <label>Phone Number:</label>
                    <input type="tel" name="ph" maxlength="10" onkeypress="return onlyNumberKey(event)" placeholder="Enter Your Phone Number" required>

                    <label>Password:</label>
                    <div class="password-helper" id="password-helper">
                        <ul>
                            <li id="req-length">At least 8 characters</li>
                            <li id="req-uppercase">At least 1 uppercase letter</li>
                            <li id="req-number">At least 1 number</li>
                            <li id="req-special">At least 1 special character (!@#$%^&*)</li>
                        </ul>
                    </div>
                    <input type="password" name="pass" maxlength="20" id="psw" placeholder="Enter Password" pattern="(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}" title="Must contain at least one uppercase letter, one number, one special character, and be at least 8 characters long" required>

                    <label>Confirm Password:</label>
                    <input type="password" name="cpass" placeholder="Re-enter the password" required>

                    <label>Gender:</label>
                    <div class="gender-group">
                        <input type="radio" id="male" name="gender" value="male" required>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="female" required>
                        <label for="female">Female</label>
                    </div>

                    <input type="submit" class="btnn" value="REGISTER" name="regs">
                </form>
            </div> 
        </div>

        <!-- Footer - Sticky at bottom -->
        <footer>
            <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
            <div class="socials">
                <a href="https://www.facebook.com/thomasbhattrai"><ion-icon name="logo-facebook"></ion-icon></a>
                <a href="https://x.com/thomashbhattarai"><ion-icon name="logo-twitter"></ion-icon></a>
                <a href="https://www.instagram.com/swostimakaju/"><ion-icon name="logo-instagram"></ion-icon></a>
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

        // Only number key for phone input
        function onlyNumberKey(evt) {
            var ASCIICode = (evt.which) ? evt.which : evt.keyCode;
            if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) {
                return false;
            }
            return true;
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
                    
                    // Validate email pattern
                    if (input.type === 'email' && !input.checkValidity()) {
                        input.style.borderColor = '#ff0000';
                        if (isValid) input.focus();
                        isValid = false;
                    }
                });
                
                // Validate password match
                const password = form.querySelector('input[name="pass"]');
                const confirmPassword = form.querySelector('input[name="cpass"]');
                if (password && confirmPassword && password.value !== confirmPassword.value) {
                    password.style.borderColor = '#ff0000';
                    confirmPassword.style.borderColor = '#ff0000';
                    if (isValid) confirmPassword.focus();
                    isValid = false;
                }

                // Validate password requirements
                if (password && !validatePassword(password.value)) {
                    password.style.borderColor = '#ff0000';
                    if (isValid) password.focus();
                    isValid = false;
                }
                
                // Validate gender selection
                const genderSelected = form.querySelector('input[name="gender"]:checked');
                if (!genderSelected) {
                    const genderGroup = form.querySelector('.gender-group');
                    if (genderGroup) {
                        genderGroup.style.outline = '2px solid #ff0000';
                        genderGroup.style.borderRadius = '8px';
                        genderGroup.style.padding = '5px';
                    }
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        }

        // Password requirement checks
        function validatePassword(password) {
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*]/.test(password);
            return hasLength && hasUppercase && hasNumber && hasSpecial;
        }

        const passwordInput = document.getElementById('psw');
        const helper = document.getElementById('password-helper');
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        if (passwordInput && helper) {
            const updateRequirements = (val) => {
                if (reqLength) {
                    reqLength.classList.toggle('valid', val.length >= 8);
                }
                if (reqUppercase) {
                    reqUppercase.classList.toggle('valid', /[A-Z]/.test(val));
                }
                if (reqNumber) {
                    reqNumber.classList.toggle('valid', /[0-9]/.test(val));
                }
                if (reqSpecial) {
                    reqSpecial.classList.toggle('valid', /[!@#$%^&*]/.test(val));
                }
            };

            passwordInput.addEventListener('focus', () => {
                helper.classList.add('show');
                updateRequirements(passwordInput.value);
            });

            passwordInput.addEventListener('input', () => {
                updateRequirements(passwordInput.value);
            });
        }

        // Remove gender group outline when radio is clicked
        const genderRadios = document.querySelectorAll('input[name="gender"]');
        genderRadios.forEach(radio => {
            radio.addEventListener('click', function() {
                const genderGroup = document.querySelector('.gender-group');
                if (genderGroup) {
                    genderGroup.style.outline = '';
                    genderGroup.style.padding = '';
                }
            });
        });

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