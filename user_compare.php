<?php
  require_once 'connect.php';

  session_start();

  // Check if the user is logged in, if not then redirect them to the login page
  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Get the session user ID
$user_id = $_SESSION["id"];

// Fetch vehicle IDs associated with the user from the user_comparisons table
$sql = "SELECT vehicle_id FROM user_comparisons WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an empty array to store vehicle IDs
$vehicle_ids = [];

if ($result->num_rows > 0) {
    // Store fetched vehicle IDs
    while ($row = $result->fetch_assoc()) {
        $vehicle_ids[] = $row["vehicle_id"];
    }
}
$stmt->close();

// Fetch vehicle details based on the fetched vehicle IDs
$vehicle_details = [];
foreach ($vehicle_ids as $vehicle_id) {
    $sql = "SELECT * FROM scrap WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $vehicle_details[] = $result->fetch_assoc();
    }
    $stmt->close();
}

// Close the database connection
$database->closeConnection();
  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Comparison Panel</title>


    <link rel="stylesheet" href="css/user_style.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
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
                    <a href="user_home.php">
                        <span class="icon">
                            <i class="fa-solid fa-house"></i>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="user_home_explore.php">
                        <span class="icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <span class="title">SEARCH</span>
                    </a>
                </li>

                <li>
                    <a href="user_compare.php">
                        <span class="icon">
                            <i class="fa-solid fa-code-compare"></i>
                        </span>
                        <span class="title">Compare</span>
                    </a>
                </li>

                <li>
                    <a href="user_recommendations.php">
                        <span class="icon">
                            <i class="fa-solid fa-car"></i>
                        </span>
                        <span class="title">RECOMMENDED</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <i class="fa-solid fa-gear"></i>
                        </span>
                        <span class="title">Settings</span>
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
                <p>WELCOME <?php echo $_SESSION["username"] ?></p>


            </div>

            <div class="main-content">
            <div class="categories_containerb">
    <div class="categories-hb">
        <h1>Vehicle Comparison Panel</h1>
    </div>
    <div class="categories-rowb">
        <?php foreach ($vehicle_details as $vehicle) : 
            $imageData = $vehicle["image_data"]; ?>
            <div class="categories-cardb">
                <div class="imageb">
                    <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
                </div>
                <div class="captionb">
                    <h3><?php echo htmlspecialchars($vehicle["title"] . " (" . $vehicle["Year"] . ")"); ?></h3>
                </div>
                <div>
                    <p><span>Price:</span> $<?php echo htmlspecialchars($vehicle["Price"]); ?></p>
                </div>
                <div>
                    <p><span>Engine:</span> <?php echo htmlspecialchars($vehicle["Engine"]); ?></p>
                </div>
                <div>
                    <p><span>Mileage:</span> <?php echo htmlspecialchars($vehicle["Transmission"]); ?> MPG</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
                
            
    

            </div>


            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/main.js"></script>

    
</body>

</html>