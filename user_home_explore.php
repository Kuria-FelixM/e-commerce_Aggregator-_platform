<?php
  require_once 'connect.php';

  session_start();

  if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
$id = $_SESSION["id"];

class ProductAggregator {
    private $conn;

    public function __construct($db) {
        $this->conn = $db->conn;
    }

    public function getAllBrands() {
        $sql = "SELECT * FROM brand"; 
        $result = $this->conn->query($sql);

        if ($result !== false && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    public function getProductsByFilters($brandChecked, $yearChecked) {
        $filters = [];

        // Construct WHERE clause for brand filter
        if (!empty($brandChecked)) {
            $brandFilters = [];
            foreach ($brandChecked as $brand) {
                $brandFilters[] = "title LIKE '%" . $this->conn->real_escape_string($brand) . "%'";
            }
            $filters[] = '(' . implode(' OR ', $brandFilters) . ')';
        }

        // Construct WHERE clause for year filter
        if (!empty($yearChecked)) {
            $yearFilters = [];
            foreach ($yearChecked as $year) {
                $startYear = $year;
                $endYear = $year + 4;
                $yearFilters[] = "(year >= $startYear AND year <= $endYear)";
            }
            $filters[] = '(' . implode(' OR ', $yearFilters) . ')';
        }

        $whereClause = !empty($filters) ? 'WHERE ' . implode(' AND ', $filters) : '';
        $sql = "SELECT * FROM scrap $whereClause";
        $result = $this->conn->query($sql);

        if ($result !== false && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }
}

$db = new Database();
$productAggregator = new ProductAggregator($db);

$allBrandsArray = $productAggregator->getAllBrands();
$brandChecked = isset($_GET['brand']) ? $_GET['brand'] : [];
$yearChecked = isset($_GET['year']) ? $_GET['year'] : [];

$products = $productAggregator->getProductsByFilters($brandChecked, $yearChecked);
  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User-Dashboard</title>

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


    <div class="home_container">
        <form action="" method="GET">
            <div class="sideh">
                <div class="brandh">
                    <h2>BRAND</h2>
                    <p>Filter</p>  
                    <button type="submit">SEARCH</button>
                    <?php foreach ($allBrandsArray as $brandlist): ?>
                        <div>
                            <input type="checkbox" name="brand[]" value="<?= $brandlist["brand"] ?>" <?= in_array($brandlist["brand"], $brandChecked) ? 'checked' : '' ?>>
                            <?= $brandlist["brand"] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="yearh">
                    <h2>YEAR</h2>
                    <p>Filter</p>  
                    <button type="submit">SEARCH</button>
                    <?php 
                    $currentYear = date('Y');
                    for ($year = 2000; $year <= $currentYear; $year += 5): ?>
                        <div>
                            <input type="checkbox" name="year[]" value="<?= $year ?>" <?= in_array($year, $yearChecked) ? 'checked' : '' ?>>
                            <?= $year ?> - <?= $year + 4 ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </form>

        <div class="categories_containerb">
            <div class="categories-hb">
                <h1>PRODUCTS</h1>
            </div>
            <div class="categories-rowb">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $proditems):
                        $externalLink = $proditems["source_url"]; ?>
                        <div class="categories-cardb">
                            <a href="user_product.php?title=<?= urlencode($proditems['title']) ?>"> 
                                <div class="imageb">
                                    <img src="data:;base64,<?= base64_encode($proditems['image_data']) ?>" alt="">
                                </div>
                                <div class="captionb">
                                    <p><?= $proditems['title'] ?></p>
                                </div>
                                <div class="captionb">
                                    <p><?= $proditems['Year'] ?></p>
                                </div>
                                <div class="captionb">
                                    <p><?= $proditems['Engine'] ?></p>
                                </div>
                                <form method="post" action="add_to_comparison.php">
                                   <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($proditems['id']) ?>">
                                   <input type="hidden" name="user_id" value="<?= htmlspecialchars($id) ?>">
                                   <button type="submit">Add to Comparison</button>
                                </form>
                            </a>
                            <button type="button" onclick="window.location.href='<?php echo $externalLink; ?>'">View External Site</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var brands = document.querySelectorAll('.categories-card');
            brands.forEach(function(brand) {
                brand.addEventListener('click', function() {
                    var brandName = this.getAttribute('data-brand');
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