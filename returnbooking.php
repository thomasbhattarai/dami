<?php
session_start();
require_once('connection.php');

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$email = $_SESSION['email'];
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $comment = trim($_POST['comment'] ?? '');
    $rating = isset($_POST['rating']) ? max(1, min(5, (int)$_POST['rating'])) : null;

    if ($bookId <= 0) {
        header('Location: bookingstatus.php?return=invalid');
        exit();
    }

    mysqli_begin_transaction($con);
    try {
        $stmt = $con->prepare("SELECT VEHICLE_ID, BOOK_STATUS FROM booking WHERE BOOK_ID=? AND EMAIL=? FOR UPDATE");
        $stmt->bind_param('is', $bookId, $email);
        $stmt->execute();
        $stmt->bind_result($vehicleId, $bookStatus);
        if (!$stmt->fetch()) {
            $stmt->close();
            mysqli_rollback($con);
            header('Location: bookingstatus.php?return=notfound');
            exit();
        }
        $stmt->close();

        if (!in_array($bookStatus, ['APPROVED', 'PAID'])) {
            mysqli_rollback($con);
            header('Location: bookingstatus.php?return=notallowed');
            exit();
        }

        // Ensure reviews table exists
        $createSql = "CREATE TABLE IF NOT EXISTS reviews (
            REVIEW_ID INT AUTO_INCREMENT PRIMARY KEY,
            VEHICLE_ID INT NOT NULL,
            EMAIL VARCHAR(255) NOT NULL,
            COMMENT TEXT NOT NULL,
            RATING TINYINT NULL,
            CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_vehicle (VEHICLE_ID),
            INDEX idx_created (CREATED_AT)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        mysqli_query($con, $createSql);

        // Insert review if provided
        if ($comment !== '' || $rating !== null) {
            $stmtRev = $con->prepare("INSERT INTO reviews (VEHICLE_ID, EMAIL, COMMENT, RATING) VALUES (?,?,?,?)");
            $stmtRev->bind_param('issi', $vehicleId, $email, $comment, $rating);
            $stmtRev->execute();
            $stmtRev->close();
        }

        $stmtUp = $con->prepare("UPDATE booking SET BOOK_STATUS='RETURNED' WHERE BOOK_ID=? AND EMAIL=?");
        $stmtUp->bind_param('is', $bookId, $email);
        $stmtUp->execute();
        $stmtUp->close();

        $stmtVeh = $con->prepare("UPDATE vehicles SET AVAILABLE='Y' WHERE VEHICLE_ID=?");
        $stmtVeh->bind_param('i', $vehicleId);
        $stmtVeh->execute();
        $stmtVeh->close();

        mysqli_commit($con);
        header('Location: bookingstatus.php?return=success');
        exit();
    } catch (Throwable $e) {
        mysqli_rollback($con);
        header('Location: bookingstatus.php?return=error');
        exit();
    }
}

// Show form for comment/review before returning
if ($bookId <= 0) {
    header('Location: bookingstatus.php?return=invalid');
    exit();
}

$verify = mysqli_query($con, "SELECT BOOK_ID FROM booking WHERE BOOK_ID=$bookId AND EMAIL='".mysqli_real_escape_string($con,$email)."' AND BOOK_STATUS IN ('APPROVED','PAID')");
if (!$verify || mysqli_num_rows($verify) === 0) {
    header('Location: bookingstatus.php?return=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Vehicle & Review</title>
    <style>
        body {font-family: 'Poppins', sans-serif; background:#f4f6f8; color:#2c3e50; display:flex; justify-content:center; align-items:center; min-height:100vh;}
        .card {background:#fff; padding:30px; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,0.1); width:90%; max-width:520px;}
        h2 {margin-bottom:10px; color:#28a745;}
        label {display:block; margin:12px 0 6px; font-weight:600;}
        textarea {width:100%; min-height:120px; padding:12px; border-radius:10px; border:1px solid #dce1e6; font-size:14px;}
        select, button {padding:12px 14px; border-radius:10px; border:1px solid #dce1e6; font-size:14px; width:100%;}
        button {background:linear-gradient(45deg,#28a745,#20c997); color:#fff; border:none; cursor:pointer; margin-top:14px; font-weight:700;}
        button:hover {opacity:0.95;}
        .back {display:block; text-align:center; margin-top:10px; color:#28a745; text-decoration:none; font-weight:600;}
    </style>
</head>
<body>
    <div class="card">
        <h2>Return Vehicle</h2>
        <p>Please share your experience with this vehicle. Your review will help other users make informed decisions.</p>
        <form method="POST">
            <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($bookId); ?>">
            <label for="rating">Rating (1-5)</label>
            <select name="rating" id="rating">
                <option value="">Select rating (optional)</option>
                <?php for($r=5;$r>=1;$r--): ?>
                    <option value="<?php echo $r; ?>"><?php echo $r; ?> Star<?php echo $r>1?'s':''; ?></option>
                <?php endfor; ?>
            </select>

            <label for="comment">Your Review</label>
            <textarea name="comment" id="comment" placeholder="Share your experience with this vehicle..."></textarea>

            <button type="submit">Submit Review & Return Vehicle</button>
        </form>
        <a class="back" href="bookingstatus.php">Go back</a>
    </div>
</body>
</html>
