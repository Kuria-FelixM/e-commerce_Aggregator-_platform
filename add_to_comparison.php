<?php
require_once 'connect.php';

//start 
session_start();

// Create a new instance of the Database class
$database = new Database();
$conn = $database->getConnection();

// Handle add to comparison
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id']) && isset($_POST['user_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $user_id = $_POST['user_id'];
    
    // Prepare the SQL statement to insert into user_comparisons
    $sql = "INSERT INTO user_comparisons (user_id, vehicle_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $vehicle_id);
    
    if ($stmt->execute()) {
        echo "Vehicle added to comparison list successfully";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$database->closeConnection();

?>
