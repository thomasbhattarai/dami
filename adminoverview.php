<?php
require_once 'connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

/* ----------  OVERVIEW COUNTS  ---------- */
$totalVeh   = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS c FROM vehicles"))['c'];
$availVeh   = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS c FROM vehicles WHERE AVAILABLE='Y'"))['c'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS c FROM users"))['c'];
$totalBook  = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(*) AS c FROM booking"))['c'];

/* ----------  1.  LAST-7-DAYS REVENUE (PAID) ---------- */
$rev7 = mysqli_query($con,
 "SELECT DATE(BOOK_DATE) AS d, SUM(PRICE) AS amt
  FROM booking
  WHERE BOOK_STATUS='PAID'
    AND BOOK_DATE >= CURDATE() - INTERVAL 6 DAY
  GROUP BY d
  ORDER BY d");
$revMap = [];                       // date => amount
while ($r = mysqli_fetch_assoc($rev7)) {
    $revMap[$r['d']] = $r['amt'];
}
/* build a clean 7-day array (today → 6 days ago) */
$revLabels = [];
$revData   = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $revLabels[] = date('M d', strtotime($date));
    $revData[]   = intval($revMap[$date] ?? 0);
}

/* ----------  2.  TOTAL REVENUE (all time, PAID)  ---------- */
$revAll = mysqli_fetch_assoc(mysqli_query($con,
  "SELECT COALESCE(SUM(PRICE),0) AS amt FROM booking WHERE BOOK_STATUS='PAID'"
));
$revenue = (int)($revAll['amt'] ?? 0);     // used in stat-card

/* ----------  3.  TRAFFIC SHARE  ---------- */
$traffic = mysqli_query($con,
 "SELECT v.VEHICLE_TYPE, COUNT(*) AS rides
  FROM booking b
  JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID
  GROUP BY v.VEHICLE_TYPE
  ORDER BY rides DESC");

/* ----------  4.  UNIQUE CUSTOMERS  ---------- */
$customers = mysqli_fetch_assoc(
    mysqli_query($con,"SELECT COUNT(DISTINCT EMAIL) AS c FROM booking")
)['c'] ?: 0;

/* ----------  RECENT BOOKINGS (last 5)  ---------- */
$recent = mysqli_query($con,
 "SELECT b.BOOK_ID, u.FNAME, u.LNAME, v.VEHICLE_NAME, b.BOOK_DATE, b.BOOK_STATUS
  FROM booking b
  JOIN users u ON b.EMAIL = u.EMAIL
  JOIN vehicles v ON b.VEHICLE_ID = v.VEHICLE_ID
  ORDER BY b.BOOK_ID DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Overview - VeloRent Admin</title>
<style>
/* ===== SAME CSS YOU ALREADY HAVE ===== */
:root{
    --primary:#667eea; --secondary:#764ba2; --danger:#dc3545; --success:#28a745;
    --warning:#ffc107; --bg:#f8f9fa; --card:#ffffff; --border:#e0e6ed;
    --text:#2c3e50; --muted:#6c757d;
}
*{margin:0;padding:0;box-sizing:border-box;font-family:'Source Sans Pro',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif}
body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column}
.navbar{position:fixed;top:0;left:0;right:0;height:56px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1rem;z-index:1030}
.navbar img{height:40px}.navbar .logo{font-size:1.1rem;font-weight:600}
.sidebar{position:fixed;top:56px;left:0;bottom:0;width:200px;background:#fff;border-right:1px solid var(--border);padding:1rem 0}
.sidebar ul{list-style:none}.sidebar li{margin:.25rem 0}
.sidebar a{display:flex;align-items:center;padding:.65rem 1rem;color:var(--muted);text-decoration:none;border-radius:.3rem;transition:.2s}
.sidebar a:hover{background:rgba(102,126,234,.12);color:var(--primary)}
.sidebar a.logout{background:var(--danger);color:#fff}.sidebar a.logout:hover{background:#c82333}
.main-content{flex:1;margin-left:200px;margin-top:56px;padding:1.5rem}
.admin-stats{display:flex;justify-content:center;gap:30px;margin-bottom:40px;flex-wrap:wrap}
.stat-card{background:var(--card);padding:25px;border-radius:12px;min-width:200px;text-align:center;border:2px solid var(--border);box-shadow:0 10px 30px rgba(102,126,234,.15)}
.stat-card h3{color:var(--primary);margin-bottom:10px}.stat-card p{font-size:2rem;font-weight:700}
.actions{margin-bottom:30px;text-align:center}.actions a{display:inline-block;background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;padding:10px 22px;border-radius:25px;margin:0 8px;font-weight:600;font-size:.9rem;text-decoration:none}.actions a:hover{opacity:.9}
.charts{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:25px;margin-bottom:40px}
.chart-box{background:var(--card);border:2px solid var(--border);border-radius:12px;padding:20px;text-align:center}
.chart-box h4{margin-bottom:15px;color:var(--primary)}canvas{max-height:180px}
.recent-header{font-size:1.5rem;margin-bottom:15px;font-weight:600}
.table-container{overflow-x:auto;border-radius:12px;border:2px solid var(--border);background:#fff}
.content-table{width:100%;border-collapse:collapse}
.content-table thead{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff;text-transform:uppercase;font-size:.85rem}
.content-table th,.content-table td{padding:.75rem;border-bottom:1px solid var(--border);text-align:center}
.content-table tbody tr:hover{background:rgba(102,126,234,.08)}
footer{margin-left:200px;background:#fff;border-top:1px solid var(--border);padding:1.5rem;text-align:center}
.socials{display:flex;justify-content:center;gap:1rem;margin-top:.75rem}.socials a{color:var(--text);font-size:1.4rem}.socials a:hover{color:var(--primary)}
@media(max-width:768px){.sidebar{position:static;width:100%;border-right:none;border-bottom:1px solid var(--border)}.main-content,footer{margin-left:0}}
</style>
</head>
<body>

<nav class="navbar"><img src="images/icon.png"><span class="logo">VeloRent Admin</span></nav>
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
    <div class="stat-card"><h3>Total Vehicles</h3><p><?=$totalVeh?></p></div>
    <div class="stat-card"><h3>Available Now</h3><p><?=$availVeh?></p></div>
    <div class="stat-card"><h3>Total Users</h3><p><?=$totalUsers?></p></div>
    <div class="stat-card"><h3>Total Bookings</h3><p><?=$totalBook?></p></div>
    <div class="stat-card"><h3>Revenue</h3><p>Rs<?=number_format($revenue)?></p></div>
    <div class="stat-card"><h3>Customers</h3><p><?=$customers?></p></div>
</div>

<div class="actions">
    <a href="addvehicle.php">+ Add Vehicle</a>
    <a href="export.php">Export Report</a>
    <a href="settings.php">Settings</a>
</div>

<!-- =========  REAL CHARTS  ========= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="charts">
  <div class="chart-box"><h4>Revenue Trend</h4><canvas id="revChart"></canvas></div>
  <div class="chart-box"><h4>Traffic Share</h4><canvas id="trafficChart"></canvas></div>
  <div class="chart-box"><h4>Customers</h4><canvas id="custChart"></canvas></div>
</div>

<script>
/* 1) 7-day revenue spark-line */
const revCtx = document.getElementById('revChart');
new Chart(revCtx, {
  type: 'line',
  data: {
    labels: <?=json_encode($revLabels)?>,
    datasets: [{
      label: 'Revenue (Rs)',
      data: <?=json_encode($revData)?>,
      borderColor: '#667eea',
      backgroundColor: 'rgba(102,126,234,.15)',
      tension: .3,
      pointRadius: 3
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

/* 2) traffic-share doughnut */
const trafficCtx = document.getElementById('trafficChart');
new Chart(trafficCtx, {
  type: 'doughnut',
  data: {
    labels: <?php
      $t_labels=[]; $t_data=[];
      if($traffic && mysqli_num_rows($traffic)){
        while($x=mysqli_fetch_assoc($traffic)){ $t_labels[]=$x['VEHICLE_TYPE']; $t_data[]=$x['rides']; }
      }else{ $t_labels=['No data']; $t_data=[1]; }
      echo json_encode($t_labels);
    ?>,
    datasets: [{ data: <?=json_encode($t_data)?>, backgroundColor: ['#667eea','#764ba2','#f093fb','#f5576c','#4facfe'] }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});

/* 3) customers radial */
const custCtx = document.getElementById('custChart');
new Chart(custCtx, {
  type: 'doughnut',
  data: {
    labels: ['Active','Remaining'],
    datasets: [{ data: [<?=$customers?>, Math.max(0,<?=$totalUsers?>-<?=$customers?>)], backgroundColor: ['#28a745','#e0e6ed'] }]
  },
  options: { cutout: '70%', plugins: { legend: { display: false } } }
});
</script>

<h2 class="recent-header">Recent Bookings</h2>
<div class="table-container">
<table class="content-table">
<thead><tr><th>ID</th><th>Customer</th><th>Vehicle</th><th>Date</th><th>Status</th></tr></thead>
<tbody>
<?php if($recent && mysqli_num_rows($recent)>0): while($row=mysqli_fetch_assoc($recent)): ?>
<tr>
  <td><?=$row['BOOK_ID']?></td>
  <td><?=htmlspecialchars(trim($row['FNAME'].' '.$row['LNAME']))?></td>
  <td><?=htmlspecialchars($row['VEHICLE_NAME'])?></td>
  <td><?=$row['BOOK_DATE']?></td>
  <td style="color:<?=$row['BOOK_STATUS']==='APPROVED'?'#28a745':'#ffc107'?>;font-weight:600"><?=$row['BOOK_STATUS']?></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="5">No bookings yet.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</main>

<footer>
  <p>&copy; 2025 VeloRent. All Rights Reserved.</p>
  <div class="socials">
    <a href="https://www.facebook.com/thomasbhattrai" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
    <a href="https://x.com/" target="_blank"><ion-icon name="logo-twitter"></ion-icon></a>
    <a href="https://www.instagram.com/swostimakaju/" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
  </div>
</footer>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>