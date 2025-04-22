<?php
include "dbconfig.php";

$fetch = $pdo->prepare("SELECT * FROM products");
$fetch->execute();
$products = $fetch->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1, h2 {
            color: #007BFF;
        }

        .product-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .total-amount {
            font-weight: bold;
            font-size: 1.2em;
            color: #28a745;
        }
    </style>
</head>
<body>
<form method="POST">
<div class="float-right">
    <a class="text-decoration-none" href="addproduct.php">Add Product</a>
</div>
<div class="container my-4">
    <a class="row g-4">
        <h1 class="text-center">MIRA'S</h1>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                    <a href="view.php" class="card product-card">
                        <img src="<?= htmlspecialchars($product['product_img']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['product_name']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                            <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                        </div>
                    </a>
            </div>
        <?php endforeach; ?>
    </a>
</div>
</form>
</body>
</html>
