<?php
if (isset($_POST['addvehicle'])) {
    require_once('connection.php');

    echo "<pre>";
    print_r($_FILES['image']);
    echo "</pre>";

    $img_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];
    $error = $_FILES['image']['error'];

    if ($error === 0) {
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);

        $allowed_exs = array("jpg", "jpeg", "png", "webp", "svg");
        if (in_array($img_ex_lc, $allowed_exs)) {
            // Generate a unique image name
            $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
            $img_upload_path = 'images/' . $new_img_name;
            move_uploaded_file($tmp_name, $img_upload_path);

            // Retrieve form data
            $vehiclename = mysqli_real_escape_string($con, $_POST['vehiclename']);
            $vehicletype = mysqli_real_escape_string($con, $_POST['vehicletype']); // Added VEHICLE_TYPE
            $ftype = mysqli_real_escape_string($con, $_POST['ftype']); // Added FUEL_TYPE
            $capacity = mysqli_real_escape_string($con, $_POST['capacity']);
            $price = mysqli_real_escape_string($con, $_POST['price']);
            $available = "Y";

            // Insert into the database
            $query = "INSERT INTO vehicles (VEHICLE_NAME, VEHICLE_TYPE, FUEL_TYPE, CAPACITY, PRICE, VEHICLE_IMG, AVAILABLE) 
                      VALUES ('$vehiclename', '$vehicletype', '$ftype', '$capacity', '$price', '$new_img_name', '$available')";

            $res = mysqli_query($con, $query);

            if ($res) {
                echo '<script src="main.js"></script>';
                echo '<script>showDialog("New VEHICLE Added Successfully!!", function() { window.location.href = "adminvehicle.php"; });</script>';
            } else {
                echo '<script>showDialog("Database Error: ' . mysqli_error($con) . '");</script>';
            }
        } else {
            echo '<script>showDialog("Invalid image format. Only JPG, JPEG, PNG, WEBP, and SVG are allowed.", function() { window.location.href = "addvehicle.php"; });</script>';
        }
    } else {
        echo '<script>showDialog("An unknown error occurred while uploading the image.", function() { window.location.href = "addvehicle.php"; });</script>';
    }
} else {
    echo "false";
}
?>
