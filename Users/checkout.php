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

// Calculate total cart amount
foreach ($cart as $item) {
    $total_amount += $item['product_price'] * $item['quantity'];
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {date_default_timezone_set('Asia/Manila'); 

    $pdo->beginTransaction();
    try {
        $now = new DateTime();
        $today = $now->format('Y-m-d');
    
        $insertOrder = $pdo->prepare("INSERT INTO orders (order_total_amount, order_date) VALUES (?, ?)");
        $insertOrder->execute([$total_amount, $today]);
        $order_id = $pdo->lastInsertId();
    
        // Update Sales Table
        $total_sales = $pdo->prepare("SELECT SUM(order_total_amount) FROM orders WHERE order_date = :date");
        $total_sales->execute([":date" => $today]);
    
        $check_sales = $pdo->prepare("SELECT * FROM sales WHERE sales_date = :sales_date");
        $check_sales->execute([":sales_date" => $today]);
    
        $total_amount_today = $total_sales->fetchColumn();
    
        if ($check_sales->rowCount() < 1) {
            $insert_sales = $pdo->prepare("INSERT INTO sales (sales_date, total_sales) VALUES (:sales_date, :total_sales)");
            $insert_sales->execute([":sales_date" => $today, ":total_sales" => $total_amount_today]);
        } else {
            $update_sales = $pdo->prepare("UPDATE sales SET total_sales = :total_sales WHERE sales_date = :sales_date");
            $update_sales->execute([":total_sales" => $total_amount_today, ":sales_date" => $today]);
        }
    
        // Insert Order Items
        foreach ($cart as $item) {
            $subtotal = $item['product_price'] * $item['quantity'];
            $insertOrderItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $insertOrderItem->execute([$order_id, $item['product_id'], $item['quantity'], $subtotal]);
        }
    
        $pdo->commit();
        unset($_SESSION['CART']);
    
        echo "
            <script>
                alert('Order Successful!');
                window.location.href='index.php';
            </script>
        ";
        exit;
    } catch (Exception $e) {
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
    <title>Checkout - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container d-flex justify-content-end gap-2 mb-3 mt-3">
    <a href="index.php" class="btn btn-warning">Shop View</a>
    <a href="cart.php" class="btn btn-primary">Cart</a>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<div class="container my-5">
    <h1 class="text-center fw-bold text-primary mb-4">Checkout</h1>

    <?php if (empty($cart)): ?>
        <div class="alert alert-info text-center">
            Your cart is empty. Please add items to your cart.
        </div>
        <div class="d-flex justify-content-center mt-3">
            <a href="index.php" class="btn btn-primary">Back to Products</a>
        </div>
    <?php else: ?>
        <form method="POST">
            <h4 class="mb-4">Items in Your Cart:</h4>

            <table class="table table-bordered text-center align-middle">
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

            <div class="d-flex justify-content-between align-items-center mt-4">
                <h4 class="mb-0">Total: ₱<?= number_format($total_amount, 2) ?></h4>
                <div class="d-flex gap-2">
                    <a href="cart.php" class="btn btn-danger">Back to Cart</a>
                    <button type="submit" class="btn btn-success">Confirm Order</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
