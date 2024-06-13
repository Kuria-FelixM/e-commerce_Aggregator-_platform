<?php
require_once 'connect.php';

session_start();

// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$id = $_SESSION["id"];

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Fetch all vehicle data from the 'scraped' table
$sql_all_vehicles = "SELECT * FROM scraped";
$result_all_vehicles = $conn->query($sql_all_vehicles);

if ($result_all_vehicles !== false && $result_all_vehicles->num_rows > 0) {
    // Initialize an array to store all vehicle data
    $all_vehicles_data = array();

    // Fetch all vehicle data and store in the array
    while ($row = $result_all_vehicles->fetch_assoc()) {
        $all_vehicles_data[] = array(
            'title' => $row['title'],
            'Year' => $row['Year'],
            'Price' => $row['Price']
            // Add other relevant columns as needed
        );
    }

    if (isset($_GET['title'])) {
        $productTitle = $_GET['title'];

        // Fetch product details from database using $productTitle
        $sql = "SELECT * FROM scrap WHERE title = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productTitle);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result !== false && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            // Display product details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?></title>

    <link rel="stylesheet" href="css/user_style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
    <style>
        .product-details{
            height : 500px;
           
        }
        .product-details .img{
            width: 500px;
            height : 400px;

        }
        .product-details .img img{
            height: 100%;
            width: 100%;
            object-fit: cover;
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
                <div class="product-details">
                    <h1><?= htmlspecialchars($product['title']) ?></h1>
                    <div class="img">
                    <img src="data:image/jpeg;base64,<?= base64_encode($product['image_data']) ?>" alt="">
                    </div>
                  
                    <p>Price: <?= htmlspecialchars($product['Price']) ?></p>
                    <p>Year: <?= htmlspecialchars($product['Year']) ?></p>
                    <p>Engine: <?= htmlspecialchars($product['Engine']) ?></p>
                    <form method="post" action="add_to_comparison.php">
                        <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($id) ?>">
                        <button type="submit">Add to Comparison</button>
                    </form>
                </div>

                <?php
                    // Call Python script to get recommendations
                    $python_script = "recommendation_script.py";
                    $command = escapeshellcmd("python $python_script " . escapeshellarg($product['title']) . " " . escapeshellarg($id));
                    $output = shell_exec($command);
                ?>
                <div>
                    <?php include 'prod.php'; ?>
                </div>

                <?php
                } else {
                    echo "Product not found.";
                }
                $stmt->close();
            } else {
                echo "Product title not provided.";
            }
        } else {
            echo "No vehicles found.";
        }
        ?>
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/main.js"></script>
</body>
</html>
