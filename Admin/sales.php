<?php
# essentials
session_start();
include "../dbconfig.php";
include "../style.php";

# sessions
if (!isset($_SESSION['USER'])) {
    header("location: ../logout.php");
}
if (isset($_SESSION['USER'])) {
    $user_session = $_SESSION['USER'];
    if ($user_session['user_role'] != "Admin") {
        header("location: ../Users/");
    }
}

# sales queries
$sales = $pdo->query("SELECT * FROM sales ORDER BY sales_date ASC")->fetchAll();

// Today's sales
$today = date('Y-m-d');
$today_sales_stmt = $pdo->prepare("SELECT total_sales FROM sales WHERE sales_date = ?");
$today_sales_stmt->execute([$today]);
$today_sales = $today_sales_stmt->fetchColumn() ?: 0;

// Yesterday's sales
$yesterday = date('Y-m-d', strtotime("-1 day"));
$yesterday_sales_stmt = $pdo->prepare("SELECT total_sales FROM sales WHERE sales_date = ?");
$yesterday_sales_stmt->execute([$yesterday]);
$yesterday_sales = $yesterday_sales_stmt->fetchColumn() ?: 0;

// Weekly average sales (past 7 days)
$weekly_avg_stmt = $pdo->query("SELECT AVG(total_sales) FROM sales WHERE sales_date >= CURDATE() - INTERVAL 7 DAY");
$weekly_avg = round($weekly_avg_stmt->fetchColumn(), 2);

// Best day ever
$best_day_stmt = $pdo->query("SELECT sales_date, total_sales FROM sales ORDER BY total_sales DESC LIMIT 1");
$best_day = $best_day_stmt->fetch();

// Weekly Sales (last 7 days)
$weekly_sales = $pdo->query("SELECT sales_date, total_sales FROM sales WHERE sales_date >= CURDATE() - INTERVAL 6 DAY ORDER BY sales_date ASC")->fetchAll();

// Monthly Sales (current month)
$monthly_sales = $pdo->query("SELECT DATE_FORMAT(sales_date, '%Y-%m-%d') AS date, SUM(total_sales) as total FROM sales WHERE MONTH(sales_date) = MONTH(CURRENT_DATE()) AND YEAR(sales_date) = YEAR(CURRENT_DATE()) GROUP BY sales_date ORDER BY date ASC")->fetchAll();

// Yearly Sales (current year, by month)
$yearly_sales = $pdo->query("SELECT DATE_FORMAT(sales_date, '%Y-%m') AS month, SUM(total_sales) as total FROM sales WHERE YEAR(sales_date) = YEAR(CURRENT_DATE()) GROUP BY month ORDER BY month ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales - Miras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- Navbar buttons -->
<div class="container d-flex justify-content-end gap-2 mb-3">
    <a href="addproduct.php" class="btn btn-success">Add a product</a>
    <a href="adduser.php" class="btn btn-primary">Add a user</a>
    <a href="table.php" class="btn btn-warning">Table View</a>
    <a href="index.php" class="btn btn-warning">Shop View</a>
    <a href="sales.php" class="btn btn-warning">Sales View</a>
</div>

<!-- Header -->
<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S SALES</h1>

    <!-- KPIs -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Today’s Sales</h5>
                    <p class="card-text fs-4">₱ <?= number_format($today_sales, 2) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Yesterday’s Sales</h5>
                    <p class="card-text fs-4">₱ <?= number_format($yesterday_sales, 2) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Weekly Average</h5>
                    <p class="card-text fs-4">₱ <?= number_format($weekly_avg, 2) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Best Day</h5>
                    <p class="card-text fs-6"><?= $best_day['sales_date'] ?> <br> ₱ <?= number_format($best_day['total_sales'], 2) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Sales Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title text-center">Weekly Sales</h4>
            <canvas id="weeklyChart" height="100"></canvas>
        </div>
    </div>

    <!-- Monthly Sales Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title text-center">Monthly Sales</h4>
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>

    <!-- Yearly Sales Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title text-center">Yearly Sales</h4>
            <canvas id="yearlyChart" height="100"></canvas>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Sales Records</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Total Sales</th>
                        <th>Sales Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $row): ?>
                        <tr>
                            <td><?= $row['sales_id'] ?></td>
                            <td>₱ <?= number_format($row['total_sales'], 2) ?></td>
                            <td><?= $row['sales_date'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="d-flex justify-content-center mt-4">
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<!-- Chart.js Script -->
<script>
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');

// Weekly Chart
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach($weekly_sales as $day){ echo '"'.$day['sales_date'].'",'; } ?>],
        datasets: [{
            label: 'Daily Sales',
            data: [<?php foreach($weekly_sales as $day){ echo $day['total_sales'].','; } ?>],
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Sales in the Last 7 Days'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Monthly Chart
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: [<?php foreach($monthly_sales as $day){ echo '"'.$day['date'].'",'; } ?>],
        datasets: [{
            label: 'Daily Sales',
            data: [<?php foreach($monthly_sales as $day){ echo $day['total'].','; } ?>],
            backgroundColor: 'rgba(255, 206, 86, 0.5)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 4
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Sales for This Month'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Yearly Chart
new Chart(yearlyCtx, {
    type: 'bar',
    data: {
        labels: [<?php foreach($yearly_sales as $month){ echo '"'.$month['month'].'",'; } ?>],
        datasets: [{
            label: 'Monthly Sales',
            data: [<?php foreach($yearly_sales as $month){ echo $month['total'].','; } ?>],
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Monthly Sales This Year'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

</body>
</html>
