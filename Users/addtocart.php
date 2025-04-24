<?php 
# essentials
session_start();
include "../dbconfig.php";
include "../style.php";

if (!isset($_SESSION['USER'])) {
    header("location: logout.php");
    exit;
}

$user_session = $_SESSION['USER'];
if ($user_session['user_role'] !== "User") {
    header("location: ../Admin/");
    exit;
}


if (isset($_POST['addtocart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);

    if (!isset($_SESSION['CART'])) {
        $_SESSION['CART'] = [];
    }

    if (isset($_SESSION['CART'][$product_id])) {
        $_SESSION['CART'][$product_id]['quantity'] += $quantity;
    } else {
        $fetch = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $fetch->execute([':product_id' => $product_id]);
        $product = $fetch->fetch();

        if ($product) {
            $_SESSION['CART'][$product_id] = [
                'product_id' => $product_id,
                'product_name' => $product['product_name'],
                'product_price' => $product['product_price'],
                'product_image' => $product['product_image'],
                'quantity' => $quantity
            ];
        }
    }

    header("location: cart.php?added=1");
    exit;
}
?>
