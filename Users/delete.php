<?php
// Database connection

// db_connection.php

$servername = "localhost"; // Your database host
$username = "root";        // Your database username
$password = "";            // Your database password
$dbname = "main_home_hunt"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if delete action is requested
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['house_id'])) {
        $house_id = (int)$_GET['house_id'];

        // Check if the house has any associated comments
        $stmt = $conn->prepare("SELECT comment_status FROM comments WHERE house_id = ?");
        $stmt->bind_param("i", $house_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If no comments are found, proceed with deletion
        if ($result->num_rows === 0) {
            // Proceed with deletion if no comments are present
            $stmt = $conn->prepare("DELETE FROM houses WHERE id = ?");
            $stmt->bind_param("i", $house_id);

            if ($stmt->execute()) {
                echo "<script>alert('House deleted successfully!'); window.location.href = 'homepage.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error deleting the house.'); window.history.back();</script>";
            }
        } else {
            // If there are comments, check their comment status
            $house = $result->fetch_assoc();
            if ($house['comment_status'] == '1') {
                // If comment status is 1, prevent deletion
                echo "<script>alert('You cannot delete this house because it has active comments.'); window.history.back();</script>";
            } else {
                // If comment status is not 1, proceed with deletion
                $stmt = $conn->prepare("DELETE FROM houses WHERE id = ?");
                $stmt->bind_param("i", $house_id);

                if ($stmt->execute()) {
                    echo "<script>alert('House deleted successfully!'); window.location.href = 'homepage.php';</script>";
                    exit;
                } else {
                    echo "<script>alert('Error deleting the house.'); window.history.back();</script>";
                }
            }
        }
        $stmt->close();
    }

    // Check if edit action is requested
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['house_id'])) {
        $house_id = (int)$_GET['house_id'];

        // Fetch house details for editing
        $stmt = $conn->prepare("SELECT * FROM houses WHERE house_id = ?");
        $stmt->bind_param("i", $house_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $house = $result->fetch_assoc();
            // Display an edit form pre-filled with house data
            ?>
            <form method="POST" action="delete&edit.php">
                <input type="hidden" name="house_id" value="<?= htmlspecialchars($house['house_id']) ?>">
                <label for="house_name">House Name:</label>
                <input type="text" id="house_name" name="house_name" value="<?= htmlspecialchars($house['house_name']) ?>" required>
                <label for="house_price">Price:</label>
                <input type="number" id="house_price" name="house_price" value="<?= htmlspecialchars($house['price']) ?>" required>
                <button type="submit" name="update" class="btn btn-primary">Update</button>
            </form>
            <?php
        } else {
            echo "<script>alert('House not found.'); window.history.back();</script>";
        }
        $stmt->close();
    }
}

