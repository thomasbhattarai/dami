<?php
// Buffer output so redirects can happen even after markup starts
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VEHICLE Details</title>
    
    <!-- Using the remembered CSS from the login page (dark futuristic theme) -->
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

        /* Navbar Styles */
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

        .navbar img {
            height: 50px;
            transition: transform 0.3s;
        }

        .navbar img:hover {
            transform: scale(1.05);
        }

        .menu ul {
            display: flex;
            list-style: none;
            gap: 30px;
            align-items: center;
        }

        .menu a, .menu p {
             color: #193959ff;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .menu a:hover {
            color: #3498db;
        }

        .nn {
            background: linear-gradient(45deg, #3498db, #764ba2);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
        }

        .nn a {
            color: #111010ff;
            text-decoration: none;
        }

        .nn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(64, 102, 178, 0.4);
        }

        /* Search & Filter Bar */
        .search-filter {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 100px 0 40px;
            flex-wrap: wrap;
        }

        .search-filter input {
            padding: 12px 20px;
            width: 300px;
            max-width: 100%;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: #221f1fff;
            font-size: 1rem;
            outline: none;
            backdrop-filter: blur(5px);
        }

        .search-filter input::placeholder {
            color: rgba(39, 35, 35, 0.6);
        }

        .search-filter input:focus {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 15px rgba(255, 123, 0, 0.3);
        }

        .filter-container {
            position: relative;
        }

        .filter-button {
            background: linear-gradient(45deg, #3498db, #764ba2);        
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #221f1fff;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .filter-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(64, 102, 178, 0.4);
        }

        .filter-dropdown {
            display: none;
            position: absolute;
            top: 110%;
            right: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            min-width: 180px;
            z-index: 10;
            overflow: hidden;
        }

        .filter-dropdown.show {
            display: block;
        }

        .filter-dropdown a {
            display: block;
            padding: 12px 20px;
            color: #221f1fff;
            text-decoration: none;
            transition: background 0.3s;
        }

        .filter-dropdown a:hover {
            background: rgba(65, 62, 60, 0.3);
        }

        /* Page Title */
        .overview {
            text-align: center;
            font-size: 2.8rem;
            margin: 40px 0;
            background: linear-gradient(45deg, #3498db, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Sections */
        .recommended-section h2,
        .other-vehicles-section h2 {
            text-align: center;
            font-size: 2.2rem;
            margin: 50px 0 30px;
            background: linear-gradient(45deg, #3498db, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Vehicle Grid */
        .de {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 5%;
            list-style: none;
        }

        .vehicle-item {
            display: none;
        }

        .vehicle-item.visible {
            display: block;
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .box {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(255, 123, 0, 0.3);
        }

        .imgBx {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .imgBx img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .box:hover .imgBx img {
            transform: scale(1.1);
        }

        .recommended-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #3498db, #764ba2);
            color: #1a1919ff;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: bold;
            z-index: 5;
        }

        .content {
            padding: 25px;
            text-align: center;
        }

        .content h1 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #764ba2;
        }

        .content h2 {
            font-size: 1.1rem;
            margin: 10px 0;
            color: #252020ff;
        }

        .content h2 a {
            color: #383434ff;
            font-weight: 600;
        }

        .utton {
            margin-top: 20px;
            display: inline-block;
            background: linear-gradient(45deg, #3498db, #764ba2);
            border: none;
            padding: 14px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            color: #342f2fff;
            text-decoration: none;
            transition: all 0.3s;
            text-align: center;
        }

        .utton:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 123, 0, 0.4);
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

        /* Responsive */
        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
                gap: 15px;
            }

            .search-filter input {
                width: 90%;
            }

            .de {
                grid-template-columns: 1fr;
                padding: 0 5%;
            }

            .overview {
                font-size: 2.2rem;
            }
        }
    </style>
</head>
<body class="body">


<?php 
    require_once('connection.php');
    session_start();

    // Require logged-in user and load their record; redirect if missing
    $value = isset($_SESSION['email']) ? $_SESSION['email'] : null;
    if (!$value) {
        header('Location: index.php');
        exit;
    }

    $_SESSION['email'] = $value;
    $sql = "select * from users where EMAIL='$value'";
    $name = mysqli_query($con, $sql);
    $rows = ($name && ($fetched = mysqli_fetch_assoc($name))) ? $fetched : null;
    if (!$rows) {
        header('Location: index.php');
        exit;
    }
    
    // Handle sorting
    $sort = '';
    if (isset($_GET['sort'])) {
        if ($_GET['sort'] == 'price_asc') {
            $sort = ' ORDER BY PRICE ASC';
        } elseif ($_GET['sort'] == 'price_desc') {
            $sort = ' ORDER BY PRICE DESC';
        }
    }
    
    $sql2 = "select * from vehicles where AVAILABLE='Y'";
    $sql2 .= $sort;
    
    $vehicles = mysqli_query($con, $sql2);
    
    // Pull the most recently returned vehicles across all users for lightweight recommendations
    $recommendedReturned = [];
    $returnedSql = "SELECT v.*, MAX(b.RETURN_DATE) AS LAST_RETURNED
                    FROM booking b
                    JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID
                    WHERE b.BOOK_STATUS = 'RETURNED' AND v.AVAILABLE = 'Y'
                    GROUP BY v.VEHICLE_ID
                    ORDER BY LAST_RETURNED DESC, MAX(b.BOOK_ID) DESC
                    LIMIT 6";
    if ($returnedResult = mysqli_query($con, $returnedSql)) {
        while ($row = mysqli_fetch_assoc($returnedResult)) {
            $recommendedReturned[] = $row;
        }
    }

    // Collect public reviews to display under vehicles
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS reviews (
        REVIEW_ID INT AUTO_INCREMENT PRIMARY KEY,
        VEHICLE_ID INT NOT NULL,
        EMAIL VARCHAR(255) NOT NULL,
        COMMENT TEXT NOT NULL,
        RATING TINYINT NULL,
        CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_vehicle (VEHICLE_ID),
        INDEX idx_created (CREATED_AT)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $reviewsByVehicle = [];
    $reviewsQuery = mysqli_query($con, "SELECT r.VEHICLE_ID, r.COMMENT, r.RATING, r.CREATED_AT, u.FNAME, u.LNAME
                                         FROM reviews r
                                         LEFT JOIN users u ON r.EMAIL = u.EMAIL
                                         ORDER BY r.CREATED_AT DESC");
    if ($reviewsQuery) {
        while ($rev = mysqli_fetch_assoc($reviewsQuery)) {
            $reviewsByVehicle[$rev['VEHICLE_ID']][] = $rev;
        }
    }

    // Store vehicle data for display
    $vehicleData = [];
    while($result = mysqli_fetch_array($vehicles)) {
        $vehicleData[] = $result;
    }
?>

<div class="cd">
    <nav class="navbar">
        <div class="icon">
            <a href="vehiclesdetails.php"><img style="height: 50px;" src="images/icon.png" alt="VeloRent Logo"></a>
        </div>
        <div class="menu">
            <ul>
                <li><p class="phello"><a id="pname" href="userprofile.php" style="cursor: pointer;"><?php echo htmlspecialchars($rows['FNAME'].' '.$rows['LNAME']); ?></a></p></li>
                <li><a id="stat" href="bookingstatus.php">BOOKING STATUS</a></li>
                <li><button class="nn"><a href="index.php">LOGOUT</a></button></li>
            </ul>
        </div>
    </nav>
    <br>
    <br>

    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Search vehicle name...">
        <div class="filter-container">
            <button class="filter-button" onclick="toggleFilterDropdown('type')">
                <ion-icon name="car-outline"></ion-icon> Vehicle Type
            </button>
            <div class="filter-dropdown" id="typeFilterDropdown">
                <a href="#" onclick="filterByType('')">All</a>
                <a href="#" onclick="filterByType('Car')">Car</a>
                <a href="#" onclick="filterByType('Bike')">Bike</a>
                <a href="#" onclick="filterByType('Scooter')">Scooter</a>
            </div>
        </div>
        <div class="filter-container">
            <button class="filter-button" onclick="toggleFilterDropdown('price')">
                <ion-icon name="options-outline"></ion-icon> Sort by Price
            </button>
            <div class="filter-dropdown" id="priceFilterDropdown">
                <a href="?sort=price_asc">Price: Low to High</a>
                <a href="?sort=price_desc">Price: High to Low</a>
            </div>
        </div>
    </div>

    <h1 class="overview">OUR VEHICLE OVERVIEW</h1>

    <!-- Recently Returned Recommendations (global) -->
    <?php if (!empty($recommendedReturned)): ?>
    <div class="recommended-section">
        <h2>Recommended</h2>
        <ul class="de">
            <?php foreach ($recommendedReturned as $vehicle):
                $res = $vehicle['VEHICLE_ID'];
            ?>
            <li class="vehicle-item visible" data-name="<?php echo htmlspecialchars(strtolower($vehicle['VEHICLE_NAME'])); ?>" data-type="<?php echo htmlspecialchars(strtolower($vehicle['VEHICLE_TYPE'])); ?>">
                <form method="POST">
                    <div class="box">
                        <div class="imgBx">
                            <img src="images/<?php echo $vehicle['VEHICLE_IMG']?>" alt="<?php echo $vehicle['VEHICLE_NAME']?>">
                            <div class="recommended-badge">Recommended</div>
                        </div>
                        <div class="content">
                            <h1><?php echo $vehicle['VEHICLE_NAME']?></h1>
                            <h2>Fuel Type: <a><?php echo $vehicle['FUEL_TYPE']?></a></h2>
                            <h2>Capacity: <a><?php echo $vehicle['CAPACITY']?></a></h2>
                            <h2>Rent Per Day: <a>Rs<?php echo $vehicle['PRICE']?>/-</a></h2>
                            <h2>Vehicle Type: <a><?php echo $vehicle['VEHICLE_TYPE']?></a></h2>
                            <a class="utton" href="booking.php?id=<?php echo $res;?>">Book Now</a>
                            <?php if (!empty($reviewsByVehicle[$res])): ?>
                                <div style="margin-top:16px; text-align:left;">
                                    <h3 style="font-size:1rem; margin-bottom:8px; color:#34495e;">Recent Reviews</h3>
                                    <?php foreach (array_slice($reviewsByVehicle[$res], 0, 2) as $rev): ?>
                                        <div style="padding:8px 10px; background:rgba(0,0,0,0.03); border-radius:8px; margin-bottom:8px;">
                                            <div style="font-weight:600; font-size:0.95rem; color:#2c3e50;">
                                                <?php echo htmlspecialchars(trim(($rev['FNAME'] ?? '').' '.($rev['LNAME'] ?? 'User'))); ?>
                                                <?php if (!empty($rev['RATING'])): ?>
                                                    <span style="color:#f39c12; margin-left:6px;">★ <?php echo (int)$rev['RATING']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size:0.9rem; color:#555; margin-top:4px;">
                                                <?php echo nl2br(htmlspecialchars($rev['COMMENT'])); ?>
                                            </div>
                                            <div style="font-size:0.8rem; color:#888; margin-top:4px;">
                                                <?php echo htmlspecialchars(date('M d, Y', strtotime($rev['CREATED_AT']))); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Vehicles Section -->
    <?php if (!empty($vehicleData)): ?>
    <div class="other-vehicles-section">
        <h2>Available Vehicles</h2>
        <ul class="de">
            <?php foreach ($vehicleData as $result): 
                $res = $result['VEHICLE_ID'];
            ?>
            <li class="vehicle-item visible" data-name="<?php echo htmlspecialchars(strtolower($result['VEHICLE_NAME'])); ?>" data-type="<?php echo htmlspecialchars(strtolower($result['VEHICLE_TYPE'])); ?>">
                <form method="POST">
                    <div class="box">
                        <div class="imgBx">
                            <img src="images/<?php echo $result['VEHICLE_IMG']?>" alt="<?php echo $result['VEHICLE_NAME']?>">
                        </div>
                        <div class="content">
                            <h1><?php echo $result['VEHICLE_NAME']?></h1>
                            <h2>Fuel Type: <a><?php echo $result['FUEL_TYPE']?></a></h2>
                            <h2>Capacity: <a><?php echo $result['CAPACITY']?></a></h2>
                            <h2>Rent Per Day: <a>Rs<?php echo $result['PRICE']?>/-</a></h2>
                            <h2>Vehicle Type: <a><?php echo $result['VEHICLE_TYPE']?></a></h2>
                            <a class="utton" href="booking.php?id=<?php echo $res;?>">Book Now</a>
                            <?php if (!empty($reviewsByVehicle[$res])): ?>
                                <div style="margin-top:16px; text-align:left;">
                                    <h3 style="font-size:1rem; margin-bottom:8px; color:#34495e;">Recent Reviews</h3>
                                    <?php foreach (array_slice($reviewsByVehicle[$res], 0, 2) as $rev): ?>
                                        <div style="padding:8px 10px; background:rgba(0,0,0,0.03); border-radius:8px; margin-bottom:8px;">
                                            <div style="font-weight:600; font-size:0.95rem; color:#2c3e50;">
                                                <?php echo htmlspecialchars(trim(($rev['FNAME'] ?? '').' '.($rev['LNAME'] ?? 'User'))); ?>
                                                <?php if (!empty($rev['RATING'])): ?>
                                                    <span style="color:#f39c12; margin-left:6px;">★ <?php echo (int)$rev['RATING']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size:0.9rem; color:#555; margin-top:4px;">
                                                <?php echo nl2br(htmlspecialchars($rev['COMMENT'])); ?>
                                            </div>
                                            <div style="font-size:0.8rem; color:#888; margin-top:4px;">
                                                <?php echo htmlspecialchars(date('M d, Y', strtotime($rev['CREATED_AT']))); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php else: ?>
        <p style="text-align:center; margin:40px 0;">No vehicles available right now.</p>
    <?php endif; ?>
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
    // Loading animation
    // Graceful loader hide with fallback
    (function() {
        const loading = document.querySelector('.loading');
        const hide = () => loading && loading.classList.add('hidden');
        window.addEventListener('load', () => setTimeout(hide, 300));
        document.addEventListener('DOMContentLoaded', () => setTimeout(hide, 800));
        // Safety: force hide after 2s in case resources stall
        setTimeout(hide, 2000);
    })();

    let selectedType = '';

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const vehicleItems = document.querySelectorAll('.vehicle-item');

        // Show all initially
        vehicleItems.forEach(item => item.classList.add('visible'));

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            filterVehicles(searchTerm, selectedType);
        });
    });

    // Toggle filter dropdown
    function toggleFilterDropdown(type) {
        const dropdowns = {
            'type': document.getElementById('typeFilterDropdown'),
            'price': document.getElementById('priceFilterDropdown')
        };
        const dropdown = dropdowns[type];
        const otherDropdown = type === 'type' ? dropdowns['price'] : dropdowns['type'];
        
        dropdown.classList.toggle('show');
        if (otherDropdown) otherDropdown.classList.remove('show');
    }

    // Filter by vehicle type
    function filterByType(type) {
        selectedType = type.toLowerCase();
        const searchInput = document.getElementById('searchInput');
        filterVehicles(searchInput.value.toLowerCase().trim(), selectedType);
        document.getElementById('typeFilterDropdown').classList.remove('show');
    }

    // Combined filtering
    function filterVehicles(searchTerm, vehicleType) {
        const vehicleItems = document.querySelectorAll('.vehicle-item');

        vehicleItems.forEach(item => {
            const vehicleName = item.getAttribute('data-name');
            const itemType = item.getAttribute('data-type');
            
            const matchesSearch = searchTerm === '' || vehicleName.includes(searchTerm);
            const matchesType = vehicleType === '' || itemType === vehicleType;

            if (matchesSearch && matchesType) {
                item.classList.add('visible');
            } else {
                item.classList.remove('visible');
            }
        });
    }

    // Close dropdown on outside click
    window.onclick = function(event) {
        if (!event.target.closest('.filter-button') && !event.target.closest('.filter-dropdown')) {
            document.getElementById('typeFilterDropdown').classList.remove('show');
            document.getElementById('priceFilterDropdown').classList.remove('show');
        }
    }
</script>
</body>
</html>