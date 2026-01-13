
<?php
// Read values from App Settings (you already added these)
$host = getenv('DB_HOST');         // vehicleproject.mysql.database.azure.com
$user = getenv('DB_USER');         // adminuser@vehicleproject
$pass = getenv('DB_PASS');         // your password
$dbname = getenv('DB_NAME');       // vehicleproject

// Azure MySQL usually enforces SSL. Use mysqli with SSL flags.
$mysqli = mysqli_init();

// Optional: Set timeouts to avoid hanging
mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// Initiate secure connection (cert paths not required for Azure managed CA)
$port = 3306;
$flags = MYSQLI_CLIENT_SSL;

if (!mysqli_real_connect($mysqli, $host, $user, $pass, $dbname, $port, null, $flags)) {
    http_response_code(500);
    die("Connection failed: (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
}

echo "✅ Connected to MySQL successfully as {$user} to DB {$dbname}";
mysqli_close($mysqli);
