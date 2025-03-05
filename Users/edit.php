<?php
session_start(); // Start the session

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to update a house.";
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

$house_id = $_GET['house_id'];  // Get house ID from the URL

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
$rent = $_POST['rent'];

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Update location
$query = "UPDATE locations SET address='$address', city='$city', latitude='$latitude', longitude='$longitude' WHERE locations.id = (SELECT houses.location_id FROM houses WHERE houses.id = '$house_id')";
if ($conn->query($query) === TRUE) {
    // Update house data
    $sql = "UPDATE houses SET name='$name', description='$description', available='$available', bedrooms='$bedrooms', bathrooms='$bathrooms', service_charge='$service_charge', garage='$garage', floors='$floors', restrictions='$restrictions', rent='$rent' WHERE houses.id='$house_id' AND user_id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        
        // Handle main image update if a new image is uploaded
        if (isset($_FILES["main_image"]) && $_FILES["main_image"]["error"] == 0) {
            $main_image = $target_dir . basename($_FILES["main_image"]["name"]);
            
            // Check if it's a valid image
            if (getimagesize($_FILES["main_image"]["tmp_name"])) {
                if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $main_image)) {
                    // Delete old image if exists
                    $query = "SELECT image_path FROM house_images WHERE post_id = '$house_id' AND type = 'main'";
                    $result = $conn->query($query);
                    $row = $result->fetch_assoc();
                    if ($row) {
                        unlink($row['image_path']); // Remove the old main image
                    }

                    // Insert the new main image
                    $query = "UPDATE house_images SET image_path = '$main_image' WHERE post_id = '$house_id' AND type = 'main'";
                    if ($conn->query($query) === TRUE) {
                        echo "Main image updated successfully!";
                    } else {
                        echo "Error updating main image: " . $conn->error;
                    }
                } else {
                    echo "Failed to upload the main image.";
                }
            } else {
                echo "The file is not a valid image.";
            }
        }

        // Handle small images
        if (isset($_FILES['small_image_1']['name'][0]) && $_FILES['small_image_1']['name'][0] != '') {
            $files = $_FILES['small_image_1'];
            $numFiles = count($files['name']);
            for ($i = 0; $i < $numFiles; $i++) {
                $imagePath = $target_dir . basename($files['name'][$i]);
                if (move_uploaded_file($files['tmp_name'][$i], $imagePath)) {
                    $query = "INSERT INTO house_images (post_id, image_path, type) VALUES ('$house_id', '$imagePath', 'small')";
                    $conn->query($query);
                }
            }
        }

        // Update features
        if (!empty($features)) {
            // Delete old features and insert new ones
            $query = "DELETE FROM house_features WHERE post_id = '$house_id'";
            $conn->query($query);

            $query = "INSERT INTO house_features (post_id, feature_name, gas, wifi, cctv) VALUES ('$house_id', '$features', '$gas', '$wifi', '$cctv')";
            $conn->query($query);
        }

        // Update essentials
        if (!empty($essentials)) {
            // Delete old essentials and insert new ones
            $query = "DELETE FROM house_essentials WHERE post_id = '$house_id'";
            $conn->query($query);

            $query = "INSERT INTO house_essentials (post_id, essential_name) VALUES ('$house_id', '$essentials')";
            $conn->query($query);
        }

        // Redirect on success
        header("Location: post.php?id=$house_id");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error updating location: " . $conn->error;
}

$conn->close();
?>
