<?php
session_start();
if (!isset($_SESSION['confirmed_booking_id'])) {
    header("Location: vehiclesdetails.php");
    exit();
}

$booking_id = $_SESSION['confirmed_booking_id'];
unset($_SESSION['confirmed_booking_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - VeloRent</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Success Container */
        .success-container {
            background: rgba(232, 227, 227, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(247, 243, 243, 1);
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Success Icon */
        .success-icon {
            font-size: 5rem;
            color: #4CAF50;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(76, 175, 80, 0.5);
            animation: bounce 1s ease infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Typography */
        h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #4CAF50;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        p {
            color: #0b0b0bff;
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        /* Alert/Success Box */
        .alert {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #4CAF50;
            text-align: center;
            font-size: 0.95rem;
        }

        .alert strong {
            color: #4CAF50;
        }

        /* Booking ID Styling */
        .booking-id {
            font-size: 1.3rem;
            font-weight: 600;
            color: #667eea;
            margin: 5px 0;
            display: block;
        }

        /* Button Styling */
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 8px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
        }

        .btn-outline-secondary {
            background: transparent;
            color: #100f0fff;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateY(-3px);
        }

        /* Button Container */
        .mt-4 {
            margin-top: 30px;
        }

        .me-2 {
            margin-right: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .success-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            .success-icon {
                font-size: 4rem;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
            
            p {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .success-container {
                padding: 25px 15px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .success-icon {
                font-size: 3.5rem;
            }
            
            .btn {
                display: block;
                width: 100%;
                margin: 8px 0;
            }
            
            .me-2 {
                margin-right: 0;
            }
            
            .alert {
                padding: 12px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h2>Booking Confirmed!</h2>
        <p>Your vehicle has been successfully booked.</p>
        <div class="alert">
            <strong>Booking ID:</strong> 
            <span class="booking-id">#<?php echo htmlspecialchars($booking_id); ?></span>
        </div>
        <p>Thank you for choosing VeloRent. You will receive a confirmation email shortly.</p>
        
        <div class="mt-4">
            <a href="bookingstatus.php" class="btn btn-success me-2">View My Bookings</a>
            <a href="vehiclesdetails.php" class="btn btn-outline-secondary">Return to Home</a>
        </div>
    </div>
</body>
</html>