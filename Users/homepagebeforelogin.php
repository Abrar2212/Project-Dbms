<?php
// Start the session at the top of the page
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "main_home_hunt");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle sorting
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'A-Z';
switch ($sort_order) {
    case 'A-Z':
        $order_by = "houses.name ASC";
        break;
    case 'Z-A':
        $order_by = "houses.name DESC";
        break;
    case 'Low-High':
        $order_by = "houses.rent ASC";
        break;
    case 'High-Low':
        $order_by = "houses.rent DESC";
        break;
    default:
        $order_by = "houses.name ASC";
}

// Pagination setup
$posts_per_page = 8;
$sql_count = "SELECT COUNT(*) as total_posts FROM houses";
$result_count = $conn->query($sql_count);
$total_posts = $result_count->fetch_assoc()['total_posts'];
$total_pages = ceil($total_posts / $posts_per_page);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Fetch posts for the current page
$sql = "SELECT users.id AS user_id, 
houses.id AS house_id, 
houses.rent, 
houses.available,
users.name as owner_name, 
user_photos.photo_path,
houses.name AS house_name, 
houses.description, 
house_images.image_path, 
houses.created_at,
houses.active_status
FROM users
JOIN houses ON users.id = houses.user_id
JOIN house_images ON houses.id = house_images.post_id
JOIN user_photos ON users.id = user_photos.user_id
WHERE house_images.type = 'main'
ORDER BY $order_by
LIMIT $posts_per_page OFFSET $offset";

$result = $conn->query($sql);
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
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="home.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between align-items-center" id="navitems">
            <a class="navbar-brand" href="homepage.php">HomeHunt</a>
            <form class="d-flex mx-auto" role="search" style="width: 35%;" action="search.php" method="GET">
                <input class="form-control me-2" type="search" name="location" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <div class="plog">
                <div class="d-flex justify-content-center">
                    <a href="login.php" class="btn btn-primary me-2">Log In</a>
                    <a href="Registration.php" class="btn btn-danger">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login/Sign Up Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                
                <div class="modal-body m-3">
                    <p class="text-center b"><b>Please login/signup</b></p>
                    <div class="d-flex justify-content-around">
                        <a href="login.php" class="btn btn-primary">Log In</a>
                        <a href="Registration.php" class="btn btn-danger">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="Homepage">
        <div class="header">
            <h1 class="name">HomeHunt</h1>
        </div>
    </div>

    <div class="container">
        <?php include('sortfilter.php');  // Mahbub ?>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($house = $result->fetch_assoc()) {
                    $house_id = $house['house_id'];

            $rating_query = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE house_id = ?";
            $stmt = $conn->prepare($rating_query);
            $stmt->bind_param('i', $house_id);
            $stmt->execute();
            $rating_result = $stmt->get_result();

            if ($rating_result && $rating_result->num_rows > 0) {
                $rating_row = $rating_result->fetch_assoc();
                $rating_stars = round($rating_row['avg_rating'] ?? 0, 1);
            } else {
                $rating_stars = "Not rated yet";
            }
            $stmt->close();

            // Check the active_status
            $active_status = $house['active_status'];
            if ($active_status == 0) {
                $status_message = '<span class="badge bg-danger">Booked</span>';
            } else {
                $status_message = '<span class="badge bg-success">Available</span>';
            }
            ?>
                    <a class="link-offset-2 link-underline link-underline-opacity-0" href="Post.php?id=<?= $house['house_id'] ?>" 
                       onclick="checkLogin(event, <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>, 'Post.php?id=<?= $house['house_id'] ?>')">
                        <div class="col">
                            <div class="card h-100">
                                <img src="<?= $house['image_path'] ?>" class="card-img-top" style="height: 300px; width: 414px;" alt="<?= $house['house_name'] ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= $house['house_name'] ?></h5>
                                    <p class="card-text"><b>Rent: </b><?= $house['rent'] ?> TK</p>
                                    <p class="card-text"><b>Description: </b><?= substr($house['description'], 0, 100) ?></p>
                                    <h6>
                                <span class="text-warning">
                                    <?= $rating_stars > 0 ? round($rating_stars, 1) . " &#9733;" : "Not rated yet"; ?>
                                </span>
                            </h6>
                            <!-- Display the booking status -->
                            <div><?= $status_message ?></div>   
                                    <div class=" d-flex justify-center align-items-center mt-2 d-flex justify-center align-items-center text-decoration-none">
                
                                   
                    
                      
                        
                    </div>
                                </div>
                                <div class="card-footer">
                                    <small class="text-body-secondary">Last updated: <?= $house['created_at'] ?></small>
                                </div>
                            </div>
                        </div>
                    </a>
            <?php
                }
            } else {
                echo "No posts found!";
            }
            $conn->close();
            ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation example" style="margin-bottom: 50px;">
            <ul class="pagination justify-content-center">
                <?php
                // Previous button
                if ($current_page > 1) {
                    echo '<li class="page-item">
                        <a class="page-link" href="?page=' . ($current_page - 1) . '">Previous</a>
                    </li>';
                } else {
                    echo '<li class="page-item disabled">
                        <a class="page-link">Previous</a>
                    </li>';
                }

                // Page numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    if ($i == $current_page) {
                        echo '<li class="page-item active">
                            <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                        </li>';
                    } else {
                        echo '<li class="page-item">
                            <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
                        </li>';
                    }
                }

                // Next button
                if ($current_page < $total_pages) {
                    echo '<li class="page-item">
                        <a class="page-link" href="?page=' . ($current_page + 1) . '">Next</a>
                    </li>';
                } else {
                    echo '<li class="page-item disabled">
                        <a class="page-link">Next</a>
                    </li>';
                }
                ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76Aq4sLdbrV1CtzYsaKOK2zFWE6ElYhD7wLpxyKkzvMXucjK4w4Bl8xkqExKeqD"
            crossorigin="anonymous"></script>

    <script>
        function checkLogin(event, isLoggedIn, postUrl) {
            if (!isLoggedIn) {
                event.preventDefault();  // Prevent the link from opening the post page
                // Show the login/signup modal
                var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            } else {
                window.location.href = postUrl;  // Redirect to the post page if the user is logged in
            }
        }
    </script>

    <?php include('footer.php');  // Mahbub ?>
</body>
</html>
