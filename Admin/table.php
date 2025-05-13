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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow-sm mb-4">
        <div class="container-fluid justify-content-center">
            <div class="row w-100">
                <div class="col-3">
                    <button value="" class="btn btn-primary w-100" onclick="filtertable(this.value)">All</button>
                </div>
                <div class="col-3">
                    <button value="Snack" class="btn btn-primary w-100" onclick="filtertable(this.value)">Snacks</button>
                </div>
                <div class="col-3">
                    <button value="Meal" class="btn btn-primary w-100" onclick="filtertable(this.value)">Meals</button>
                </div>
                <div class="col-3">
                    <button value="Drink" class="btn btn-primary w-100" onclick="filtertable(this.value)">Drinks</button>
                </div>
            </div>
        </div>
    </nav>

    <div id="product-table"></div> <!-- Dynamic products will be loaded here -->

</div>

<script>
    window.onload = function() {
    filtertable("");
    };


    function filtertable(filter){
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                document.getElementById('product-table').innerHTML = xhr.responseText;
                addSelectAll();
            }
        }
        xhr.open("GET", "products.php?filter=" + filter, true);
        xhr.send();
    }

    function addSelectAll(){
        const selectAll = document.getElementById('select-all');
        selectAll.addEventListener("click", () => {
            let checkboxes = document.querySelectorAll('.delete-checkbox');
            let isChecked = Array.from(checkboxes).some(checkbox => !checkbox.checked);
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }
</script>
</body>
</html>

