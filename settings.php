<?php
require_once './connection.php';   // Adjust path if necessary
session_start();

// 1. Check if Admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php"); 
    exit();
}

$msg = "";
$error = "";

/* ---------------- UPDATE HANDLER ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Site Meta
    $siteName    = mysqli_real_escape_string($con, $_POST['site_name']    ?? '');
    $logoText    = mysqli_real_escape_string($con, $_POST['logo_text']    ?? '');
    $contactMail = mysqli_real_escape_string($con, $_POST['contact_mail'] ?? '');
    $contactPhone= mysqli_real_escape_string($con, $_POST['contact_phone'] ?? '');
    $currency    = mysqli_real_escape_string($con, $_POST['currency']     ?? 'Rs');

    // 2. Business Rules
    $minHours   = intval($_POST['min_hours']   ?? 1);
    $taxPercent = floatval($_POST['tax']       ?? 0);
    $lateFee    = floatval($_POST['late_fee']  ?? 0);

    // 3. Passwords
    $newPass    = $_POST['new_password'] ?? '';
    $confirm    = $_POST['confirm_pass'] ?? '';

    // Execute Updates for Site Meta
    mysqli_query($con, "UPDATE settings SET val='$siteName'     WHERE k='site_name'");
    mysqli_query($con, "UPDATE settings SET val='$logoText'     WHERE k='logo_text'");
    mysqli_query($con, "UPDATE settings SET val='$contactMail'  WHERE k='contact_mail'");
    mysqli_query($con, "UPDATE settings SET val='$contactPhone' WHERE k='contact_phone'");
    mysqli_query($con, "UPDATE settings SET val='$currency'     WHERE k='currency'");

    // Execute Updates for Business Rules
    mysqli_query($con, "UPDATE settings SET val='$minHours'     WHERE k='min_hours'");
    mysqli_query($con, "UPDATE settings SET val='$taxPercent'   WHERE k='tax_percent'");
    mysqli_query($con, "UPDATE settings SET val='$lateFee'      WHERE k='late_fee'");

    $msg = "Settings saved successfully.";

    // Handle Password Update
    if (!empty($newPass)) {
        if ($newPass === $confirm) {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $aid  = (int)$_SESSION['admin_id'];
            mysqli_query($con, "UPDATE admin SET password='$hash' WHERE admin_id=$aid");
            $msg = "Settings and password updated successfully.";
        } else {
            $error = "Passwords do not match. Settings saved, but password remains unchanged.";
        }
    }

    // Redirect to avoid form resubmission on refresh
    if ($error) {
        header("Location: settings.php?err=" . urlencode($error));
    } else {
        header("Location: settings.php?msg=" . urlencode($msg));
    }
    exit();
}

/* ---------------- READ CURRENT VALUES ---------------- */
$sets = [];
$q = mysqli_query($con, "SELECT k, val FROM settings");
while ($r = mysqli_fetch_assoc($q)) { 
    $sets[$r['k']] = $r['val']; 
}

// Function to safely output settings
function s($key, $sets) { 
    return htmlspecialchars($sets[$key] ?? ''); 
}

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - VeloRent Admin</title>
    <style>
        :root{
            --primary:#667eea; --secondary:#764ba2; --danger:#dc3545; --success:#28a745;
            --bg:#f8f9fa; --card:#ffffff; --border:#e0e6ed; --text:#2c3e50; --muted:#6c757d;
        }
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Source Sans Pro',sans-serif}
        body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column}
        
        /* Navbar */
        .navbar{position:fixed;top:0;left:0;right:0;height:56px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1rem;z-index:1030}
        .navbar img{height:40px}.navbar .logo{font-size:1.1rem;font-weight:600;margin-left:10px}
        
        /* Sidebar */
        .sidebar{position:fixed;top:56px;left:0;bottom:0;width:200px;background:#fff;border-right:1px solid var(--border);padding:1rem 0}
        .sidebar ul{list-style:none}
        .sidebar a{display:flex;align-items:center;padding:.65rem 1rem;color:var(--muted);text-decoration:none;border-radius:.3rem;transition:.2s;margin: 0 .5rem}
        .sidebar a:hover{background:rgba(102,126,234,.12);color:var(--primary)}
        .sidebar a.active{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
        .sidebar a.logout{background:var(--danger);color:#fff; margin-top: 20px}
        .sidebar a.logout:hover{background:#c82333}

        /* Main Content */
        .main-content{flex:1;margin-left:200px;margin-top:56px;padding:2rem}
        .form-box{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:35px;max-width:700px;margin:auto;box-shadow:0 10px 30px rgba(102,126,234,.15)}
        .form-box h2{margin-bottom:25px;color:var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px;}
        .form-box label{display:block;margin-bottom:6px;font-weight:600; font-size: 0.9rem; color: var(--muted)}
        .form-box input, .form-box select{width:100%;padding:12px;margin-bottom:18px;border:1px solid var(--border);border-radius:8px; font-size: 1rem}
        .form-box input:focus{border-color:var(--primary);outline:none; box-shadow: 0 0 0 3px rgba(102,126,234,0.1)}
        
        .btn-row{display:flex;gap:12px; margin-top: 20px}
        .btn{flex: 1; padding:14px;border:none;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer; text-align: center; text-decoration: none;}
        .btn-primary{background:linear-gradient(135deg,var(--primary),var(--secondary));color:#fff}
        .btn-secondary{background:#e9ecef;color:var(--text)}
        
        .alert{padding:12px 16px;border-radius:8px;margin-bottom:20px}
        .alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
        .alert-danger{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}

        footer{margin-left:200px;background:#fff;border-top:1px solid var(--border);padding:1.5rem;text-align:center}
        @media(max-width:768px){.sidebar{display:none}.main-content,footer{margin-left:0}}
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
        <li><a href="settings.php" class="active">⚙️ Settings</a></li>
        <li><a href="logout.php" class="logout">⎗ Logout</a></li>
    </ul>
</aside>

<main class="main-content">
    <div class="form-box">
        <h2>System Settings</h2>
        
        <?php if($msg): ?>
            <div class="alert alert-success"><?=htmlspecialchars($msg)?></div>
        <?php endif; ?>
        
        <?php if($err): ?>
            <div class="alert alert-danger"><?=htmlspecialchars($err)?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <label>Site / Brand name</label>
            <input type="text" name="site_name" value="<?=s('site_name', $sets)?>" required>

            <label>Logo text (top-left)</label>
            <input type="text" name="logo_text" value="<?=s('logo_text', $sets)?>" required>

            <label>Contact e-mail</label>
            <input type="email" name="contact_mail" value="<?=s('contact_mail', $sets)?>" required>

            <label>Contact phone</label>
            <input type="text" name="contact_phone" value="<?=s('contact_phone', $sets)?>">

            <label>Currency symbol</label>
            <input type="text" name="currency" value="<?=s('currency', $sets)?>" placeholder="Rs / $ / €" required>

            <h3 style="margin: 20px 0 15px; color: var(--secondary)">Business Logic</h3>
            
            <label>Minimum rental (hours)</label>
            <input type="number" min="1" name="min_hours" value="<?=s('min_hours', $sets) ?: 1?>" required>

            <label>Tax %</label>
            <input type="number" step="0.01" min="0" name="tax" value="<?=s('tax_percent', $sets)?>" placeholder="0">

            <label>Late-return fee (per day)</label>
            <input type="number" step="0.01" min="0" name="late_fee" value="<?=s('late_fee', $sets)?>" placeholder="0">

            <hr style="margin:25px 0; border: 0; border-top: 1px solid var(--border);">
            <h3 style="margin-bottom:15px;color:var(--primary)">Security</h3>
            
            <label>New password</label>
            <input type="password" name="new_password" placeholder="Leave blank to keep current">

            <label>Confirm new password</label>
            <input type="password" name="confirm_pass" placeholder="Leave blank to keep current">

            <div class="btn-row">
                <button type="submit" class="btn btn-primary">Save All Changes</button>
                <a href="adminoverview.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
</main>

<footer>
  <p>&copy; 2025 VeloRent. All Rights Reserved.</p>
</footer>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>