<?php
require_once('connection.php');
session_start();

if(!isset($_SESSION['admin_id'])) {
    header("location: adminlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vehicle Management - VeloRent Admin</title>

<style>
:root{
    --primary:#667eea;
    --secondary:#764ba2;
    --danger:#dc3545;
    --success:#28a745;
    --bg:#f8f9fa;
    --card:#ffffff;
    --border:#e0e6ed;
    --text:#2c3e50;
    --muted:#6c757d;
}

/* BASE */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Source Sans Pro',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;}
body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column;}

/* TOP BAR */
.navbar{
    position:fixed;top:0;left:0;right:0;height:56px;
    background:#ffffff;border-bottom:1px solid var(--border);
    display:flex;align-items:center;padding:0 1rem;z-index:1030;
}
.navbar img{height:40px}
.navbar span{font-size:1.1rem;font-weight:600}

/* SIDEBAR */
.sidebar{
    position:fixed;top:56px;left:0;bottom:0;width:200px;
    background:#ffffff;border-right:1px solid var(--border);
    padding:1rem 0;z-index:1020;
}
.sidebar ul{list-style:none}
.sidebar li{margin:.25rem 0}
.sidebar a{
    display:flex;align-items:center;
    padding:.65rem 1rem;color:var(--muted);
    text-decoration:none;border-radius:.3rem;
    transition:.2s;
}
.sidebar a:hover{
    background:rgba(102,126,234,.12);
    color:var(--primary);
}
.sidebar a.logout{
    background:var(--danger);color:#fff;
}
.sidebar a.logout:hover{background:#c82333}

/* MAIN */
.main-content{
    flex:1;margin-left:200px;margin-top:56px;
    padding:1.5rem;background:var(--bg);
}

/* STATS */
.admin-stats{
    display:flex;gap:30px;justify-content:center;
    margin-bottom:40px;flex-wrap:wrap;
}
.stat-card{
    background:var(--card);padding:25px;
    border-radius:12px;min-width:200px;
    text-align:center;border:2px solid var(--border);
    box-shadow:0 10px 30px rgba(102,126,234,.15);
}
.stat-card h3{color:var(--primary);margin-bottom:10px}
.stat-card p{font-size:2rem;font-weight:700}

/* HEADER */
.header-container{
    display:flex;justify-content:space-between;
    align-items:center;margin-bottom:40px;
    flex-wrap:wrap;gap:20px;
}
.header{font-size:1.75rem;font-weight:600}

/* ADD BUTTON */
.add{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;padding:10px 25px;border-radius:50px;
    text-decoration:none;font-weight:600;
}
.add:hover{opacity:.9}

/* TABLE */
.table-container{
    overflow-x:auto;border-radius:12px;
    border:2px solid var(--border);background:#fff;
}
.content-table{
    width:100%;border-collapse:collapse;min-width:1000px;
}
.content-table thead{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;text-transform:uppercase;font-size:.85rem;
}
.content-table th,.content-table td{
    padding:.75rem;border-bottom:1px solid var(--border);
    text-align:center;
}
.content-table tbody tr:hover{
    background:rgba(102,126,234,.08);
}
.content-table .button{
    background:var(--danger);
    color:#fff;padding:.4rem .7rem;
    border-radius:.3rem;text-decoration:none;
}
.content-table .button:hover{background:#c82333}

/* FOOTER */
footer{
    margin-left:200px;background:#fff;
    border-top:1px solid var(--border);
    padding:1.5rem;text-align:center;
}
.socials{
    display:flex;justify-content:center;
    gap:1rem;margin-top:.75rem;
}
.socials a{color:var(--text);font-size:1.4rem}
.socials a:hover{color:var(--primary)}

/* MOBILE */
@media(max-width:768px){
    .sidebar{position:static;width:100%;border-right:none;border-bottom:1px solid var(--border)}
    .main-content,footer{margin-left:0}
}
</style>
</head>

<body>

<nav class="navbar">
    <img src="images/icon.png">
    <span>VeloRent Admin</span>
</nav>

<aside class="sidebar">
<ul>
    <li><a href="adminoverview.php">📊 Overview</a></li>
    <li><a href="adminvehicle.php">🚗 Vehicles</a></li>
    <li><a href="adminusers.php">👤 Users</a></li>
    <li><a href="adminbook.php">📑 Bookings</a></li>
    <li><a href="index.php" class="logout">⎗ Logout</a></li>
</ul>
</aside>

<main class="main-content">
<div class="admin-stats">
<?php
$total=mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) total FROM vehicles"))['total'];
$available=mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) available FROM vehicles WHERE AVAILABLE='Y'"))['available'];
?>
<div class="stat-card"><h3>Total Vehicles</h3><p><?=$total?></p></div>
<div class="stat-card"><h3>Available Now</h3><p><?=$available?></p></div>
</div>

<div class="header-container">
<h1 class="header">Vehicle Management</h1>
<a href="addvehicle.php" class="add">+ Add Vehicle</a>
</div>

<div class="table-container">
<table class="content-table">
<thead>
<tr>
<th>ID</th><th>Name</th><th>Type</th><th>Fuel</th>
<th>Capacity</th><th>Price</th><th>Available</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php
$res=mysqli_query($con,"SELECT * FROM vehicles");
while($row=mysqli_fetch_assoc($res)):
$avail=$row['AVAILABLE']=='Y';
?>
<tr>
<td><?=$row['VEHICLE_ID']?></td>
<td><?=$row['VEHICLE_NAME']?></td>
<td><?=$row['VEHICLE_TYPE']?></td>
<td><?=$row['FUEL_TYPE']?></td>
<td><?=$row['CAPACITY']?></td>
<td>Rs <?=$row['PRICE']?>/day</td>
<td style="color:<?=$avail?'#28a745':'#dc3545'?>;font-weight:600">
<?=$avail?'YES':'NO'?>
</td>
<td>
<a class="button" href="#" onclick="confirmDelete(<?=$row['VEHICLE_ID']?>);return false;">DELETE</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</main>

 <footer>
        <p>&copy; 2025 VeloRent. All Rights Reserved.</p>
         <div class="socials">
        <a href="https://www.facebook.com/thomasbhattrai " target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
        <a href="https://x.com/ " target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
        <a href="https://www.instagram.com/swostimakaju/ " target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
    </div>
    </footer>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
