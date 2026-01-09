<?php
session_start();

// Check if there's a pending booking in session
if (!isset($_SESSION['pending_booking'])) {
    die("No pending booking found. Please start the booking process again.");
}

// Retrieve data from the PHP session
$booking_data = $_SESSION['pending_booking'];
$total_amount = $_SESSION['total_price'] ?? 0;

// Generate a temporary booking ID for payment reference
$temp_booking_id = uniqid('temp_', true);
$_SESSION['temp_booking_id'] = $temp_booking_id;

// Store the booking data with temp ID for verification after payment
$_SESSION['pending_booking']['temp_id'] = $temp_booking_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esewa Payment - VeloRent</title>
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

        /* Payment Container */
        .payment-container {
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

        .payment-container h2 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #667eea;
            background: linear-gradient(45deg, #764ba2, #667eea);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .payment-container p {
            color: #0b0a0aff;
            margin-bottom: 30px;
        }

        /* Booking Details */
        .booking-details {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(20, 52, 210, 0.3);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .booking-details h5 {
            font-size: 1.3rem;
            margin-bottom: 20px;
            text-align: center;
            color: #764ba2;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-label {
            font-weight: 500;
            color: #2b2a2aff;
        }

        .detail-value {
            color: #0e0d0dff;
            font-weight: 600;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: #764ba2;
            text-align: center;
            margin: 20px 0 10px;
            padding: 15px;
            background: rgba(255, 123, 0, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(75, 26, 211, 0.3);
        }

        /* Spinner */
        .spinner-border {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: #764ba2;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        .text-muted {
            color: #2c2828ff !important;
            font-size: 0.9rem;
        }

        /* Esewa Form (Hidden) */
        #esewaForm {
            display: none;
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0c0c0c;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loading.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: #764ba2;
            animation: spin 1s ease-in-out infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .payment-container {
                padding: 30px 20px;
            }
            
            .payment-container h2 {
                font-size: 1.8rem;
            }
            
            .booking-details {
                padding: 20px 15px;
            }
            
            .total-amount {
                font-size: 1.5rem;
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .payment-container {
                padding: 25px 15px;
            }
            
            .payment-container h2 {
                font-size: 1.5rem;
            }
            
            .detail-item {
                flex-direction: column;
                gap: 5px;
            }
            
            .spinner-border {
                width: 50px;
                height: 50px;
                border-width: 4px;
            }
        }

        /* No JavaScript Warning */
        noscript {
            display: block;
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            color: #667eea;
            text-align: center;
        }

        noscript .btn {
            background: linear-gradient(45deg, #667eea, #3c487aff);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
        }

        noscript .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 123, 0, 0.4);
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
    <div class="loading hidden">
        <div class="loader"></div>
    </div>

    <div class="payment-container">
        <div class="mb-4">
            <h2>Redirecting to eSewa</h2>
            <p class="text-muted">Please wait while we process your payment</p>
        </div>

        <div class="booking-details">
            <h5>Booking Summary</h5>
            <div class="detail-item">
                <span class="detail-label">Vehicle:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking_data['vehicle_name']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Booking Date:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking_data['book_date']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Return Date:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking_data['return_date']); ?></span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Duration:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking_data['duration']); ?> days</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Destination:</span>
                <span class="detail-value"><?php echo htmlspecialchars($booking_data['destination']); ?></span>
            </div>
            <div class="total-amount">
                Total: Rs. <?php echo number_format($total_amount, 2); ?>
            </div>
        </div>

        <div class="spinner-border" role="status"></div>
        <p class="text-muted small">Do not close or refresh this page</p>
    </div>

    <form id="esewaForm" action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" id="amount" name="amount" value="<?php echo htmlspecialchars($total_amount); ?>">
        <input type="hidden" id="tax_amount" name="tax_amount" value="0">
        <input type="hidden" id="total_amount" name="total_amount" value="<?php echo htmlspecialchars($total_amount); ?>">
        <input type="hidden" id="transaction_uuid" name="transaction_uuid">
        <input type="hidden" id="product_code" name="product_code" value="EPAYTEST">
        <input type="hidden" id="product_service_charge" name="product_service_charge" value="0">
        <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="0">
        <input type="hidden" id="success_url" name="success_url" value="http://localhost/v/psucess.php">
        <input type="hidden" id="failure_url" name="failure_url" value="http://localhost/v/pfailure.php">
        <input type="hidden" id="signed_field_names" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
        <input type="hidden" id="signature" name="signature">
        
        <noscript>
            <div class="text-center mt-3">
                <p class="text-warning">Please enable JavaScript to continue with the payment.</p>
                <input type="submit" class="btn" value="Continue to Payment">
            </div>
        </noscript>
    </form>

    <script>
        // Remove Bootstrap dependencies and use your own spinner
        document.addEventListener("DOMContentLoaded", function() {
            // Get data from the PHP-populated form fields
            const totalAmount = document.getElementById("total_amount").value;
            const productCode = document.getElementById("product_code").value;
            const tempBookingId = "<?php echo $temp_booking_id; ?>";

            // Generate a unique transaction UUID using the temp booking ID and current timestamp
            const transactionUuid = `<?php echo $temp_booking_id; ?>_${Date.now()}`;
            document.getElementById("transaction_uuid").value = transactionUuid;

            // Construct the message string for signature
            const message = `total_amount=${totalAmount},transaction_uuid=${transactionUuid},product_code=${productCode}`;
            const secret = "8gBm/:&EnhH.1/q";  // This is the UAT key

            // Load CryptoJS from CDN if not already loaded
            if (typeof CryptoJS === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js';
                script.onload = generateSignature;
                document.head.appendChild(script);
            } else {
                generateSignature();
            }

            function generateSignature() {
                // Generate the signature using HMAC-SHA256
                const hash = CryptoJS.HmacSHA256(message, secret);
                const signature = CryptoJS.enc.Base64.stringify(hash);
                document.getElementById("signature").value = signature;

                // Add a small delay to show the loading screen before redirecting
                setTimeout(function() {
                    document.getElementById("esewaForm").submit();
                }, 2000);
            }
        });
    </script>
</body>
</html>