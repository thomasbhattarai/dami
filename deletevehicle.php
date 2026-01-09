<?php

require_once('connection.php');
$vehicleid=$_GET['id'];
$sql="DELETE from vehicles where VEHICLE_ID=$vehicleid";
$result=mysqli_query($con,$sql);

echo '<script src="main.js"></script>';
echo '<script>showDialog("VEHICLE DELETED SUCCESFULLY", function() { window.location.href = "adminvehicle.php"; });</script>';



?>