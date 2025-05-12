<?php  
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$farmer = $result->fetch_assoc();

$query_requests = "SELECT * FROM produce_requests WHERE user_id = ? ORDER BY request_date DESC";
$stmt_requests = $conn->prepare($query_requests);
$stmt_requests->bind_param('i', $farmer_id);
$stmt_requests->execute();
$requests_result = $stmt_requests->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movers - Farmer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('assets/terraces-7878191_1280.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .navbar {
            background-color: rgba(255, 255, 255, 0.95);
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color:rgb(49, 132, 228);
            color: white;
            font-weight: 500;
        }
        h2 {
            font-weight: 600;
            color:rgb(2, 2, 2);
        }
        table {
            background-color: white;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Movers</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active fw-semibold" href="farmer_dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold" href="#orders">My Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold text-danger" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Dashboard Content -->
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($farmer['name']); ?></h2>

    <!-- Profile Section -->
    <div class="card mt-3">
        <div class="card-header">
            Your Profile
        </div>
        <div class="card-body">
            <h5 class="card-title">Name: <?php echo htmlspecialchars($farmer['name']); ?></h5>
            <p>Email: <?php echo htmlspecialchars($farmer['email']); ?></p>
            <a href="profile.php" class="btn btn-outline-primary">Edit Profile</a>
        </div>
    </div>

    <!-- Transportation Request Form -->
    <div class="card mt-4">
        <div class="card-header">
            Submit Transportation Request
        </div>
        <div class="card-body">
            <form action="submit_request.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Produce Type</label>
                    <select class="form-select" name="produce_type" required>
                        <option value="Eggs">Eggs</option>
                        <option value="Cereals">Cereals</option>
                        <option value="Potatoes">Potatoes</option>
                        <option value="Milk">Milk</option>
                        <option value="Flowers">Flowers</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="quantity" placeholder="Enter quantity in kg/tons" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pickup Location</label>
                    <input type="text" class="form-control" name="pickup_location" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Drop-off Location</label>
                    <input type="text" class="form-control" name="dropoff_location" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pickup Date and Time</label>
                    <input type="datetime-local" class="form-control" name="pickup_date" required>
                </div>
                <button type="submit" class="btn btn-success">Submit Request</button>
            </form>
        </div>
    </div>

    <!-- View Submitted Requests -->
    <div id="orders" class="card mt-4 mb-5">
        <div class="card-header">
            View Your Transportation Requests
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Produce Type</th>
                        <th>Status</th>
                        <th>Pickup Location</th>
                        <th>Drop-off Location</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['produce_type']); ?></td>
                            <td>
                                <?php 
                                    $status = $request['status'];
                                    if ($status == 'Completed') {
                                        echo "<span class='badge bg-success'>Completed</span>";
                                    } elseif ($status == 'Approved') {
                                        echo "<span class='badge bg-primary'>Approved</span>";
                                    } else {
                                        echo "<span class='badge bg-warning text-dark'>Pending</span>";
                                    }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($request['pickup_location']); ?></td>
                            <td><?php echo htmlspecialchars($request['dropoff_location']); ?></td>
                            <td><?php echo htmlspecialchars($request['request_date']); ?></td>
                            <td>
                                <a href="remove_request.php?id=<?php echo $request['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Success/Error Message -->
<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-<?php echo ($_GET['status'] == 'deleted') ? 'success' : 'danger'; ?>" role="alert" id="statusMessage">
        <?php echo ($_GET['status'] == 'deleted') ? 'Request successfully removed.' : 'An error occurred while removing the request.'; ?>
    </div>
<?php endif; ?>

<script>
    window.onload = function() {
        const message = document.getElementById('statusMessage');
        if (message) {
            setTimeout(function() {
                message.style.display = 'none';
            }, 10000);
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
