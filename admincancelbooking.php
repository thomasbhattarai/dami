<?php
session_start();
require_once('connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

// Check if booking ID is provided
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($con, $_GET['id']);
    
    // Update booking status to Canceled
    $sql = "UPDATE booking SET BOOK_STATUS = 'Canceled' WHERE BOOK_ID = '$book_id'";
    $result = mysqli_query($con, $sql);
    
    if ($result) {
        echo '<script src="main.js"></script>';
        echo '<script>showDialog("Booking canceled successfully by admin", function() { window.location.href = "adminbook.php"; });</script>';
    } else {
        echo '<script>showDialog("Error canceling booking. Please try again.", function() { window.location.href = "adminbook.php"; });</script>';
    }
} else {
    echo '<script>showDialog("No booking ID provided", function() { window.location.href = "adminbook.php"; });</script>';
}
?>
