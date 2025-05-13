<?php
include "adminsession.php";
$filter = "";

if (isset($_POST['Snacks'])) {
    $filter = "Snack";
} elseif (isset($_POST['Meals'])) {
    $filter = "Meal";
} elseif (isset($_POST['Drinks'])) {
    $filter = "Drink";
} elseif (isset($_POST['All'])) {
    $filter = ""; // No filter, show all products
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Miras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        table-layout: auto;
        border-collapse: collapse;
    }

    th, td {
        white-space: normal; /* Allow wrapping */
        padding: 0.5rem;
        vertical-align: middle;
        text-align: center;
    }

    .btn-sm, .btn {
        white-space: nowrap;
    }

    input[type="checkbox"] {
        transform: scale(1.2);
    }
</style>



</head>
<body>
<!-- Header Buttons -->
<div class="container my-4 d-flex justify-content-between flex-wrap gap-2">
    <div class="d-flex flex-wrap gap-2">
        <a href="index.php" class="btn btn-success btn-custom">Sales View</a>
        <a href="history.php" class="btn btn-info text-white btn-custom">Sales History</a>
        <a href="manageuser.php" class="btn btn-primary btn-custom">Manage User</a>
        <a href="table.php" class="btn btn-warning btn-custom">All Products</a>
        <a href="shopview.php" class="btn btn-danger btn-custom">Shop View</a>
    </div>
    <div>
        <a href="../logout.php" class="btn btn-dark btn-custom">Logout</a>
    </div>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>
    <form method="POST">
        <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow-sm mb-4">
            <div class="container-fluid justify-content-center">
                <div class="row w-100">
                    <div class="col-3">
                        <button type="submit" name="All" class="btn btn-primary w-100">All</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Snacks" class="btn btn-primary w-100">Snacks</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Meals" class="btn btn-primary w-100">Meals</button>
                    </div>
                    <div class="col-3">
                        <button type="submit" name="Drinks" class="btn btn-primary w-100">Drinks</button>
                    </div>
                </div>
            </div>
        </nav>
    </form>

    <div class="table-responsive">
        <form action="delete.php" method="post">
            <?php if (count($products) === 0): ?>
                <div class="text-center my-4">
                    <h2>No Product Found</h2>
                    <a href="addproduct.php" class="btn btn-success mt-2">Add Product</a>
                </div>
            <?php else: ?>
                <div class="row justify-content-between mb-3">
                    <div class="col-auto">
                        <a href="addproduct.php" class="btn btn-success">Add Product</a>
                    </div>
                    <div class="col-auto">
                        <button name="delete_selected" class="btn btn-danger">Delete Selected</button>
                    </div>
                </div>

                <table class="table table-bordered align-middle">
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
                                    <a href="edit.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
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
</div>
<script>
    const selectAll = document.getElementById('select-all');
    selectAll.addEventListener("click", () => {
        let checkboxes = document.querySelectorAll('.delete-checkbox');
        let isChecked = Array.from(checkboxes).some(checkbox => !checkbox.checked);
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });
</script>
</body>
</html>
