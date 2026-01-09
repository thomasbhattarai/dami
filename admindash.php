<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMINISTRATOR</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding-bottom: 60px; /* Space to avoid footer overlap */
        }
        .navbar {
            width: 100%;
            height: 75px;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .logo {
            color: #007bff; /* Blue */
            font-size: 36px;
            font-family: 'Lora', serif;
        }
        .menu ul {
            display: flex;
            gap: 25px;
            list-style: none;
        }
        .menu ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 16px;
            transition: color 0.3s;
        }
        .menu ul li a:hover {
            color: #66b3ff; /* Lighter Blue */
        }
        .content-table {
            border-collapse: collapse;
            font-size: 0.9em;
            width: 80%;
            margin: 25px auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        .content-table thead tr {
            background-color: #007bff; /* Blue */
            color: white;
            text-align: left;
        }
        .content-table th, .content-table td {
            padding: 12px 15px;
        }
        .content-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }
        .content-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }
        .content-table tbody tr:last-of-type {
            border-bottom: 2px solid #007bff; /* Blue */
        }
        .header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
            color: #333;
        }
        .button {
            background: #007bff; /* Blue */
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            font-weight: bold;
        }
        .button:hover {
            background: #66b3ff; /* Lighter Blue */
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #333;
            color: #fff;
            text-align: center;
            padding: 5px 0;
        }

        footer p {
            margin: 0;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .socials a {
            color: white;
            font-size: 20px;
            margin: 1px;
            transition: color 0.3s;
        }
        .socials a:hover {
            color: #66b3ff; /* Lighter Blue */
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="navbar">
          <img style="height: 50px; " src="images\icon.png" alt="">
        <div class="menu">
            <ul>
                <li><a href="adminvehicle.php">VEHICLE MANAGEMENT</a></li>
                <li><a href="adminusers.php">USERS</a></li>
                <li><a href="admindash.php">FEEDBACKS</a></li>
                <li><a href="adminbook.php">BOOKING REQUEST</a></li>
                <li><a href="index.php" class="button">LOGOUT</a></li>
            </ul>
        </div>
    </div>

    <h1 class="header">FEEDBACKS</h1>

    <table class="content-table">
        <thead>
            <tr>
                <th>FEEDBACK ID</th>
                <th>EMAIL</th>
                <th>COMMENT</th>
            </tr>
        </thead>
        <tbody>
        <?php
            require_once('connection.php');
            $query = "SELECT * FROM feedback";
            $queryy = mysqli_query($con, $query);
            while ($res = mysqli_fetch_array($queryy)) {
        ?>
            <tr>
                <td><?php echo $res['FED_ID']; ?></td>
                <td><?php echo $res['EMAIL']; ?></td>
                <td><?php echo $res['COMMENT']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

    <footer>
        <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
        <div class="socials">
            <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://x.com/thomashbhattarai" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
            <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
        </div>
    </footer>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>
