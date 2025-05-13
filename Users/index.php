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
    </style>
</head>

<body>
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
            <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow-sm mb-4">
                <div class="container-fluid justify-content-center">
                    <div class="row w-100">
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <button type="submit" name="All" class="btn btn-primary w-100">All</button>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <button type="submit" name="Snacks" class="btn btn-primary w-100">Snacks</button>
                        </div>
                        <div class="col-6 col-md-3 mb-2 mb-md-0">
                            <button type="submit" name="Meals" class="btn btn-primary w-100">Meals</button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button type="submit" name="Drinks" class="btn btn-primary w-100">Drinks</button>
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

    <!-- Logout Button -->
    <div class="d-flex justify-content-center mt-4">
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
