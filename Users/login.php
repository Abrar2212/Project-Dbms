<?php
session_start();
include 'connect.php'; // Include the database connection file

// Prevent caching of the page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

if (isset($_POST['submit'])) {
    // Retrieve data from POST request
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $password = $_POST['password'];

    // Validate if name and password are not empty
    if (!empty($name) && !empty($password)) {
        // Query to check if user exists in the database
        $sql = "SELECT * FROM `users` WHERE name='$name'";
        $result = mysqli_query($con, $sql);

        if ($result) {
          $row = mysqli_fetch_assoc($result);
      
          // Ensure the row is not null
          if ($row) {
              // Check if password matches (plain text comparison)
              if ($password == $row['password']) {
                  // Start a session for the logged-in user
                  $_SESSION['user_id'] = $row['id'];
                  $_SESSION['user_name'] = $row['name'];
                  header('location: homepage.php'); // Redirect to the dashboard after successful login
                  exit;
              } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User not found.";
        }
    } else {
        $error_message = "Please fill in both fields.";
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="Login_styles.css">
</head>

<body>
  <div class="container">
    <div class="Login-section">

      <h2>Login to your Account</h2>
      <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your Name" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your Password" required>

        <button type="submit" name="submit" class="Login-btn">Login</button>
      </form>

      <p class="forget">Forget Password?</p>

      <p class="if-no-account">Don't have an account? <a href="Registration.php" class="sign">Signup</a></p>

    </div>
    <div class="image-section">
      <img src="Login page.png" alt="Login">
    </div>
  </div>
</body>

</html>
