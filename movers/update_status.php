<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id']) && isset($_POST['status'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];

        // Update the status in the database
        $query = "UPDATE produce_requests SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $request_id);

        if ($stmt->execute()) {
            // Redirect to the dashboard with the 'status=updated' parameter
            header("Location: admin_dashboard.php?status=updated");
        } else {
            // If there was an error, redirect with an error message
            header("Location: admin_dashboard.php?status=error");
        }
    }
}
?>
