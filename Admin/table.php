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
    if($user_session['user_role'] != "Admin"){
        header("location: ../Users/");
    }
}
#end of essentials

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
</head>
<body>
<div class="container d-flex justify-content-end gap-2 mb-3">
    <a href="addproduct.php" class="btn btn-success">Add a product</a>
    <a href="adduser.php" class="btn btn-primary">Add a user</a>
    <a href="table.php" class="btn btn-warning">Table View</a>
    <a href="index.php" class="btn btn-warning">Shop View</a>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>
    <form method="POST">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5 rounded shadow-sm">
            <div class="container-fluid">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="All">All</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Snacks">Snacks</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Meals">Meals</button>
                    </li>
                    <li class="nav-item nav-item-spacing">
                        <button class="btn btn-outline-primary nav-link" name="Drinks">Drinks</button>
                    </li>
                </ul>
            </div>
        </nav>
    </form>

    <div class="table-responsive">
        <form action="delete.php" method = "post">
            <button name = "delete_selected" class = "btn btn-danger mb-2 float-end">Delete Selected</button>
            <table class="table table-bordered table-striped">
                <?php if($fetch->rowCount()<1):?>
                    <center><h2>No Product Found</h2></center>
                <?php else:?>
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>
                                <button type="button" class="btn btn-warning btn-sm mx-auto" id="select-all">Select All</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products AS $product):?>
                            <tr>
                                <td><?= $product['product_id']?></td>
                                <td><?= $product['product_name']?></td>
                                <td><?= $product['product_price']?></td>
                                <td><?= $product['product_status']?></td>
                                <td>
                                    <a href="edit.php?id=<?= $product['product_id']?>" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                                <td>
                                    <input type="checkbox" class="delete-checkbox" name = "product_ids[]" value = "<?= $product['product_id']?>">
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                <?php endif?>
            </table>
        </form>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    <a href="../logout.php" class="btn btn-danger">Logout</a>
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
