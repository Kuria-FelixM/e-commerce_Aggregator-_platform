<?php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Search Results</title>

    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/105d3540aa.js" crossorigin="anonymous"></script>
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
                    window.location.href = 'get_categories.php?brand=' + encodeURIComponent(brandName);
                });
            });
        });
    </script>
</body>
</html>
