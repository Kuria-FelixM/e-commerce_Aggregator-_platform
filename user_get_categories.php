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

class ProductSearch {
    private $conn;

    public function __construct(Database $conn) {
        $this->conn = $conn;
    }

    public function searchProducts($brand) {
        // Prevent SQL injection by using prepared statements
        $stmt = $this->conn->getConnection()->prepare("SELECT * FROM scrap WHERE title LIKE ?");
        $brand = "%$brand%";
        $stmt->bind_param("s", $brand);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }
}

$productSearch = new ProductSearch($database);

$search_results = [];

if(isset($_GET['brand'])) {
    $brand = $_GET['brand'];
    $search_results = $productSearch->searchProducts($brand);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggregator Website</title>


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
    </header>

<div class="featured">
    <div class="categories-h">
        <h2><?php echo htmlspecialchars($_GET['brand'] ?? ''); ?></h2>
    </div>
    <div class="featured_categories">
        <?php while ($row = $search_results->fetch_assoc()): ?>
        <div class="categories-card">
            <a href="user-product.php?title=<?= urlencode($row['title']) ?>">
                <div class="image">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image_data']); ?>" alt="">
                </div>
                <div class="caption">
                    <p><?php echo htmlspecialchars($row['title']); ?></p>
                </div>
                <div class="caption">
                    <p><?php echo htmlspecialchars($row['Year']); ?></p>
                </div>
                <div class="caption">
                    <p><?php echo htmlspecialchars($row['Engine']); ?></p>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>


<script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get all brand elements
            var brands = document.querySelectorAll('.categories-card');

            // Attach click event listener to each brand
            brands.forEach(function(brand) {
                brand.addEventListener('click', function() {
                    var brandName = this.getAttribute('data-brand');
                    // Redirect to search_products.php with brand as a query parameter
                    window.location.href = 'user_get_categories.php?brand=' + encodeURIComponent(brandName);
                });
            });
        });
    </script>
                
            
    

            </div>


            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/main.js"></script>

</body>
</html>
