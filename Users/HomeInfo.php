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

// Get the current user's role
$role_query = "SELECT role_name FROM users
JOIN roles ON users.role_id = roles.id
WHERE users.id = ?";
$stmt = $con->prepare($role_query);
$stmt->bind_param('i', $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_role = $result->fetch_assoc();

// Initialize the roles array
$roles = [];

// Check the role and run the corresponding SQL
if ($user_role && $user_role['role_name'] === 'Owner') {
    // Query for owners
    $sql = "SELECT 
        users.name AS user_name,
        enrollments.user_id,
        user_photos.photo_path,
        users.email AS user_email,
          houses.floors,
        locations.address AS house_address,
        users.created_at 
    FROM enrollments
    JOIN users ON enrollments.user_id = users.id
    JOIN user_photos ON user_photos.user_id = users.id
    JOIN houses ON enrollments.home_id = houses.id
    JOIN locations ON houses.location_id = locations.id
    WHERE 
        enrollments.owner_id = ? AND enrollments.status = 'approved'";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $current_user_id);
    
} else {
    // Query for customers
    $sql = "SELECT 
    enrollments.owner_id as user_id,
        users.name AS owner_name,
        houses.name AS house_title,
        houses.floors,
        user_photos.photo_path AS photo_path,
        users.email AS user_email,
        locations.address AS house_address,
        houses.created_at 
    FROM enrollments
    JOIN users ON enrollments.owner_id = users.id
    JOIN houses ON enrollments.home_id = houses.id
    JOIN user_photos ON users.id=user_photos.user_id
    JOIN locations ON houses.location_id = locations.id
    WHERE 
        enrollments.user_id = ? AND enrollments.status = 'approved'
        GROUP BY owner_name";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $current_user_id);
    
}

// Execute the query and fetch results


$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
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
    <style>
        .slidebar {
            width: 400px;
            height: 100vh;
            background-color: #036e40;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .main-content {
         
            padding: 20px;
        }

        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="fullpage">
        <!-- Sidebar -->
        <?php include('slide.php'); ?>

        <!-- Main Content -->
        <div class="main-content container mt-5">
            <div class="row">
                <!-- Renters or Houses Section -->
                <div class="col-12">
                    <div class="row g-4">
                        <?php foreach ($roles as $role) : ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="row g-0">
                                        <div class="col-12">
                                            <?php
                                            $imagePath = !empty($role['photo_path']) && file_exists($role['photo_path'])
                                                ? htmlspecialchars($role['photo_path'])
                                                : 'default-placeholder.png';
                                            ?>
                                            <img src="<?= $imagePath ?>" 
                                                 class="img-fluid rounded-top" 
                                                 alt="User Image" 
                                                 style="height: 200px; width: 100%; object-fit: cover;">
                                        </div>
                                        <div class="col-12">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <?= htmlspecialchars($role['user_name'] ?? $role['owner_name']); ?>
                                                </h5>
                                                <p class="card-text">
                                                    <strong>Email:</strong> <?= htmlspecialchars($role['user_email']); ?>
                                                </p>
                                                <p class="card-text">
                                                    <strong>House Address:</strong> <?= htmlspecialchars($role['house_address']); ?>
                                                </p>
                                                <p class="card-text">
                                                    <strong>floor:</strong> <?= htmlspecialchars($role['floors']); ?>
                                                </p>
                                                <p class="card-text">
                                                    <small class="text-muted"><?= htmlspecialchars($role['created_at']); ?></small>
                                                </p>
                                                <button class="btn btn-primary w-100 mt-2" 
                                                        onclick="location.href='viewProfile.php?id=<?= htmlspecialchars($role['user_id'] ?? ''); ?>';">
                                                    View Profile
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>
