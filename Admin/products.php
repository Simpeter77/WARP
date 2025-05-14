<?php
include "adminsession.php";

$filter = "";
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}

if ($filter) {
    $condition = "%" . $filter . "%";
    $fetch = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :condition");
    $fetch->execute([':condition' => $condition]);
} else {
    $fetch = $pdo->prepare("SELECT * FROM products");
    $fetch->execute();
}

$products = $fetch->fetchAll();
?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid black;
            border-radius: 4px;
            appearance: none; /* Remove default styling */
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            position: relative;
            background-color: white;
            transition: all 0.2s ease-in-out;
        }

        .form-check-input:checked {
            background-color: #198754; /* Bootstrap success green */
            border-color: #198754;
        }
    </style>
</head>
<div class="table-responsive" style="overflow-x: hidden; width: 100%;">
    <form action="delete.php" method="post">
        <div class="row justify-content-between mb-3">
            <div class="col-auto">
                <a href="addproduct.php" class="btn btn-success">Add Product</a>
            </div>
            <div class="col-auto">
                <?php if (count($products) !== 0):?>
                    <button name="delete_selected" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected products?')">Delete Selected</button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($products) === 0): ?>
            <div class="text-center my-4">
                <h2>No Product Found</h2>
            </div>
        <?php else: ?>
            <table class="table table-bordered align-middle" style="width: 100%; table-layout: auto;">
                <thead class="table-dark text-center">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Price</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                        <th scope="col">
                            <button type="button" class="btn btn-warning btn-sm" id="select-all">Select All</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="text-center"><?= $product['product_id'] ?></td>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td class="text-center">₱<?= number_format($product['product_price'], 2) ?></td>
                            <td class="text-center"><?= $product['product_status'] ?></td>
                            <td class="text-center">
                                <a href="edit.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input delete-checkbox" name="product_ids[]" value="<?= $product['product_id'] ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </form>
</div>

