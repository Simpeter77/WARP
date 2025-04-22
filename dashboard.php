<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ST3S Dash</title>
</head>
<body>
<div class="container my-4">
    <a class="row g-4">
        <h1 class="text-center">MIRA'S</h1>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="card product-card">
                    <img src="<?= htmlspecialchars($product['product_img']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <p class="card-text">₱<?= number_format($product['product_price'], 2) ?></p>
                        <button><a href="../sss/remove.php?ID=<?php echo $row['product_id']; ?>">Remove</a></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </a>
</div>
    
</body>
</html>