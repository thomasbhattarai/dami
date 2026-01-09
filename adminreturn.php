<?php
require_once('connection.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['bookid'])) {
    $vehicle_id = (int)$_GET['id'];
    $book_id = (int)$_GET['bookid'];

    mysqli_begin_transaction($con);
    try {
        // 1) Mark vehicle available again
        $stmt1 = $con->prepare("UPDATE vehicles SET AVAILABLE='Y' WHERE VEHICLE_ID=?");
        $stmt1->bind_param('i', $vehicle_id);
        $stmt1->execute();

        // 2) Keep history but mark booking as returned so revenue records remain
        $stmt2 = $con->prepare("UPDATE booking SET BOOK_STATUS='RETURNED' WHERE BOOK_ID=? AND VEHICLE_ID=?");
        $stmt2->bind_param('ii', $book_id, $vehicle_id);
        $stmt2->execute();

        mysqli_commit($con);
        header('Location: adminbook.php?returned=1');
        exit();
    } catch (Throwable $e) {
        mysqli_rollback($con);
        header('Location: adminbook.php?returned=0&error=' . urlencode($e->getMessage()));
        exit();
    }
}

header('Location: adminbook.php?returned=0&error=invalid');
exit();
?>