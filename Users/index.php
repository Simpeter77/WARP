<?php
# Essentials
session_start();
include "../dbconfig.php";
include "../style.php";

# Session Management
if (!isset($_SESSION['USER'])) {
    header("location: logout.php");
}

if (isset($_SESSION['USER'])) {
    $user_session = $_SESSION['USER'];
    if ($user_session['user_role'] != "User") {
        header("location: ../Admin/");
    }
}

# Filter Handling
$filter = "";

if (isset($_POST['Snacks'])) {
    $filter = "Snack";
} elseif (isset($_POST['Meals'])) {
    $filter = "Meal";
} elseif (isset($_POST['Drinks'])) {
    $filter = "Drink";
}

if ($filter) {
    $condition = "%" . $filter . "%";
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_status = 'Available' AND product_name LIKE :condition ");
    $fetch->execute([':condition' => $condition]);
} else {
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_status = 'Available' ");
    $fetch->execute();
}

$cart = $_SESSION['CART'] ?? [];
$hasCartItems = !empty($cart);

$products = $fetch->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .card.product-card {
            height: 100%;
            max-height: 400px;
            min-height: 350px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            border: 1px solid #dee2e6;
            transition: transform 0.2s ease;
        }

        .card.product-card:hover {
            transform: scale(1.02);
        }

        .card.product-card img {
            height: 200px;
            object-fit: cover;
        }

        .card.product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .card.product-card {
                min-height: 320px;
                max-height: 360px;
            }

            .card.product-card img {
                height: 180px;
            }
        }
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
            <a href="index.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Shop</a>
            <a href="history.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">History</a>
            </div>

            <div>
            <a href="../logout.php" class="nav-button logout">Logout</a>
            </div>

        </div>
    </div>
    <div class="container my-4">
        <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>

        <!-- Cart View Button -->
        <?php if ($hasCartItems): ?>
            <div class="text-end me-4 mb-3">
                <a href="cart.php" class="btn btn-outline-success position-relative">
                    🛒 View Cart
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= count($cart) ?>
                    </span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Filter Buttons -->
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

        <!-- Product Cards -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            <?php foreach ($products as $product): ?>
                <div class="col">
                    <a href="view.php?product_id=<?= $product['product_id']?>" class="text-decoration-none text-dark">
                        <div class="card product-card">
                            <img src="../img/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
