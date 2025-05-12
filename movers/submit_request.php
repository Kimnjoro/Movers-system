<?php
session_start();
include 'db.php';

// Redirect if not logged in or if the role is not 'farmer'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$produce_type = $_POST['produce_type'];
$quantity = $_POST['quantity'];
$pickup_location = $_POST['pickup_location'];
$dropoff_location = $_POST['dropoff_location'];
$pickup_date = $_POST['pickup_date'];

// Automatically categorize based on produce type
$category = in_array($produce_type, ['Eggs', 'Milk', 'Flowers']) ? 'Perishable' : 'Non-Perishable';

// Insert the request into the database
$query = "INSERT INTO produce_requests (user_id, produce_type, category, quantity, pickup_location, dropoff_location, request_date, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param('issdsss', $farmer_id, $produce_type, $category, $quantity, $pickup_location, $dropoff_location, $pickup_date);

if ($stmt->execute()) {
    header("Location: index.php?status=success");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
