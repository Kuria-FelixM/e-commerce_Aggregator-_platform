<?php
  require_once 'connect.php';

 // Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_user'])) {
        // Add user
        $new_username = trim($_POST['username']);
        $new_email = trim($_POST['email']);
        $new_password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $new_username, $new_email, $new_password);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['edit_user'])) {
        // Edit user
        $id = intval($_POST['id']);
        $edit_username = trim($_POST['username']);
        $edit_email = trim($_POST['email']);
        $edit_password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

        $sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $edit_username, $edit_email, $edit_password, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_user'])) {
      
        // Delete user
        $id = intval($_POST['id']);
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch users
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);
 ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <link rel="stylesheet" href="css/user_style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .containerm {
            width: 80%;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="title">USER</span>
                    </a>
                </li>

                <li>
                    <a href="admin_home.php">
                        <span class="icon">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="title">HOME</span>
                    </a>
                </li>

                <li>
                    <a href="admin_vehicle.php">
                        <span class="icon">
                            <i class="fa-solid fa-car"></i>
                        </span>
                        <span class="title">VEHICLES</span>
                    </a>
                </li>



                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
                


            </div>

            <div class="main-content">
            <div class="containerm">
        <h2>Admin Panel</h2>

        <h3>Add New User</h3>
        <form method="post" action="">
            <input type="hidden" name="add_user" value="1">
            <label for="add_username">Username:</label><br>
            <input type="text" id="add_username" name="username" required><br><br>
            <label for="add_email">Email:</label><br>
            <input type="email" id="add_email" name="email" required><br><br>
            <label for="add_password">Password:</label><br>
            <input type="password" id="add_password" name="password" required><br><br>
            <input type="submit" value="Add User">
        </form>

        <h3>Users List</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <!-- Edit User Form -->
                    <form style="display:inline;" method="post" action="">
                        <input type="hidden" name="edit_user" value="1">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                        <input type="password" name="password" placeholder="New Password" required>
                        <input type="submit" value="Update">
                    </form>
                    <!-- Delete User Form -->
                    <form style="display:inline;" method="post" action="">
                        <input type="hidden" name="delete_user" value="1">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                        <input type="submit" value="Delete" onclick="return confirm('Are you sure?')">
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

            </div>


            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/main.js"></script>

    
</body>

</html>

<?php
// Close the database connection
$database->closeConnection();
?>
