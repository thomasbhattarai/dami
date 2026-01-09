<?php
require_once('connection.php');
session_start();

if(!isset($_SESSION['admin_id'])) {
    header("location: adminlogin.php");
    exit();
}

$count_query = "SELECT COUNT(*) as total FROM users";
$count_result = mysqli_query($con, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_users = $count_row['total'];
$active_users = $total_users;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users Management - VeloRent Admin</title>

<style>
/* ===== LIGHT PURPLE THEME ===== */
:root{
    --primary:#667eea;
    --secondary:#764ba2;
    --danger:#dc3545;
    --bg:#f8f9fa;
    --card:#ffffff;
    --border:#e0e6ed;
    --text:#2c3e50;
    --muted:#6c757d;
}

/* Base */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;
}
body{
    background:var(--bg);
    color:var(--text);
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* ===== TOP BAR ===== */
.navbar{
    position:fixed;
    top:0;left:0;right:0;
    height:56px;
    background:#ffffff;
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    padding:0 1rem;
    z-index:1030;
}
.navbar img{height:40px}
.navbar span{color:var(--text)}

/* ===== SIDEBAR ===== */
.sidebar{
    position:fixed;
    top:56px;left:0;bottom:0;
    width:200px;
    background:#ffffff;
    border-right:1px solid var(--border);
    padding:1rem 0;
    z-index:1020;
}
.sidebar ul{list-style:none}
.sidebar li{margin:.25rem 0}
.sidebar a{
    display:flex;
    align-items:center;
    padding:.65rem 1rem;
    color:var(--muted);
    text-decoration:none;
    border-radius:.3rem;
    transition:.2s;
}
.sidebar a:hover{
    background:rgba(102,126,234,.12);
    color:var(--primary);
}
.sidebar a .icon{
    font-size:1.2rem;
    margin-right:.75rem
}
.sidebar a.logout{
    background:var(--danger);
    color:#fff;
}
.sidebar a.logout:hover{
    background:#c82333;
}

/* ===== MAIN CONTENT ===== */
.main-content{
    flex:1;
    margin-left:200px;
    margin-top:56px;
    padding:1.5rem;
    background:var(--bg);
}

/* ===== STATS ===== */
.admin-stats{
    display:flex;
    gap:30px;
    margin-bottom:40px;
    flex-wrap:wrap;
}
.stat-card{
    background:var(--card);
    padding:25px;
    border-radius:12px;
    min-width:200px;
    text-align:center;
    border:2px solid var(--border);
    box-shadow:0 10px 30px rgba(102,126,234,.15);
}
.stat-card h3{
    color:var(--primary);
    margin-bottom:10px;
}
.stat-card p{
    font-size:2rem;
    font-weight:700;
}

/* ===== TABLE ===== */
.table-container{
    overflow-x:auto;
    border-radius:12px;
    border:2px solid var(--border);
    background:#ffffff;
}
.content-table{
    width:100%;
    border-collapse:collapse;
}
.content-table thead{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
}
.content-table th,
.content-table td{
    padding:.75rem;
    border-bottom:1px solid var(--border);
}
.content-table tbody tr:hover{
    background:rgba(102,126,234,.08);
}
.content-table a.button{
    background:var(--danger);
    color:#fff;
    padding:.3rem .6rem;
    font-size:.8rem;
    border-radius:.3rem;
    text-decoration:none;
}
.content-table a.button:hover{
    background:#c82333;
}

/* ===== FOOTER ===== */
footer{
    margin-left:200px;
    background:#ffffff;
    border-top:1px solid var(--border);
    padding:1.5rem;
    text-align:center;
    color:var(--text);
}
.socials{
    display:flex;
    justify-content:center;
    gap:1rem;
    margin-top:.75rem;
}
.socials a{
    color:var(--text);
    font-size:1.4rem;
}
.socials a:hover{
    color:var(--primary);
}

/* ===== MOBILE ===== */
@media(max-width:768px){
    .sidebar{
        position:static;
        width:100%;
        border-right:none;
        border-bottom:1px solid var(--border);
    }
    .main-content,
    footer{
        margin-left:0;
    }
}
</style>
</head>

<body>

<!-- TOP BAR -->
<nav class="navbar">
    <div style="display:flex;align-items:center;gap:10px;">
        <img src="images/icon.png">
        <span style="font-weight:600;">VeloRent Admin</span>
    </div>
</nav>

<!-- SIDEBAR -->
<aside class="sidebar">
    <ul>
        <li><a href="adminoverview.php">📊 Overview</a></li>
        <li><a href="adminvehicle.php">🚗 Vehicles</a></li>
        <li><a href="adminusers.php">👤 Users</a></li>
        <li><a href="adminbook.php">📑 Bookings</a></li>
        <li><a href="index.php" class="logout">⎗ Logout</a></li>
    </ul>
</aside>

<!-- MAIN -->
<main class="main-content">
    <div class="admin-stats">
        <div class="stat-card"><h3>Total Users</h3><p><?php echo $total_users; ?></p></div>
        <div class="stat-card"><h3>Active Users</h3><p><?php echo $active_users; ?></p></div>
    </div>

    <h1>Users Management</h1>

    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>NAME</th>
                    <th>EMAIL</th>
                    <th>LICENSE</th>
                    <th>PHONE</th>
                    <th>GENDER</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $query = "SELECT * FROM users";
            $queryy = mysqli_query($con, $query);
            while($res = mysqli_fetch_array($queryy)):
            ?>
                <tr>
                    <td><?=htmlspecialchars($res['FNAME']." ".$res['LNAME'])?></td>
                    <td><?=htmlspecialchars($res['EMAIL'])?></td>
                    <td><?=htmlspecialchars($res['LIC_NUM'])?></td>
                    <td><?=htmlspecialchars($res['PHONE_NUMBER'])?></td>
                    <td><?=htmlspecialchars($res['GENDER'])?></td>
                    <td>
                        <a href="deleteuser.php?id=<?=urlencode($res['EMAIL'])?>" class="button" onclick="return confirm('Delete this user?')">DELETE</a>
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
