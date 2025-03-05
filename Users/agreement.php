<?php
include 'connect.php'; // Include the database connection file
session_start();

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

// Initialize message variable
$message = "";

// Fetch user details from the database
$userQuery = "SELECT name, email FROM users WHERE id = ?";
$userStmt = $con->prepare($userQuery);
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
if ($userResult && $userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
    $name = $userData['name'];
    $email = $userData['email'];
} else {
    $message = "Error fetching user data.";
}

$house_id = $_GET['house_id'] ?? null;
$owner_id = $_GET['owner_id'] ?? null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Retrieve data from POST request
    $phone = $_POST['phone'];
    $nid = $_POST['nid'];
    $members = $_POST['members'];
    $gender = $_POST['gender'];
    $user_type = $_POST['user_type'];

    // Check if all fields are filled
    if (!empty($user_id) && !empty($phone) && !empty($house_id) && !empty($nid) && !empty($members) && !empty($gender) && !empty($user_type) && !empty($house_id)) {
        // Insert data into the agreements table
        $sql = "INSERT INTO `agreements` (user_id, phone, house_id, nid, members, gender, user_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);

// Corrected bind_param call with 7 arguments
$stmt->bind_param("iississ", $user_id, $phone, $house_id, $nid, $members, $gender, $user_type);


if ($stmt->execute()) {
    // Check if the user is already enrolled in the home
    $checkEnrollmentSql = "SELECT * FROM enrollments WHERE user_id = ? AND home_id = ?";
    $checkEnrollmentStmt = $con->prepare($checkEnrollmentSql);
    $checkEnrollmentStmt->bind_param("ii", $user_id, $house_id);
    $checkEnrollmentStmt->execute();
    $existingEnrollment = $checkEnrollmentStmt->get_result();

    if ($existingEnrollment->num_rows > 0) {
        // User is already enrolled
        $message = "You are already enrolled in this home.";
    } else {
        // Insert enrollment request
        $enrollmentSql = "INSERT INTO `enrollments` (user_id, owner_id, home_id, agreement_id) VALUES (?, ?, ?, ?)";
        $enrollmentStmt = $con->prepare($enrollmentSql);
        $agreement_id = $stmt->insert_id; // Get the last inserted agreement ID
        $enrollmentStmt->bind_param("iiii", $user_id, $owner_id, $house_id, $agreement_id);

        if ($enrollmentStmt->execute()) {
            // Successful enrollment
            header('Location: display.php'); // Redirect to display.php
            exit();
        } else {
            // Error enrolling user
            $message = "Error enrolling user: " . $enrollmentStmt->error;
        }
    }
} else {
    // Error creating agreement
    $message = "Error creating agreement: " . $stmt->error;
}
    } else {
        $message = "Please fill in all the fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Hunt Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="agree.css">
</head>

<body>

<div class="nav1"><?php
    include('searchAndnav.php');  // Mahbub 
    ?></div>


    <div class="container d-flex ">


        <div class="agreement-section">
       
            <div class="d-flex justify-content-center ">
            <p class="agreement-info">Agreement Info</p>
            </div>
            

            <!-- Display success or error message -->
            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form class="form" method="POST" action="">
                <!-- Pre-fill Name and Email from the session -->
                <input type="hidden" name="house_id" value="<?= htmlspecialchars($house_id); ?>">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Your Name" value="<?= htmlspecialchars($name); ?>" readonly>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Your Email" value="<?= htmlspecialchars($email); ?>" readonly>

                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="Your Phone" required>

                <label for="nid">NID/Birth Certificate</label>
                <input type="text" id="nid" name="nid" placeholder="NID" required>

                <label for="members">Members</label>
                <input type="number" id="members" name="members" placeholder="No." min="1" required>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="user_type" class="form-label">User Type</label>
                    <select id="user_type" name="user_type" class="form-select" required>
                        <option value="" disabled selected>User Type</option>
                        <option value="family">Family</option>
                        <option value="office">Office</option>
                        <option value="bachelor">Bachelor</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="submit-btn" style="display: block; position: static;">Submit</button>



                <p class="terms">By clicking Register, you agree to our <br> <a href="#">Terms and Data Policy</a></p>
            </form>
        </div>

        <div class="image-section">
            <img src="registration.jpg" alt="House Image">
        </div>
    </div>
</body>

</html>