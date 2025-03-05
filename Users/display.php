<?php
// This is an example of an operation that could be successful, like updating a database.
// For simplicity, we simulate success with a boolean variable.

$operation_successful = true; // Simulate that an operation (like inserting data into the database) was successful.

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Operation Success</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .message-box {
      background-color: #d4edda;
      color: #155724;
      padding: 20px;
      border-radius: 5px;
      border: 1px solid #c3e6cb;
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    .message-box h2 {
      margin: 0;
    }

    .message-box p {
      font-size: 16px;
    }

    .homepage-link {
      display: inline-block;
      margin-top: 10px;
      text-decoration: none;
      font-weight: bold;
      color: #007bff;
      border: 1px solid #007bff;
      padding: 5px 10px;
      border-radius: 3px;
      transition: background-color 0.3s, color 0.3s;
    }

    .homepage-link:hover {
      background-color: #007bff;
      color: #fff;
    }
  </style>
</head>

<body>

  <?php if ($operation_successful): ?>
    <div class="message-box">
      <h2>Operation Successful</h2>
      <p>Your operation has been completed successfully!</p>
      <a href="homepage.php" class="homepage-link">Click here to go to the homepage</a>
    </div>
  <?php else: ?>
    <div class="message-box" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
      <h2>Operation Failed</h2>
      <p>There was an error while performing the operation. Please try again.</p>
      <a href="homepage.php" class="homepage-link">Click here to go to the homepage</a>
    </div>
  <?php endif; ?>

</body>

</html>
