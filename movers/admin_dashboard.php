<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetching transportation requests data
$query_requests = "SELECT * FROM produce_requests ORDER BY request_date DESC";
$stmt_requests = $conn->prepare($query_requests);
$stmt_requests->execute();
$requests_result = $stmt_requests->get_result();

// Fetching produce summary data for charts
$query_summary = "SELECT produce_type, COUNT(*) AS total_requests FROM produce_requests GROUP BY produce_type";
$stmt_summary = $conn->prepare($query_summary);
$stmt_summary->execute();
$summary_result = $stmt_summary->get_result();
$summary_data = [];
while ($row = $summary_result->fetch_assoc()) {
    $summary_data[$row['produce_type']] = $row['total_requests'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background-color: rgb(49, 132, 228);
            color: white;
            font-weight: 500;
        }
        h2 {
            font-weight: 600;
            color: rgb(2, 2, 2);
        }
        table {
            background-color: white;
        }
        .status-completed {
            color: green;
            font-weight: bold;
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
                    <a class="nav-link active fw-semibold" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <!-- <a class="nav-link fw-semibold" href="profile.php">Profile</a> -->
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Admin Dashboard Content -->
<div class="container">
    <h2>Welcome, Admin</h2>

    <!-- View All Requests -->
    <div class="card mt-4">
        <div class="card-header">
            All Transportation Requests
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>User ID</th>
                        <th>Produce Type</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Pickup</th>
                        <th>Drop-off</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests_result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($request['user_id']) ?></td>
                            <td><?= htmlspecialchars($request['produce_type']) ?></td>
                            <td><?= htmlspecialchars($request['quantity']) ?></td>
                            <td>
                                <?php if ($request['status'] === 'Completed'): ?>
                                    <span class="status-completed">Completed</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($request['status']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($request['pickup_location']) ?></td>
                            <td><?= htmlspecialchars($request['dropoff_location']) ?></td>
                            <td><?= htmlspecialchars($request['request_date']) ?></td>
                            <td>
                                <?php if ($request['status'] !== 'Completed'): ?>
                                    <form method="POST" action="update_status.php">
                                        <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        <select name="status" class="form-select form-select-sm">
                                            <option value="Pending" <?= $request['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $request['status'] == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <!-- <option value="Completed">Completed</option> -->
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="card mt-4 mb-5">
        <div class="card-header">Produce Requests Summary</div>
        <div class="card-body">
            <canvas id="requestsChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script>
    const produceTypes = <?php echo json_encode(array_keys($summary_data)); ?>;
    const totalRequests = <?php echo json_encode(array_values($summary_data)); ?>;

    const ctx = document.getElementById('requestsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: produceTypes,
            datasets: [{
                label: 'Number of Requests',
                data: totalRequests,
                backgroundColor: 'rgba(49, 132, 228, 0.5)',
                borderColor: 'rgb(49, 132, 228)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
