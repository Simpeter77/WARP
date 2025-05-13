<?php 
include "adminsession.php";
$order_id = $_GET['orderid'];
$fetch_order_details = $pdo->prepare("SELECT products.product_name, products.product_price, order_items.* 
FROM order_items 
LEFT JOIN products ON order_items.product_id = products.product_id 
WHERE order_id = :order_id");
$fetch_order_details->execute([':order_id' => $order_id]);
$order_details = $fetch_order_details->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body style="font-family: 'Arial', sans-serif; background-color: #f4f4f4; padding: 20px;">

<div class="receipt-container" style="width: 100%; max-width: 500px; margin: auto; padding: 20px; border: 1px solid #000; background-color: #fff; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); font-size: 14px;">
    <div class="receipt-header" style="text-align: center; font-weight: bold; margin-bottom: 20px;">
        <h4 style="margin: 0; font-size: 18px;">Order Receipt</h4>
        <p style="margin: 5px 0; font-size: 12px; color: #555;">Order ID: <?= htmlspecialchars($order_id) ?></p>
    </div>

    <div class="receipt-body" style="margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 10px;">
        <?php if (empty($order_details)): ?>
            <p>No items found for this order.</p>
        <?php else: ?>
            <?php foreach ($order_details as $item): ?>
                <div class="receipt-item" style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee;">
                    <span style="display: inline-block; width: 24%;"><?= htmlspecialchars($item['product_name']) ?></span>
                    <span style="display: inline-block; width: 25%;">₱ <?= number_format($item['product_price'], 2) ?> x <?= htmlspecialchars($item['quantity']) ?></span>
                    <span style="display: inline-block; width: 25%;">₱ <?= number_format($item['subtotal'], 2) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="receipt-footer" style="margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 10px; font-weight: bold; display: flex; justify-content: space-between; padding-bottom: 10px;">
        <span class="total-label" style="font-weight: bold;">Total</span>
        <span class="total-amount" style="font-size: 16px; color: #333;">₱ <?= number_format(array_sum(array_column($order_details, 'subtotal')), 2) ?></span>
    </div>
</div>
</body>
</html>
