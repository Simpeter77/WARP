<?php
session_start();
include "../dbconfig.php"; 
include "../style.php"; 

// Check if user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: logout.php");
    exit;
}

$user_session = $_SESSION['USER'];
if ($user_session['user_role'] !== "User") {
    header("Location: ../Admin/");
    exit;
}

// Fetch cart data
$cart = $_SESSION['CART'] ?? [];
$total_amount = 0;

// Calculate the total amount from the cart
foreach ($cart as $item) {
    $total_amount += $item['product_price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Insert order into orders table
        $insertOrder = $pdo->prepare("INSERT INTO orders (order_total_amount) VALUES (?)");
        $insertOrder->execute([$total_amount]);

        $order_id = $pdo->lastInsertId();

        // Insert each cart item into order_items table
        foreach ($cart as $item) {
            $subtotal = $item['product_price'] * $item['quantity'];
            $insertOrderItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $insertOrderItem->execute([$order_id, $item['product_id'], $item['quantity'], $subtotal]);
        }

        // Commit transaction
        $pdo->commit();

        // Clear the cart session
        unset($_SESSION['CART']);

        echo "
            <script>
                alert('Order Successful');
                window.location.href='index.php';
            </script>
        ";
        exit;
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
<div class="container my-5">
    <h2>Checkout</h2>
    <h4>Total: ₱<?= number_format($total_amount, 2) ?></h4>
    
    <?php if (empty($cart)): ?>
        <div class="alert alert-info">Your cart is empty. Please add items to your cart.</div>
        <a href="index.php" class="btn btn-primary">Back to Products</a>
    <?php else: ?>
        <form method="POST">
            <h5>Items in Your Cart:</h5>
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart as $item): ?>
                        <tr>
                            <td><img src="../img/<?= htmlspecialchars($item['product_image']) ?>" width="80" alt="<?= htmlspecialchars($item['product_name']) ?>"></td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>₱<?= number_format($item['product_price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₱<?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success">Confirm Order</button>
            <a href="cart.php" class="btn btn-danger">Back to Cart</a>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
