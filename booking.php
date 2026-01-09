<?php
session_start();
require_once('connection.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$vehicleid = $_GET['id'];

// Fetch vehicle details
$sql = "SELECT * FROM vehicles WHERE VEHICLE_ID='$vehicleid'";
$cname = mysqli_query($con, $sql);
$vehicle = mysqli_fetch_assoc($cname);

// Fetch user details
$value = $_SESSION['email'];
$sql = "SELECT * FROM users WHERE EMAIL='$value'";
$name = mysqli_query($con, $sql);
$user = mysqli_fetch_assoc($name);
$uemail = $user['EMAIL'];
$base_price = $vehicle['PRICE'];

// Dynamic Pricing Algorithm - UPDATED VERSION
function calculateDynamicPrice($base_price, $duration)
{
    // For 1-day bookings: Always use base price (no discount)
    if ($duration <= 1) {
        return $base_price;
    }
    
    // For multi-day bookings: Apply discount based on duration
    $discount_percentage = 0;
    
    if ($duration >= 7) {
        $discount_percentage = 10; // 10% discount for 7+ days
    } elseif ($duration >= 3) {
        $discount_percentage = 5; // 5% discount for 3-6 days
    } elseif ($duration >= 2) {
        $discount_percentage = 2; // 2% discount for 2 days
    }
    
    // Calculate discounted price
    $discount_amount = $base_price * ($discount_percentage / 100);
    $dynamic_price = $base_price - $discount_amount;
    
    return round($dynamic_price, 2);
}

if (isset($_POST['book'])) {
    $bplace = mysqli_real_escape_string($con, $_POST['place']);
    $bdate = date('Y-m-d', strtotime($_POST['date']));
    $dur = (int)$_POST['dur']; // Ensure duration is integer
    $des = mysqli_real_escape_string($con, $_POST['des']);
    $rdate = date('Y-m-d', strtotime($_POST['rdate']));

    if (empty($bplace) || empty($bdate) || empty($dur) || empty($des) || empty($rdate)) {
        echo '<script>showDialog("Please fill all fields")</script>';
    } else {
        if ($bdate < $rdate) {
            // Calculate dynamic price based on duration only
            $dynamic_price = calculateDynamicPrice($base_price, $dur);
            $total_price = $dynamic_price * $dur;

            // Store booking data in session instead of inserting into database
            $_SESSION['pending_booking'] = [
                'vehicle_id' => $vehicleid,
                'email' => $uemail,
                'book_place' => $bplace,
                'book_date' => $bdate,
                'duration' => $dur,
                'phone_number' => $user['PHONE_NUMBER'],
                'destination' => $des,
                'price' => $total_price,
                'daily_price' => $dynamic_price,
                'return_date' => $rdate,
                'vehicle_name' => $vehicle['VEHICLE_NAME'],
                'base_price' => $base_price
            ];
            
            $_SESSION['total_price'] = $total_price;
            
            header("Location: esewa-intregation.php");
            exit();
        } else {
            echo '<script>showDialog("Please enter a correct return date")</script>';
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
    <title>VEHICLE BOOKING - VeloRent</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            overflow-x: hidden;
            position: relative;
        }

        /* Cancel Button */
        .cancel-btn {
            position: fixed;
            top: 30px;
            right: 30px;
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
        }

        .cancel-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 123, 0, 0.4);
        }

        /* Main Content Box */
        .content-box {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 100px 20px 50px;
        }

        .register {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .register h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: #764ba2;
            background: linear-gradient(45deg, #764ba2, #667eea);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Form Styling */
        #register {
            display: flex;
            flex-direction: column;
        }

        #register > h2 {
            font-size: 1.5rem;
            color: #101010ff;
            margin-bottom: 20px;
            text-align: center;
            text-shadow: 0 0 10px rgba(130, 112, 96, 0.5);
        }

        label {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: #282525ff;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            background-color: #d5daefff;
            color: #222222ff;
            font-size: 1rem;
            outline: none;
            transition: background 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="number"]:focus,
        input[type="tel"]:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(143, 135, 128, 0.7);
        }

        input::placeholder {
            color: rgba(37, 35, 35, 0.6);
        }

        input[readonly] {
            background: rgba(255, 255, 255, 0.08);
            cursor: not-allowed;
        }

        /* Price Display */
        .price-display {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(99, 83, 68, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 1.1rem;
            color: #667eea;
            font-weight: 600;
            text-align: center;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .discount-info {
            font-size: 0.9rem;
            color: #4CAF50;
            margin-top: 5px;
            font-weight: 500;
        }

        /* Submit Button */
        .btnn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.3);
        }

        .btnn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content-box {
                padding: 80px 15px 40px;
            }
            
            .register {
                padding: 30px 20px;
                margin-top: 20px;
            }
            
            .register h2 {
                font-size: 1.8rem;
            }
            
            .cancel-btn {
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                font-size: 14px;
            }
            
            .price-display {
                font-size: 1rem;
                padding: 12px;
                min-height: 55px;
            }
            
            .discount-info {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .content-box {
                padding: 70px 10px 30px;
            }
            
            .register {
                padding: 25px 15px;
            }
            
            .register h2 {
                font-size: 1.5rem;
            }
            
            input[type="text"],
            input[type="date"],
            input[type="number"],
            input[type="tel"] {
                padding: 12px;
                font-size: 0.95rem;
            }
            
            .btnn {
                padding: 12px;
                font-size: 1rem;
            }
            
            .cancel-btn {
                top: 15px;
                right: 15px;
                padding: 8px 16px;
                font-size: 13px;
            }
            
            .price-display {
                padding: 10px;
                min-height: 50px;
                font-size: 0.95rem;
            }
            
            .discount-info {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <!-- Cancel Button -->
    <button class="cancel-btn" onclick="window.location.href='vehiclesdetails.php'">CANCEL</button>

    <div class="content-box">
        <div class="register">
            <h2>VEHICLE BOOKING</h2>
            <form id="register" method="POST">
                <h2><?php echo htmlspecialchars($vehicle['VEHICLE_NAME']); ?></h2>
                
                <label for="baseprice">BASE PRICE PER DAY:</label>
                <div class="price-display">
                    Rs.<?php echo number_format($base_price, 2); ?>
                    <div class="discount-info">(1-day booking price)</div>
                </div>

                <label for="place">BOOKING PLACE:</label>
                <input type="text" name="place" id="place" placeholder="Enter Booking Place" required>

                <label for="date">BOOKING DATE:</label>
                <input type="date" name="date" id="datefield" required onchange="updatePriceAndDuration()">

                <label for="rdate">RETURN DATE:</label>
                <input type="date" name="rdate" id="dfield" required onchange="updatePriceAndDuration()">

                <label for="dur">DURATION (days):</label>
                <input type="number" name="dur" id="dur" readonly>

                <!-- Phone number removed: we will use the user's saved phone from profile -->

                <label for="des">DESTINATION:</label>
                <input type="text" name="des" id="des" placeholder="Enter Your Destination" required>

                <label for="totalprice">TOTAL PRICE:</label>
                <div class="price-display" id="total-price">
                    Select booking and return dates to see total price
                </div>
                
                <div class="discount-info" id="discount-details" style="margin-bottom: 20px; text-align: center; display: none;">
                    <strong>Discount Policy:</strong><br>
                    • 2 days: 2% discount per day<br>
                    • 3-6 days: 5% discount per day<br>
                    • 7+ days: 10% discount per day
                </div>

                <input type="submit" class="btnn" value="PROCEED TO PAYMENT" name="book">
            </form>
        </div>
    </div>

    <script>
        // Base price from PHP
        const basePrice = <?php echo json_encode($base_price); ?>;
        
        // Function to calculate dynamic price based on duration
        function calculateDynamicPrice(duration) {
            // For 1-day bookings: Always use base price (no discount)
            if (duration <= 1) {
                return basePrice;
            }
            
            // For multi-day bookings: Apply discount based on duration
            let discountPercentage = 0;
            
            if (duration >= 7) {
                discountPercentage = 10; // 10% discount for 7+ days
            } else if (duration >= 3) {
                discountPercentage = 5; // 5% discount for 3-6 days
            } else if (duration >= 2) {
                discountPercentage = 2; // 2% discount for 2 days
            }
            
            // Calculate discounted price
            const discountAmount = basePrice * (discountPercentage / 100);
            const dynamicPrice = basePrice - discountAmount;
            
            return Math.round(dynamicPrice * 100) / 100;
        }

        // Function to update price and duration when dates change
        function updatePriceAndDuration() {
            const bookingDate = document.getElementById('datefield').value;
            const returnDate = document.getElementById('dfield').value;
            const totalPriceElement = document.getElementById('total-price');
            const discountDetails = document.getElementById('discount-details');
            const durationField = document.getElementById('dur');

            if (bookingDate && returnDate && new Date(bookingDate) < new Date(returnDate)) {
                const differenceInTime = new Date(returnDate).getTime() - new Date(bookingDate).getTime();
                const differenceInDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));
                durationField.value = differenceInDays;

                // Calculate prices
                const dynamicPrice = calculateDynamicPrice(differenceInDays);
                const totalPrice = dynamicPrice * differenceInDays;
                
                // Determine discount percentage
                let discountPercentage = 0;
                if (differenceInDays >= 7) {
                    discountPercentage = 10;
                } else if (differenceInDays >= 3) {
                    discountPercentage = 5;
                } else if (differenceInDays >= 2) {
                    discountPercentage = 2;
                }
                
                // Build display text
                let displayText = `<span style="font-size: 1.2em;">Rs.${totalPrice.toFixed(2)}</span>`;
                
                if (differenceInDays === 1) {
                    displayText += `<br><small>Rs.${dynamicPrice.toFixed(2)} × ${differenceInDays} day (Base Price)</small>`;
                } else {
                    const originalTotal = basePrice * differenceInDays;
                    const discountAmount = originalTotal - totalPrice;
                    
                    displayText += `
                        <br><small>
                            Rs.${dynamicPrice.toFixed(2)}/day × ${differenceInDays} days
                        </small>
                        <div class="discount-info">
                            ✓ ${discountPercentage}% discount applied<br>
                            You save: Rs.${discountAmount.toFixed(2)}
                        </div>
                    `;
                }
                
                totalPriceElement.innerHTML = displayText;
                
                // Show discount details if booking is for more than 1 day
                if (differenceInDays > 1) {
                    discountDetails.style.display = 'block';
                } else {
                    discountDetails.style.display = 'none';
                }
                
            } else {
                durationField.value = '';
                totalPriceElement.innerHTML = 'Select booking and return dates to see total price';
                discountDetails.style.display = 'none';
                
                if (bookingDate && returnDate && new Date(bookingDate) >= new Date(returnDate)) {
                    showDialog("Return date must be after the booking date.");
                    document.getElementById('dfield').value = '';
                }
            }

            // Update min date for return date and validate
            if (bookingDate) {
                document.getElementById('dfield').setAttribute('min', bookingDate);
                const currentReturnDate = document.getElementById('dfield').value;
                
                if (currentReturnDate && new Date(currentReturnDate) < new Date(bookingDate)) {
                    document.getElementById('dfield').value = '';
                    document.getElementById('dur').value = '';
                    totalPriceElement.innerHTML = 'Select booking and return dates to see total price';
                    discountDetails.style.display = 'none';
                    showDialog("Return date has been reset as it was earlier than the new pickup date.");
                }
            }
        }

        // Set minimum dates to today
        const today = new Date();
        const dd = today.getDate();
        const mm = today.getMonth() + 1;
        const yyyy = today.getFullYear();
        const formattedToday = `${yyyy}-${mm < 10 ? '0' + mm : mm}-${dd < 10 ? '0' + dd : dd}`;
        document.getElementById("datefield").setAttribute("min", formattedToday);
        document.getElementById("dfield").setAttribute("min", formattedToday);

        // Add event listeners
        document.getElementById("datefield").addEventListener('change', updatePriceAndDuration);
        document.getElementById("dfield").addEventListener('change', updatePriceAndDuration);
        
        // Form submission validation
        document.getElementById('register').addEventListener('submit', function(e) {
            const duration = document.getElementById('dur').value;
            if (!duration || duration < 1) {
                e.preventDefault();
                showDialog('Please select valid booking and return dates.');
            }
        });
    </script>
</body>
</html>