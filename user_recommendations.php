<?php
  require_once 'connect.php';

  session_start();

  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}


// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Get the session user ID
$user_id = $_SESSION["id"];

// Fetch recommended titles for the user from the recommendations table
$sql_recommendations = "SELECT title FROM recommendations WHERE user_id = ?";
$stmt = $conn->prepare($sql_recommendations);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_recommendations = $stmt->get_result();

$recommended_titles = [];
while ($row = $result_recommendations->fetch_assoc()) {
    $recommended_titles[] = $row['title'];
}
$stmt->close();

$vehicle_details = [];
if (!empty($recommended_titles)) {
    // Prepare the SQL query to fetch vehicle details from the scraped table
    $placeholders = implode(',', array_fill(0, count($recommended_titles), '?'));
    $types = str_repeat('s', count($recommended_titles));
    $sql_vehicle_details = "SELECT * FROM scrap WHERE title IN ($placeholders)";
    $stmt_vehicle_details = $conn->prepare($sql_vehicle_details);
    $stmt_vehicle_details->bind_param($types, ...$recommended_titles);
    $stmt_vehicle_details->execute();
    $result_vehicle_details = $stmt_vehicle_details->get_result();

    // Fetch vehicle details
    while ($row = $result_vehicle_details->fetch_assoc()) {
        $vehicle_details[] = $row;
    }
    $stmt_vehicle_details->close();
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
    <title>Vehicle Recommendations Panel</title>
    

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
            <div class="categories_container">
    <div class="categories-h">
        <h1>Vehicle Recommendations Panel</h1>
    </div>
    <div class="categories-row">
        <?php if (!empty($vehicle_details)) : ?>
            <?php foreach ($vehicle_details as $vehicle) : 
                $imageData = $vehicle["image_data"]; ?>
                <div class="categories-card">
                    <div class="image">
                        <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
                    </div>
                    <div class="caption">
                        <h3><?php echo htmlspecialchars($vehicle["title"] . " (" . htmlspecialchars($vehicle["Year"]) . ")"); ?></h3>
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
        <?php else : ?>
            <p>No recommendations found for this user.</p>
        <?php endif; ?>
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