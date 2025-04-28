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
    <title>View Product</title>
</head>
<body>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="../img/<?= htmlspecialchars($product['product_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['product_name']) ?>">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h3>
                    <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                    <input type="hidden" id="price" value = "<?= number_format($product['product_price'], 2) ?>">
                    <form action="addtocart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <div class="mb-3">
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
                            <p id = "subtotal"></p>
                        </div>
                        <button type="submit" class="btn btn-success w-100" name="addtocart">Add to Cart</button>
                    </form>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Back to Products</a>
                </div>
            </div>
        </div>
    </div>
</div>
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

</body>
</html>
