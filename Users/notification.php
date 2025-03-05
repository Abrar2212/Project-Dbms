<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "main_home_hunt";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Start session if not already started

// Assuming user ID is stored in session after login
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    echo "<div class='alert alert-danger'>User not logged in.</div>";
    exit;
}

// Fetch user role from the database
$stmt = $conn->prepare("SELECT role_name FROM users 
    JOIN roles ON users.role_id = roles.id 
    WHERE users.id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Role not found for the user.</div>";
    exit;
}

$userRole = $result->fetch_assoc()['role_name']; // 'owner' or 'customer'
$stmt->close();

$enrollments = null; // Initialize enrollments variable
$successMessage = '';
$errorMessage = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollmentId = intval($_POST['enrollment_id'] ?? 0);
    $action = $_POST['action'] ?? null;

    if ($enrollmentId && in_array($action, ['approve', 'reject', 'delete'])) {
        if ($action === 'delete') {
            // Update active_status to 1 and delete the enrollment
            $stmt = $conn->prepare("UPDATE houses 
                                    INNER JOIN enrollments ON houses.id = enrollments.home_id
                                    SET houses.active_status = '1' 
                                    WHERE enrollments.id = ?");
            $stmt->bind_param("i", $enrollmentId);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
            $stmt->bind_param("i", $enrollmentId);
        } elseif ($action === 'approve') {
            // Update active_status to 0 and set enrollment status to 'approved'
            $stmt = $conn->prepare("UPDATE houses 
                                    INNER JOIN enrollments ON houses.id = enrollments.home_id
                                    SET houses.active_status = '0' 
                                    WHERE enrollments.id = ?");
            $stmt->bind_param("i", $enrollmentId);
            $stmt->execute();
            $stmt->close();

            $status = 'approved';
            $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $enrollmentId);
        } else {
            // Reject the enrollment (no active_status update)
            $status = 'rejected';
            $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $enrollmentId);
        }

        if ($stmt->execute()) {
            $successMessage = "Enrollment has been successfully processed.";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $errorMessage = "Invalid input. Please try again.";
    }
}

// Fetch enrollments based on role
if (strtolower($userRole) === 'owner') {
    $stmt = $conn->prepare("SELECT enrollments.id, enrollments.status, users.name AS user_name, houses.name AS home_name
        FROM enrollments 
        JOIN users ON enrollments.user_id = users.id
        JOIN houses ON enrollments.home_id = houses.id
        WHERE enrollments.owner_id = ?");
    $stmt->bind_param("i", $userId);

    $stmt->execute();
    $enrollments = $stmt->get_result();
    $stmt->close();
} elseif (strtolower($userRole) === 'customer') {
    $stmt = $conn->prepare("SELECT users.name AS user_name, houses.name AS home_name, enrollments.status AS status
        FROM enrollments
        JOIN users ON users.id = enrollments.owner_id
        JOIN houses ON enrollments.home_id = houses.id
        WHERE enrollments.user_id = ?");
    $stmt->bind_param("i", $userId);

    $stmt->execute();
    $enrollments = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Enrollments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="fullpage d-flex">
        <?php include('slide.php'); ?>

        <div class="container mt-5">
            <!-- Success or Error Messages -->
            <?php if (!empty($successMessage)) { ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage); ?></div>
            <?php } ?>

            <?php if (!empty($errorMessage)) { ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorMessage); ?></div>
            <?php } ?>

            <?php if (strtolower($userRole) === 'owner') { ?>
                <h2 class="mb-4">All Enrollments</h2>
                <?php if ($enrollments && $enrollments->num_rows > 0) { ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>User</th>
                                <th>Home</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $enrollments->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['user_name']); ?></td>
                                    <td><?= htmlspecialchars($row['home_name']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'pending') {
                                            echo 'Pending';
                                        } elseif ($row['status'] === 'approved') {
                                            echo 'Approved';
                                        } elseif ($row['status'] === 'rejected') {
                                            echo 'Rejected';
                                        } ?>
                                    </td>
                                    <td>
                                        <form method="POST">
                                            <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($row['id']); ?>">
                                            <?php if ($row['status'] === 'pending') { ?>
                                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                            <?php } ?>
                                            <button type="submit" name="action" value="delete" class="btn btn-warning btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info">You have no enrollments.</div>
                <?php } ?>

            <?php } elseif (strtolower($userRole) === 'customer') { ?>
                <h2 class="mb-4">My Enrollments</h2>
                <?php if ($enrollments && $enrollments->num_rows > 0) { ?>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Owner</th>
                                <th>Home</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $enrollments->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['user_name']); ?></td>
                                    <td><?= htmlspecialchars($row['home_name']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'approved') { ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php } elseif ($row['status'] === 'rejected') { ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-info">You have no enrollments.</div>
                <?php } ?>
            <?php } else { ?>
                <div class="alert alert-danger">Invalid role.</div>
            <?php } ?>
        </div>
    </div>

    <!-- Bootstrap JS (Optional, for enhanced UI interactions) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
