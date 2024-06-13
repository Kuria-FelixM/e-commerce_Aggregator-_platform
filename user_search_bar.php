<?php
  require_once 'connect.php';

  session_start();

  // Check if the user is logged in, if not then redirect them to the login page
  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once 'connect.php';

class ProductSearch {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function searchProducts($searchTerm) {
        $searchQuery = "%" . $this->db->getConnection()->real_escape_string($searchTerm) . "%";
        $sql = "SELECT * FROM scrap WHERE title LIKE ?";
        $params = ["s", $searchQuery];

        // Access mysqli object from the Database instance and execute the query
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all rows from the result
        $search_results = [];
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }

        return $search_results;
    }
}

$db = new Database();
$productSearch = new ProductSearch($db);

$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_results = $productSearch->searchProducts($search);
}
  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vehicle Search Results</title>



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
                    <a href="user_search.php">
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


<div class="categories_containerb">
    <div class="categories-hb">
        <h1>SEARCH RESULTS</h1>
    </div>
    <div class="categories-rowb">
        <?php
        if (!empty($search_results)) {
            foreach ($search_results as $result){
                $imageData = $result["image_data"];
        ?>
        <div class="categories-cardb">
        <a href="user_product.php?title=<?= urlencode($result['title']) ?>"> 
            <div class="imageb">
                <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
            </div>
            <div class="captionb">
                <p><?php echo htmlspecialchars($result['title']); ?></p>
                <p><?php echo htmlspecialchars($result['Year']); ?></p>
                <p><?php echo htmlspecialchars($result['Engine']); ?></p>
                <p><?php echo htmlspecialchars($result['Transmission']); ?></p>
                <p><?php echo htmlspecialchars($result['Price']); ?></p>
            </div>
            </a>
        </div>
        <?php
            }
        } else {
            echo "<p>No results found for \"" . htmlspecialchars($search) . "\"</p>";
        }
        ?>
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
                    window.location.href = 'get_categories.php?brand=' + encodeURIComponent(brandName);
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