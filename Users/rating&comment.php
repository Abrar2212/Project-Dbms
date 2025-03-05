<?php
include 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start(); // Start output buffering

// Extract user_id from the session
$user_id = $_SESSION['user_id'] ?? null;

// Debugging the IDs


// Define the safe_redirect function
function safe_redirect($url)
{
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        // Fallback to JavaScript redirect if headers are already sent
        echo "<script>window.location.href = '$url';</script>";
        exit();
    }
}

// Ensure `house_id` is passed as a query parameter
$house_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($house_id === 0) {
    die("Invalid house ID.");
}

// Initialize variables
$rating_stars = 0;
$comments_result = null;

// Fetch the average rating
$rating_query = "SELECT AVG(rating) AS avg_rating FROM ratings WHERE house_id = ?";
$stmt = $con->prepare($rating_query);
$stmt->bind_param('i', $house_id);
$stmt->execute();
$rating_result = $stmt->get_result();

if ($rating_result && $rating_result->num_rows > 0) {
    $rating_row = $rating_result->fetch_assoc();
    $rating = $rating_row['avg_rating'] ?? 0;
    $rating_stars = round($rating, 1);
} else {
    $rating = 0;
    $rating_stars = "Not rated yet";
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user_id) {
        die("You must be logged in to rate or comment.");
    }

    // Handle ratings
    if (isset($_POST['rating']) && !empty($_POST['rating'])) {
        $rating = intval($_POST['rating']);
        $sql = "INSERT INTO ratings (user_id, house_id, rating) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE rating = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('iiii', $user_id, $house_id, $rating, $rating);
        if (!$stmt->execute()) {
            die("Error inserting rating: " . $stmt->error);
        }
    }

    // Handle comments
    if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
        $comment = trim($_POST['comment']); // Sanitize input
        $sql = "INSERT INTO comments (user_id, house_id, comment, comment_status) VALUES (?, ?, ?, 1)
                ON DUPLICATE KEY UPDATE comment_status = 1, comment = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('iiss', $user_id, $house_id, $comment, $comment);
        if (!$stmt->execute()) {
            die("Error inserting comment: " . $stmt->error);
        }
        safe_redirect("post.php?id=" . $house_id);
    }

    // Redirect back to the page
    safe_redirect("post.php?id=" . $house_id);
}

// Handle delete comment
if (isset($_GET['delete_id']) && $user_id) {
    $delete_id = intval($_GET['delete_id']);

    // Delete only if the comment belongs to the logged-in user
    $delete_query = "DELETE FROM comments WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($delete_query);
    $stmt->bind_param('ii', $delete_id, $user_id);
    $stmt->execute();

    // Redirect back to the page
    safe_redirect("post.php?id=" . $house_id);
}

// Fetch comments for the house
$comments_query = "SELECT comments.id, comments.comment, comments.created_at, users.name, ratings.rating, comments.user_id,comments.comment_status,enrollments.status as e_status
                   FROM comments
                   JOIN users ON comments.user_id = users.id
                   JOIN enrollments on users.id=enrollments.user_id
                   LEFT JOIN ratings ON comments.user_id = ratings.user_id AND comments.house_id = ratings.house_id
                   WHERE comments.house_id = ? group by user_id ORDER BY comments.created_at DESC";
$stmt = $con->prepare($comments_query);
$stmt->bind_param('i', $house_id);
$stmt->execute();
$comments_result = $stmt->get_result();

$sql = "SELECT enrollments.status AS e_status 
        FROM enrollments 
        WHERE enrollments.user_id = ? 
        AND enrollments.home_id = ? 
        AND enrollments.status = 'approved'";

// Prepare and bind the statement
$stmt = $con->prepare($sql);
$stmt->bind_param('ii', $user_id, $house_id);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

// Check if the status exists and is approved
$status = false; // Default status
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['e_status'] === 'approved') {
        $status = true; // Set status to true if approved
    }
}


// Check if the user has already commented on the house
$comment_status_query = "SELECT comment_status FROM comments WHERE user_id = ? AND house_id = ?";
$stmt = $con->prepare($comment_status_query);
$stmt->bind_param('ii', $user_id, $house_id);
$stmt->execute();
$status_result = $stmt->get_result();
$has_commented = ($status_result->num_rows > 0 && $status_result->fetch_assoc()['comment_status'] == 1);


$enroll = "SELECT enrollments.user_id AS renter 
           FROM enrollments 
           JOIN users ON users.id = enrollments.user_id 
           WHERE users.id = ? AND home_id = ?";
$stmt = $con->prepare($enroll);

// Bind the parameters
$stmt->bind_param('ii', $user_id, $house_id);

// Execute the query
$stmt->execute();

// Fetch the result
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $enrollment_row = $result->fetch_assoc();
   $enrollment_id = $enrollment_row['renter']; // Fetch 'renter' (user_id) from the query
} else {
    $enrollment_id = null; // Handle case where no match is found
}


// Fetch the owner_id for the house
$owner_query = "SELECT user_id FROM houses WHERE id = ?";
$stmt = $con->prepare($owner_query);
$stmt->bind_param('i', $house_id);
$stmt->execute();
$owner_result = $stmt->get_result();

// If the house exists and the owner_id is fetched
if ($owner_result && $owner_result->num_rows > 0) {
    $owner_row = $owner_result->fetch_assoc();
    $owner_id = $owner_row['user_id'];
} else {
    // Handle error if house doesn't exist
    die("House not found.");
}

?>

<!-- HTML starts here -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .comment-section {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        background-color: #f9f9f9;
    }

    .comment-box {
        border-bottom: 1px solid #ddd;
        padding: 10px 0;
    }

    .comment-box:last-child {
        border-bottom: none;
    }

    .comment-box small {
        color: #666;
    }

    .btn-delete {
        float: right;
        color: #ff4d4d;
        border: none;
        background: none;
        cursor: pointer;
        padding: 0;
    }

    .btn-delete:hover {
        color: #d9534f;
    }
</style>

<div class="container mt-4">
    <div class="row">
        <!-- Rating and Comment Form Section -->
        <?php if (!$has_commented && $status && isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] !== (int)$owner_id && (int)$user_id ===(int) $enrollment_id): ?>

            <div class="col-md-6">
                <h5>Rate and Comment</h5>

                <!-- Display Average Rating -->
                <div class="mb-3">
                    <h6>Average Rating:
                        <span class="text-warning">
                            <?= $rating_stars > 0 ? round($rating_stars, 1) . " &#9733;" : "Not rated yet"; ?>
                        </span>
                    </h6>
                </div>

                <form action="post.php?id=<?= $house_id; ?>" method="POST">
                    <!-- Rating Dropdown -->
                    <label for="rating" class="form-label">Rate this house:</label>
                    <select name="rating" id="rating" class="form-select form-select-sm mt-2">
                        <option value="">Select a rating (optional)</option>
                        <option value="1">1 &#9733;</option>
                        <option value="2">2 &#9733;&#9733;</option>
                        <option value="3">3 &#9733;&#9733;&#9733;</option>
                        <option value="4">4 &#9733;&#9733;&#9733;&#9733;</option>
                        <option value="5">5 &#9733;&#9733;&#9733;&#9733;&#9733;</option>
                    </select>

                    <!-- Comment Textarea -->
                    <label for="comment" class="form-label mt-3">Leave a comment:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Add a comment..."></textarea>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm mt-3 w-100">Submit</button>
                </form>
            </div>
        <?php else: ?>
            <span class="text-warning">
                            <?= $rating_stars > 0 ? round($rating_stars, 1) . " &#9733;" : "Not rated yet"; ?>
                        </span>
        <?php endif; ?>

        <!-- Display Comments Section -->
        <div class="col-md-6">
            <h5>Comments</h5>
            <div class="comment-section">
                <?php if ($comments_result->num_rows > 0): ?>
                    <?php while ($comment = $comments_result->fetch_assoc()): ?>
                        <div class="comment-box mb-3">
                            <p class="mb-1">
                                <?= htmlspecialchars($comment['comment']); ?>
                                <?php if ($comment['rating']): ?>
                                    <span class="text-warning">(<?= $comment['rating']; ?> &#9733;)</span>
                                <?php endif; ?>
                            </p>
                            <small class="text-muted">
                                <?= htmlspecialchars($comment['name']); ?>,
                                <?= date("F j, Y, g:i a", strtotime($comment['created_at'])); ?>
                            </small>

                            <!-- Delete Button -->
                            <?php if ($user_id !== $owner_id && isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                                <form method="GET" action="" style="display:inline-block;">
                                    <input type="hidden" name="id" value="<?= $house_id; ?>">
                                    <input type="hidden" name="delete_id" value="<?= $comment['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm mt-1">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>