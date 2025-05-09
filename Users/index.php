<?php
#essentials
session_start();
include "../dbconfig.php";
include "../style.php";
#sessions
if(!isset($_SESSION['USER'])){
    header("location: logout.php");
}

if(isset($_SESSION['USER'])){
    $user_session = $_SESSION['USER'];
    if($user_session['user_role'] != "User"){
        header("location: ../Admin/");
    }
}
#end of essentials
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
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :condition");
    $fetch->execute([':condition' => $condition]);
} else {
    $fetch = $pdo->prepare("SELECT * FROM products");
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
</head>
<body>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>
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
    <form method="POST">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5 rounded shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="All">All</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Snacks">Snacks</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Meals">Meals</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Drinks">Drinks</button>
                    </li>
                </ul>
            </div>
        </nav>
    </form>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mt-4">
                <a href="view.php?product_id=<?= $product['product_id']?>" class="card product-card text-decoration-none">
                    <img src="../img/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="d-flex justify-content-center mt-4">
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>
</body>
</html>
