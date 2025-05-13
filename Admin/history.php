<?php 
include "adminsession.php";

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
        .container {
            max-height: 40vh;
            overflow-y: auto;
        }
        .element-class {
            overflow: scroll;
            -ms-overflow-style: none;  
            scrollbar-width: none;
        }
        .element-class::-webkit-scrollbar {
            display: none;  
        }
        td {
            text-align: center;
        }

        .card-header {
            font-size: 1.25rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>
<body class="bg-light">

<!-- Header Buttons -->
<div class="container my-4 d-flex justify-content-between flex-wrap gap-2">
    <div class="d-flex flex-wrap gap-2">
        <a href="index.php" class="btn btn-success btn-custom">Sales View</a>
        <a href="history.php" class="btn btn-info text-white btn-custom">Sales History</a>
        <a href="manageuser.php" class="btn btn-primary btn-custom">Manage User</a>
        <a href="table.php" class="btn btn-warning btn-custom">All Products</a>
        <a href="shopview.php" class="btn btn-danger btn-custom">Shop View</a>
    </div>
    <div>
        <a href="../logout.php" class="btn btn-dark btn-custom">Logout</a>
    </div>
</div>

<!-- Today's Orders Table -->
<div class="container mb-5">
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

<!-- All Sales Table -->
<div class="container mb-5">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">All Sales Records</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive element-class">
                <table class="table table-hover align-middle table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Total Sales</th>
                            <th>Sales Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['sales_id']) ?></td>
                            <td>₱ <?= number_format($row['total_sales'], 2) ?></td>
                            <td><?= htmlspecialchars($row['sales_date']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($sales)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No sales data available.</td>
                        </tr>
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
