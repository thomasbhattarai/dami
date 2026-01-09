<?php

require_once('connection.php');
$email=$_GET['id'];

$sql="DELETE from users where EMAIL='$email'";
$result=mysqli_query($con,$sql);

echo '<script src="main.js"></script>';
echo '<script>showDialog("USER DELETED SUCCESFULLY", function() { window.location.href = "adminusers.php"; });</script>';

?>