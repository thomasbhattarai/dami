<?php
session_start();
require_once('connection.php');

try {
    /* your existing DB code here */
    $stmt = $con->prepare("UPDATE booking SET BOOK_STATUS='PAID' WHERE BOOK_ID=?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
} catch (Exception $e) {
    // THIS line writes the real error
    file_put_contents('esewa_real_error.log', date('Y-m-d H:i:s')." | ".$e->getMessage().PHP_EOL, FILE_APPEND);
    header("Location: pfailure.php?reason=database_error");
    exit();
}

// Check if payment was successful and we have pending booking data
if (!isset($_SESSION['pending_booking']) || !isset($_SESSION['temp_booking_id'])) {
    header("Location: booking-failed.php?reason=no_booking_data");
    exit();
}

// Verify payment success (you should implement proper eSewa verification here)
$payment_verified = true; // Set to false by default, implement actual verification

if ($payment_verified) {
    $booking_data = $_SESSION['pending_booking'];
    
    try {
        // Insert into database only after successful payment verification
        $sql = "INSERT INTO booking (VEHICLE_ID, EMAIL, BOOK_PLACE, BOOK_DATE, DURATION, PHONE_NUMBER, DESTINATION, PRICE, RETURN_DATE, BOOK_STATUS) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'PAID')";
        
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "issssisss", 
            $booking_data['vehicle_id'],
            $booking_data['email'],
            $booking_data['book_place'],
            $booking_data['book_date'],
            $booking_data['duration'],
            $booking_data['phone_number'],
            $booking_data['destination'],
            $booking_data['price'],
            $booking_data['return_date']
        );
        
        $result = mysqli_stmt_execute($stmt);
        $booking_id = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            // Clear session data
            unset($_SESSION['pending_booking']);
            unset($_SESSION['total_price']);
            unset($_SESSION['temp_booking_id']);
            
            // Store success booking ID for confirmation page
            $_SESSION['confirmed_booking_id'] = $booking_id;
            
            header("Location: booking-success.php");
            exit();
        } else {
            throw new Exception("Database insertion failed");
        }
        
    } catch (Exception $e) {
        // Log the error and redirect to failure page
        error_log("Booking error: " . $e->getMessage());
        header("Location: pfailure.php?reason=database_error");
        exit();
    }
} else {
    // Payment verification failed
    header("Location: pfailure.php?reason=payment_verification_failed");
    exit();
}
?>