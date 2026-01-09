<?php
require_once 'connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

/* ----------  IF DATES POSTED → STREAM CSV  ---------- */
if (isset($_POST['start']) && isset($_POST['end'])) {
    $start = $_POST['start'];
    $end   = $_POST['end'];

    $stmt = $con->prepare(
        "SELECT b.BOOK_ID, b.BOOK_DATE, b.BOOK_STATUS, b.DURATION, b.PRICE,
                u.FNAME, u.LNAME, u.EMAIL, u.PHONE_NUMBER,
                v.VEHICLE_NAME, v.VEHICLE_TYPE
         FROM booking b
         JOIN users u ON b.EMAIL = u.EMAIL
         JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID
         WHERE b.BOOK_DATE BETWEEN ? AND ?
         ORDER BY b.BOOK_DATE DESC");
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $res = $stmt->get_result();

    /* ----------  BUILD CSV  ---------- */
    $fileName = "VeloRent_Report_{$start}_to_{$end}.csv";
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename={$fileName}");

    $out = fopen('php://output', 'w');
    fputcsv($out, ['Booking ID', 'Date', 'Customer', 'Email', 'Phone', 'Vehicle', 'Type', 'Duration (days)', 'Price (Rs)', 'Status']);

    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $row['BOOK_ID'],
            $row['BOOK_DATE'],
            trim($row['FNAME'].' '.$row['LNAME']),
            $row['EMAIL'],
            $row['PHONE_NUMBER'],
            $row['VEHICLE_NAME'],
            $row['VEHICLE_TYPE'],
            $row['DURATION'],
            $row['PRICE'],
            $row['BOOK_STATUS']
        ]);
    }
    fclose($out);
    exit();   // stop HTML
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Export Report - VeloRent Admin</title>
    <!-- =====  SAME DARK THEME  ===== -->
  <style>
:root{
    --primary: #667eea;
    --secondary: #764ba2;
    --danger: #dc3545;
    --bg: #f8f9fa;
    --card: #ffffff;
    --border: #e0e6ed;
    --text-dark: #2c3e50;
    --text-light: #6c757d;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:var(--bg);
    color:var(--text-dark);
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* ===== TOP BAR ===== */
.navbar{
    position:fixed;
    top:0; left:0; right:0;
    height:60px;
    background:#fff;
    border-bottom:1px solid var(--border);
    display:flex;
    align-items:center;
    padding:0 1.5rem;
    z-index:1000;
}

.navbar img{height:42px}
.navbar .logo{
    margin-left:10px;
    font-size:1.2rem;
    font-weight:600;
    color:var(--primary);
}

/* ===== SIDEBAR ===== */
.sidebar{
    position:fixed;
    top:60px;
    left:0;
    bottom:0;
    width:220px;
    background:#fff;
    border-right:1px solid var(--border);
    padding:1rem 0;
}

.sidebar ul{list-style:none}
.sidebar li{margin:.3rem 0}

.sidebar a{
    display:flex;
    align-items:center;
    padding:.7rem 1.2rem;
    color:var(--text-light);
    text-decoration:none;
    border-radius:6px;
    transition:.25s;
}

.sidebar a:hover,
.sidebar a.active{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
}

.sidebar a .icon{font-size:1.2rem;margin-right:.8rem}
.sidebar a.logout{
    background:var(--danger);
    color:#fff;
    margin-top:10px;
}
.sidebar a.logout:hover{opacity:.9}

/* ===== MAIN ===== */
.main-content{
    flex:1;
    margin-left:220px;
    margin-top:60px;
    padding:2rem;
}

/* ===== FORM ===== */
.form-box{
    background:var(--card);
    border:1px solid var(--border);
    border-radius:14px;
    padding:35px;
    max-width:500px;
    margin:auto;
    box-shadow:0 10px 30px rgba(102,126,234,.15);
}

.form-box h2{
    margin-bottom:25px;
    color:var(--primary);
}

.form-box label{
    display:block;
    margin-bottom:6px;
    font-weight:500;
}

.form-box input[type=date]{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border:1px solid var(--border);
    border-radius:8px;
}

.form-box input:focus{
    border-color:var(--primary);
    outline:none;
}

.form-box button{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    border:none;
    border-radius:25px;
    font-size:1rem;
    font-weight:600;
    cursor:pointer;
}

.form-box button:hover{
    box-shadow:0 10px 20px rgba(102,126,234,.4);
}

/* ===== FOOTER ===== */
footer{
    margin-left:220px;
    background:#fff;
    border-top:1px solid var(--border);
    padding:1.5rem;
    text-align:center;
}

.socials{
    display:flex;
    justify-content:center;
    gap:1rem;
    margin-top:.75rem;
}

.socials a{
    color:var(--primary);
    font-size:1.4rem;
}

.socials a:hover{color:var(--secondary)}

@media(max-width:768px){
    .sidebar{position:static;width:100%;height:auto}
    .main-content,footer{margin-left:0}
}
</style>

</head>
<body>

<!-- =========  TOP BAR  ========= -->
<nav class="navbar">
    <img src="images/icon.png" alt="VeloRent Logo">
    <span class="logo">VeloRent Admin</span>
</nav>

<!-- =========  200 PX TEXT SIDEBAR  ========= -->
<aside class="sidebar">
    <ul>
        <li><a href="adminoverview.php"><span class="icon">📊</span><span class="text">Overview</span></a></li>
        <li><a href="adminvehicle.php"><span class="icon">🚗</span><span class="text">Vehicles</span></a></li>
        <li><a href="adminusers.php"><span class="icon">👤</span><span class="text">Users</span></a></li>
        <li><a href="adminbook.php"><span class="icon">📑</span><span class="text">Bookings</span></a></li>
        <li><a href="export.php" class="active"><span class="icon">⬇️</span><span class="text">Export</span></a></li>
        <li><a href="index.php" class="logout"><span class="icon">⎗</span><span class="text">Logout</span></a></li>
    </ul>
</aside>

<!-- =========  MAIN CONTENT  ========= -->
<main class="main-content">
    <div class="form-box">
        <h2>Export Booking Report</h2>
        <form method="post" action="">
            <label>Start Date</label>
            <input type="date" name="start" required>
            <label>End Date</label>
            <input type="date" name="end" required>
           <div style="display:flex; gap:12px;">
    <button type="submit">Download CSV</button>
    <a href="adminoverview.php" style="flex:1;">
        <button type="button" style="width:100%; background:#6c757d;">Cancel</button>
    </a>
</div>   </form>
    </div>
</main>

<!-- =========  FOOTER  ========= -->
<footer>
    <p>&copy; 2024 VeloRent. All Rights Reserved.</p>
    <div class="socials">
        <a href="https://www.facebook.com/thomasbhattrai " target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
        <a href="https://x.com/thomashbhattarai " target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
        <a href="https://www.instagram.com/swostimakaju/ " target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
    </div>
</footer>

<!-- Ion icons -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js "></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js "></script>
</body>
</html>