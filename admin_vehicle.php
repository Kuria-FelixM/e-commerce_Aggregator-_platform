<?php
  require_once 'connect.php';

 // Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// Handle form submissions for CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $image_data = file_get_contents($_FILES['image_data']['tmp_name']);
        
        $stmt = $conn->prepare("INSERT INTO scrap (title, description, image_data) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $image_data);
        $stmt->execute();
        $stmt->close();
        
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        
        if ($_FILES['image_data']['tmp_name']) {
            $image_data = file_get_contents($_FILES['image_data']['tmp_name']);
            $stmt = $conn->prepare("UPDATE scrap SET title = ?, description = ?, image_data = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $image_data, $id);
        } else {
            $stmt = $conn->prepare("UPDATE scrap SET title = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $id);
        }
        
        $stmt->execute();
        $stmt->close();
        
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM scrap WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all records from the scrap table
$results = $conn->query("SELECT * FROM scrap");
 ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Entries Management</title>

    <link rel="stylesheet" href="css/user_style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        .containerm { width: 80%; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
        form { margin: 20px 0; }
        input, textarea { width: 100%; padding: 8px; margin: 4px 0; }
        button { padding: 8px 16px; }
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
    <h1>Entries Management</h1>
    
    <!-- Add New Scrap Entry -->
    <h2>Add New Scrap Entry</h2>
    <form action="admin_scrap.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="add" value="1">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="image_data" required>
        <button type="submit">Add</button>
    </form>

    <!-- Update or Delete Existing Scrap Entry -->
    <h2>Existing Scrap Entries</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['Transmission']); ?></td>
                    <td><img src="data:image/jpeg;base64,<?php echo base64_encode($row['image_data']); ?>" alt="" width="100"></td>
                    <td>
                        <form action="admin_scrap.php" method="post" enctype="multipart/form-data" style="display:inline-block;">
                            <input type="hidden" name="update" value="1">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                            <textarea name="description" required><?php echo htmlspecialchars($row['Price']); ?></textarea>
                            <input type="file" name="image_data">
                            <button type="submit">Update</button>
                        </form>
                        <form action="admin_scrap.php" method="post" style="display:inline-block;">
                            <input type="hidden" name="delete" value="1">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
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
$conn->close();
?>
