<?php
include "adminsession.php";

// Filter products
$filter = "";

if (isset($_POST['Snacks'])) {
    $filter = "Snack";
} elseif (isset($_POST['Meals'])) {
    $filter = "Meal";
} elseif (isset($_POST['Drinks'])) {
    $filter = "Drink";
} elseif (isset($_POST['All'])) {
    $filter = "";
}

if ($filter) {
    $condition = "%" . $filter . "%";
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :condition AND product_status = 'Available'");
    $fetch->execute([':condition' => $condition]);
} else {
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_status = 'Available'");
    $fetch->execute();
}

$products = $fetch->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
<body>
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

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>

    <form method="POST">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4">
            <div class="container-fluid justify-content-center">
                <div class="row w-100">
                    <div class="col-3">
                        <button type="submit" name="All" class="btn w-100 <?= $filter == "" ? 'btn-primary' : 'btn-secondary' ?>">All</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Snacks" class="btn w-100 <?= $filter == "Snack" ? 'btn-primary' : 'btn-secondary' ?>">Snacks</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Meals" class="btn w-100 <?= $filter == "Meal" ? 'btn-primary' : 'btn-secondary' ?>">Meals</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Drinks" class="btn w-100 <?= $filter == "Drink" ? 'btn-primary' : 'btn-secondary' ?>">Drinks</button>
                    </div>
                </div>
            </div>
        </nav>
    </form>



    <div class="row">
        <?php if ($fetch->rowCount() < 1): ?>
            <div class="col-12 text-center">
                <h2>No Product Found</h2>
            </div>
        <?php endif; ?>

        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 position-relative shadow-sm">
                    <div class="dropdown position-absolute top-0 end-0 m-2">
                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton<?= htmlspecialchars($product['product_id']) ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            Options
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $product['product_id'] ?>">
                            <li><a class="dropdown-item text-primary" href="edit.php?id=<?= $product['product_id'] ?>">Edit</a></li>
                            <li><a class="dropdown-item text-danger" href="delete.php?id=<?= $product['product_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a></li>
                        </ul>
                    </div>

                    <img src="../img/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>">

                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
