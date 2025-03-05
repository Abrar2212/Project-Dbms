<?php
include 'connect.php'; // Include the database connection file
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: homepagebeforelogin.php");
    exit();
}

// Get the current user ID from the session
$current_user_id = $_SESSION['user_id'];
$q = "SELECT users.name FROM users WHERE users.id = ?";
$stmt = $con->prepare($q);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get the house owner's ID from the query string (or default to the logged-in user's ID)
$post_holder_id = isset($_GET['id']) ? intval($_GET['id']) : $current_user_id;

// Fetch the house owner's details from the database
$sql = "SELECT users.name, users.email, users.created_at, user_photos.photo_path, users.phone, users.address
        FROM users
        JOIN user_photos ON users.id = user_photos.user_id
        WHERE user_photos.type = 'profile' AND users.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $post_holder_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a record was found
if ($result->num_rows > 0) {
    $post_holder = $result->fetch_assoc();
} else {
    // Default fallback values
    $post_holder = [
        'name' => 'Unknown',
        'email' => 'N/A',
        'phone' => 'N/A',
        'address' => 'N/A',
        'created_at' => 'N/A',
        'role' => 'N/A',
        'photo_path' => 'default-profile.jpg',
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Holder Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="viewProfile.css">
</head>

<body class="bg-light d-flex" >


<?php include('slide.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Profile Card -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white text-center">
                        <img src="<?= htmlspecialchars($post_holder['photo_path']) ?>" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3" 
                             style="width: 120px; height: 120px; object-fit: cover;">
                        <h2><?= htmlspecialchars($post_holder['name']) ?></h2>
                    </div>
                    <div class="card-body bg-white">
                        <h4 class="text-success mb-4">Profile Details</h4>
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th class="text-muted">Full Name:</th>
                                    <td><?= htmlspecialchars($post_holder['name']) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email:</th>
                                    <td><?= htmlspecialchars($post_holder['email']) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Phone:</th>
                                    <td><?= htmlspecialchars($post_holder['phone']) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Address:</th>
                                    <td><?= htmlspecialchars($post_holder['address']) ?></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Join Date:</th>
                                    <td><?= htmlspecialchars($post_holder['created_at']) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
