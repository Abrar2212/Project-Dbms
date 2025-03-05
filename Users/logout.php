<?php
// Start the session (if not already started)
session_start();

// Delete specific cookies
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/'); // Replace 'user_id' with your cookie name
}

if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/');
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login or homepage
header("Location:homepagebeforelogin.php");
exit();
?>

