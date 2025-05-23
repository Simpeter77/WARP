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

# Fetch Product Data
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $fetch_ids = $pdo->query("SELECT product_id FROM products");
    $all_ids = $fetch_ids->fetchAll(PDO::FETCH_COLUMN);

    $exists = in_array($product_id, $all_ids);
    
    if (!$exists) {
        header("location: ../");
        exit;
    }
}

$fetch = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
$fetch->execute([':product_id' => $product_id]);
$product = $fetch->fetch();

if (!$product) {
    echo "<h3>Product not found.</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            max-width: 600px;
            margin: 0 auto;
        }

        .card-img-top {
            height: 300px;
            object-fit: cover;
        }

        @media (max-width: 576px) {
            .card-img-top {
                height: 250px;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <!-- Product Image -->
                    <img src="../img/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>

                        <!-- Hidden Price Value for Calculation -->
                        <input type="hidden" id="price" value="<?= $product['product_price']?>">

                        <form action="addtocart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                            <!-- Quantity Input Section -->
                            <div class="mb-3 align-self-center mx-auto">
                                <label for="quantity" class="form-label">Quantity:</label>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" id="decrease">-</button>
                                    </div>
                                    <div class="col-4">
                                        <input type="number" class="form-control text-center" name="quantity" id="quantity" min="1" value="1" required>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" id="increase">+</button>
                                    </div>
                                </div>
                                <p id="subtotal" class="mt-2"></p>
                            </div>
                            <div class="row d-flex justify-content-between align-items-center">
                                <div class="col-md-4">
                                    <!-- Add to Cart Button -->
                                    <button type="submit" class="btn btn-success w-100" name="addtocart">Add to Cart</button>
                                </div>
                                
                                <div class="col-md-4">
                                    <!-- Back to Products Button -->
                                        <a href="index.php" class="btn btn-secondary w-100">Back to Products</a>
                                </div>
                               
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to Update Subtotal -->
    <script>
        const quantityInput = document.getElementById("quantity");
        const increaseBtn = document.getElementById("increase");
        const decreaseBtn = document.getElementById("decrease");
        const price = parseFloat(document.getElementById("price").value);
        const subtotal = document.getElementById("subtotal");

        function updateSubtotal() {
            const qty = parseInt(quantityInput.value);
            subtotal.innerHTML = "Sub Total: ₱" + (qty * price).toFixed(2);
        }

        increaseBtn.addEventListener("click", () => {
            quantityInput.value = parseInt(quantityInput.value) + 1;
            updateSubtotal();
        });

        decreaseBtn.addEventListener("click", () => {
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
                updateSubtotal();
            }
        });

        quantityInput.addEventListener("change", () => {
            if (quantityInput.value < 1) quantityInput.value = 1;
            updateSubtotal();
        });

        updateSubtotal();
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
