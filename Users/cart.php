<?php
session_start();
include "../style.php";
include "../dbconfig.php";

if (!isset($_SESSION['USER'])) {
    header("location: logout.php");
    exit;
}

$user_session = $_SESSION['USER'];
if ($user_session['user_role'] !== "User") {
    header("location: ../Admin/");
    exit;
}

$cart = $_SESSION['CART'] ?? [];

if (isset($_POST['remove'])) {
    $removeId = $_POST['remove'];
    if (isset($_SESSION['CART'][$removeId])) {
        unset($_SESSION['CART'][$removeId]);
    }
    header("Location: cart.php");
    exit;
}

if (isset($_POST['update_cart']) && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $id => $qty) {
        $id = intval($id);
        $qty = intval($qty);
        if ($qty > 0 && isset($_SESSION['CART'][$id])) {
            $_SESSION['CART'][$id]['quantity'] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

if(isset($_POST['clear_cart'])){
    unset($_SESSION['CART']);
    header("location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">My Shopping Cart</h2>

    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="index.php" class="btn btn-primary">Back to Products</a>
    <?php else: ?>
        <form action="cart.php" method="POST" id="cart-form">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart as $id => $item):
                        $subtotal = $item['product_price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><img src="../img/<?= htmlspecialchars($item['product_image']) ?>" width="80" class="img-fluid"></td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>₱<?= number_format($item['product_price'], 2) ?></td>
                            <td>
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(<?= $id ?>)">-</button>
                                    </div>
                                    <div class="col-4">
                                        <input type="number"
                                               value="<?= $item['quantity'] ?>"
                                               name="quantities[<?= $id ?>]"
                                               class="form-control text-center quantity-input"
                                               data-id="<?= $id ?>"
                                               data-price="<?= $item['product_price'] ?>"
                                               min="1">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(<?= $id ?>)">+</button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span id="subtotal-<?= $id ?>">₱<?= number_format($subtotal, 2) ?></span>
                            </td>
                            <td>
                                <form action="" method="post">
                                    <button type="submit" name="remove" value="<?= $id ?>" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between flex-column flex-md-row">
                <h4>Total: ₱<span id="total"><?= number_format($total, 2) ?></span></h4>
                <div class="mt-3 mt-md-0">
                    <button type="submit" name="clear_cart" class="btn btn-warning">Clear Cart</button>
                    <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                    <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="checkout.php" class="btn btn-success">Checkout</a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    function updateSubtotal(id) {
        const input = document.querySelector(`input[data-id="${id}"]`);
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value);
        const subtotal = price * quantity;
        document.getElementById(`subtotal-${id}`).innerText = "₱" + subtotal.toFixed(2);
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.quantity-input').forEach(input => {
            const price = parseFloat(input.dataset.price);
            const quantity = parseInt(input.value);
            total += price * quantity;
        });
        document.getElementById("total").innerText = total.toFixed(2);
    }

    function increaseQuantity(id) {
        const input = document.querySelector(`input[data-id="${id}"]`);
        input.value = parseInt(input.value) + 1;
        updateSubtotal(id);
    }

    function decreaseQuantity(id) {
        const input = document.querySelector(`input[data-id="${id}"]`);
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateSubtotal(id);
        }
    }

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => {
            if (input.value < 1) input.value = 1;
            updateSubtotal(input.dataset.id);
        });
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        updateSubtotal(input.dataset.id);
    });
</script>

</body>
</html>
