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
<title>Booking Management - VeloRent Admin</title>

<style>
:root{
    --primary:#667eea;
    --secondary:#764ba2;
    --success:#28a745;
    --info:#007bff;
    --danger:#dc3545;
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
.navbar .logo{font-size:1.1rem;font-weight:600;margin-left:10px}

/* SIDEBAR */
.sidebar{
    position:fixed;top:56px;left:0;bottom:0;width:200px;
    background:#ffffff;border-right:1px solid var(--border);
    padding:1rem 0;z-index:1020;overflow-y:auto;
}
.sidebar ul{list-style:none}
.sidebar li{margin:.25rem 0}
.sidebar a{
    display:flex;align-items:center;
    padding:.65rem 1rem;color:var(--muted);
    text-decoration:none;border-radius:.3rem;
    transition:.2s;
}
.sidebar a:hover{background:rgba(102,126,234,.12);color:var(--primary);}
.sidebar a.logout{background:var(--danger);color:#fff}
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

/* TABLE */
.header{font-size:1.75rem;margin-bottom:1.5rem}
.table-container{
    overflow-x:auto;border-radius:12px;
    border:2px solid var(--border);background:#fff;
}
.content-table{
    width:100%;border-collapse:collapse;min-width:1200px;
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
.button{
    display:inline-block;padding:8px 16px;
    border-radius:6px;font-size:.85rem;
    font-weight:600;text-decoration:none;color:#fff;
}
.approve-btn{background:linear-gradient(45deg,#28a745,#1e7e34)}
.return-btn{background:linear-gradient(45deg,#007bff,#0056b3)}
.cancel-btn{background:linear-gradient(45deg,#dc3545,#bd2130)}

/* FOOTER */
footer{
    margin-left:200px;background:#fff;
    border-top:1px solid var(--border);
    padding:1.5rem;text-align:center;
}
.socials{display:flex;justify-content:center;gap:1rem;margin-top:.75rem}
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
    <span class="logo">VeloRent Admin</span>
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
$total=mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) total FROM booking"))['total'];
$pending=mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) pending FROM booking WHERE BOOK_STATUS='PENDING'"))['pending'];
?>
<div class="stat-card"><h3>Total Bookings</h3><p><?=$total?></p></div>
<div class="stat-card"><h3>Pending Approval</h3><p><?=$pending?></p></div>
</div>

<h1 class="header">Booking Management</h1>

<div class="table-container">
<table class="content-table">
<thead>
<tr>
<th>User</th><th>Vehicle</th><th>Email</th><th>Place</th>
<th>Book Date</th><th>Duration</th><th>Phone</th>
<th>Destination</th><th>Return Date</th><th>Status</th>
<th>Approve</th><th>Returned</th><th>Cancel</th>
</tr>
</thead>
<tbody>
<?php
$res=mysqli_query($con,
"SELECT b.*,v.VEHICLE_NAME,u.FNAME,u.LNAME
FROM booking b
LEFT JOIN vehicles v ON b.VEHICLE_ID=v.VEHICLE_ID
LEFT JOIN users u ON b.EMAIL=u.EMAIL
WHERE b.BOOK_STATUS NOT IN ('RETURNED', 'Canceled')
ORDER BY b.BOOK_ID DESC");

while($row=mysqli_fetch_assoc($res)):
$statusColor=$row['BOOK_STATUS']=='APPROVED'?'#28a745':'#ffc107';
?>
<tr>
<td><?=htmlspecialchars(trim($row['FNAME'].' '.$row['LNAME'])?:'N/A')?></td>
<td><?=htmlspecialchars($row['VEHICLE_NAME']?:'N/A')?></td>
<td><?=htmlspecialchars($row['EMAIL'])?></td>
<td><?=htmlspecialchars($row['BOOK_PLACE'])?></td>
<td><?=$row['BOOK_DATE']?></td>
<td><?=$row['DURATION']?> days</td>
<td><?=$row['PHONE_NUMBER']?></td>
<td><?=$row['DESTINATION']?></td>
<td><?=$row['RETURN_DATE']?></td>
<td style="color:<?=$statusColor?>;font-weight:600"><?=$row['BOOK_STATUS']?></td>
<td>
<?php if($row['BOOK_STATUS']!=='APPROVED'): ?>
    <a class="button approve-btn" href="approve.php?id=<?=$row['BOOK_ID']?>">APPROVE</a>
<?php else: ?>
    <!-- Approved: hide button -->
<?php endif; ?>
</td>
<td><a class="button return-btn" href="adminreturn.php?id=<?=$row['VEHICLE_ID']?>&bookid=<?=$row['BOOK_ID']?>">RETURNED</a></td>
<td><a class="button cancel-btn" href="admincancelbooking.php?id=<?=$row['BOOK_ID']?>" onclick="return confirm('Cancel this booking?')">CANCEL</a></td>
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
