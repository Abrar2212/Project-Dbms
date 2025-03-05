<?php
    include('searchAndnav.php');  // Mahbub 

    if (session_status() === PHP_SESSION_NONE) {
        
        session_start();
      
    } 
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to edit a house.";
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "main_home_hunt");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $house_id = $_GET['house_id']; 

    // Fetch existing house data
    $query = "SELECT * FROM users
        JOIN houses ON users.id = houses.user_id
        JOIN house_images ON houses.id = house_images.post_id
        JOIN house_essentials ON house_essentials.post_id = houses.id
        JOIN house_features ON house_features.post_id = houses.id
        JOIN user_photos ON user_photos.user_id = users.id
        JOIN locations ON locations.id = houses.location_id
        WHERE houses.id = '$house_id' AND users.id = '{$_SESSION['user_id']}'";
    $result = $conn->query($query);
    if ($result->num_rows == 0) {
        echo "House not found or you don't have permission to edit this house.";
        exit();
    }
    $house = $result->fetch_assoc();

    // Fetch location data
    $location_query = "SELECT * FROM locations WHERE id = '{$house['location_id']}'";
    $location_result = $conn->query($location_query);
    $location = $location_result->fetch_assoc();

    ?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post edit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="home.css">
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <script src="map.js"></script>
</head>
<body>
<body>
    
    <div class="container mt-5">
        <h1 class="text-center mb-4">Update House</h1>
        <form action="edit.php?house_id=<?php echo $house_id; ?>" method="POST" enctype="multipart/form-data">
            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">House Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $house['name']; ?>" required>
            </div>
            <!-- Available FROM -->
            <div class="mb-3">
                <label for="available" class="form-label">Available From</label>
                <input type="text" class="form-control" id="available" name="available" value="<?php echo $house['available']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $house['description']; ?></textarea>
            </div>

            <!-- Main Image -->
            <div class="form-group">
        <label for="main_image">Main Image</label>
        <input type="file" class="form-control" id="main_image" name="main_image">
    </div>

    <div class="form-group">
        <label for="small_image_1">Small Images</label>
        <input type="file" class="form-control" id="small_image_1" name="small_image_1[]" multiple>
    </div>


            <!-- Bedrooms -->
            <div class="mb-3">
                <label for="bedrooms" class="form-label">Bedrooms</label>
                <input type="number" class="form-control" id="bedrooms" name="bedrooms" value="<?php echo $house['bedrooms']; ?>" required>
            </div>

            <!-- Bathrooms -->
            <div class="mb-3">
                <label for="bathrooms" class="form-label">Bathrooms</label>
                <input type="number" class="form-control" id="bathrooms" name="bathrooms" value="<?php echo $house['bathrooms']; ?>" required>
            </div>

            

            <!-- Floors -->
            <div class="mb-3">
                <label for="floors" class="form-label">Floors</label>
                <input type="number" class="form-control" id="floors" name="floors" value="<?php echo $house['floors']; ?>" required>
            </div>

            <!-- Restrictions -->
            <div class="mb-3">
                <label for="restrictions" class="form-label">Restrictions</label>
                <input type="text" class="form-control" id="restrictions" name="restrictions" value="<?php echo $house['restrictions']; ?>" required>
            </div>

            <!-- Essentials -->
            <div class="mb-3">
                <label for="essential" class="form-label">Essentials</label>
                <input type="text" class="form-control" id="essential" name="essential" value="<?php echo $house['essential_name']; ?>" required>
            </div>
            <!-- feature -->

            <div class="mb-3">
        <label for="feature" class="form-label">Features</label>
        <input type="text" class="form-control" id="feature" name="feature" value="<?php echo htmlspecialchars($house['feature_name']); ?>" required>
    </div>
            <div class="form-group">
        <label for="gas">Gas:</label>
        <select class="form-control" id="gas" name="gas" required>
            <option value="" disabled>Select</option>
            <option value="Yes" <?php echo ($house['gas'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($house['gas'] == 'No') ? 'selected' : ''; ?>>No</option>
        </select>
    </div>

    <!-- Wi-Fi -->
    <div class="form-group">
        <label for="wifi">Wi-Fi:</label>
        <select class="form-control" id="wifi" name="wifi" required>
            <option value="" disabled>Select</option>
            <option value="Yes" <?php echo ($house['wifi'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($house['wifi'] == 'No') ? 'selected' : ''; ?>>No</option>
        </select>
    </div>

    <!-- CCTV -->
    <div class="form-group">
        <label for="cctv">CCTV:</label>
        <select class="form-control" id="cctv" name="cctv" required>
            <option value="" disabled>Select</option>
            <option value="Yes" <?php echo ($house['cctv'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($house['cctv'] == 'No') ? 'selected' : ''; ?>>No</option>
        </select>
    </div>

    <!-- Garage -->
    <div class="form-group">
        <label for="garage">Garage:</label>
        <select class="form-control" id="garage" name="garage" required>
            <option value="" disabled>Select</option>
            <option value="Yes" <?php echo ($house['garage'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
            <option value="No" <?php echo ($house['garage'] == 'No') ? 'selected' : ''; ?>>No</option>
        </select>
    </div>


    <!-- Service Charge -->
    <div class="mb-3">
                <label for="service_charge" class="form-label">Service Charge</label>
                <input type="text" class="form-control" id="service_charge" name="service_charge" value="<?php echo $house['service_charge']; ?>" required>
            </div>


            <!-- Rent -->
            <div class="mb-3">
                <label for="rent" class="form-label">Rent</label>
                <input type="text" class="form-control" id="rent" name="rent" value="<?php echo $house['rent']; ?>" required>
            </div>

            <!-- City Field -->
            <div class="mb-3">
                <label for="city" class="form-label">City</label>
                <input type="text" class="form-control" id="city" name="city" value="<?php echo $location['city']; ?>" required>
            </div>

            <!-- Address Field -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo $location['address']; ?>" required>
                <button type="button" class="btn btn-secondary btn-success mt-3 mb-2" onclick="searchLocation()">Search Location</button>
            </div>

<?php

$house_id = $_GET['house_id'];

// Fetch latitude and longitude from the database
$query ="SELECT latitude,longitude FROM houses
JOIN locations on houses.location_id=locations.id
 WHERE houses.id = '$house_id'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    // Fetch the row
    $row = $result->fetch_assoc();
    $latitude = $row['latitude'];
    $longitude = $row['longitude'];
} else {
   
    $latitude = "";
    $longitude = "";
}
?> 
 <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($latitude) ?>">
    <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($longitude) ?>">

            <div id="map"></div>
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


            <button type="submit" class="btn btn-success" style="margin-bottom: 10px; margin-top:10px">Update</button>
        </form>
    </div>

    <?php
    include('footer.php');  // Mahbub
    ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</body>
</html>