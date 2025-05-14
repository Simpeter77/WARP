<?php
include "adminsession.php";

// Database connection assumed as $pdo

// --- Daily Sales (last 7 days) ---
$daily_sales = $pdo->query("
    SELECT sales_date, total_sales 
    FROM sales 
    WHERE sales_date >= CURDATE() - INTERVAL 6 DAY 
    ORDER BY sales_date ASC
")->fetchAll();

// --- Weekly Sales in current month (monthly chart input) ---
$startOfMonth = date('Y-m-01');
$endOfMonth = date('Y-m-t');

$start = new DateTime($startOfMonth);
$end = new DateTime($endOfMonth);
$end->modify('+1 day');

$interval = new DateInterval('P1W');
$period = new DatePeriod($start, $interval, $end);

$monthly_labels = [];
$monthly_sales = [];

$monthly_labels = [];
$monthly_sales = [];

$weekNumber = 1;
foreach ($period as $weekStart) {
    $weekEnd = clone $weekStart;
    $weekEnd->modify('+6 days');

    // Ensure we don't go past the end of the current month
    if ($weekEnd > new DateTime($endOfMonth)) {
        $weekEnd = new DateTime($endOfMonth);
    }

    // Format: Week 1 (May 01–May 07)
    $label = "Week $weekNumber (" . $weekStart->format('M d') . '–' . $weekEnd->format('M d') . ')';
    $monthly_labels[] = $label;

    // Get total sales for this week range
    $stmt = $pdo->prepare("
        SELECT SUM(total_sales) 
        FROM sales 
        WHERE sales_date BETWEEN ? AND ?
    ");
    $stmt->execute([$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
    $sum = $stmt->fetchColumn() ?: 0;

    $monthly_sales[] = $sum;
    $weekNumber++;
}


// --- Yearly Sales (fixed 12 months) ---
$yearly_raw = $pdo->query("
    SELECT 
        MONTH(sales_date) AS month_num, 
        SUM(total_sales) AS total 
    FROM sales 
    WHERE YEAR(sales_date) = YEAR(CURDATE()) 
    GROUP BY month_num
")->fetchAll(PDO::FETCH_KEY_PAIR);

$yearly_sales = [];
for ($i = 1; $i <= 12; $i++) {
    $yearly_sales[] = isset($yearly_raw[$i]) ? $yearly_raw[$i] : 0;
}

// --- KPIs ---
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

$today_sales = $pdo->prepare("SELECT total_sales FROM sales WHERE sales_date = ?");
$today_sales->execute([$today]);
$today_sales = $today_sales->fetchColumn() ?: 0;

$yesterday_sales = $pdo->prepare("SELECT total_sales FROM sales WHERE sales_date = ?");
$yesterday_sales->execute([$yesterday]);
$yesterday_sales = $yesterday_sales->fetchColumn() ?: 0;

$weekly_avg = $pdo->query("
    SELECT AVG(total_sales) 
    FROM sales 
    WHERE DATE(sales_date) >= CURDATE() - INTERVAL 7 DAY
")->fetchColumn();

$weekly_avg = $weekly_avg !== null ? round($weekly_avg, 2) : 0;

$best_day = $pdo->query("SELECT * FROM sales ORDER BY total_sales DESC LIMIT 1")->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales - Miras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .nav-row {
            background-color: #ffffff;
            border-radius: 12px;
        }

        .nav-button {
            padding: 0.45rem 1.1rem;
            background-color: #f2f2f2;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .nav-button:hover,
        .nav-button:focus {
            background-color: #e0e0e0;
            color: #111;
            transform: translateY(-1px);
        }

        .nav-button.active {
            background-color: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
        }

        .logout {
            background-color: #ffe5e5;
            color: #c0392b;
        }

        .logout:hover,
        .logout:focus {
            background-color: #ffd6d6;
            color: #922b21;
        }
    </style>
</head>
<body class="bg-light">

<!-- Header Navigation -->
<div class="container my-4 mt-0">
  <div class="nav-row d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 rounded">
    
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <a href="index.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Sales View</a>
      <a href="history.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">Sales History</a>
      <a href="manageuser.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'manageuser.php' ? 'active' : '' ?>">Manage User</a>
      <a href="table.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'table.php' ? 'active' : '' ?>">All Products</a>
      <a href="shopview.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'shopview.php' ? 'active' : '' ?>">Shop View</a>
    </div>

    <div>
      <a href="../logout.php" class="nav-button logout">Logout</a>
    </div>

  </div>
</div>


<div class="container">
    <!-- Title -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary">MIRA'S SALES DASHBOARD</h1>
    </div>
    <!-- KPIs -->
    <div class="mb-5">
        <div class="row g-4 align-items-stretch">
            <div class="col-md-3">
                <div class="card border-primary text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <p class="card-text fs-4 text-primary">₱ <?= number_format($today_sales, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Yesterday's Sales</h5>
                        <p class="card-text fs-4 text-success">₱ <?= number_format($yesterday_sales, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Week Average</h5>
                        <p class="card-text fs-4 text-warning">₱ <?= number_format($weekly_avg, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger text-center h-100">
                    <div class="card-body">
                        <h5 class="card-title">Best Day</h5>
                        <p class="card-text fs-6 text-danger">
                            <?= $best_day['sales_date'] ?><br>
                            ₱ <?= number_format($best_day['total_sales'], 2) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts -->
    <div class="mb-5">
        <div class="row g-4">
            <!-- Daily Chart -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Daily Sales (Last 7 Days)</h5>
                        <canvas id="dailyChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Weekly (Monthly) Chart -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Weekly Sales (This Month)</h5>
                        <canvas id="monthlyChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Yearly Chart -->
            <div class="col-12 d-flex justify-content-center">
                <div class="card w-100" style="max-width: 900px;">
                    <div class="card-body">
                        <h5 class="card-title text-center">Yearly Sales</h5>
                        <canvas id="yearlyChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Scripts -->
<script>
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');

// Daily Chart
new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($daily_sales, 'sales_date')) ?>,
        datasets: [{
            label: 'Daily Sales',
            data: <?= json_encode(array_column($daily_sales, 'total_sales')) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

// Weekly Sales (monthly view)
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($monthly_labels) ?>,
        datasets: [{
            label: 'Weekly Sales',
            data: <?= json_encode($monthly_sales) ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.7)',
            borderColor: 'rgba(255, 206, 86, 1)',
            borderWidth: 1
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});

// Yearly Chart
new Chart(yearlyCtx, {
    type: 'bar',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: 'Monthly Sales',
            data: <?= json_encode($yearly_sales) ?>,
            backgroundColor: 'rgba(153, 102, 255, 0.7)',
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});
</script>

</body>
</html>
