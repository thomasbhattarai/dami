<?php
require_once('connection.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email'];
$isAdmin = isset($_SESSION['admin']);

// Get user info
$user_sql = "SELECT * FROM users WHERE EMAIL='$email'";
$user_result = mysqli_query($con, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Determine which bookings to show
if ($isAdmin) {
    // Admin can view all bookings
    $sql = "SELECT * FROM booking ORDER BY BOOK_DATE DESC";
} else {
    // Regular users see all their bookings (active + history)
    $sql = "SELECT * FROM booking WHERE EMAIL='$email' ORDER BY BOOK_DATE DESC";
}

$result = mysqli_query($con, $sql);
$bookings = [];
while($row = mysqli_fetch_assoc($result)) {
    $bookings[] = $row;
}

// Split bookings into current vs past for separate tables
$currentBookings = array_values(array_filter($bookings, function($b) {
    return !in_array($b['BOOK_STATUS'], ['RETURNED', 'Canceled']);
}));

$pastBookings = array_values(array_filter($bookings, function($b) {
    return in_array($b['BOOK_STATUS'], ['RETURNED', 'Canceled']);
}));

$totalBookings = count($bookings);
$activeCount = count($currentBookings);
$completedCount = count(array_filter($bookings, function($b) {
    return $b['BOOK_STATUS'] == 'RETURNED';
}));

// Reusable renderer for booking rows
function renderBookingRow($booking, $isAdmin, $con) {
    $vehicleId = $booking['VEHICLE_ID'];
    $vehicleRes = mysqli_query($con, "SELECT * FROM vehicles WHERE VEHICLE_ID='$vehicleId'");
    $vehicle = mysqli_fetch_assoc($vehicleRes);

    $status = $booking['BOOK_STATUS'];
    $statusClass = 'status-processing';
    $displayStatus = $status;

    if ($status == 'APPROVED') {
        $statusClass = 'status-approved';
    } elseif ($status == 'RETURNED') {
        $statusClass = 'status-returned';
    } elseif ($status == 'Canceled') {
        $statusClass = 'status-canceled';
    } elseif (in_array($status, ['UNDER PROCESSING', 'PENDING', 'PAID'])) {
        $statusClass = 'status-pending';
        $displayStatus = 'PENDING APPROVAL';
    }
    ?>
    <tr>
        <td><strong>#<?php echo $booking['BOOK_ID']; ?></strong></td>
        <?php if ($isAdmin): ?>
            <td><?php echo htmlspecialchars($booking['EMAIL']); ?></td>
        <?php endif; ?>
        <td><?php echo htmlspecialchars($vehicle['VEHICLE_NAME'] ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($booking['BOOK_PLACE']); ?></td>
        <td><?php echo htmlspecialchars($booking['DESTINATION']); ?></td>
        <td><?php echo date('M d, Y', strtotime($booking['BOOK_DATE'])); ?></td>
        <td><?php echo date('M d, Y', strtotime($booking['RETURN_DATE'])); ?></td>
        <td><?php echo $booking['DURATION']; ?> days</td>
        <td>Rs. <?php echo number_format($booking['PRICE']); ?></td>
        <td>Rs. <?php echo number_format($booking['FINE']); ?></td>
        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $displayStatus; ?></span></td>
        <td>
            <div class="action-buttons">
                <?php if (!$isAdmin && $booking['BOOK_STATUS'] === 'APPROVED'): ?>
                    <a href="returnbooking.php?id=<?php echo $booking['BOOK_ID']; ?>" class="btn-sm btn-return">Return</a>
                <?php endif; ?>
                <?php if ($booking['BOOK_STATUS'] !== 'RETURNED' && $booking['BOOK_STATUS'] !== 'Canceled' && $booking['BOOK_STATUS'] !== 'APPROVED'): ?>
                    <a href="cancelbooking.php?id=<?php echo $booking['BOOK_ID']; ?>" class="btn-sm btn-cancel" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel</a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isAdmin ? 'Admin - Booking History' : 'Booking Status'; ?> - VeloRent</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f8f9fa;
            color: #2c3e50;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: #ffffff;
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
        }

        .navbar .icon {
            flex-shrink: 0;
        }

        .navbar img {
            height: 50px;
            transition: transform 0.3s;
        }

        .navbar img:hover {
            transform: scale(1.05);
        }

        .navbar .menu ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .navbar .menu a, .navbar .menu p {
            color: #193959ff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .navbar .menu a:hover {
            color: #3498db;
        }

        .navbar .nn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
        }

        .navbar .nn a {
            color: #111010ff;
            text-decoration: none;
        }

        .navbar .nn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(64, 102, 178, 0.4);
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            z-index: 1001;
        }

        .hamburger span {
            width: 30px;
            height: 3px;
            background: #193959ff;
            margin: 4px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            padding-top: 100px;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            color: #2c3e50;
        }

        .page-header h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.05);
            padding: 10px 5%;
            text-align: center;
            margin-top: 80px;
        }

        footer p {
            margin-bottom: 20px;
            color: #524f4f;
        }

        .socials {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .socials a {
            color: #333;
            font-size: 1.5rem;
            transition: color 0.3s, transform 0.3s;
        }

        .socials a:hover {
            color: #667eea;
        }

        /* No Bookings Message */
        .no-bookings-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .no-bookings-container h3 {
            font-size: 1.8rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .no-bookings-container p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
        }

        /* Table Styles */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }

        th {
            padding: 18px;
            text-align: left;
            font-size: 0.95rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px 18px;
            border-bottom: 1px solid #e0e6ed;
            font-size: 0.95rem;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background-color: #f5f7ff;
            transform: scale(1.01);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .table-title {
            margin: 0 0 10px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .empty-note {
            margin: 10px 0 0;
            color: #6c757d;
            font-weight: 600;
        }

        .table-container + .table-container {
            margin-top: 25px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-returned {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-canceled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-processing {
            background: #e7d4f5;
            color: #5a189a;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 0.85rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-return {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-return:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(220, 53, 69, 0.3);
        }

        /* Summary Section */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2);
        }

        .summary-card h4 {
            color: #667eea;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .summary-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 5%;
            }

            .hamburger {
                display: flex;
            }

            .menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 100%;
                max-width: 300px;
                height: 100vh;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                justify-content: flex-start;
                padding-top: 80px;
                transition: right 0.4s ease;
                z-index: 1000;
                box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
            }

            .menu.active {
                right: 0;
            }

            .navbar .menu ul {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
                padding: 0 30px;
                gap: 0;
            }

            .navbar .menu li {
                margin: 20px 0;
                width: 100%;
            }

            .navbar .menu a, .navbar .menu p {
                display: block;
            }

            .page-header h2 {
                font-size: 1.8rem;
            }

            .table-container {
                padding: 20px;
            }

            th, td {
                padding: 12px;
                font-size: 0.85rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-sm {
                width: 100%;
                text-align: center;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 10px 3%;
            }

            .navbar img {
                height: 40px;
            }

            .navbar .menu ul {
                gap: 10px;
            }

            .navbar .menu a, .navbar .menu p {
                font-size: 0.9rem;
            }

            .page-header h2 {
                font-size: 1.5rem;
            }

            .page-header p {
                font-size: 0.95rem;
            }

            .table-container {
                padding: 15px;
                border-radius: 15px;
            }

            th, td {
                padding: 10px;
                font-size: 0.8rem;
            }

            table {
                font-size: 0.75rem;
            }

            .status-badge {
                padding: 6px 10px;
                font-size: 0.75rem;
            }

            .no-bookings-container {
                padding: 40px 20px;
            }

            .no-bookings-container h3 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 8px 3%;
            }

            .navbar img {
                height: 35px;
            }

            .navbar .menu ul {
                gap: 8px;
            }

            .navbar .menu a, .navbar .menu p {
                font-size: 0.85rem;
            }

            .page-header h2 {
                font-size: 1.3rem;
            }

            .summary-card .number {
                font-size: 2rem;
            }

            .btn-sm {
                padding: 6px 10px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body class="body">
    <div class="cd">
        <nav class="navbar">
            <div class="icon">
                <a href="vehiclesdetails.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
            </div>
            
            <!-- Hamburger Menu -->
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="menu" id="menu">
                <ul>
                    <?php if (!$isAdmin): ?>
                        <li><p class="phello"><a id="pname" href="userprofile.php" style="cursor: pointer;"><?php echo htmlspecialchars($user['FNAME'].' '.$user['LNAME']); ?></a></p></li>
                        <li><a id="stat" href="bookingstatus.php">BOOKING STATUS</a></li>
                    <?php else: ?>
                        <li><p class="phello"><a href="admindash.php" style="cursor: pointer;">ADMIN PANEL</a></p></li>
                    <?php endif; ?>
                    <li><a id="vehicles" href="vehiclesdetails.php">VEHICLES</a></li>
                    <li><a href="index.php">LOGOUT</a></li>
                </ul>
            </div>
        </nav>
        <br>
        <br>
        
        <div class="container">
        <div class="page-header">
            <h2><?php echo $isAdmin ? 'All Bookings History' : 'Your Booking History'; ?></h2>
            <p><?php echo $isAdmin ? 'View and manage all user bookings' : 'Track all your vehicle rentals (including past bookings)'; ?></p>
        </div>

        <!-- Summary Cards -->
            <?php 
            $active = $activeCount;
            $completed = $completedCount;
            ?>
            <div class="summary-cards">
                <div class="summary-card">
                    <h4>Total Bookings</h4>
                    <div class="number"><?php echo $totalBookings; ?></div>
                </div>
                <div class="summary-card">
                    <h4>Active Bookings</h4>
                    <div class="number"><?php echo $active; ?></div>
                </div>
                <div class="summary-card">
                    <h4>Completed</h4>
                    <div class="number"><?php echo $completed; ?></div>
                </div>
            </div>

        <?php if (count($bookings) == 0): ?>
            <!-- No Bookings Message -->
            <div class="no-bookings-container">
                <h3>No Bookings Found</h3>
                <p><?php echo $isAdmin ? 'No booking records available.' : 'You haven\'t made any bookings yet.'; ?></p>
                <?php if (!$isAdmin): ?>
                    <a href="vehiclesdetails.php" class="nav-btn" style="display: inline-block;">Start Booking Now</a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <!-- Current Bookings Table -->
            <div class="table-container">
                <h3 class="table-title"><?php echo $isAdmin ? 'Current Bookings (All Users)' : 'Your Current Bookings'; ?></h3>
                <?php if (count($currentBookings) === 0): ?>
                    <p class="empty-note">No current bookings.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <?php if ($isAdmin): ?>
                                    <th>User Email</th>
                                <?php endif; ?>
                                <th>Vehicle</th>
                                <th>Pickup Location</th>
                                <th>Destination</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Fine</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($currentBookings as $booking): ?>
                                <?php renderBookingRow($booking, $isAdmin, $con); ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Past Bookings Table -->
            <div class="table-container">
                <h3 class="table-title"><?php echo $isAdmin ? 'Past Bookings (All Users)' : 'Your Past Bookings'; ?></h3>
                <?php if (count($pastBookings) === 0): ?>
                    <p class="empty-note">No past bookings yet.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th>
                                <?php if ($isAdmin): ?>
                                    <th>User Email</th>
                                <?php endif; ?>
                                <th>Vehicle</th>
                                <th>Pickup Location</th>
                                <th>Destination</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th>Duration</th>
                                <th>Price</th>
                                <th>Fine</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pastBookings as $booking): ?>
                                <?php renderBookingRow($booking, $isAdmin, $con); ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>
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
    <script>
        // Hamburger menu toggle
        const hamburger = document.getElementById('hamburger');
        const menu = document.getElementById('menu');

        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            menu.classList.toggle('active');
            
            // Prevent scrolling when menu is open
            if(menu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        });

        // Close menu when clicking on a link
        const menuLinks = document.querySelectorAll('.menu a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
                hamburger.classList.remove('active');
                menu.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    </script>
</body>
</html>