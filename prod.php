<?php
require_once 'connect.php';

$id = $_SESSION["id"];

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Query to retrieve recommendations
$sqll = "SELECT title FROM recommendationss WHERE car_id = ?";
$car_id = 1; 
$stmt = $conn->prepare($sqll);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any recommendations
$recommendations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = $row['title'];
    }
}

// Query to retrieve all vehicles
$sql = "SELECT * FROM scrap";
$allproduct = $conn->query($sql);


// Close the database connection
$database->closeConnection();
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
    
<div class="featured">
    <div class="categories-h">
        <h2>RECOMMENDATIONS</h2>
    </div>

    <div class="featured_categories">
        <?php
        if ($allproduct->num_rows > 0) {
        
            foreach ($recommendations as $title) {
                $title = strtolower(trim($title));

                // Flag to indicate if a match is found
                $found = false;

    
                $allproduct->data_seek(0);

                // Search for the title in $allproduct result set
                while ($row = $allproduct->fetch_assoc()) {
                    $dbTitle = strtolower(trim($row['title']));
                    if ($dbTitle == $title) {
                        $imageData = $row["image_data"];
        ?>
                        <div class="categories-card">
                        <a href="user_product.php?title=<?= urlencode($row['title']) ?>"> 
                            <div class="image">
                                <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($imageData) . '" alt="">'; ?>
                            </div>
                            <div class="caption">
                                <p><?php echo htmlspecialchars($row['title']); ?></p>
                                <p><?php echo htmlspecialchars($row['Price']); ?></p>
                                <form method="post" action="add_to_comparison.php">
                                    <input type="hidden" name="vehicle_id" value="<?php echo htmlspecialchars($row["id"]); ?>">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($id); ?>"> 
                                    <button type="submit">Add to Comparison</button>
                                </form>
                            </div>
                    </a>
                        </div>
        <?php
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    echo "<p>No product found for title: " . htmlspecialchars($title) . "</p>";
                }
            }
        } else {
            echo "<p>No titles received from the database.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
