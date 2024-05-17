<?php
require_once 'connect.php';


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are set
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Retrieve values from the form
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL statement to check user credentials using the username
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);

        // Execute SQL statement
        $stmt->execute();

        // Store result
        $result = $stmt->get_result();

        // Check if the query returned a row
        if ($result->num_rows > 0) {
            // User found, verify password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                            // Password is correct, start a new session
                            session_start();

                         // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $user['id'];
                            $_SESSION["username"] = $username;

                            // Redirect user to home page
                            header("location: user_home.php");
            } else {
                // Invalid password
                $error = "Invalid username or password. Please try again.";
            }
        } else {
            // User not found
            $error = "User not found. Please sign up.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // Form fields not set
        $error = "Username and password are required.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggregator Website</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="login-header">
                <h1 class="login-title">Login</h1>
                <p class="login-description">Enter your username and password below to login to your account</p>
            </div>
            <!-- Form for login -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="login-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" placeholder="Your username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" required>
                    </div>
                    <!-- Submit button for login -->
                    <button type="submit" class="btn-login">Login</button>
                    <!-- Error message -->
                    <?php if (isset($error)) { ?>
                        <div class="error"><?php echo $error; ?></div>
                    <?php } ?>
                </div>
            </form>
            <!-- End of login form -->
            <div class="login-footer">
                <p>Don't have an account? <a href="signup.php" class="signup-link">Sign up</a></p>
            </div>
        </div>
    </div>
    
</body>
</html>
