<?php
require_once('connection.php');
session_start();
$email = $_SESSION['email'];

$sql = "SELECT * FROM booking WHERE EMAIL='$email' AND BOOK_STATUS NOT IN ('Canceled','RETURNED') ORDER BY BOOK_ID DESC";
$name = mysqli_query($con, $sql);

if (mysqli_num_rows($name) == 0) {
    ?>
    <button class="utton"><a href="vehiclesdetails.php">Go to Home</a></button>
    <div class="name">HELLO!</div>
    <div class="box">
        <div class="content">
            <p class="no-bookings">No active bookings</p>
        </div>
    </div>
    <?php
} else {

    $sql2 = "SELECT * FROM users WHERE EMAIL='$email'";
    $name2 = mysqli_query($con, $sql2);
    $rows2 = mysqli_fetch_assoc($name2);
?>

<button class="utton"><a href="vehiclesdetails.php">Go to Home</a></button>
<div class="name">HELLO!</div>

<?php
    // Loop through all bookings
    while($rows = mysqli_fetch_assoc($name)) {
        $vehicle_id = $rows['VEHICLE_ID'];
        $book_id = $rows['BOOK_ID'];
        $sql3 = "SELECT * FROM vehicles WHERE VEHICLE_ID='$vehicle_id'";
        $name3 = mysqli_query($con, $sql3);
        $rows3 = mysqli_fetch_assoc($name3);
?>

<div class="box">
    <div class="content">
        <h1>VEHICLE NAME: <?php echo htmlspecialchars($rows3['VEHICLE_NAME']); ?></h1><br>
        <h1>NO OF DAYS: <?php echo $rows['DURATION']; ?></h1><br>
        <h1>BOOKING STATUS: <?php echo $rows['BOOK_STATUS']; ?></h1><br>
        <div class="button-container">
            <?php if ($rows['BOOK_STATUS'] === 'APPROVED'): ?>
                <a href="returnbooking.php?id=<?php echo $book_id; ?>" class="return-booking-btn">Return Vehicle</a>
            <?php endif; ?>
            <a href="cancelbooking.php?id=<?php echo $book_id; ?>" class="cancel-booking-btn" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel This Booking</a>
        </div>
    </div>
</div>

<?php 
    } // End while loop
} // End else
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKING STATUS - VeloRent</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* CHANGED */
            background: #e3e4e6ff;
            color: #2c3e50;

            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        /* Cancel Button (unchanged) */
        .cancel-btn {
            position: absolute;
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
            box-shadow: 0 5px 15px rgba(255, 68, 68, 0.3);
            z-index: 100;
        }

        .cancel-btn a {
            color: white;
            text-decoration: none;
        }

        /* Home Button (unchanged) */
        .utton {
            position: absolute;
            top: 30px;
            left: 30px;
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

        .utton a {
            color: white;
            text-decoration: none;
        }

        /* Welcome Message */
        .name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Content Box */
        .box {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 40px;
            margin: 20px 0;
            width: 100%;
            max-width: 600px;
        }

        .content {
            text-align: center;
        }

        h1 {
            /* CHANGED */
            color: #2c3e50;

            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* No Bookings Message */
        .no-bookings {
            font-size: 1.2rem;
            color: #667eea;
            padding: 20px;
            text-align: center;
            font-weight: 500;
        }

        /* Cancel Booking Button (unchanged) */
                .cancel-booking-btn {
            display: inline-block;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            cursor: pointer;
        }

        .return-booking-btn {
            display: inline-block;
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            cursor: pointer;
            margin-right: 10px;
        }

        /* Responsive text color fix */
        @media (max-width: 768px) {
            body {
                color: #2c3e50;
            }
        }
    </style>
</head>
<body>

    
</body>
</html>