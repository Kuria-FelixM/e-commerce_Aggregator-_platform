<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$id = $_SESSION["id"];

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Fetch all vehicle data from the 'scraped' table
$sql_all_vehicles = "SELECT * FROM scrap";
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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?></title>
    <style>
                .product-details{
            height : 600px;
            background-color: white;
            align-items:center;
           
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
<header>
        <div class="logo">
            <span class="word-1">AUTO</span>
            <span class="word-2">HUB</span>
        </div>
        <div class="search_bar">
            <form action="search_bar.php" method="get">
                <input type="text" name="search" placeholder="Search for a vehicle" required>
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="user">
            <div class="user_container">
                <i class="fa-solid fa-user"></i>
                <div class="options">
                    <a href="login.php" class="button signin">Sign In</a>
                    <a href="signup.php" class="button signup">Sign Up</a>
                </div>
            </div>
        </div>
    </header>
    <nav>
        <div class="dropdown">
            <button class="dropbtn">BRANDS 
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <div class="categories-card" data-brand="Mercedes">MERCEDES</div>
                <div class="categories-card" data-brand="Toyota">TOYOTA</div>
                <div class="categories-card" data-brand="Audi">AUDI</div>
                <div class="categories-card" data-brand="Nissan">NISSAN</div>
                <div class="categories-card" data-brand="Mazda">MAZDA</div>
                <div class="categories-card" data-brand="BMW">BMW</div>
            </div>
        </div> 
        <ul>
            <li><a href="index.php">HOME</a></li>
            <li><a href="explore.php">EXPLORE</a></li>
            <li><a href="">DETAILS</a></li>
        </ul>
    </nav>
    <div class="product-details">
        <h1><?= htmlspecialchars($product['title']) ;
        $externalLink = $product["source_url"];?></h1>
        <div class="img">
        <img src="data:image/jpeg;base64,<?= base64_encode($product['image_data']) ?>" alt="">
    </div>
        
        <p>Price: <?= htmlspecialchars($product['Price']) ?></p>
        <p>Year: <?= htmlspecialchars($product['Year']) ?></p>
        <p>Engine: <?= htmlspecialchars($product['Engine']) ?></p>
        <button type="button" onclick="window.location.href='<?php echo $externalLink; ?>'">View External Site</button>
    </div>

    <?php
        // Call Python script to get recommendations
        $python_script = "recommendation_script.py";
        $command = escapeshellcmd("python $python_script " . escapeshellarg($productTitle));
        $output = shell_exec($command);
    ?>
    <div>
        <?php include 'prod.php'; ?>
    </div>
</body>
</html>
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
