<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    // Start the session if not started
    session_start();
}
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: homepagebeforelogin.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "main_home_hunt");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT photo_path 
          FROM users
          JOIN user_photos ON users.id = user_photos.user_id
          WHERE user_photos.type = 'profile' AND users.id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$photo = !empty($user['photo_path']) ? htmlspecialchars($user['photo_path']) : 'default.jpg';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protected Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        *{
            font-family: 'Times New Roman', serif;
        }
        .a {
            font-size: xx-large;
        }

        form {
            margin-right: 297px;
        }

        .navbar {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between align-items-center" id="navitems">
            <div>
                <a class="navbar-brand a" href="homepage.php">HomeHunt</a>
                <a class="navbar-brand" href="UserProfile.php">Profile</a>
                <a class="navbar-brand" href="update_profile.php">Edit Profile</a>
                <a class="navbar-brand" href="notification.php">Status</a>
            </div>
            <form class="d-flex " role="search" style="width: 35%;" action="search.php" method="GET">
                <input class="form-control me-2" type="search" name="location" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <div class="plog">
                <div class="d-flex justify-content-center">
                    <div class="dropdown">
                        <button class="btn aj" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $photo ?>" alt="Profile Image" class="profile_img border border-success border-3 rounded-circle" style="width: 45px; height: 45px;">
                        </button>
                        <ul class="dropdown-menu text-dark" style="left: -100px; top: 50px;" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="UserProfile.php?user_id=<?= $user_id ?>">Setting</a></li>
                            <li><a class="dropdown-item" href="#">Help & Support</a></li>
                            <li><a class="dropdown-item" href="#">Theme</a></li>
                            <li><a class="dropdown-item" href="#">Language</a></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php" onclick="handleLogout(event)">Log Out</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Handle logout
        function handleLogout(event) {
            event.preventDefault();
            sessionStorage.removeItem("isLoggedIn"); // Clear client-side session
            window.location.href = "logout.php"; // Redirect to server-side logout
        }
    </script>
</body>

</html>