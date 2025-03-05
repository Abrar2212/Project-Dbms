<?php

session_start();
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



// Fetch user data from the database
$query =  "SELECT 
            users.name, 
            users.email, 
            users.dob, 
            user_photos.photo_path,
            users.password,
            users.role_id,
            roles.role_name
          FROM 
            users 
          JOIN 
            roles ON users.role_id = roles.id 
            JOIN
            user_photos ON users.id=user_photos.user_id
          WHERE 
            users.id = $user_id";
$result = mysqli_query($con, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $name = $row['name'];
    $email = $row['email'];
    $dob = $row['dob'];
    $password = $row['password'];
    $photo = $row['photo_path'];
    $role = $row['role_id'];
} else {
    $name = $email = $mobile = $password = ""; // Set default empty values to avoid undefined warnings
}





// Handle form submission for editing profile
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    

    // Handle photo upload
    $photo_name = "";
    if (!empty($_FILES['photo_path']['name'])) {
        $photo_name = basename($_FILES['photo_path']['name']);
        $photo_path = 'uploads/' . $photo_name;

        // Move the uploaded file to the designated folder
        if (!move_uploaded_file($_FILES['photo_path']['tmp_name'], $photo_path)) {
            $error_message = "Failed to upload photo. Please try again.";
        }
    }

    // Prepare the update query
    $update_query = "UPDATE users SET 
        name = '$name', 
        email = '$email', 
        dob = '$dob', 
        password = '$password'";

    // Include the photo field if a new photo is uploaded
    if (mysqli_query($con, $update_query)) {
        // If photo is uploaded, update the photo_path in user_photos table
        if (!empty($photo_name)) {
            $update_photo_query = "UPDATE user_photos SET 
                                    photo_path = '$photo_path' 
                                    WHERE user_id = $user_id";
            mysqli_query($con, $update_photo_query);
        }
        header('Location: UserProfile.php');
        exit();
    } else {
        $error_message = "There was an error updating your profile. Please try again.";
    }

    $update_query .= " WHERE id = $user_id";

    // Execute the query
    if (mysqli_query($con, $update_query)) {
        header('Location: UserProfile.php');
        exit();
    } else {
        $error_message = "There was an error updating your profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Info</title>
    <link rel="stylesheet" href="update_profile_styles.css">
</head>

<body>
    <div class="container">
        <div class="edit-section">
            <h1>Edit Info</h1>
            <p>Update your Account Details</p>
            <?php if (isset($success_message)) : ?>
                <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)) : ?>
                <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Your Name" value="<?php echo $name; ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Your Email" value="<?php echo $email; ?>">


                <label for="password">New Password</label>
                <input type="password" id="password" name="password" placeholder="Enter New Password" value="<?php echo $password; ?>">

                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob"value="<?php echo $dob; ?>">

                <label for="photo">Profile Photo</label>
                <input type="file" id="photo_path" name="photo_path" accept="image/*">

                <button type="submit" name="update" class="update-btn">Update</button>
            </form>

            <p class="terms">
                By clicking Update, you agree to our<br> <a href="#" class="terms-link">Terms and Data Policy</a>
            </p>
        </div>
    </div>
</body>

</html>