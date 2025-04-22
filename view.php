<?php
include "dbconfig.php";
session_start();
$name = $_SESSION['NAME'];
$fetch=$pdo->prepare("SELECT * FROM products where product_name =:name");
$fetch->execute([":name" => $name]);
$display = $fetch->fetch();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>view</title>
</head>
<body>
    <div>
        <div>
            <p>Product Name:<?= $display['product_name'] ?></p>
            <p>Product price:<?= $display['product_price'] ?></p>
        </div>
    </div>
</body>
</html>