<?php
session_start();
// Database connection settings
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

$user_id = $_SESSION['user_id']; // Get logged-in user's ID from session

// Cache control headers to prevent caching of the page
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch user data from the database
$query = "SELECT 
            users.name, 
            users.email, 
            users.dob, 
            user_photos.photo_path, 
            roles.role_name 
          FROM 
            users 
          JOIN 
            roles ON users.role_id = roles.id 
          JOIN
            user_photos ON users.id=user_photos.user_id
          WHERE 
            users.id = ?";
$stmt = $con->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $con->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Calculate the age from the date of birth
$dob = new DateTime($user['dob']);
$today = new DateTime();
$age = $today->diff($dob)->y;

// Set default photo if not available
$photo = !empty($user['photo_path']) ? htmlspecialchars($user['photo_path']) : 'default.jpg';

// Get the user's role
$role = $user['role_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="owner-styles.css">
</head>

<body>

    <div class="fullpage d-flex">

        <!-- Sidebar -->
        <div class="slidebar">
            <!-- Logo -->
            <div class="mb-4 nav flex-column">
                <h1>Hello, <?= htmlspecialchars($user['name']) ?>!</h1>
                <!-- Sidebar Navigation -->
                <a href="homepage.php" class="nav-link active"><i class="fas fa-home"></i> Home</a>
                <a href="update_profile.php" class="nav-link"><i class="fas fa-user-edit"></i> Edit Profile</a>
                <a href="notification.php" class="nav-link"><i class="fas fa-envelope"></i> Notification</a>
                <a href="HomeInfo.php" class="nav-link"><i class="fas fa-house-user"></i> House Info</a>
                <a href="login.php" onclick="handleLogout(event)" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="container main_content py-5">
            <div class="row mb-5">
                <div class="col-md-4 text-center">
                    <img src="<?= $photo ?>" alt="Profile Picture" class="rounded-circle img-fluid" style="max-width: 150px;">
                </div>
                <div class="col-md-8 userInfo">
                    <h1><?= htmlspecialchars($user['name']) ?></h1>
                    <p><strong>Age:</strong> <?= $age ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>

            <?php if ($role !== 'Customer'): ?>
                <div class="mb-2 d-flex justify-content-start text-center">
                    <a href="post_form.php" class="btn btn-success btn-md">New Post</a>
                </div>
            <?php endif; ?>

            <div class="mb-5">
                <h2 class="mb-4"><?= $role === 'Customer' ? "Your Enrolled Houses" : "Your Recent Posts" ?></h2>
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <?php
                    // Adjust SQL query based on role
                    if ($role === 'Customer') {
                        $sql = "SELECT houses.id AS house_id, houses.name AS house_name, houses.description,active_status, 
                                house_images.image_path, houses.created_at
                                FROM enrollments
                                JOIN houses ON enrollments.home_id = houses.id
                                JOIN house_images ON houses.id = house_images.post_id
                                WHERE house_images.type='main' AND enrollments.status='approved' and enrollments.user_id=?";
                    } else {
                        $sql = "SELECT users.id AS user_id, houses.id AS house_id, houses.name AS house_name,active_status, 
                                houses.description, house_images.image_path, houses.created_at
                                FROM users
                                JOIN houses ON users.id = houses.user_id
                                JOIN house_images ON houses.id = house_images.post_id
                                WHERE house_images.type='main' AND users.id=?";
                    }

                    $stmt = $con->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($house = $result->fetch_assoc()) {
                                $house_id = $house['house_id'];

                                $rating_query = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE house_id = ?";
                                $stmt = $con->prepare($rating_query);
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
                                <a class="link-offset-2 link-underline link-underline-opacity-0" href="post.php?id=<?= $house['house_id'] ?>">
                                    <div class="col">
                                        <div class="card h-100">
                                            <img style="height: 300px; width: 414px;" src="<?= $house['image_path'] ?>" class="card-img-top" alt="<?= $house['house_name'] ?>">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= $house['house_name'] ?></h5>
                                                <p class="card-text"><?= substr($house['description'], 0, 100) ?>...</p>
                                                <span class="text-warning">
                                                    <?= $rating_stars > 0 ? round($rating_stars, 1) . " &#9733;" : "Not rated yet"; ?>
                                                </span>
                                                <div><?= $status_message ?></div>
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
                            echo $role === 'Customer' ? "No enrolled houses found!" : "No posts found!";
                        }
                    } else {
                        echo "Query preparation failed: " . $con->error;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>