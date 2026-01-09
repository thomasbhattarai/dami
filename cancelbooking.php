<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bookId <= 0) {
    header('Location: bookingstatus.php?cancel=invalid');
    exit();
}

mysqli_begin_transaction($con);
try {
    $stmt = $con->prepare("SELECT VEHICLE_ID FROM booking WHERE BOOK_ID=? AND EMAIL=? FOR UPDATE");
    $stmt->bind_param('is', $bookId, $email);
    $stmt->execute();
    $stmt->bind_result($vehicleId);
    if (!$stmt->fetch()) {
        $stmt->close();
        mysqli_rollback($con);
        header('Location: bookingstatus.php?cancel=notfound');
        exit();
    }
    $stmt->close();

    $stmtUp = $con->prepare("UPDATE booking SET BOOK_STATUS='Canceled' WHERE BOOK_ID=? AND EMAIL=?");
    $stmtUp->bind_param('is', $bookId, $email);
    $stmtUp->execute();
    $stmtUp->close();

    $stmtVeh = $con->prepare("UPDATE vehicles SET AVAILABLE='Y' WHERE VEHICLE_ID=?");
    $stmtVeh->bind_param('i', $vehicleId);
    $stmtVeh->execute();
    $stmtVeh->close();

    mysqli_commit($con);
    header('Location: bookingstatus.php?cancel=success');
    exit();
} catch (Throwable $e) {
    mysqli_rollback($con);
    header('Location: bookingstatus.php?cancel=error');
    exit();
}
?>