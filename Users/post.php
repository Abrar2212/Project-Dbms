<?php
// Start session and establish database connection
session_start();
$con = new mysqli('localhost', 'root', '', 'main_home_hunt');

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: homepagebeforelogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the logged-in user's photo
$sq = "SELECT photo_path FROM users 
        JOIN user_photos ON users.id = user_photos.user_id 
        WHERE users.id = ?";
$stmt = $con->prepare($sq);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $photo = !empty($user['photo_path']) ? htmlspecialchars($user['photo_path']) : 'default.jpg';
    } else {
        echo "User photo not found.";
        exit;
    }
    $stmt->close();
} else {
    echo "Query failed: " . $con->error;
    exit;
}

// Get the house ID from the URL
$house_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($house_id <= 0) {
    echo "Invalid house ID.";
    exit;
}


$sql = "SELECT 
            houses.user_id AS owner_id,
            users.name, 
            houses.id AS house_id, 
            houses.name AS house_name, 
            users.name AS owner_name, 
            houses.rent,
            feature_name, 
            essential_name, 
            house_images.image_path,
            house_images.type, 
            available, 
            bedrooms, 
            bathrooms, 
            service_charge, 
            garage, 
            floors, 
            user_photos.photo_path,
            restrictions, 
            latitude, 
            longitude, 
            gas, 
            wifi, 
            cctv
        
        FROM users
        JOIN houses ON users.id = houses.user_id
        JOIN house_images ON houses.id = house_images.post_id
        JOIN house_essentials ON house_essentials.post_id = houses.id
        JOIN house_features ON house_features.post_id = houses.id
        JOIN user_photos ON user_photos.user_id = users.id
        JOIN locations ON locations.id = houses.location_id
        WHERE houses.id = ?";

$stmt = $con->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $house_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $house = [];
        $main = null;
        $small = []; // Array for multiple small images

        while ($row = $result->fetch_assoc()) {
            // Populate house details only once
            if (empty($house)) {
                $house = [
                    'owner_id' => $row['owner_id'],
                    'owner_name' => $row['owner_name'],
                    'house_id' => $row['house_id'],
                    'house_name' => $row['house_name'],
                    'rent' => $row['rent'],
                    'feature_name' => $row['feature_name'],
                    'essential_name' => $row['essential_name'],
                    'available' => $row['available'],
                    'bedrooms' => $row['bedrooms'],
                    'bathrooms' => $row['bathrooms'],
                    'service_charge' => $row['service_charge'],
                    'garage' => $row['garage'],
                    'floors' => $row['floors'],
                    'photo_path' => $row['photo_path'],
                    'restrictions' => $row['restrictions'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'gas' => $row['gas'],
                    'wifi' => $row['wifi'],
                    'cctv' => $row['cctv'],
                    
                ];
            }

            // Categorize images based on type
            if ($row['type'] === 'main') {
                $main = $row['image_path'];
            } elseif ($row['type'] === 'small') {
                $small[] = $row['image_path'];
            }
        }

        // Add images to the house details
        $house['main_image'] = $main;
        $house['small_images'] = $small;
    } else {
        echo "House not found!";
        exit;
    }
    $stmt->close();
} else {
    echo "Query failed: " . $con->error;
    exit;
}

// Close the database connection
$con->close();


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Tour Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link rel="stylesheet" href="home.css"> -->
    <link rel="stylesheet" href="post.css">

</head>

<body>

    <?php
    include('searchAndnav.php');  // Mahbub 
    ?>



    <div class="fullpage d-flex">

        <!-- Profile -->

        <div class="container mt-4">



            <!-- Agent Section -->
            <div class="row mt-4  agent-info">
                <div class="contact d-flex align-items-center  gap-3">
                    <!-- Agent Profile Section -->
                    <div class="pro d-flex align-items-center gap-3">
    <a href="viewProfile.php?id=<?= htmlspecialchars($house['owner_id']) ?>" 
       class="d-flex align-items-center gap-3 text-decoration-none" 
       style="color: inherit;">
        <img
            src="<?= htmlspecialchars($house['photo_path']) ?>"
            alt="Agent"
            style="width: 35px; height: 35px; border-radius: 50%;">
        <p style="margin-left: 10px;"> 
            <b><?= htmlspecialchars($house['owner_name']) ?></b>
        </p>
    </a>
</div>
<?php


$house_id = (int)$_GET['id'];
$query = "SELECT active_status FROM houses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $house_id);
$stmt->execute();
$stmt->bind_result($active_status);
$stmt->fetch();
$stmt->close();
?>


                  <!-- Contact Button -->
<div>
    <?php if ((int)$user_id !== (int)$house['owner_id'] && (int)$active_status !== 0) : ?>
        <a class="btn btn-success"
            href="agreement.php?house_id=<?= htmlspecialchars($house['house_id']) ?>&owner_id=<?= htmlspecialchars($house['owner_id']) ?>">
            Agreement
        </a>
    <?php elseif ((int)$user_id === (int)$house['owner_id']) : ?>
        <a class="btn btn-warning"
           href="updatepost.php?action=edit&house_id=<?= htmlspecialchars($house['house_id']) ?>">Edit Post</a>

        <a class="btn btn-danger"
           href="delete.php?action=delete&house_id=<?= htmlspecialchars($house['house_id']) ?>"
           onclick="return confirm('Are you sure you want to delete this house?');">
           Delete
        </a>
    <?php else: ?>
        <span class="btn btn-secondary">Booked</span>
    <?php endif; ?>
</div>





                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- HTML to display the fetched data -->
                    <div class="col-12">
                        <h3 class="fw-bold">Let's Tour And See Our House <?php echo $house['house_name']; ?>!</h3>
                        <p class="text-muted">Available From <?php echo $house['available']; ?></p>
                    </div>

                </div>
            </div>

            <!-- Picture Section -->
            <div class="row picture-section">
                <!-- Main Image -->
                <div class="main-image-container">
                    <?php if ($main): ?>
                        <img style="height:600px; width:600px;" src="<?= htmlspecialchars($main) ?>" alt="House" class="img-fluid">
                    <?php else: ?>
                        <p>Main image not available for this house.</p>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <div class="small-images d-flex flex-column align-items-start gap-3">
                    <?php if (!empty($small)): ?>
                        <?php foreach ($small as $thumbnail): ?>
                            <div>
                                <img
                                    src="<?= htmlspecialchars($thumbnail) ?>"
                                    alt="Thumbnail"
                                    class="img-thumbnail thumbnail-image"
                                    data-bs-toggle="modal"
                                    data-bs-target="#zoomModal"
                                    data-img="<?= htmlspecialchars($thumbnail) ?>"
                                    style="cursor: pointer; width: 100px; height: auto;">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                   
                    <?php endif; ?>
                </div>

                <!-- Modal for Zoomed Image -->
                <div class="modal fade" id="zoomModal" tabindex="-1" aria-labelledby="zoomModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <img id="zoomedImage" src="" alt="Zoomed Image" class="img-fluid">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // JavaScript to update the modal image
                document.addEventListener("DOMContentLoaded", function() {
                    const thumbnails = document.querySelectorAll(".thumbnail-image");
                    const zoomedImage = document.getElementById("zoomedImage");

                    thumbnails.forEach(thumbnail => {
                        thumbnail.addEventListener("click", function() {
                            const imgSrc = this.getAttribute("data-img");
                            zoomedImage.setAttribute("src", imgSrc);
                        });
                    });
                });
            </script>



            <div class="row mt-5 ">
                <div class="col-lg-6">
                    <p class="home"><strong>House Detail</strong></p>
                    <div class="d-flex justify-content-between">

                        <div class="first">
                            <p class="list">Bedrooms: <?= $house['bedrooms'] ?> </p>
                            <p class="list">Bathrooms: <?= $house['bathrooms'] ?> </p>
                            <p class="list">Service Charge: <?= $house['service_charge'] ?> </p>
                        </div>
                        <div class="second">
                            <p class="list">Garage: <?= $house['garage'] ?> </p>
                            <p class="list">Floors: <?= $house['floors'] ?> </p>
                            <p class="list">Rent: <?= $house['rent'] ?> </p>

                        </div>


                    </div>
                    <p class="text-warning">Ready to Sell!</p>
                </div>

                <div class="feature">



                    <!-- live location -->

                    <div class="col-lg-4">
                        <div class="card  text-center" style="width:1250px; height:400px">
                            <div class="card-body ">
                                <h5 class="card-title">Live Location</h5>
                                <div id="map" style="width: 100%; height: 330px;"></div> <!-- Map container -->
                                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                            </div>
                        </div>
                    </div>

                    <script>
                        // Initialize the map with dynamic latitude and longitude from PHP
                        var latitude = <?php echo $house['latitude']; ?>;
                        var longitude = <?php echo $house['longitude']; ?>;

                        // Initialize the map
                        var map = L.map('map').setView([latitude, longitude], 13);

                        // Add OpenStreetMap tile layer
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                        }).addTo(map);

                        //  marker 
                        L.marker([latitude, longitude]).addTo(map)
                            .bindPopup("Location: " + latitude + ", " + longitude)
                            .openPopup
                    </script>








                    <!-- Features and Extras -->
                    <div class="row mt-5 d-flex align-items-stretch">
                        <div class="col-lg-4">
                            <div class="card essentials-card text-center h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Nearby Essentials</h5>
                                    <p><?= $house['essential_name'] ?> </p>
                                </div>
                            </div>
                        </div>

                        <!-- Restriction -->
                        <div class="col-lg-4">
                            <div class="card border-danger mb-3 hover-shadow h-100">
                                <div class="card-header">Restrictions</div>
                                <div class="card-body text-danger">
                                    <h5 class="card-title"><?= $house['restrictions'] ?> </h5>
                                    <p class="card-text">Must Follow</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature -->
                        <div class="col-lg-4">
                            <div class="card feature-card text-center h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Features</h5>
                                    <ul class="list-unstyled">
                                        <li><?= $house['feature_name'] ?></li>
                                        <li>GAS : <?= $house['gas'] ?></li>
                                        <li>WIFI : <?= $house['wifi'] ?></li>
                                        <li>CCTV : <?= $house['cctv'] ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





            </div>
            <?php
            include('rating&comment.php');
            ?>
        </div>
    </div>



    </div>
    <?php
    include('footer.php');
    ?>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>