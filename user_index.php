<?php
require_once 'connect.php';



class CarModel {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->db->getConnection()->prepare($sql);
    
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result; 
        } else {
            error_log("Failed to prepare statement: " . $this->db->getConnection()->error);
            return null; 
        }
    }
    

    public function getRecommendedCars() {
        $sql = "SELECT * FROM scraped";
        $stmt = $this->db->getConnection()->prepare($sql);
    
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result; 
        } else {
            error_log("Failed to prepare statement: " . $this->db->getConnection()->error);
            return null; 
        }
    }
    
}


$database = new Database();

// Create a new CarModel object
$carModel = new CarModel($database);

$allproduct = $carModel->getAllCategories();
$recommended = $carModel->getRecommendedCars();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggregator Website</title>
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
            <form action="user_search_bar.php" method="get">
                <input type="text" name="search" placeholder="Search for a vehicle" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </header>
    <main>
        <div class="main">
            <h2>FIND BEST CAR DEALS ACROSS ALL ONLINE STORES</h2>
            <div class="img-container">
                <img src="img/courosel-1.jpg" alt="CARS">
            </div>
        </div>
        <div class="side">
            <div class="side_row">
                <h2>BEST CAR DEALS</h2>
                <div class="img-container1">
                    <img src="img/courosel-3.jpg" alt="CARS">
                </div>
            </div>
            <div class="side_row">
                <h2>PERSONALIZED </br> RECOMMENDATIONS</h2>
                <div class="img-container1">
                    <img src="img/courosel-4.jpg" alt="CARS">
                </div>
            </div>
        </div>
    </main>
    <div class="categories_container">
        <div class="categories-h">
            <h2>CATEGORIES</h2>
        </div>
        <div class="categories-row">
                <div class="categories-card" data-brand="Mercedes">
                    <img src="img/mercedes.jpg" alt="Brand 1">
                    <div class="card-content">
                        <h2 class="card-title">MERCEDES</h2>
                    </div>
                </div>
                <div class="categories-card" data-brand="Toyota">
                    <img src="img/toyota.jpg" alt="Brand 2">
                    <div class="card-content">
                       <h2 class="card-title">TOYOTA</h2>
                    </div>
                </div>
                <div class="categories-card" data-brand="Audi">
                    <img src="img/audi.jpg" alt="Brand 2">
                    <div class="card-content">
                       <h2 class="card-title">AUDI</h2>
                    </div>
                </div>
                <div class="categories-card" data-brand="Nissan">
                    <img src="img/nissan.jpg" alt="Brand 2">
                    <div class="card-content">
                       <h2 class="card-title">NISSAN</h2>
                    </div>
                </div>
                <div class="categories-card" data-brand="Mazda">
                    <img src="img/mazda.jpg" alt="Brand 2">
                    <div class="card-content">
                       <h2 class="card-title">MAZDA</h2>
                    </div>
                </div>
                <div class="categories-card" data-brand="BMW">
                    <img src="img/bmw.jpg" alt="Brand 2">
                    <div class="card-content">
                       <h2 class="card-title">BMW</h2>
                    </div>
                </div>
            </div>
    </div>
    <div class="categories_containerb">
        <div class="categories-hb">
            <h2>FEATURED</h2>
        </div>
        <div class="categories-rowb">
            <?php
            while ($row = mysqli_fetch_assoc($allproduct)) {
                $imageData = $row["image_data"];
            ?>
                <div class="categories-cardb">
                    <div class="imageb">
                        <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
                    </div>
                    <div class="captionb">
                        <p><?php echo htmlspecialchars($row['name']); ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="categories_containerb">
        <div class="categories-hb">
            <h2>RECOMMENDED</h2>
        </div>
        <div class="categories-rowb">
            <?php
            $count = 0;
            while ($row = mysqli_fetch_assoc($recommended)) {
                if ($count >= 8) break;
                $imageData = $row["image_data"];
            ?>
                <div class="categories-cardb">
                    <div class="imageb">
                        <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
                    </div>
                    <div class="captionb">
                        <p><?php echo htmlspecialchars($row['title']); ?></p>
                    </div>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
    <footer>
        <p>@autohub</p>
        <p>All Right Reserved</p>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get all brand elements
            var brands = document.querySelectorAll('.categories-card');

            // Attach click event listener to each brand
            brands.forEach(function(brand) {
                brand.addEventListener('click', function() {
                    var brandName = this.getAttribute('data-brand');
                    window.location.href = 'user_get_categories.php?brand=' + encodeURIComponent(brandName);
                });
            });
        });
    </script>
</body>
</html>
