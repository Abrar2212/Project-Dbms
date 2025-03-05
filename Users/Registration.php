<?php
include 'connect.php';

if (isset($_POST['submit'])) {
    // Retrieve data from POST request
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $dob = isset($_POST['dob']) ? $_POST['dob'] : '';
    $photo = isset($_FILES['photo_path']) ? $_FILES['photo_path'] : null;

    // Validate the date of birth (must be greater than 16 years old)
    $dob_date = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dob_date)->y;

    if ($age < 16) {
        $error_message = "You must be at least 16 years old.";
    }

    // Validate photo upload
    if ($photo && $photo['error'] === 0) {
        $upload_dir = 'uploads/';
        $photo_name = $photo['name'];
        $photo_tmp_name = $photo['tmp_name'];
        $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        // Check file type
        if (!in_array($photo_ext, $allowed_exts)) {
            $error_message = "Only JPG, JPEG, PNG, and GIF images are allowed.";
        } else {
            $photo_path = $upload_dir . basename($photo_name);
            move_uploaded_file($photo_tmp_name, $photo_path); // Save the photo
        }
    }

    // Check if all required fields are filled
    if (!empty($name) && !empty($email) && !empty($password) && !empty($role) && !empty($dob) && isset($photo_path) && !empty($phone) && !empty($address)) {
        // Check if the role exists
        $role_query = "SELECT id FROM roles WHERE role_name = '$role'";
        $role_result = mysqli_query($con, $role_query);

        if (!$role_result) {
            die("Error in role query: " . mysqli_error($con));
        }

        $role_row = mysqli_fetch_assoc($role_result);

        if ($role_row) {
            // Role exists, get the role ID
            $role_id = $role_row['id'];
        } else {
            // Role does not exist, insert it into the roles table
            $insert_role_query = "INSERT INTO roles (role_name) VALUES ('$role')";
            $insert_role_result = mysqli_query($con, $insert_role_query);

            if (!$insert_role_result) {
                die("Error inserting new role: " . mysqli_error($con));
            }

            $role_id = mysqli_insert_id($con); // Get the ID of the newly inserted role
        }

        // Insert data into the `users` table
        $user_query = "INSERT INTO `users` (name, email, password, dob, phone, address, role_id) 
                       VALUES ('$name', '$email', '$password', '$dob', '$phone', '$address', $role_id)";
        $user_result = mysqli_query($con, $user_query);

        if ($user_result) {
            $user_id = mysqli_insert_id($con);

            // Insert photo into `user_photos` table
            $query = "INSERT INTO user_photos (user_id, photo_path, type) VALUES ('$user_id', '$photo_path', 'profile')";
            $photo_result = mysqli_query($con, $query);

            if (!$photo_result) {
                die("Error inserting into user_photos table: " . mysqli_error($con));
            }

            header('location:login.php'); // Redirect to login page on success
            exit;
        } else {
            die("Error inserting into users table: " . mysqli_error($con));
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="signup_styles.css">
</head>
<body>
  <div class="container">
    <div class="signup-section">
      <h1>Sign Up</h1>
      <p>Create your Account</p>
      <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
      <?php endif; ?>
      <form method="POST" action="" enctype="multipart/form-data">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" placeholder="Your Name" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Your Email" required>

    <label for="phone">Phone Number</label>
<input type="text" id="phone" name="phone" placeholder="Your Phone Number" required>

<label for="address">Address</label>
<input  id="address" name="address" placeholder="Your Address" required>


    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Your Password" required>

    <label for="dob">Date of Birth</label>
    <input type="date" id="dob" name="dob" required>

    <label for="photo">Profile Photo</label>
    <input type="file" id="photo" name="photo_path" accept="image/*" required>

    <label for="role">Select User</label>
    <select id="role" name="role" required>
        <option value="" disabled selected>Select</option>
        <option value="owner">Owner</option>
        <option value="customer">Customer</option>
    </select>

    <button type="submit" name="submit" class="register-btn">Register</button>
</form>

      <p class="terms">
        By clicking Register, you agree to our<br> <a href="#" class="terms-link">Terms and Data Policy</a>
      </p>
    </div>
    <div class="image-section">
      <img src="Signup photo.jpg" alt="Signup">
    </div>
  </div>
</body>
</html>
