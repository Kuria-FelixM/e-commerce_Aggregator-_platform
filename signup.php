
<?php
require_once 'connect.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $retypePassword = $_POST['retype_password'];

    // Validate form data (you can add more validation if needed)

    // Check if passwords match
    if ($password !== $retypePassword) {
        echo "Passwords do not match. Please try again.";
        exit; // Stop further execution
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert user data into the database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    // Execute the prepared statement
    if ($stmt->execute()) {
        // User inserted successfully
        header("Location: login.php"); // Redirect to login page
        exit; // Stop further execution
    } else {
        // Error occurred while inserting user
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>




<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggregator Website</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="signup-container">

        <div class="signup-content">
            <div class="signup-header">
                <h1 class="signup-title">Create an Account</h1>
                <p class="signup-description">Enter your information to create an account</p>
            </div>
            <form action="signup.php" method="post">
                <div class="signup-form">
                    <div class="form-group">
                       <label for="username">Username</label>
                       <input id="username" name="username" type="text" placeholder="Enter your username">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="Enter your email">
                    </div>
                    <div class="form-group">
                       <label for="password">Password</label>
                       <input id="password" name="password" type="password" placeholder="Enter your password">
                    </div>
                    <div class="form-group">
                       <label for="retype-password">Retype Password</label>
                       <input id="retype-password" name="retype_password" type="password" placeholder="Retype your password">
                    </div>
                    <button type="submit" class="btn-signup">Sign Up</button>
                </div>
            </form>

                <div class="signup-footer">
                    <p>Already have an account? <a href="login.php" class="signin-link">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
    
</body>