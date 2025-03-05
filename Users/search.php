<?php
// Start the session
session_start();

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
$conn = new mysqli("localhost", "root", "", "main_home_hunt");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logged-in user
if (!isset($_SESSION['user_id'])) {
    header("Location: homepagebeforelogin.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch profile photo
$query = "SELECT photo_path 
          FROM users
          JOIN user_photos ON users.id = user_photos.user_id
          WHERE user_photos.type = 'profile' AND users.id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $photo = !empty($user['photo_path']) ? htmlspecialchars($user['photo_path']) : 'images/default_profile.jpg';
    $stmt->close();
} else {
    $photo = 'images/default_profile.jpg';
}

// Sort order
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'A-Z';
$valid_sort_orders = [
    'A-Z' => 'houses.name ASC',
    'Z-A' => 'houses.name DESC',
    'Low-High' => 'houses.rent ASC',
    'High-Low' => 'houses.rent DESC',
];
$order_by = $valid_sort_orders[$sort_order] ?? 'houses.name ASC';

// Pagination setup
$search_location = isset($_GET['location']) ? $_GET['location'] : '';
$posts_per_page = 8;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);
$offset = ($current_page - 1) * $posts_per_page;

// Fetch total posts
$sql_count = "SELECT COUNT(*) as total_posts 
              FROM houses 
              JOIN locations ON houses.location_id = locations.id
              WHERE locations.address LIKE ? OR locations.city LIKE ?";
$stmt_count = $conn->prepare($sql_count);
if ($stmt_count) {
    $like_search_location = "%" . $search_location . "%";
    $stmt_count->bind_param("ss", $like_search_location, $like_search_location);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $total_posts = $result_count->fetch_assoc()['total_posts'];
    $stmt_count->close();
} else {
    $total_posts = 0;
}
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts
$sql = "SELECT users.id AS user_id, 
               houses.id AS house_id, 
               houses.name AS house_name, 
               houses.description, 
               house_images.image_path, 
               houses.created_at
        FROM users
        JOIN houses ON users.id = houses.user_id
        JOIN house_images ON houses.id = house_images.post_id
        JOIN locations ON houses.location_id = locations.id
        WHERE house_images.type = 'main' 
        AND (locations.city LIKE ? OR locations.address LIKE ?)
        ORDER BY $order_by
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssii", $like_search_location, $like_search_location, $posts_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A basic Bootstrap 5.3 template">
    <link rel="icon" href="file.png">
    <title>HomeHunt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="home.css">

    <style>
        .res{
            font-size: larger;
            font-weight: bold;
        }
        .a{
        font-size: xx-large;
    }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between align-items-center" id="navitems">
            <!-- Brand Logo -->
            <a class="a navbar-brand" href="homepage.php">HomeHunt</a>
            

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                    <a class="navbar-brand" href="UserProfile.php">Profile</a>
            <a class="navbar-brand" href="update_profile.php">Edit Profile</a>
            <a class="navbar-brand" href="notification.php">Status</a>
                    </li>
                </ul>

                <!-- Search Bar -->
                <form class="d-flex mx-auto" role="search" style="width: 50%;" action="search.php" method="GET">
    <input class="form-control me-2" type="search" name="location" placeholder="Search for your location" aria-label="Search">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>

                <!-- Profile Dropdown -->
                <div class="plog">
                    <div class="d-flex justify-content-center">
                        <div class="dropdown">
                            <button class="btn aj" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <img src="<?= $photo ?>" alt="Profile Image" class="profile_img border border-success border-3 rounded-circle" style="width: 45px; height: 45px;">
                            </button>
                            <ul class="dropdown-menu text-dark" style="left: -100px; top: 50px;" aria-labelledby="dropdownMenuButton">
                                <li class="d-flex justify-content-center">
                                    
                                <li><a class="dropdown-item" href="UserProfile.php?user_id=<?= $user_id ?> ">Setting</a></li>
                                <li><a class="dropdown-item" href="#">Help & Support</a></li>
                                <li><a class="dropdown-item" href="#">Theme</a></li>
                                <li><a class="dropdown-item" href="#">Language</a></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="login.php" onclick="handleLogout(event)">Log Out</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

   

<div class="container">
<div class="filterSort">

<div class="dropdown d-flex">
    <div class="suggestion">
        
    </div>
    <button class="btn but btn-success dropdown-toggle " type="button" data-bs-toggle="dropdown" aria-expanded="false" style="margin-left: 10px;">
        Sort
    </button>

    <ul class="dropdown-menu dropdown-menu-dark">
    <li><a class="dropdown-item <?= $sort_order == 'A-Z' ? 'active' : '' ?>"
            href="?sort=A-Z<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">A-Z</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'Z-A' ? 'active' : '' ?>"
            href="?sort=Z-A<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">Z-A</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'Low-High' ? 'active' : '' ?>"
            href="?sort=Low-High<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">Low to High</a></li>
    <li><a class="dropdown-item <?= $sort_order == 'High-Low' ? 'active' : '' ?>"
            href="?sort=High-Low<?= !empty($search_location) ? '&location=' . urlencode($search_location) : '' ?>">High to Low</a></li>
    
</ul>

</div>





</div>

<div class="res">
    <p>Your search result</p>
   </div>
    

    <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php

$search_location = isset($_GET['location']) ? $_GET['location'] : '';


$posts_per_page = 8;


$sql_count = "SELECT COUNT(*) as total_posts 
              FROM houses 
              JOIN locations ON houses.location_id = locations.id
              WHERE locations.address LIKE ? OR locations.city LIKE ?";

$stmt_count = $conn->prepare($sql_count);

if ($stmt_count === false) {
    die("Error preparing statement for count: " . $conn->error);
}

// Prepare the search string with wildcards for both address and city
$like_search_location = "%" . $search_location . "%";

// Bind parameters: two string values for address and city search
$stmt_count->bind_param("ss", $like_search_location, $like_search_location);

// Execute and get the result
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_posts = $result_count->fetch_assoc()['total_posts'];

// Calculate total pages for pagination
$total_pages = ceil($total_posts / $posts_per_page);

// Get the current page from the query string
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1; // Ensure the page number is at least 1
$offset = ($current_page - 1) * $posts_per_page;

// Fetch posts for the current page based on search location
$sql = "SELECT users.id AS user_id, 
               houses.id AS house_id, 
               houses.name AS house_name, 
               houses.description, 
               house_images.image_path, 
               houses.created_at
        FROM users
        JOIN houses ON users.id = houses.user_id
        JOIN house_images ON houses.id = house_images.post_id
        JOIN locations ON houses.location_id = locations.id
        WHERE house_images.type = 'main' 
        AND (locations.city LIKE ? OR locations.address LIKE ?)
        ORDER BY $order_by
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

// Bind parameters: address and city search, followed by limit and offset
$stmt->bind_param("ssii", $like_search_location, $like_search_location, $posts_per_page, $offset);

// Execute and get the result
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($house = $result->fetch_assoc()) {
?>
        <a class="link-offset-2 link-underline link-underline-opacity-0" href="Post.php?id=<?= htmlspecialchars($house['house_id']) ?>">
            <div class="col">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($house['image_path']) ?>" class="card-img-top" style="height: 300px; width: 414px;" alt="<?= htmlspecialchars($house['house_name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($house['house_name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars(substr($house['description'], 0, 100)) ?>...</p>
                    </div>
                    <div class="card-footer">
                        <small class="text-body-secondary">Last updated: <?= htmlspecialchars($house['created_at']) ?></small>
                    </div>
                </div>
            </div>
        </a>
<?php
    }
} else {
    echo "<div class='col'><p>No house found for <strong>" . htmlspecialchars($search_location) . "</strong></p></div>";
}

$conn->close();
?>

    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation example" style="margin-bottom: 50px;">
        <ul class="pagination justify-content-center">
            <?php
            if ($current_page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($current_page - 1) . '&location=' . urlencode($search_location) . '">Previous</a></li>';
            } else {
                echo '<li class="page-item disabled"><a class="page-link">Previous</a></li>';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    echo '<li class="page-item active"><a class="page-link" href="?page=' . $i . '&location=' . urlencode($search_location) . '">' . $i . '</a></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . $i . '&location=' . urlencode($search_location) . '">' . $i . '</a></li>';
                }
            }

            if ($current_page < $total_pages) {
                echo '<li class="page-item"><a class="page-link" href="?page=' . ($current_page + 1) . '&location=' . urlencode($search_location) . '">Next</a></li>';
            } else {
                echo '<li class="page-item disabled"><a class="page-link">Next</a></li>';
            }
            ?>
        </ul>
    </nav>

</div>
<?php
    include('footer.php');  // Mahbub 
    ?>
</body>
</html>
