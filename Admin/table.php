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
            overflow-x: hidden; 
        }

        table {
            width: 100%; 
            table-layout: auto; 
            border-collapse: collapse;
        }

        th, td {
            white-space: normal; 
            word-wrap: break-word;
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
        .nav-row {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 1rem 1.5rem;
        }

        .nav-row {
            background-color: #ffffff;
            border-radius: 12px;
        }

        .nav-button {
            padding: 0.45rem 1.1rem;
            background-color: #f2f2f2;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .nav-button:hover,
        .nav-button:focus {
            background-color: #e0e0e0;
            color: #111;
            transform: translateY(-1px);
        }

        .nav-button.active {
            background-color: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
        }

        .logout {
            background-color: #ffe5e5;
            color: #c0392b;
        }

        .logout:hover,
        .logout:focus {
            background-color: #ffd6d6;
            color: #922b21;
        }
    </style>
</head>
<body>

<!-- Header Navigation -->
<div class="container my-4 mt-0">
  <div class="nav-row d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 rounded">
    
    <div class="d-flex flex-wrap gap-2 align-items-center">
      <a href="index.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Sales View</a>
      <a href="history.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">Sales History</a>
      <a href="manageuser.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'manageuser.php' ? 'active' : '' ?>">Manage User</a>
      <a href="table.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'table.php' ? 'active' : '' ?>">All Products</a>
      <a href="shopview.php" class="nav-button <?= basename($_SERVER['PHP_SELF']) == 'shopview.php' ? 'active' : '' ?>">Shop View</a>
    </div>

    <div>
      <a href="../logout.php" class="nav-button logout">Logout</a>
    </div>

  </div>
</div>

<div class="container my-4">
    <h1 class="text-center fw-bold text-primary mb-4">MIRA'S</h1>

    <nav class="navbar navbar-expand-lg navbar-light bg-white rounded mb-4">
        <div class="container-fluid justify-content-center">
            <div class="row w-100">
                <div class="col-3">
                    <button value="" class="btn w-100 <?= $currentFilter == '' ? 'btn-primary' : 'btn-secondary' ?>" onclick="filtertable('')">All</button>
                </div>
                <div class="col-3">
                    <button value="Snack" class="btn w-100 <?= $currentFilter == 'Snack' ? 'btn-primary' : 'btn-secondary' ?>" onclick="filtertable('Snack')">Snacks</button>
                </div>
                <div class="col-3">
                    <button value="Meal" class="btn w-100 <?= $currentFilter == 'Meal' ? 'btn-primary' : 'btn-secondary' ?>" onclick="filtertable('Meal')">Meals</button>
                </div>
                <div class="col-3">
                    <button value="Drink" class="btn w-100 <?= $currentFilter == 'Drink' ? 'btn-primary' : 'btn-secondary' ?>" onclick="filtertable('Drink')">Drinks</button>
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
        const buttons = document.querySelectorAll('.navbar .btn');
        buttons.forEach(btn => {
            if (btn.getAttribute("value") === filter) {
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-secondary');
            }
        });
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

