<?php
require_once 'connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: adminbook.php?approved=0&error=missing');
    exit;
}

$bookId = (int)$_GET['id'];

mysqli_begin_transaction($con);

try {
    // 1) Get the vehicle for this booking
    $get = $con->prepare("SELECT VEHICLE_ID, BOOK_STATUS FROM booking WHERE BOOK_ID = ? FOR UPDATE");
    $get->bind_param('i', $bookId);
    $get->execute();
    $res = $get->get_result();
    $row = $res->fetch_assoc();
    if (!$row) {
        throw new Exception('Booking not found');
    }

    $vehicleId = (int)$row['VEHICLE_ID'];

    // 2) Mark booking approved if not already
    $updBook = $con->prepare("UPDATE booking SET BOOK_STATUS = 'APPROVED' WHERE BOOK_ID = ?");
    $updBook->bind_param('i', $bookId);
    $updBook->execute();

    // 3) Set vehicle as not available
    $updVeh = $con->prepare("UPDATE vehicles SET AVAILABLE = 'N' WHERE VEHICLE_ID = ?");
    $updVeh->bind_param('i', $vehicleId);
    $updVeh->execute();

    mysqli_commit($con);
    header('Location: adminbook.php?approved=1');
    exit;
} catch (Throwable $e) {
    mysqli_rollback($con);
    header('Location: adminbook.php?approved=0&error=' . urlencode($e->getMessage()));
    exit;
}
?>