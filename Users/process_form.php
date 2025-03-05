<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to add a house.";
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "main_home_hunt");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// File upload directories
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Create uploads directory if it doesn't exist
}

// Upload main image
$main_image = $target_dir . basename($_FILES["main_image"]["name"]);
if (!move_uploaded_file($_FILES["main_image"]["tmp_name"], $main_image)) {
    die("Error uploading main image.");
}

// Collect form data
$name = $_POST['name'];
$description = $conn->real_escape_string($_POST['description']);
$available = $_POST['available'];
$bedrooms = $_POST['bedrooms'];
$bathrooms = $_POST['bathrooms'];
$service_charge = $_POST['service_charge'];
$garage = $_POST['garage'];
$floors = $_POST['floors'];
$restrictions = $_POST['restrictions'];
$essentials = $_POST['essential'];
$address = $_POST['address'];
$city = $_POST['city'];
$features = $_POST['feature'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$gas = $_POST['gas'];
$cctv = $_POST['cctv'];
$wifi = $_POST['wifi'];
$rent=$_POST['rent'];
$renter_id=$_POST['renter_id'];
// Get user ID from session
$user_id = $_SESSION['user_id'];

// Insert location first and get location_id
$query = "INSERT INTO locations (address, city, latitude, longitude) VALUES ('$address', '$city', '$latitude', '$longitude')";
if ($conn->query($query) === TRUE) {
    $location_id = $conn->insert_id; // Get the last inserted location ID

    // Insert house data
    $sql = "INSERT INTO houses (name, user_id, location_id, description, available, bedrooms, bathrooms, service_charge, garage, floors, restrictions,rent,renter_id)
            VALUES ('$name', '$user_id', '$location_id', '$description', '$available', '$bedrooms', '$bathrooms', '$service_charge', '$garage', '$floors', '$restrictions','$rent',$renter_id)";

    if ($conn->query($sql) === TRUE) {
        $post_id = $conn->insert_id; // Get the last inserted house ID

        // Insert main image
        $query = "INSERT INTO house_images (post_id, image_path) VALUES ('$post_id', '$main_image')";
        $conn->query($query);

        // Insert small images
        if (isset($_FILES['small_image_1']['name'][0]) && $_FILES['small_image_1']['name'][0] != '') {
            $files = $_FILES['small_image_1'];
            $numFiles = count($files['name']);
            for ($i = 0; $i < $numFiles; $i++) {
                $imagePath = $target_dir . basename($files['name'][$i]);
                if (move_uploaded_file($files['tmp_name'][$i], $imagePath)) {
                    $query = "INSERT INTO house_images (post_id, image_path ,type) VALUES ('$post_id', '$imagePath','small')";
                    $conn->query($query);
                }
            }
        }

        // Insert features
        if (!empty($features)) {
            $query = "INSERT INTO house_features (post_id, feature_name,gas,wifi,cctv) VALUES ('$post_id', '$features','$gas','$wifi','$cctv')";
            $conn->query($query);
        }

        // Insert essentials
        if (!empty($essentials)) {
            $query = "INSERT INTO house_essentials (post_id, essential_name) VALUES ('$post_id', '$essentials')";
            $conn->query($query);
        }

        // Redirect on success
        header("Location: UserProfile.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error inserting location: " . $conn->error;
}

$conn->close();
?>
