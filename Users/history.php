<?php 
include "usersession.php";

$sales = $pdo->query("SELECT * FROM sales ORDER BY sales_date DESC")->fetchAll();

// Today's date and today's sales (from orders table)
$now = date("Y-m-d");
$fetch_today = $pdo->prepare("SELECT * FROM orders WHERE order_date = :sales_date ORDER BY order_id DESC ");
$fetch_today->execute([":sales_date" => $now]);
$today = $fetch_today->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales History</title>
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
        table thead th {
        background-color: #f5f5f5;
        text-align: center;
        vertical-align: middle;
        }

        table td {
            text-align: center;
            vertical-align: middle;
        }

        .table-container {
            max-height: 80vh;
            overflow-y: auto;
            overflow-x: auto;
            display: block;
            scrollbar-width: thin;
        }

        .table-responsive::-webkit-scrollbar {
            width: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 3px;
        }

        .modal-body {
            font-size: 0.95rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .card {
            border-radius: 0.75rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>
<body class="bg-light">

<!-- Header Navigation -->
    <div class="container my-4 mt-0">
        <div class="nav-row d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 rounded">
            
            <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="index.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Shop</a>
            <a href="history.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">History</a>
            </div>

            <div>
            <a href="../logout.php" class="nav-button logout">Logout</a>
            </div>

        </div>
    </div>
<div class="container">
    <!-- Today's Orders Table -->
    <div class="table-container mb-5">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Today's Sales (<?= date('l, F j, Y', strtotime($now)) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive element-class">
                    <table class="table table-hover align-middle table-bordered">
                        <thead>
                            <tr>
                                <th style = 'background-color: #f8f9fa; text-align: center;'>Order ID</th>
                                <th style = 'background-color: #f8f9fa; text-align: center;'>Order Amount</th>
                                <th style = 'background-color: #f8f9fa; text-align: center;'>Order Date</th>
                                <th style = 'background-color: #f8f9fa; text-align: center;'>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($today)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No sales today.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($today as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['order_id']) ?></td>
                                    <td>₱ <?= number_format($row['order_total_amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" data-order-id="<?= $row['order_id'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for Order Details -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Order ID: <span id="order-id"></span></h6>
                <div id="order-breakdown">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = document.getElementById('orderDetailsModal');
    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const orderId = button.getAttribute('data-order-id');
        // Set the order ID in the modal header
        document.getElementById('order-id').textContent = orderId;
        
        let xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function(){
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
                document.getElementById('order-breakdown').innerHTML = xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET", "order_items.php?orderid=" + orderId, true);
        xmlhttp.send();
    });
</script>

</body>
</html>
