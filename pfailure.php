<?php
session_start();

/* 1.  capture the raw reason  */
$reason = $_GET['reason'] ?? 'unknown';

/* 2.  log the full line for YOU  */
file_put_contents('esewa_fail.log', date('Y-m-d H:i:s')." | {$reason} | ".json_encode($_GET).PHP_EOL, FILE_APPEND | LOCK_EX);

/* 3.  keep your existing switch  */
switch($reason){
    case 'database_error': $msg = 'Database error – check log.'; break;
    case 'payment_verification_failed': $msg = 'Payment verification failed.'; break;
    default: $msg = 'An unexpected error occurred.'; break;
}

/* 4.  clear session  */
unset($_SESSION['pending_booking'], $_SESSION['total_price'], $_SESSION['temp_booking_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - VeloRent</title>
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

        /* Failure Container */
        .failure-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .failure-icon {
            font-size: 5rem;
            color: #ff4444;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(255, 68, 68, 0.5);
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #ff4444;
        }

        p {
            color: #322b2bff;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        /* Alert Messages */
        .alert {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #667eea;
            text-align: center;
            font-size: 0.95rem;
        }

        /* Button Styling */
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 5px;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 5px 15px rgba(31, 22, 14, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(38, 33, 195, 0.4);
        }

        .btn-outline-secondary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #111010ff;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline-secondary:hover {
           background: linear-gradient(45deg, #667eea, #764ba2);
            color: black;
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
            .failure-container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 1.8rem;
            }
            
            .failure-icon {
                font-size: 4rem;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .failure-container {
                padding: 25px 15px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
            
            .failure-icon {
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
        }
    </style>
</head>
<body>
    <div class="failure-container">
        <div class="failure-icon">❌</div>
        <h2>Payment Failed</h2>
        <p>We're sorry, but your payment could not be processed.</p>
        
        <?php
        $reason = $_GET['reason'] ?? 'unknown';
        switch($reason) {
            case 'no_booking_data':
                echo "<div class='alert'>No booking data found. Please start the booking process again.</div>";
                break;
            case 'database_error':
                echo "<div class='alert'>There was an error processing your booking. Please contact support.</div>";
                break;
            case 'payment_verification_failed':
                echo "<div class='alert'>Payment verification failed. Please try again.</div>";
                break;
            default:
                echo "<div class='alert'>An unexpected error occurred. Please try again.</div>";
        }
        ?>
        
        <div class="mt-4">
            <a href="vehiclesdetails.php" class="btn btn-primary me-2">Try Again</a>
            <a href="index.php" class="btn btn-outline-secondary">Return to Home</a>
        </div>
        
        <?php
        // Clear session data on failure
        unset($_SESSION['pending_booking']);
        unset($_SESSION['total_price']);
        unset($_SESSION['temp_booking_id']);
        ?>
    </div>
</body>
</html>