<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
$query = "SELECT users.name FROM users WHERE users.id = ?";
$stmt = $con->prepare($query);

if (!$stmt) {
    die("Query preparation failed: " . $con->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Handle case where no user is found
    die("No user found with ID: " . htmlspecialchars($user_id));
}

$stmt->close();
$con->close();
?>

<style>
  
.slidebar{
  padding: 20px;
  position: sticky;
  top: 0;
  left: 0;
  height: 100vh;
  width: 400px;
  background-color: #ffffff;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  
}
.main_content {
  overflow: auto;
}

.slidebar {
  width: 400px;
  height: 100vh;
  background: #036e40;
  padding: 20px;
  box-shadow: 3px 0px 15px rgba(0, 0, 0, 0.15);
  color: #fff;
}

.slidebar h1 {
  text-align: center;
  font-size: 1.6rem;
  font-weight: 600;
  margin-bottom: 25px;
  color: #e9ecef;
}

.slidebar img {
  width: 70px;
  height: auto;
  border-radius: 50%;
  margin-bottom: 20px;
  border: 3px solid #fff;
}

.nav-link {
  color: #fff !important;
  font-size: 1.2rem;
  padding: 15px 20px;
}

.nav-link:hover {
  background-color: #495057;
  color: #00c6ff !important;
  border-radius: 5px;
}

.nav-link i {
  margin-right: 10px;
}

.nav-item.active .nav-link {
  background-color: #00c6ff;
  color: white !important;
}
.social-icons {
  display: flex;
  justify-content: center;
  
  gap: 20px;
  margin-bottom: 10px;
}

.social-icons i {
    transition: transform 0.3s ease-in-out 0.2s; /* Add delay of 0.2s */
}

.social-icons i:hover {
    transform: scale(1.2); /* Corrected the scale effect */
    transition: transform 0.3s ease-in-out 0s; /* No delay on hover */
}


.social-icons a {
  color: #fff;
  font-size: 1.5rem;
  text-decoration: none;
  transition: color 0.3s ease;
}

.social-icons a:hover {
  color: #00c6ff;
}


</style>
<link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- HTML Section -->
<div class="slidebar">
    <!-- Logo -->
    <div class="mb-4 nav flex-column">
        
        <h1 style="text-align: center;">Hello, <?= htmlspecialchars($user['name']) ?>!</h1>

        <!-- Sidebar Navigation -->
        <a href="homepage.php" class="nav-link active">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="update_profile.php" class="nav-link">
            <i class="fas fa-user-edit"></i> Edit Profile
        </a>
        <a href="notification.php" class="nav-link">
            <i class="fas fa-envelope"></i> Notification
        </a>
        <a href="HomeInfo.php" class="nav-link">
            <i class="fas fa-house-user"></i> House Info
        </a>
       
     
        
        <a href="login.php" onclick="handleLogout(event)" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>

        <script>
            // Function to delete all cookies
            function deleteCookies() {
                document.cookie.split(";").forEach(function(cookie) {
                    const eqPos = cookie.indexOf("=");
                    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;";
                });
            }

            // Function to handle logout
            function handleLogout(event) {
                event.preventDefault();
                deleteCookies();
                setTimeout(() => {
                    window.location.href = "login.php";
                }, 100); // Ensure cookies are deleted before redirection
            }
        </script>
    </div>

    <div class="social-icons">
        <a href="https://www.facebook.com" target="_blank" title="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.whatsapp.com" target="_blank" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="https://twitter.com" target="_blank" title="Twitter">
            <i class="fab fa-x-twitter"></i>
        </a>
    </div>
</div>
